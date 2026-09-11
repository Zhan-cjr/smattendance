<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Models\Userkaryawan;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WebPushController extends Controller
{
    protected WebPushService $webPushService;

    public function __construct(WebPushService $webPushService)
    {
        $this->webPushService = $webPushService;
    }

    /**
     * Get VAPID Public Key
     */
    public function getPublicKey(): JsonResponse
    {
        $publicKey = config('webpush.vapid.public_key', env('VAPID_PUBLIC_KEY'));
        return response()->json([
            'publicKey' => $publicKey,
        ]);
    }

    /**
     * Store / Update Push Subscription
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $endpoint = $request->endpoint;
        $endpointHash = hash('sha256', $endpoint);
        $user = Auth::user();
        $nik = null;

        if ($user) {
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if ($userKaryawan) {
                $nik = $userKaryawan->nik;
            }
        }

        PushSubscription::updateOrCreate(
            ['endpoint_hash' => $endpointHash],
            [
                'user_id' => $user ? $user->id : null,
                'nik' => $nik,
                'endpoint' => $endpoint,
                'public_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
                'content_encoding' => $request->input('contentEncoding', 'aesgcm'),
                'user_agent' => $request->header('User-Agent'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Push subscription saved successfully.',
        ]);
    }

    /**
     * Remove Push Subscription
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $endpointHash = hash('sha256', $request->endpoint);
        PushSubscription::where('endpoint_hash', $endpointHash)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Push subscription removed successfully.',
        ]);
    }

    /**
     * Test Push Notification to the currently authenticated user
     */
    public function testPush(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $result = $this->webPushService->sendToUser(
            $user->id,
            '🔔 Tes Notifikasi SMAttendance',
            'Halo ' . $user->name . '! Web Push Notification berhasil terhubung dengan perangkat Anda.',
            url('/dashboard')
        );

        return response()->json($result);
    }
}
