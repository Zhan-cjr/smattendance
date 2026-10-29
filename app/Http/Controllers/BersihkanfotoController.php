<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Presensi;
use App\Models\Kunjungan;
use App\Models\AktivitasKaryawan;
use App\Models\ChecklistLaporan;
use App\Models\Pengaturanumum;

class BersihkanfotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $setting = Pengaturanumum::first();
        $defaultDays = $setting->auto_hapus_foto_hari ?? 60;
        $cutoffDate = Carbon::now()->subDays($defaultDays)->format('Y-m-d');

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.utilities.bersihkanfoto.index' : 'utilities.bersihkanfoto.index';
        return view($view, compact('setting', 'defaultDays', 'cutoffDate'));
    }

    /**
     * Toggle auto-clean photos setting (Cron Job toggle)
     */
    public function toggleAutoClean(Request $request)
    {
        try {
            $setting = Pengaturanumum::first();
            if (!$setting) {
                $setting = new Pengaturanumum();
            }

            $isActive = $request->boolean('auto_hapus_foto');
            $days = $request->input('auto_hapus_foto_hari', 60);

            if ($days < 1 || $days > 365) {
                $days = 60;
            }

            $setting->auto_hapus_foto = $isActive;
            $setting->auto_hapus_foto_hari = $days;
            $setting->save();

            $statusText = $isActive ? 'Diaktifkan' : 'Dinonaktifkan';

            return response()->json([
                'success' => true,
                'status' => $isActive,
                'days' => $days,
                'message' => "Pembersihan foto otomatis berhasil {$statusText} (Batas retensi: {$days} hari)."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengubah pengaturan otomatis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            // Validasi input
            if (!$request->filled('tanggal_awal') || !$request->filled('tanggal_akhir')) {
                return response()->json(['error' => 'Tanggal awal dan tanggal akhir harus diisi'], 400);
            }

            $jenis = $request->input('jenis', []);
            if (empty($jenis)) {
                return response()->json(['error' => 'Silakan pilih minimal satu jenis foto yang akan dihapus'], 400);
            }

            $totalDeleted = 0;
            $details = [];
            $disk = Storage::disk('public');

            // 1. Hapus semua foto presensi dalam periode
            if (in_array('presensi', $jenis)) {
                $query = Presensi::where(function ($q) {
                    $q->whereNotNull('foto_in')->where('foto_in', '!=', '')
                        ->orWhere(function ($q2) {
                            $q2->whereNotNull('foto_out')->where('foto_out', '!=', '');
                        })
                        ->orWhere(function ($q3) {
                            $q3->whereNotNull('foto_istirahat_in')->where('foto_istirahat_in', '!=', '');
                        })
                        ->orWhere(function ($q4) {
                            $q4->whereNotNull('foto_istirahat_out')->where('foto_istirahat_out', '!=', '');
                        });
                });

                // Filter berdasarkan periode
                $query->whereBetween('tanggal', [
                    $request->tanggal_awal,
                    $request->tanggal_akhir
                ]);

                $presensis = $query->get();

                $deletedCount = 0;
                foreach ($presensis as $presensi) {
                    $deletedCount += $this->deletePresensiPhotos($presensi);
                }
                $totalDeleted += $deletedCount;
                if ($deletedCount > 0) $details[] = "{$deletedCount} Presensi";
            }

            // 2. Kunjungan
            if (in_array('kunjungan', $jenis)) {
                $kunjungans = Kunjungan::whereNotNull('foto')->where('foto', '!=', '')
                    ->whereBetween('tanggal_kunjungan', [$request->tanggal_awal, $request->tanggal_akhir])
                    ->get();
                
                $deletedCount = 0;
                foreach ($kunjungans as $kunjungan) {
                    if ($disk->exists('uploads/kunjungan/' . $kunjungan->foto)) {
                        $disk->delete('uploads/kunjungan/' . $kunjungan->foto);
                        $deletedCount++;
                    }
                }
                $totalDeleted += $deletedCount;
                if ($deletedCount > 0) $details[] = "{$deletedCount} Kunjungan";
            }

            // 3. Aktivitas Karyawan
            if (in_array('aktivitas', $jenis)) {
                $aktivitas = AktivitasKaryawan::whereNotNull('foto')->where('foto', '!=', '')
                    ->whereDate('created_at', '>=', $request->tanggal_awal)
                    ->whereDate('created_at', '<=', $request->tanggal_akhir)
                    ->get();
                
                $deletedCount = 0;
                foreach ($aktivitas as $akt) {
                    if ($disk->exists('uploads/aktivitas/' . $akt->foto)) {
                        $disk->delete('uploads/aktivitas/' . $akt->foto);
                        $deletedCount++;
                    }
                }
                $totalDeleted += $deletedCount;
                if ($deletedCount > 0) $details[] = "{$deletedCount} Aktivitas Karyawan";
            }

            // 4. Checklist & Kolase Grid 4-in-1
            if (in_array('checklist', $jenis)) {
                $checklistLaporans = ChecklistLaporan::with(['details', 'collages'])
                    ->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir])
                    ->get();

                $deletedChecklistCount = 0;
                foreach ($checklistLaporans as $lap) {
                    // Checkpoint detail photos
                    foreach ($lap->details as $det) {
                        if ($det->foto) {
                            $path = str_starts_with($det->foto, 'checklist/') ? $det->foto : 'checklist/photos/' . $det->foto;
                            if ($disk->exists($path)) {
                                $disk->delete($path);
                                $deletedChecklistCount++;
                            }
                        }
                    }

                    // Collages 4-in-1
                    foreach ($lap->collages as $col) {
                        if ($col->foto_collage) {
                            $colPath = str_starts_with($col->foto_collage, 'checklist/') ? $col->foto_collage : 'checklist/collages/' . $col->foto_collage;
                            if ($disk->exists($colPath)) {
                                $disk->delete($colPath);
                                $deletedChecklistCount++;
                            }
                        }
                    }
                }
                $totalDeleted += $deletedChecklistCount;
                if ($deletedChecklistCount > 0) $details[] = "{$deletedChecklistCount} Checklist & Grid";
            }

            $detailStr = !empty($details) ? implode(', ', $details) : '0';

            return response()->json([
                'success' => "Berhasil menghapus total {$totalDeleted} file foto ({$detailStr}) dalam periode {$request->tanggal_awal} sampai {$request->tanggal_akhir} (data record & rekapitulasi tetap tersimpan 100%)"
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete all photos from a presensi record (only files, not database records)
     */
    private function deletePresensiPhotos($presensi)
    {
        $deletedCount = 0;
        $disk = Storage::disk('public');

        // Delete foto_in file only
        if ($presensi->foto_in && $disk->exists('uploads/absensi/' . $presensi->foto_in)) {
            $disk->delete('uploads/absensi/' . $presensi->foto_in);
            $deletedCount++;
        }

        // Delete foto_out file only
        if ($presensi->foto_out && $disk->exists('uploads/absensi/' . $presensi->foto_out)) {
            $disk->delete('uploads/absensi/' . $presensi->foto_out);
            $deletedCount++;
        }

        // Delete foto_istirahat_in file only
        if ($presensi->foto_istirahat_in && $disk->exists('uploads/absensi/' . $presensi->foto_istirahat_in)) {
            $disk->delete('uploads/absensi/' . $presensi->foto_istirahat_in);
            $deletedCount++;
        }

        // Delete foto_istirahat_out file only
        if ($presensi->foto_istirahat_out && $disk->exists('uploads/absensi/' . $presensi->foto_istirahat_out)) {
            $disk->delete('uploads/absensi/' . $presensi->foto_istirahat_out);
            $deletedCount++;
        }

        // Note: Database records are NOT updated - only files are deleted
        return $deletedCount;
    }
}
