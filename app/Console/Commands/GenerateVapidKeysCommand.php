<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeysCommand extends Command
{
    protected $signature = 'webpush:vapid {--show : Hanya tampilkan key tanpa menulis ke .env}';
    protected $description = 'Generate VAPID Public and Private Keys untuk Web Push Notification';

    public function handle(): int
    {
        $cnf = null;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $phpDir = str_replace('\\', '/', dirname(PHP_BINARY));
            $candidates = [
                $phpDir . '/extras/ssl/openssl.cnf',
                'C:/laragon/etc/ssl/openssl.cnf',
                'C:/xampp/apache/bin/openssl.cnf',
                'C:/xampp/php/extras/ssl/openssl.cnf',
            ];
            foreach ($candidates as $c) {
                if (file_exists($c)) {
                    $cnf = $c;
                    putenv("OPENSSL_CONF=" . $c);
                    $_ENV['OPENSSL_CONF'] = $c;
                    $_SERVER['OPENSSL_CONF'] = $c;
                    break;
                }
            }
        }

        try {
            // Try standard Minishlink VAPID
            $keys = VAPID::createVapidKeys();
        } catch (\Exception $e) {
            // Fallback: direct OpenSSL EC P-256 generation
            $configArgs = [
                'curve_name' => 'prime256v1',
                'private_key_type' => OPENSSL_KEYTYPE_EC,
            ];
            if ($cnf) {
                $configArgs['config'] = $cnf;
            }

            $res = openssl_pkey_new($configArgs);
            if ($res) {
                openssl_pkey_export($res, $pem, null, $cnf ? ['config' => $cnf] : []);
                $details = openssl_pkey_get_details($res);
                if (isset($details['ec']['x'], $details['ec']['y'], $details['ec']['d'])) {
                    // Convert raw EC points to Base64Url
                    $x = $details['ec']['x'];
                    $y = $details['ec']['y'];
                    $d = $details['ec']['d'];
                    $pubRaw = "\x04" . $x . $y;
                    $keys = [
                        'publicKey' => rtrim(strtr(base64_encode($pubRaw), '+/', '-_'), '='),
                        'privateKey' => rtrim(strtr(base64_encode($d), '+/', '-_'), '='),
                    ];
                } else {
                    $this->error('Gagal generate VAPID keys: ' . $e->getMessage());
                    return Command::FAILURE;
                }
            } else {
                $this->error('Gagal generate VAPID keys: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        $publicKey = $keys['publicKey'];
        $privateKey = $keys['privateKey'];

        $this->info('=== VAPID Keys Generated Successfully ===');
        $this->line('<comment>VAPID_PUBLIC_KEY=</comment>' . $publicKey);
        $this->line('<comment>VAPID_PRIVATE_KEY=</comment>' . $privateKey);

        if ($this->option('show')) {
            return Command::SUCCESS;
        }

        // Write to .env
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);

            if (str_contains($envContent, 'VAPID_PUBLIC_KEY=')) {
                $envContent = preg_replace('/VAPID_PUBLIC_KEY=.*/', 'VAPID_PUBLIC_KEY=' . $publicKey, $envContent);
                $envContent = preg_replace('/VAPID_PRIVATE_KEY=.*/', 'VAPID_PRIVATE_KEY=' . $privateKey, $envContent);
            } else {
                $envContent .= "\n# Web Push VAPID Keys\nVAPID_PUBLIC_KEY={$publicKey}\nVAPID_PRIVATE_KEY={$privateKey}\nVAPID_SUBJECT=mailto:admin@smattendance.com\n";
            }

            file_put_contents($envPath, $envContent);
            $this->info('VAPID keys berhasil disimpan ke file .env!');
        } else {
            $this->warn('File .env tidak ditemukan. Silakan tambahkan variabel di atas ke file .env Anda secara manual.');
        }

        return Command::SUCCESS;
    }
}
