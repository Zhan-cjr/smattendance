<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Update;
use App\Models\UpdateLog;
use App\Services\UpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{
    protected $updateService;

    public function __construct(UpdateService $updateService)
    {
        $this->updateService = $updateService;
    }

    /**
     * Check update terbaru
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @url GET /api/update/check
     * @bodyParam update_server_url string optional URL server update (jika menggunakan server eksternal)
     */
    public function checkUpdate(Request $request)
    {
        try {
            $updateServerUrl = $request->input('update_server_url');
            $result = $this->updateService->checkUpdate($updateServerUrl);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek update: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current version
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * @url GET /api/update/version
     */
    public function getCurrentVersion()
    {
        try {
            $currentVersion = $this->updateService->getCurrentVersion();

            return response()->json([
                'success' => true,
                'data' => [
                    'version' => $currentVersion,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan versi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download update file
     * 
     * @param Request $request
     * @param string $version
     * @return \Illuminate\Http\JsonResponse
     * 
     * @url POST /api/update/{version}/download
     * @bodyParam user_id integer optional ID user yang melakukan update (jika authenticated)
     */
    public function downloadUpdate(Request $request, $version)
    {
        try {
            $update = Update::where('version', $version)->firstOrFail();
            
            // Buat log update
            $updateLog = UpdateLog::create([
                'user_id' => Auth::id() ?? $request->input('user_id'),
                'version' => $update->version,
                'status' => 'pending',
            ]);

            // Download file
            $success = $this->updateService->downloadUpdate($update, $updateLog);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'File berhasil diunduh' : 'Gagal mengunduh file',
                'data' => [
                    'update_log_id' => $updateLog->id,
                    'version' => $update->version,
                    'status' => $updateLog->fresh()->status,
                ],
            ], $success ? 200 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh update: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Install update
     * 
     * @param Request $request
     * @param string $version
     * @return \Illuminate\Http\JsonResponse
     * 
     * @url POST /api/update/{version}/install
     * @bodyParam user_id integer optional ID user yang melakukan update (jika authenticated)
     */
    public function installUpdate(Request $request, $version)
    {
        try {
            $update = Update::where('version', $version)->firstOrFail();
            
            // Cari atau buat log update
            $updateLog = UpdateLog::where('version', $version)
                ->where('status', '!=', 'success')
                ->latest()
                ->first();

            if (!$updateLog) {
                $updateLog = UpdateLog::create([
                    'user_id' => Auth::id() ?? $request->input('user_id'),
                    'version' => $update->version,
                    'status' => 'pending',
                ]);
            }

            // Install update
            $success = $this->updateService->installUpdate($update, $updateLog, Auth::id() ?? $request->input('user_id'));

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Update berhasil diinstall' : 'Gagal menginstall update',
                'data' => [
                    'update_log' => $updateLog->fresh(),
                    'current_version' => $this->updateService->getCurrentVersion(),
                ],
            ], $success ? 200 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menginstall update: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update langsung (download + install)
     * 
     * @param Request $request
     * @param string $version
     * @return \Illuminate\Http\JsonResponse
     * 
     * @url POST /api/update/{version}/update-now
     * @bodyParam user_id integer optional ID user yang melakukan update (jika authenticated)
     */
    public function updateNow(Request $request, $version)
    {
        try {
            $update = Update::where('version', $version)->firstOrFail();
            
            // Buat log update
            $updateLog = UpdateLog::create([
                'user_id' => Auth::id() ?? $request->input('user_id'),
                'version' => $update->version,
                'status' => 'pending',
            ]);

            // Download
            $downloadSuccess = $this->updateService->downloadUpdate($update, $updateLog);
            
            if (!$downloadSuccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunduh file update',
                    'data' => [
                        'update_log' => $updateLog->fresh(),
                    ],
                ], 500);
            }

            // Install
            $installSuccess = $this->updateService->installUpdate($update, $updateLog, Auth::id() ?? $request->input('user_id'));

            return response()->json([
                'success' => $installSuccess,
                'message' => $installSuccess ? 'Update berhasil diinstall' : 'Gagal menginstall update',
                'data' => [
                    'update_log' => $updateLog->fresh(),
                    'current_version' => $this->updateService->getCurrentVersion(),
                    'previous_version' => $updateLog->previous_version,
                ],
            ], $installSuccess ? 200 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get update history
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @url GET /api/update/history
     * @queryParam page integer optional Halaman (default: 1)
     * @queryParam per_page integer optional Item per halaman (default: 10)
     */
    public function history(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $updateLogs = UpdateLog::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $updateLogs->items(),
                'pagination' => [
                    'current_page' => $updateLogs->currentPage(),
                    'last_page' => $updateLogs->lastPage(),
                    'per_page' => $updateLogs->perPage(),
                    'total' => $updateLogs->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan riwayat update: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get update log detail
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * @url GET /api/update/log/{id}
     */
    public function showLog($id)
    {
        try {
            $updateLog = UpdateLog::with('user')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $updateLog,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Log update tidak ditemukan: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get list of available updates
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @url GET /api/update/list
     * @queryParam active boolean optional Hanya update aktif (default: true)
     * @queryParam major boolean optional Hanya update major
     */
    public function listUpdates(Request $request)
    {
        try {
            $query = Update::query();

            if ($request->has('active')) {
                $query->where('is_active', $request->boolean('active'));
            } else {
                $query->where('is_active', true);
            }

            if ($request->has('major')) {
                $query->where('is_major', $request->boolean('major'));
            }

            $updates = $query->orderBy('released_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $updates,
                'count' => $updates->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan daftar update: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get update detail by version
     * 
     * @param string $version
     * @return \Illuminate\Http\JsonResponse
     * 
     * @url GET /api/update/{version}
     */
    public function show($version)
    {
        try {
            $update = Update::where('version', $version)->firstOrFail();
            
            return response()->json([
                'success' => true,
                'data' => $update,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update tidak ditemukan: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get update status/progress
     * 
     * @param int $logId
     * @return \Illuminate\Http\JsonResponse
     * 
     * @url GET /api/update/status/{logId}
     */
    public function getStatus($logId)
    {
        try {
            $updateLog = UpdateLog::findOrFail($logId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $updateLog->id,
                    'version' => $updateLog->version,
                    'status' => $updateLog->status,
                    'message' => $updateLog->message,
                    'started_at' => $updateLog->started_at,
                    'completed_at' => $updateLog->completed_at,
                    'progress' => $this->calculateProgress($updateLog),
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
     * Calculate update progress
     */
    protected function calculateProgress(UpdateLog $updateLog): int
    {
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
