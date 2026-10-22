<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ChecklistImageService
{
    protected ?ImageManager $manager = null;

    protected function getManager(): ImageManager
    {
        if (!$this->manager) {
            $this->manager = new ImageManager(new Driver());
        }
        return $this->manager;
    }

    /**
     * Save single checkpoint photo and stamp watermark
     * 
     * @param string|\Illuminate\Http\UploadedFile $imageInput Base64 data URL or UploadedFile
     * @param array $meta ['nama_karyawan', 'waktu', 'lokasi', 'nama_checkpoint', 'tanggal']
     * @return string Relative storage path (e.g. 'checklist/photos/photo_xxx.jpg')
     */
    public function saveCheckpointPhoto($imageInput, array $meta = []): string
    {
        $disk = Storage::disk('public');
        $photoDir = 'checklist/photos';
        if (!$disk->exists($photoDir)) {
            $disk->makeDirectory($photoDir);
        }

        $filename = 'chk_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.jpg';
        $relativePath = $photoDir . '/' . $filename;
        $fullPath = $disk->path($relativePath);

        // OPTIMIZATION 1: Binary UploadedFile from client canvas.toBlob() (instant 1ms disk write)
        if ($imageInput instanceof \Illuminate\Http\UploadedFile) {
            $disk->putFileAs($photoDir, $imageInput, $filename);

            return $relativePath;
        }

        // OPTIMIZATION 2: Base64 data string from client canvas
        if (is_string($imageInput) && str_starts_with($imageInput, 'data:image')) {
            $dataParts = explode(',', $imageInput);
            $rawBinary = base64_decode(end($dataParts));
            $disk->put($relativePath, $rawBinary);

            return $relativePath;
        }

        // Fallback for raw file uploads without client watermark
        $manager = $this->getManager();
        $image = $manager->read($imageInput);

        if ($image->width() > 1000 || $image->height() > 1000) {
            $image->scaleDown(1000, 1000);
        }

        $this->applyWatermark($image, $meta);
        $image->toJpeg(80)->save($fullPath);

        return $relativePath;
    }

    /**
     * Create 2x2 collage (4 photos per grid) for WhatsApp sharing
     * Uses optimized native GD truecolor resampler for ultra-fast execution (~20ms per grid)
     *
     * @param array $photoPaths Array of relative paths in public disk
     * @param int|string $laporanId
     * @return array Array of created collage paths
     */
    public function createCollages(array $photoPaths, $laporanId): array
    {
        $disk = Storage::disk('public');
        $collageDir = 'checklist/collages';
        if (!$disk->exists($collageDir)) {
            $disk->makeDirectory($collageDir);
        }

        $chunks = array_chunk($photoPaths, 4);
        $results = [];

        $gridWidth = 1280;
        $gridHeight = 960;
        $gap = 10;
        $cellW = (int)(($gridWidth - (3 * $gap)) / 2); // 625px (4:3 aspect ratio)
        $cellH = (int)(($gridHeight - (3 * $gap)) / 2); // 465px (4:3 aspect ratio)

        $positions = [
            0 => ['x' => $gap, 'y' => $gap],
            1 => ['x' => $gap + $cellW + $gap, 'y' => $gap],
            2 => ['x' => $gap, 'y' => $gap + $cellH + $gap],
            3 => ['x' => $gap + $cellW + $gap, 'y' => $gap + $cellH + $gap],
        ];

        foreach ($chunks as $idx => $chunkPhotos) {
            $batchNo = $idx + 1;
            
            // Native GD truecolor canvas with pure white divider grid lines
            $canvas = imagecreatetruecolor($gridWidth, $gridHeight);
            $bgColor = imagecolorallocate($canvas, 255, 255, 255); // White divider lines between photos
            imagefilledrectangle($canvas, 0, 0, $gridWidth, $gridHeight, $bgColor);

            foreach ($chunkPhotos as $pIdx => $photoRelPath) {
                if ($pIdx > 3) break;
                if (!$disk->exists($photoRelPath)) continue;

                $fullPhotoPath = $disk->path($photoRelPath);
                $srcImg = @imagecreatefromjpeg($fullPhotoPath);
                if (!$srcImg) {
                    $srcImg = @imagecreatefromstring(file_get_contents($fullPhotoPath));
                }

                if ($srcImg) {
                    $sw = imagesx($srcImg);
                    $sh = imagesy($srcImg);

                    $srcAspect = $sw / $sh;
                    $cellAspect = $cellW / $cellH;

                    if (abs($srcAspect - $cellAspect) < 0.05) {
                        // Exact match (Landscape 4:3) - Zero crop!
                        $srcX = 0;
                        $srcY = 0;
                        $tempW = $sw;
                        $tempH = $sh;
                    } elseif ($srcAspect > $cellAspect) {
                        $tempH = $sh;
                        $tempW = (int)($sh * $cellAspect);
                        $srcX = (int)(($sw - $tempW) / 2);
                        $srcY = 0;
                    } else {
                        $tempW = $sw;
                        $tempH = (int)($sw / $cellAspect);
                        $srcX = 0;
                        // Anchor to bottom so watermark is never cut off
                        $srcY = max(0, $sh - $tempH);
                    }

                    $pos = $positions[$pIdx];
                    imagecopyresampled(
                        $canvas, $srcImg,
                        $pos['x'], $pos['y'],
                        $srcX, $srcY,
                        $cellW, $cellH,
                        $tempW, $tempH
                    );

                    imagedestroy($srcImg);
                }
            }

            $collageFilename = 'collage_' . $laporanId . '_b' . $batchNo . '_' . time() . '.jpg';
            $collageRelPath = $collageDir . '/' . $collageFilename;
            $collageFullPath = $disk->path($collageRelPath);

            // Save at 88% quality (ultra crisp text on WhatsApp)
            imagejpeg($canvas, $collageFullPath, 88);
            imagedestroy($canvas);

            $results[] = [
                'batch_no' => $batchNo,
                'path' => $collageRelPath,
                'count' => count($chunkPhotos),
            ];
        }

        return $results;
    }

    /**
     * Draw watermarking overlay on GD resource
     */
    protected function applyWatermark($image, array $meta): void
    {
        $gd = $image->core()->native();
        $w = $image->width();
        $h = $image->height();

        $jam = $meta['waktu'] ?? date('H:i');
        $tgl = $meta['tanggal'] ?? date('d M Y');
        $nama = $meta['nama_karyawan'] ?? 'Karyawan';
        $lokasi = $meta['lokasi'] ?? 'Lokasi Toko';
        $point = $meta['nama_checkpoint'] ?? 'Checkpoint';

        // Bottom banner background
        $bannerH = max(110, (int)($h * 0.18));
        $bannerY = $h - $bannerH;

        // Semi-transparent black gradient overlay at bottom
        for ($i = 0; $i < $bannerH; $i++) {
            $alpha = (int)(10 + (60 * ($i / $bannerH))); // 0 to 127 in GD (0 opaque, 127 transparent)
            // GD imagecolorallocatealpha alpha 0 (opaque) - 127 (transparent)
            // We want darker at bottom
            $gdAlpha = max(0, min(127, (int)(110 - (70 * ($i / $bannerH)))));
            $col = imagecolorallocatealpha($gd, 0, 0, 0, $gdAlpha);
            imageline($gd, 0, $bannerY + $i, $w, $bannerY + $i, $col);
        }

        // Colors
        $white = imagecolorallocate($gd, 255, 255, 255);
        $amber = imagecolorallocate($gd, 245, 158, 11);
        $slate = imagecolorallocate($gd, 203, 213, 225);
        $badgeBg = imagecolorallocate($gd, 217, 119, 6);

        // Badge REPORT TIME
        $padX = 18;
        $padY = $bannerY + 12;
        imagefilledrectangle($gd, $padX, $padY, $padX + 130, $padY + 26, $badgeBg);
        imagestring($gd, 4, $padX + 8, $padY + 5, "REPORT " . $jam, $white);

        // Line 1: Checkpoint Title
        imagestring($gd, 5, $padX + 145, $padY + 5, substr($point, 0, 42), $amber);

        // Line 2: Date & Employee
        $line2Y = $padY + 34;
        $strDatePIC = $tgl . " - Petugas: " . substr($nama, 0, 30);
        imagestring($gd, 4, $padX, $line2Y, $strDatePIC, $white);

        // Line 3: Location / Coordinates
        $line3Y = $line2Y + 22;
        $strLoc = "Lokasi: " . substr($lokasi, 0, 60);
        imagestring($gd, 3, $padX, $line3Y, $strLoc, $slate);
    }
}
