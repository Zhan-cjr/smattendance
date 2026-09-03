<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | Here is where you can register API routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "api" middleware group. Make something great! | */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/presensimachine', App\Http\Controllers\Api\PresensiController::class);
Route::post('/presensi/log', [App\Http\Controllers\Api\PresensiController::class , 'log']);

// Endpoint fingerprint tanpa rate limiting
// Karena sudah ada mekanisme duplikasi via cache di controller
// dan mesin fingerprint perlu mengirim data real-time tanpa batasan
Route::post('/presensi/receive-data', [App\Http\Controllers\Api\PresensiController::class , 'receiveRevoData'])
    ->withoutMiddleware('throttle:api');

// Endpoint untuk capture data mentah ADMS
Route::any('/iclock/cdata', [App\Http\Controllers\Api\AdmsController::class , 'capture'])
    ->withoutMiddleware('throttle:api');

// Endpoint khusus untuk cek data mentah dari mesin (debug only)
Route::any('/rawdump/{any?}', [App\Http\Controllers\Api\AdmsController::class , 'rawDump'])
    ->where('any', '.*')
    ->withoutMiddleware('throttle:api');

// Endpoint untuk menerima data dari mesin Fingerspot REVO melalui ADMS
// Route::post('/presensi/revo', [App\Http\Controllers\Api\PresensiController::class, 'receiveRevoData'])
//     ->withoutMiddleware('throttle:api');

// Endpoint khusus untuk test X100C Solution


// Route::any('/iclock/cdata', [App\Http\Controllers\Api\AdmsController::class, 'testX100c'])
//     ->withoutMiddleware('throttle:api');
// Update API Routes
Route::prefix('update')->group(function () {
    // Public endpoints (tidak perlu auth) - Route spesifik dulu
    Route::get('/check', [App\Http\Controllers\Api\UpdateController::class , 'checkUpdate']);
    Route::get('/version', [App\Http\Controllers\Api\UpdateController::class , 'getCurrentVersion']);
    Route::get('/list', [App\Http\Controllers\Api\UpdateController::class , 'listUpdates']);

    // Protected endpoints (disarankan menggunakan auth) - Route spesifik dulu
    Route::middleware('auth:sanctum')->group(function () {
            Route::get('/history', [App\Http\Controllers\Api\UpdateController::class , 'history']);
            Route::get('/log/{id}', [App\Http\Controllers\Api\UpdateController::class , 'showLog']);
            Route::get('/status/{logId}', [App\Http\Controllers\Api\UpdateController::class , 'getStatus']);
            Route::post('/{version}/download', [App\Http\Controllers\Api\UpdateController::class , 'downloadUpdate']);
            Route::post('/{version}/install', [App\Http\Controllers\Api\UpdateController::class , 'installUpdate']);
            Route::post('/{version}/update-now', [App\Http\Controllers\Api\UpdateController::class , 'updateNow']);
        }
        );

        // Route dengan parameter di akhir (agar tidak conflict)
        Route::get('/{version}', [App\Http\Controllers\Api\UpdateController::class , 'show']);
    });

// Live Location Tracking API Routes
Route::prefix('live-location')->group(function () {
    // Store location data (dari mobile karyawan)
    Route::post('/store', [App\Http\Controllers\Api\LiveLocationController::class, 'store']);
    
    // Check spy status untuk mobile app (protected)
    Route::middleware('auth:sanctum')->get('/check-spy-status', [App\Http\Controllers\Api\LiveLocationController::class, 'checkSpyStatus']);
    
    // Get latest location untuk karyawan tertentu
    Route::get('/{nik}/latest', [App\Http\Controllers\Api\LiveLocationController::class, 'getLatest']);
    
    // Get location history
    Route::get('/{nik}/history', [App\Http\Controllers\Api\LiveLocationController::class, 'getHistory']);
    
    // Get all spy employees dan lokasi terakhir mereka
    Route::get('/all/spy-locations', [App\Http\Controllers\Api\LiveLocationController::class, 'getAllSpyLocations']);
    
    // Cleanup old data (dapat dipanggil dari scheduled task)
    Route::delete('/cleanup/old-data', [App\Http\Controllers\Api\LiveLocationController::class, 'cleanupOldData']);
});

Route::middleware('web')->get('/whoami', function (Request $request) {
    if (!auth()->check()) {
        return response()->json(['logged_in' => false]);
    }
    
    $user = auth()->user();
    $karyawan = null;
    if ($user->userkaryawan) {
        $karyawan = \App\Models\Karyawan::find($user->userkaryawan->nik);
    }
    
    return response()->json([
        'logged_in' => true,
        'nik'       => $karyawan?->nik,
        'nama'      => $karyawan?->nama_karyawan,
        'spy'       => (int) ($karyawan?->spy ?? 0),
    ]);
});