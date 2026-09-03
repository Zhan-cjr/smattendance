<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Presensi;
use App\Models\Kunjungan;
use App\Models\AktivitasKaryawan;

class BersihkanfotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('utilities.bersihkanfoto.index');
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

            // Hapus semua foto presensi dalam periode
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

            if (in_array('kunjungan', $jenis)) {
                $kunjungans = Kunjungan::whereNotNull('foto')->where('foto', '!=', '')
                    ->whereBetween('tanggal_kunjungan', [$request->tanggal_awal, $request->tanggal_akhir])
                    ->get();
                
                $deletedCount = 0;
                foreach ($kunjungans as $kunjungan) {
                    if (Storage::disk('public')->exists('uploads/kunjungan/' . $kunjungan->foto)) {
                        Storage::disk('public')->delete('uploads/kunjungan/' . $kunjungan->foto);
                        $deletedCount++;
                    }
                }
                $totalDeleted += $deletedCount;
                if ($deletedCount > 0) $details[] = "{$deletedCount} Kunjungan";
            }

            if (in_array('aktivitas', $jenis)) {
                $aktivitas = AktivitasKaryawan::whereNotNull('foto')->where('foto', '!=', '')
                    ->whereDate('created_at', '>=', $request->tanggal_awal)
                    ->whereDate('created_at', '<=', $request->tanggal_akhir)
                    ->get();
                
                $deletedCount = 0;
                foreach ($aktivitas as $akt) {
                    if (Storage::disk('public')->exists('uploads/aktivitas/' . $akt->foto)) {
                        Storage::disk('public')->delete('uploads/aktivitas/' . $akt->foto);
                        $deletedCount++;
                    }
                }
                $totalDeleted += $deletedCount;
                if ($deletedCount > 0) $details[] = "{$deletedCount} Aktivitas Karyawan";
            }

            $detailStr = !empty($details) ? implode(', ', $details) : '0';

            return response()->json([
                'success' => "Berhasil menghapus total {$totalDeleted} file foto ({$detailStr}) dalam periode {$request->tanggal_awal} sampai {$request->tanggal_akhir} (data presensi/record tetap tersimpan)"
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

        // Delete foto_in file only
        if ($presensi->foto_in && Storage::disk('public')->exists('uploads/absensi/' . $presensi->foto_in)) {
            Storage::disk('public')->delete('uploads/absensi/' . $presensi->foto_in);
            $deletedCount++;
        }

        // Delete foto_out file only
        if ($presensi->foto_out && Storage::disk('public')->exists('uploads/absensi/' . $presensi->foto_out)) {
            Storage::disk('public')->delete('uploads/absensi/' . $presensi->foto_out);
            $deletedCount++;
        }

        // Delete foto_istirahat_in file only
        if ($presensi->foto_istirahat_in && Storage::disk('public')->exists('uploads/absensi/' . $presensi->foto_istirahat_in)) {
            Storage::disk('public')->delete('uploads/absensi/' . $presensi->foto_istirahat_in);
            $deletedCount++;
        }

        // Delete foto_istirahat_out file only
        if ($presensi->foto_istirahat_out && Storage::disk('public')->exists('uploads/absensi/' . $presensi->foto_istirahat_out)) {
            Storage::disk('public')->delete('uploads/absensi/' . $presensi->foto_istirahat_out);
            $deletedCount++;
        }

        // Note: Database records are NOT updated - only files are deleted
        return $deletedCount;
    }
}
