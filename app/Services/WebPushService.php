<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\Pengaturanumum;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushService
{
    protected ?WebPush $webPush = null;

    public function __construct()
    {
        $this->ensureOpenSslConfig();
        $this->initWebPush();
    }

    /**
     * Ensure OpenSSL config is properly loaded on Windows environments
     */
    protected function ensureOpenSslConfig(): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $phpDir = dirname(PHP_BINARY);
            $candidates = [
                $phpDir . '/extras/ssl/openssl.cnf',
                'C:/laragon/etc/ssl/openssl.cnf',
                'C:/xampp/apache/bin/openssl.cnf',
                'C:/xampp/php/extras/ssl/openssl.cnf',
            ];

            foreach ($candidates as $c) {
                if (file_exists($c)) {
                    putenv("OPENSSL_CONF=" . $c);
                    $_ENV['OPENSSL_CONF'] = $c;
                    $_SERVER['OPENSSL_CONF'] = $c;
                    break;
                }
            }
        }
    }

    /**
     * Initialize WebPush instance
     */
    protected function initWebPush(): void
    {
        $publicKey = config('webpush.vapid.public_key', env('VAPID_PUBLIC_KEY'));
        $privateKey = config('webpush.vapid.private_key', env('VAPID_PRIVATE_KEY'));
        $subject = config('webpush.vapid.subject', env('VAPID_SUBJECT', 'mailto:admin@smattendance.com'));

        if (!$publicKey || !$privateKey) {
            Log::warning('WebPushService: VAPID keys not configured.');
            return;
        }

        $auth = [
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ];

        $defaultOptions = [
            'TTL' => config('webpush.settings.default_ttl', 86400),
            'urgency' => config('webpush.settings.urgency', 'normal'),
        ];

        $this->webPush = new WebPush($auth, $defaultOptions);
        $this->webPush->setReuseVAPIDHeaders(true);
    }

    /**
     * Build standard notification payload
     */
    public function buildPayload(string $title, string $body, ?string $url = null, array $options = []): string
    {
        $setting = Pengaturanumum::first();
        $defaultIcon = asset('logo.png');
        if ($setting && $setting->logo) {
            $defaultIcon = asset('storage/logo/' . $setting->logo);
        }

        $payload = [
            'title' => $title,
            'body' => $body,
            'icon' => $options['icon'] ?? $defaultIcon,
            'badge' => $options['badge'] ?? asset('logo.png'),
            'url' => $url ?? url('/dashboard'),
            'data' => [
                'url' => $url ?? url('/dashboard'),
                'timestamp' => time(),
            ],
            'vibrate' => [100, 50, 100],
            'requireInteraction' => $options['requireInteraction'] ?? false,
        ];

        if (!empty($options['actions'])) {
            $payload['actions'] = $options['actions'];
        }

        return json_encode($payload);
    }

    /**
     * Send push notification to a specific subscription collection
     */
    public function sendToSubscriptions($subscriptions, string $title, string $body, ?string $url = null, array $options = []): array
    {
        if (!$this->webPush) {
            return ['success' => false, 'message' => 'WebPush not configured'];
        }

        $payload = $this->buildPayload($title, $body, $url, $options);
        $queuedCount = 0;

        foreach ($subscriptions as $sub) {
            if (empty($sub->endpoint)) {
                continue;
            }

            try {
                $webPushSub = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?? 'aesgcm',
                ]);

                $this->webPush->queueNotification($webPushSub, $payload);
                $queuedCount++;
            } catch (\Exception $e) {
                Log::warning('WebPushService queue error: ' . $e->getMessage());
            }
        }

        if ($queuedCount === 0) {
            return ['success' => true, 'sent' => 0, 'failed' => 0];
        }

        // Flush notifications
        $sentCount = 0;
        $failedCount = 0;
        $expiredEndpointHashes = [];

        try {
            foreach ($this->webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    $sentCount++;
                } else {
                    $failedCount++;
                    Log::info("WebPush failed for {$endpoint}: {$report->getReason()}");

                    // If subscription has expired or is invalid (404/410), mark for deletion
                    if ($report->isSubscriptionExpired()) {
                        $expiredEndpointHashes[] = hash('sha256', $endpoint);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('WebPushService flush error: ' . $e->getMessage());
        }

        // Clean up expired subscriptions
        if (!empty($expiredEndpointHashes)) {
            PushSubscription::whereIn('endpoint_hash', $expiredEndpointHashes)->delete();
        }

        return [
            'success' => true,
            'sent' => $sentCount,
            'failed' => $failedCount,
        ];
    }

    /**
     * Send to specific NIK
     */
    public function sendToNik(string $nik, string $title, string $body, ?string $url = null, array $options = []): array
    {
        $subs = PushSubscription::where('nik', $nik)->get();
        return $this->sendToSubscriptions($subs, $title, $body, $url, $options);
    }

    /**
     * Send to multiple NIKs
     */
    public function sendToNiks(array $niks, string $title, string $body, ?string $url = null, array $options = []): array
    {
        $subs = PushSubscription::whereIn('nik', $niks)->get();
        return $this->sendToSubscriptions($subs, $title, $body, $url, $options);
    }

    /**
     * Send to specific user ID
     */
    public function sendToUser(int $userId, string $title, string $body, ?string $url = null, array $options = []): array
    {
        $subs = PushSubscription::where('user_id', $userId)->get();
        return $this->sendToSubscriptions($subs, $title, $body, $url, $options);
    }

    /**
     * Send to all subscribers (Broadcast)
     */
    public function sendToAll(string $title, string $body, ?string $url = null, array $options = []): array
    {
        $subs = PushSubscription::all();
        return $this->sendToSubscriptions($subs, $title, $body, $url, $options);
    }

    /**
     * Send to department
     */
    public function sendToDept(string $kodeDept, string $title, string $body, ?string $url = null, array $options = []): array
    {
        $niks = Karyawan::where('kode_dept', $kodeDept)->where('status_aktif_karyawan', '1')->pluck('nik')->toArray();
        return $this->sendToNiks($niks, $title, $body, $url, $options);
    }

    /**
     * Send to branch / cabang
     */
    public function sendToCabang(string $kodeCabang, string $title, string $body, ?string $url = null, array $options = []): array
    {
        $niks = Karyawan::where('kode_cabang', $kodeCabang)->where('status_aktif_karyawan', '1')->pluck('nik')->toArray();
        return $this->sendToNiks($niks, $title, $body, $url, $options);
    }
}
