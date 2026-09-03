<?php

namespace App\Console\Commands;

use App\Models\Pengaturanumum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestGatewayInfoDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:gateway-info-device {device?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test endpoint info-device di gateway WA (untuk debugging)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TEST ENDPOINT INFO-DEVICE ===');
        $this->newLine();

        // Ambil data dari general setting
        $generalsetting = Pengaturanumum::where('id', 1)->first();

        if (!$generalsetting) {
            $this->error('General setting tidak ditemukan!');
            return 1;
        }

        if (!$generalsetting->domain_wa_gateway) {
            $this->error('Domain WA Gateway belum dikonfigurasi!');
            return 1;
        }

        if (!$generalsetting->wa_api_key) {
            $this->error('WA API Key belum dikonfigurasi!');
            return 1;
        }

        $domain = str_replace(['http://', 'https://'], '', $generalsetting->domain_wa_gateway);
        $apiKey = $generalsetting->wa_api_key;
        $deviceNumber = $this->argument('device');

        if (!$deviceNumber) {
            $deviceNumber = $this->ask('Masukkan nomor device (contoh: 6281234567890)');
        }

        $this->info("Domain: {$domain}");
        $this->info("API Key: " . substr($apiKey, 0, 10) . "...");
        $this->info("Device Number: {$deviceNumber}");
        $this->newLine();

        // Test 1: POST method dengan form-data
        $this->info('1. Test dengan POST method (form-data):');
        $this->line("   URL: http://{$domain}/info-device");
        $this->line("   Method: POST (form-data)");
        $this->line("   Data: api_key, number");
        $this->newLine();

        try {
            $apiUrl = 'http://' . $domain . '/info-device';
            $apiData = [
                'api_key' => $apiKey,
                'number' => $deviceNumber
            ];

            $response = Http::timeout(30)->post($apiUrl, $apiData);

            $this->line("   Status Code: " . $response->status());
            $this->line("   Response Body:");
            $this->line("   " . $response->body());
            $this->newLine();

            if ($response->successful()) {
                $responseData = $response->json();
                $this->info("   ✓ Request berhasil!");
                if (is_array($responseData)) {
                    $this->line("   Response JSON: " . json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                }
            } else {
                $this->error("   ✗ Request gagal!");
            }
        } catch (\Exception $e) {
            $this->error("   Error: " . $e->getMessage());
        }

        $this->newLine();

        // Test 1b: POST method dengan JSON (seperti di Postman)
        $this->info('1b. Test dengan POST method (JSON):');
        $this->line("   URL: http://{$domain}/info-device");
        $this->line("   Method: POST (JSON)");
        $this->line("   Data: api_key, number");
        $this->newLine();

        try {
            $apiUrl = 'http://' . $domain . '/info-device';
            $apiData = [
                'api_key' => $apiKey,
                'number' => $deviceNumber
            ];

            $response = Http::timeout(30)
                ->asJson()
                ->post($apiUrl, $apiData);

            $this->line("   Status Code: " . $response->status());
            $this->line("   Response Body:");
            $this->line("   " . $response->body());
            $this->newLine();

            if ($response->successful()) {
                $responseData = $response->json();
                $this->info("   ✓ Request berhasil!");
                if (is_array($responseData)) {
                    $this->line("   Response JSON: " . json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                }
            } else {
                $this->error("   ✗ Request gagal!");
            }
        } catch (\Exception $e) {
            $this->error("   Error: " . $e->getMessage());
        }

        $this->newLine();

        // Test 2: GET method (untuk melihat apakah ada perbedaan)
        $this->info('2. Test dengan GET method (query string):');
        $this->line("   URL: http://{$domain}/info-device?api_key=...&number=...");
        $this->line("   Method: GET");
        $this->newLine();

        try {
            $apiUrl = 'http://' . $domain . '/info-device';
            $response = Http::timeout(30)->get($apiUrl, [
                'api_key' => $apiKey,
                'number' => $deviceNumber
            ]);

            $this->line("   Status Code: " . $response->status());
            $this->line("   Response Body:");
            $this->line("   " . $response->body());
            $this->newLine();

            if ($response->successful()) {
                $this->info("   ✓ Request berhasil!");
            } else {
                $this->error("   ✗ Request gagal!");
            }
        } catch (\Exception $e) {
            $this->error("   Error: " . $e->getMessage());
        }

        $this->newLine();

        // Test 3: Cek endpoint generate-qr dengan POST form-data (HTTP)
        $this->info('3. Test endpoint generate-qr dengan POST (form-data) - HTTP:');
        $this->line("   URL: http://{$domain}/generate-qr");
        $this->line("   Method: POST (form-data)");
        $this->newLine();

        try {
            $apiUrl = 'http://' . $domain . '/generate-qr';
            $apiData = [
                'device' => $deviceNumber,
                'api_key' => $apiKey,
                'force' => true
            ];

            $response = Http::timeout(60)
                ->withoutRedirecting()
                ->post($apiUrl, $apiData);

            $this->line("   Status Code: " . $response->status());
            $this->line("   Response Body:");
            $this->line("   " . substr($response->body(), 0, 300) . "...");
            $this->newLine();

            if ($response->successful()) {
                $this->info("   ✓ Request berhasil!");
            } else {
                $this->error("   ✗ Request gagal!");
            }
        } catch (\Exception $e) {
            $this->error("   Error: " . $e->getMessage());
        }

        $this->newLine();

        // Test 4: Cek endpoint generate-qr dengan POST JSON (HTTP)
        $this->info('4. Test endpoint generate-qr dengan POST JSON - HTTP:');
        $this->line("   URL: http://{$domain}/generate-qr");
        $this->line("   Method: POST (JSON)");
        $this->newLine();

        try {
            $apiUrl = 'http://' . $domain . '/generate-qr';
            $apiData = [
                'device' => $deviceNumber,
                'api_key' => $apiKey,
                'force' => true
            ];

            $response = Http::timeout(60)
                ->asJson()
                ->withoutRedirecting()
                ->post($apiUrl, $apiData);

            $this->line("   Status Code: " . $response->status());
            $this->line("   Response Body:");
            $this->line("   " . substr($response->body(), 0, 300) . "...");
            $this->newLine();

            if ($response->successful()) {
                $this->info("   ✓ Request berhasil!");
                $responseData = $response->json();
                if (is_array($responseData)) {
                    $this->line("   Response JSON: " . json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                }
            } else {
                $this->error("   ✗ Request gagal!");
            }
        } catch (\Exception $e) {
            $this->error("   Error: " . $e->getMessage());
        }

        $this->newLine();

        // Test 5: Cek endpoint generate-qr dengan POST JSON (HTTPS - seperti di Postman)
        $this->info('5. Test endpoint generate-qr dengan POST JSON - HTTPS:');
        $this->line("   URL: https://{$domain}/generate-qr");
        $this->line("   Method: POST (JSON)");
        $this->newLine();

        try {
            $apiUrl = 'https://' . $domain . '/generate-qr';
            $apiData = [
                'device' => $deviceNumber,
                'api_key' => $apiKey,
                'force' => true
            ];

            $response = Http::timeout(60)
                ->asJson()
                ->post($apiUrl, $apiData);

            $this->line("   Status Code: " . $response->status());
            $this->line("   Response Body:");
            $this->line("   " . substr($response->body(), 0, 300) . "...");
            $this->newLine();

            if ($response->successful()) {
                $this->info("   ✓ Request berhasil!");
                $responseData = $response->json();
                if (is_array($responseData)) {
                    $this->line("   Response JSON: " . json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                }
            } else {
                $this->error("   ✗ Request gagal!");
            }
        } catch (\Exception $e) {
            $this->error("   Error: " . $e->getMessage());
        }

        $this->newLine();
        $this->info('=== SELESAI ===');

        return 0;
    }
}
