<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\LiveLocation;use App\Models\Pengaturanumum;
use App\Models\Presensi;use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LiveLocationController extends Controller
{
    /**
     * Store live location tracking data
     * Called from mobile karyawan when spy=1
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nik' => 'required|string|size:9|exists:karyawan,nik',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'lokasi' => 'nullable|string',
                'accuracy' => 'nullable|numeric'
            ]);

            $karyawan = Karyawan::find($validated['nik']);

            // Check if spy is enabled for this employee
            if ($karyawan && $karyawan->spy == 1) {
                // Get general settings
                $generalSetting = Pengaturanumum::first();
                
                // Check if live location monitoring is enabled globally
                if (!($generalSetting->enable_live_location_monitoring ?? true)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Fitur monitoring live location dinonaktifkan'
                    ], 403);
                }
                
                $monitoringMode = $generalSetting->monitoring_live_location_mode ?? 0;

                // Check if monitoring should be active based on mode
                $shouldTrack = false;

                if ($monitoringMode == 0) {
                    // Mode 0: 24 jam - always track if spy=1
                    $shouldTrack = true;
                } elseif ($monitoringMode == 1) {
                    // Mode 1: Hanya jam kerja - track only if checked in and not checked out
                    $today = now()->toDateString();
                    $presensi = Presensi::where('nik', $validated['nik'])
                        ->where('tanggal', $today)
                        ->whereNotNull('jam_in')
                        ->whereNull('jam_out')
                        ->first();

                    if ($presensi) {
                        $shouldTrack = true;
                    }
                }

                if ($shouldTrack) {
                    $liveLocation = LiveLocation::create([
                        'nik' => $validated['nik'],
                        'latitude' => $validated['latitude'],
                        'longitude' => $validated['longitude'],
                        'lokasi' => $validated['lokasi'] ?? null,
                        'accuracy' => $validated['accuracy'] ?? null,
                        'tracked_at' => now()
                    ]);

                    Log::info('Live location tracked for employee: ' . $validated['nik'] . ' (Mode: ' . $monitoringMode . ')');

                    return response()->json([
                        'status' => true,
                        'message' => 'Lokasi berhasil dicatat',
                        'data' => $liveLocation
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Monitoring live location tidak aktif sesuai pengaturan'
                    ], 403);
                }
            }

            return response()->json([
                'status' => false,
                'message' => 'Live location monitoring tidak diaktifkan untuk karyawan ini'
            ], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing live location: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest location for a specific employee
     */
    public function getLatest($nik)
    {
        try {
            $liveLocation = LiveLocation::where('nik', $nik)
                ->latest('tracked_at')
                ->first();

            if (!$liveLocation) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data lokasi'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $liveLocation
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get location history for a specific employee (last 100 records)
     */
    public function getHistory($nik, Request $request)
    {
        try {
            $limit = $request->input('limit', 100);
            $minutes = $request->input('minutes', 60); // Default last 60 minutes

            $locations = LiveLocation::where('nik', $nik)
                ->where('tracked_at', '>=', now()->subMinutes($minutes))
                ->orderBy('tracked_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => true,
                'count' => count($locations),
                'data' => $locations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all employees with spy enabled and their latest locations
     * For monitoring dashboard
     */
    public function getAllSpyLocations(Request $request)
    {
        try {
            $spyEmployees = Karyawan::where('spy', 1)
                ->where('status_aktif_karyawan', 1)
                ->with(['latestLiveLocation', 'jabatan', 'departemen', 'cabang'])
                ->get();

            return response()->json([
                'status' => true,
                'count' => count($spyEmployees),
                'data' => $spyEmployees
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete old location data (keep only last 7 days)
     * Run via scheduled command
     */
    public function cleanupOldData()
    {
        try {
            $deleted = LiveLocation::where('tracked_at', '<', now()->subDays(7))->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data lama berhasil dihapus',
                'deleted_records' => $deleted
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check spy status untuk mobile app
     * Called from mobile to determine jika user punya spy=1
     * Used untuk setup background location tracking
     */
    public function checkSpyStatus(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $karyawan = Karyawan::where('nik', $user->nik)->first();

            if (!$karyawan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'nik' => $user->nik,
                'name' => $user->name ?? $karyawan->nama_karyawan,
                'spy' => (int) $karyawan->spy,
                'message' => $karyawan->spy == 1 
                    ? 'Live location monitoring enabled' 
                    : 'Live location monitoring disabled'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error checking spy status: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
