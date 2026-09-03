<?php

namespace App\Http\Controllers;

use App\Models\Update;
use App\Models\UpdateLog;
use App\Services\UpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    protected $updateService;

    public function __construct(UpdateService $updateService)
    {
        $this->updateService = $updateService;
        $this->middleware('auth');
    }

    /**
     * Halaman utama update
     */
    public function index()
    {
        $currentVersion = $this->updateService->getCurrentVersion();
        $updateLogs = UpdateLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('update.index', compact('currentVersion', 'updateLogs'));
    }

    /**
     * Check update terbaru
     */
    public function checkUpdate(Request $request)
    {
        try {
            $updateServerUrl = $request->input('update_server_url');
            $result = $this->updateService->checkUpdate($updateServerUrl);

            if ($request->expectsJson()) {
                return response()->json($result);
            }

            return redirect()->route('update.index')
                ->with(
                    $result['has_update'] ? 'success' : 'info',
                    $result['has_update']
                        ? 'Update tersedia: Versi ' . $result['latest_version']
                        : 'Aplikasi sudah menggunakan versi terbaru'
                );
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Gagal mengecek update: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('update.index')
                ->with('error', 'Gagal mengecek update: ' . $e->getMessage());
        }
    }

    /**
     * Download update
     */
    public function downloadUpdate(Request $request, $version)
    {
        try {
            // Cek apakah update ada di database lokal
            $update = Update::where('version', $version)->first();

            // Jika tidak ada di database lokal, ambil dari server eksternal
            if (!$update) {
                $updateServerUrl = config('update.server_url');

                if ($updateServerUrl) {
                    // Ambil data update dari server eksternal
                    $checkResult = $this->updateService->checkUpdate($updateServerUrl);

                    if (isset($checkResult['update']) && $checkResult['update']['version'] === $version) {
                        $updateData = $checkResult['update'];

                        // Parse released_at jika berupa string
                        $releasedAt = now();
                        if (isset($updateData['released_at'])) {
                            try {
                                $releasedAt = is_string($updateData['released_at'])
                                    ? \Carbon\Carbon::parse($updateData['released_at'])
                                    : $updateData['released_at'];
                            } catch (\Exception $e) {
                                $releasedAt = now();
                            }
                        }

                        // Simpan ke database lokal sementara
                        $update = Update::create([
                            'version' => $updateData['version'],
                            'title' => $updateData['title'] ?? 'Update ' . $updateData['version'],
                            'description' => $updateData['description'] ?? null,
                            'changelog' => $updateData['changelog'] ?? null,
                            'file_url' => $updateData['file_url'],
                            'file_size' => $updateData['file_size'] ?? null,
                            'checksum' => $updateData['checksum'] ?? null,
                            'is_major' => $updateData['is_major'] ?? false,
                            'is_active' => false,
                            'migrations' => is_array($updateData['migrations'] ?? null) ? json_encode($updateData['migrations']) : ($updateData['migrations'] ?? null),
                            'seeders' => is_array($updateData['seeders'] ?? null) ? json_encode($updateData['seeders']) : ($updateData['seeders'] ?? null),
                            'released_at' => $releasedAt,
                        ]);
                    } else {
                        throw new \Exception('Update versi ' . $version . ' tidak ditemukan di server');
                    }
                } else {
                    throw new \Exception('Update versi ' . $version . ' tidak ditemukan. Pastikan update sudah diinput di database atau server update sudah dikonfigurasi.');
                }
            }

            // Buat log update
            $updateLog = UpdateLog::create([
                'user_id' => Auth::id(),
                'version' => $update->version,
                'status' => 'pending',
            ]);

            // Download file
            $success = $this->updateService->downloadUpdate($update, $updateLog);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => $success,
                    'message' => $success ? 'File berhasil diunduh' : 'Gagal mengunduh file',
                    'update_log_id' => $updateLog->id,
                ]);
            }

            return redirect()->route('update.index')
                ->with(
                    $success ? 'success' : 'error',
                    $success ? 'File update berhasil diunduh' : 'Gagal mengunduh file update'
                );
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Gagal mengunduh update: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('update.index')
                ->with('error', 'Gagal mengunduh update: ' . $e->getMessage());
        }
    }

    /**
     * Install update
     */
    public function installUpdate(Request $request, $version)
    {
        try {
            // Cek apakah update ada di database lokal
            $update = Update::where('version', $version)->first();

            // Jika tidak ada di database lokal, ambil dari server eksternal
            if (!$update) {
                $updateServerUrl = config('update.server_url');

                if ($updateServerUrl) {
                    // Ambil data update dari server eksternal
                    $checkResult = $this->updateService->checkUpdate($updateServerUrl);

                    if (isset($checkResult['update']) && $checkResult['update']['version'] === $version) {
                        $updateData = $checkResult['update'];

                        // Parse released_at jika berupa string
                        $releasedAt = now();
                        if (isset($updateData['released_at'])) {
                            try {
                                $releasedAt = is_string($updateData['released_at'])
                                    ? \Carbon\Carbon::parse($updateData['released_at'])
                                    : $updateData['released_at'];
                            } catch (\Exception $e) {
                                $releasedAt = now();
                            }
                        }

                        // Simpan ke database lokal sementara
                        $update = Update::create([
                            'version' => $updateData['version'],
                            'title' => $updateData['title'] ?? 'Update ' . $updateData['version'],
                            'description' => $updateData['description'] ?? null,
                            'changelog' => $updateData['changelog'] ?? null,
                            'file_url' => $updateData['file_url'],
                            'file_size' => $updateData['file_size'] ?? null,
                            'checksum' => $updateData['checksum'] ?? null,
                            'is_major' => $updateData['is_major'] ?? false,
                            'is_active' => false,
                            'migrations' => is_array($updateData['migrations'] ?? null) ? json_encode($updateData['migrations']) : ($updateData['migrations'] ?? null),
                            'seeders' => is_array($updateData['seeders'] ?? null) ? json_encode($updateData['seeders']) : ($updateData['seeders'] ?? null),
                            'released_at' => $releasedAt,
                        ]);
                    } else {
                        throw new \Exception('Update versi ' . $version . ' tidak ditemukan di server');
                    }
                } else {
                    throw new \Exception('Update versi ' . $version . ' tidak ditemukan. Pastikan update sudah diinput di database atau server update sudah dikonfigurasi.');
                }
            }

            // Cari atau buat log update
            $updateLog = UpdateLog::where('version', $version)
                ->where('status', '!=', 'success')
                ->latest()
                ->first();

            if (!$updateLog) {
                $updateLog = UpdateLog::create([
                    'user_id' => Auth::id(),
                    'version' => $update->version,
                    'status' => 'pending',
                ]);
            }

            // Install update
            $success = $this->updateService->installUpdate($update, $updateLog, Auth::id());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => $success,
                    'message' => $success ? 'Update berhasil diinstall' : 'Gagal menginstall update',
                    'update_log' => $updateLog->fresh(),
                ]);
            }

            return redirect()->route('update.index')
                ->with(
                    $success ? 'success' : 'error',
                    $success ? 'Update berhasil diinstall' : 'Gagal menginstall update'
                );
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Gagal menginstall update: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('update.index')
                ->with('error', 'Gagal menginstall update: ' . $e->getMessage());
        }
    }

    /**
     * Update langsung (download + install)
     */
    public function updateNow(Request $request, $version)
    {
        try {
            // Cek apakah versi yang akan diupdate sama dengan versi saat ini
            $currentVersion = $this->updateService->getCurrentVersion();
            if (version_compare($currentVersion, $version, '>=')) {
                $message = 'Aplikasi sudah menggunakan versi terbaru (v' . $currentVersion . ')';
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'already_updated' => true,
                        'message' => $message,
                        'current_version' => $currentVersion,
                    ]);
                }
                
                return redirect()->route('update.index')->with('info', $message);
            }

            // Cek apakah update ada di database lokal
            $update = Update::where('version', $version)->first();

            // Jika tidak ada di database lokal, ambil dari server eksternal
            if (!$update) {
                $updateServerUrl = config('update.server_url');

                if ($updateServerUrl) {
                    // Ambil data update dari server eksternal
                    $checkResult = $this->updateService->checkUpdate($updateServerUrl);

                    if (isset($checkResult['update']) && $checkResult['update']['version'] === $version) {
                        $updateData = $checkResult['update'];

                        // Simpan ke database lokal sementara (tidak aktif, hanya untuk proses update)
                        $update = Update::create([
                            'version' => $updateData['version'],
                            'title' => $updateData['title'] ?? 'Update ' . $updateData['version'],
                            'description' => $updateData['description'] ?? null,
                            'changelog' => $updateData['changelog'] ?? null,
                            'file_url' => $updateData['file_url'],
                            'file_size' => $updateData['file_size'] ?? null,
                            'checksum' => $updateData['checksum'] ?? null,
                            'is_major' => $updateData['is_major'] ?? false,
                            'is_active' => false, // Tidak aktif karena hanya untuk proses update
                            'migrations' => $updateData['migrations'] ?? null,
                            'seeders' => $updateData['seeders'] ?? null,
                            'released_at' => isset($updateData['released_at']) ? $updateData['released_at'] : now(),
                        ]);
                    } else {
                        throw new \Exception('Update versi ' . $version . ' tidak ditemukan di server');
                    }
                } else {
                    throw new \Exception('Update versi ' . $version . ' tidak ditemukan. Pastikan update sudah diinput di database atau server update sudah dikonfigurasi.');
                }
            }

            // Buat log update
            $updateLog = UpdateLog::create([
                'user_id' => Auth::id(),
                'version' => $update->version,
                'status' => 'pending',
            ]);

            // Download
            $downloadSuccess = $this->updateService->downloadUpdate($update, $updateLog);

            if (!$downloadSuccess) {
                throw new \Exception('Gagal mengunduh file update');
            }

            // Install
            $installSuccess = $this->updateService->installUpdate($update, $updateLog, Auth::id());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => $installSuccess,
                    'message' => $installSuccess ? 'Update berhasil diinstall' : 'Gagal menginstall update',
                    'update_log' => $updateLog->fresh(),
                    'update_log_id' => $updateLog->id,
                ]);
            }

            return redirect()->route('update.index')
                ->with(
                    $installSuccess ? 'success' : 'error',
                    $installSuccess ? 'Update berhasil diinstall' : 'Gagal menginstall update'
                );
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Gagal update: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('update.index')
                ->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * History update
     */
    public function history()
    {
        $updateLogs = UpdateLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('update.history', compact('updateLogs'));
    }

    /**
     * Detail update log
     */
    public function showLog($id)
    {
        $updateLog = UpdateLog::with('user')->findOrFail($id);

        return view('update.log-detail', compact('updateLog'));
    }

    /**
     * Get update progress
     */
    public function getProgress($id)
    {
        try {
            $updateLog = UpdateLog::findOrFail($id);

            $progress = $this->calculateProgress($updateLog);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $updateLog->id,
                    'version' => $updateLog->version,
                    'status' => $updateLog->status,
                    'message' => $updateLog->message,
                    'progress_percentage' => $updateLog->progress_percentage ?? $progress,
                    'progress_log' => $updateLog->progress_log ?? '',
                    'started_at' => $updateLog->started_at,
                    'completed_at' => $updateLog->completed_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Log update tidak ditemukan: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Calculate progress based on status
     */
    protected function calculateProgress(UpdateLog $updateLog): int
    {
        if ($updateLog->progress_percentage !== null) {
            return $updateLog->progress_percentage;
        }

        switch ($updateLog->status) {
            case 'pending':
                return 0;
            case 'downloading':
                return 25;
            case 'installing':
                return 75;
            case 'success':
                return 100;
            case 'failed':
                return 0;
            default:
                return 0;
        }
    }
}
