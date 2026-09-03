<?php

namespace App\Services;

use App\Models\Update;
use App\Models\UpdateLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive as Zip;

class UpdateService
{
    /**
     * Get current application version
     */
    public function getCurrentVersion(): string
    {
        // Cek dari file version atau database
        $versionFile = base_path('VERSION');
        if (File::exists($versionFile)) {
            return trim(File::get($versionFile));
        }

        // Default version jika belum ada
        return '1.0.0';
    }

    /**
     * Set current application version
     */
    public function setCurrentVersion(string $version): void
    {
        File::put(base_path('VERSION'), $version);
    }

    /**
     * Check for updates from server
     */
    public function checkUpdate(string $updateServerUrl = null): array
    {
        try {
            $currentVersion = $this->getCurrentVersion();
            
            // Ambil URL server dari config jika tidak ada parameter
            if (!$updateServerUrl) {
                $updateServerUrl = config('update.server_url');
            }
            
            // Jika tidak ada URL server, cek dari database lokal
            if (!$updateServerUrl) {
                // Bandingkan versi menggunakan versi semver
                $latestUpdate = Update::active()
                    ->orderByRaw("CAST(SUBSTRING_INDEX(version, '.', 1) AS UNSIGNED) DESC, 
                                  CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 2), '.', -1) AS UNSIGNED) DESC,
                                  CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC")
                    ->first();
                
                // Cek apakah versi lebih baru
                if ($latestUpdate && version_compare($latestUpdate->version, $currentVersion, '>')) {
                    return [
                        'has_update' => true,
                        'current_version' => $currentVersion,
                        'latest_version' => $latestUpdate->version,
                        'update' => $latestUpdate,
                    ];
                }

                return [
                    'has_update' => false,
                    'current_version' => $currentVersion,
                    'latest_version' => $currentVersion,
                ];
            }

            // Check dari server eksternal via API
            // Coba endpoint /api/update/check dulu
            try {
                $response = Http::timeout(30)->get(rtrim($updateServerUrl, '/') . '/api/update/check', [
                    'current_version' => $currentVersion,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Jika response menggunakan format {success: true, data: {...}}
                    if (isset($data['success']) && isset($data['data'])) {
                        $result = $data['data'];
                        return [
                            'has_update' => $result['has_update'] ?? false,
                            'current_version' => $currentVersion,
                            'latest_version' => $result['latest_version'] ?? $currentVersion,
                            'update' => $result['update'] ?? null,
                        ];
                    }
                    
                    // Jika response langsung return data
                    return [
                        'has_update' => $data['has_update'] ?? false,
                        'current_version' => $currentVersion,
                        'latest_version' => $data['latest_version'] ?? $currentVersion,
                        'update' => $data['update'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Error checking update from external server: ' . $e->getMessage());
            }

            // Fallback: coba endpoint /api/update/list
            try {
                $response = Http::timeout(30)->get(rtrim($updateServerUrl, '/') . '/api/update/list', [
                    'active' => true,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $updates = $data['data'] ?? [];
                    
                    if (count($updates) > 0) {
                        // Ambil update terbaru
                        $latestUpdate = collect($updates)->sortByDesc(function($update) {
                            return $update['version'];
                        })->first();
                        
                        if ($latestUpdate && version_compare($latestUpdate['version'], $currentVersion, '>')) {
                            return [
                                'has_update' => true,
                                'current_version' => $currentVersion,
                                'latest_version' => $latestUpdate['version'],
                                'update' => $latestUpdate,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error checking update list from external server: ' . $e->getMessage());
            }

            return [
                'has_update' => false,
                'current_version' => $currentVersion,
                'latest_version' => $currentVersion,
                'error' => 'Gagal menghubungi server update. Pastikan server ' . $updateServerUrl . ' dapat diakses dan memiliki endpoint API update.',
            ];
        } catch (\Exception $e) {
            Log::error('Error checking update: ' . $e->getMessage());
            return [
                'has_update' => false,
                'current_version' => $this->getCurrentVersion(),
                'latest_version' => $this->getCurrentVersion(),
                'error' => 'Terjadi kesalahan saat mengecek update: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Add progress log
     */
    protected function addProgressLog(UpdateLog $updateLog, string $message, int $percentage = null): void
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$message}\n";
        
        $currentLog = $updateLog->progress_log ?? '';
        $newLog = $currentLog . $logEntry;
        
        $updateData = [
            'progress_log' => $newLog,
        ];
        
        if ($percentage !== null) {
            $updateData['progress_percentage'] = $percentage;
        }
        
        $updateLog->update($updateData);
    }

    /**
     * Download update file
     */
    public function downloadUpdate(Update $update, UpdateLog $updateLog): bool
    {
        try {
            $updateLog->update([
                'status' => 'downloading',
                'progress_percentage' => 0,
                'progress_log' => '',
                'started_at' => now(),
            ]);

            $this->addProgressLog($updateLog, 'Memulai proses download update...', 0);

            if (!$update->file_url) {
                throw new \Exception('URL file update tidak tersedia');
            }

            $this->addProgressLog($updateLog, "Mendownload file dari: {$update->file_url}", 10);

            // Buat direktori untuk menyimpan file update
            $updateDir = storage_path('app/updates');
            if (!File::exists($updateDir)) {
                File::makeDirectory($updateDir, 0755, true);
                $this->addProgressLog($updateLog, 'Membuat direktori updates...', 15);
            }

            $zipPath = $updateDir . '/update_' . $update->version . '.zip';

            // Download file - disable time limit for large files
            set_time_limit(0);
            ini_set('memory_limit', '512M');
            
            $this->addProgressLog($updateLog, 'Mengunduh file update (mohon tunggu)...', 20);
            
            // Use simple streaming download with sink
            $response = Http::timeout(600)->withOptions([
                'sink' => $zipPath,
            ])->get($update->file_url);
            
            // Check if file was downloaded successfully
            if (!File::exists($zipPath) || File::size($zipPath) == 0) {
                throw new \Exception('Gagal mengunduh file update - file kosong atau tidak tersimpan');
            }
            
            $this->addProgressLog($updateLog, 'File berhasil diunduh ke disk...', 60);
            
            $fileSize = filesize($zipPath);
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);
            $this->addProgressLog($updateLog, "File berhasil diunduh ({$fileSizeMB} MB)", 80);

            // Validasi checksum jika ada
            if ($update->checksum) {
                $this->addProgressLog($updateLog, 'Memvalidasi checksum file...', 85);
                $fileChecksum = md5_file($zipPath);
                if ($fileChecksum !== $update->checksum) {
                    File::delete($zipPath);
                    throw new \Exception('Checksum file tidak valid. File mungkin rusak.');
                }
                $this->addProgressLog($updateLog, 'Checksum valid ✓', 90);
            }

            // Simpan path file ke update log
            $updateLog->update([
                'message' => 'File berhasil diunduh',
                'progress_percentage' => 100,
            ]);
            
            $this->addProgressLog($updateLog, 'Download selesai!', 100);

            return true;
        } catch (\Exception $e) {
            $this->addProgressLog($updateLog, "ERROR: {$e->getMessage()}", 0);
            $updateLog->update([
                'status' => 'failed',
                'message' => 'Gagal mengunduh update: ' . $e->getMessage(),
                'error_log' => $e->getTraceAsString(),
                'completed_at' => now(),
            ]);
            return false;
        }
    }

    /**
     * Install update
     */
    public function installUpdate(Update $update, UpdateLog $updateLog, $userId = null): bool
    {
        try {
            $updateLog->update([
                'status' => 'installing',
                'progress_percentage' => 0,
            ]);

            $this->addProgressLog($updateLog, 'Memulai proses instalasi update...', 0);

            $currentVersion = $this->getCurrentVersion();
            $zipPath = storage_path('app/updates/update_' . $update->version . '.zip');

            if (!File::exists($zipPath)) {
                throw new \Exception('File update tidak ditemukan');
            }

            // 1. Backup database
            $this->addProgressLog($updateLog, 'Membackup database...', 5);
            $this->backupDatabase($updateLog);
            $this->addProgressLog($updateLog, 'Backup database selesai ✓', 10);

            // 2. Extract file update
            $this->addProgressLog($updateLog, 'Mengekstrak file ZIP...', 15);
            $extractPath = storage_path('app/updates/extract_' . $update->version);
            if (File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
            File::makeDirectory($extractPath, 0755, true);

            $this->smartExtract($zipPath, $extractPath, $updateLog);

            // 3. Copy files ke aplikasi
            $this->addProgressLog($updateLog, 'Menyalin file ke aplikasi...', 35);
            $this->copyUpdateFiles($extractPath, $updateLog);
            $this->addProgressLog($updateLog, 'File berhasil disalin ✓', 50);

            // 4. Run migrations
            $this->addProgressLog($updateLog, 'Menjalankan semua pending migrations...', 55);
            Artisan::call('migrate', ['--force' => true]);
            $this->addProgressLog($updateLog, 'Migrations selesai ✓', 70);

            // 5. Run seeders
            $this->addProgressLog($updateLog, 'Menjalankan seeders...', 75);
            Artisan::call('db:seed', ['--force' => true]);
            $this->addProgressLog($updateLog, 'Seeders selesai ✓', 80);

            // 6. Update version
            $this->addProgressLog($updateLog, 'Mengupdate versi aplikasi...', 85);
            $this->setCurrentVersion($update->version);
            $this->addProgressLog($updateLog, "Versi diupdate dari {$currentVersion} ke {$update->version} ✓", 87);

            // 7. Clear cache
            $this->addProgressLog($updateLog, 'Membersihkan cache...', 90);
            Artisan::call('optimize:clear');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            $this->addProgressLog($updateLog, 'Cache berhasil dibersihkan ✓', 95);

            // 8. Cleanup
            $this->addProgressLog($updateLog, 'Membersihkan file temporary...', 97);
            File::delete($zipPath);
            File::deleteDirectory($extractPath);
            $this->addProgressLog($updateLog, 'Cleanup selesai ✓', 99);

            $updateLog->update([
                'status' => 'success',
                'previous_version' => $currentVersion,
                'message' => 'Update berhasil diinstall',
                'progress_percentage' => 100,
                'completed_at' => now(),
            ]);

            $this->addProgressLog($updateLog, 'Update berhasil diinstall! 🎉', 100);

            return true;
        } catch (\Exception $e) {
            $this->addProgressLog($updateLog, "ERROR: {$e->getMessage()}", 0);
            $updateLog->update([
                'status' => 'failed',
                'message' => 'Gagal menginstall update: ' . $e->getMessage(),
                'error_log' => $e->getTraceAsString(),
                'completed_at' => now(),
            ]);

            // Rollback jika ada backup
            $this->rollbackUpdate($updateLog);

            return false;
        }
    }

    /**
     * Smart Extract (Native > PHP Fallback)
     */
    private function smartExtract($zipPath, $extractPath, $updateLog)
    {
        $this->addProgressLog($updateLog, 'Mengekstrak file ZIP...', 20);
        
        // 1. Try Native Unzip
        try {
            $unzipPath = 'unzip'; // Default to PATH
            if (file_exists('/usr/bin/unzip') && is_executable('/usr/bin/unzip')) {
                $unzipPath = '/usr/bin/unzip';
            } elseif (file_exists('/bin/unzip') && is_executable('/bin/unzip')) {
                $unzipPath = '/bin/unzip';
            }

            if ($this->hasUnzip()) {
                // Use detected path
                $command = "{$unzipPath} -o -q '{$zipPath}' -d '{$extractPath}' 2>&1";
                $output = [];
                $returnVar = -1;
                
                exec($command, $output, $returnVar);

                if ($returnVar === 0) {
                    $this->addProgressLog($updateLog, 'Ekstraksi cepat berhasil (Native) ✓', 35);
                    return;
                }
                
                $errorMsg = implode("\n", $output);
                $this->addProgressLog($updateLog, "Native unzip gagal (code {$returnVar}), menggunakan fallback PHP... Error: " . substr($errorMsg, 0, 100), 20);
            }
        } catch (\Exception $e) {
            // Ignore and fallback
        }

        // 2. PHP Fallback (Optimized: EXTRACT ALL AT ONCE)
        $this->addProgressLog($updateLog, "Mengekstrak menggunakan PHP (Mohon tunggu, ini mungkin memakan waktu)...", 25);
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === true) {
            set_time_limit(0);
            ini_set('memory_limit', '1024M');
            
            if ($zip->extractTo($extractPath)) {
                $zip->close();
                $this->addProgressLog($updateLog, 'Ekstraksi selesai (PHP) ✓', 35);
            } else {
                $zip->close();
                throw new \Exception('Gagal mengekstrak file ZIP (PHP ZipArchive failed)');
            }
        } else {
            throw new \Exception('Gagal membuka file ZIP');
        }
    }

    private function hasUnzip()
    {
        // Check common absolute paths first
        if (file_exists('/usr/bin/unzip') && is_executable('/usr/bin/unzip')) {
            return true;
        }
        if (file_exists('/bin/unzip') && is_executable('/bin/unzip')) {
            return true;
        }
        
        exec('which unzip', $output, $returnVar);
        return $returnVar === 0;
    }

    /**
     * Backup database with fallback
     */
    protected function backupDatabase(UpdateLog $updateLog): void
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $backupFile = $backupDir . '/backup_' . date('Y-m-d_His') . '_' . $updateLog->version . '.sql';
            
            // Try native mysqldump first
            if ($this->hasMysqldump()) {
                $dbName = config('database.connections.mysql.database');
                $dbUser = config('database.connections.mysql.username');
                $dbPass = config('database.connections.mysql.password');
                $dbHost = config('database.connections.mysql.host');
                $dbPort = config('database.connections.mysql.port', 3306);
                
                $command = "mysqldump --no-tablespaces --column-statistics=0 -h {$dbHost} -P {$dbPort} -u {$dbUser} -p{$dbPass} {$dbName} > '{$backupFile}' 2>&1";
                
                exec($command, $output, $returnVar);

                if ($returnVar === 0 && File::exists($backupFile) && File::size($backupFile) > 0) {
                    $this->addProgressLog($updateLog, 'Backup database berhasil (Native) ✓', 10);
                    return;
                }
                
                $this->addProgressLog($updateLog, "mysqldump gagal/hilang, mencoba fallback PHP...", 5);
            } else {
                 $this->addProgressLog($updateLog, "mysqldump tidak ditemukan, menggunakan fallback PHP...", 5);
            }

            // Fallback: PHP Based Backup
            $this->backupDatabasePHP($backupFile, $updateLog);
            
        } catch (\Exception $e) {
            Log::warning('Gagal backup database: ' . $e->getMessage());
             $this->addProgressLog($updateLog, "Backup gagal, melanjutkan update (Warning: {$e->getMessage()})", 10);
        }
    }

    private function hasMysqldump()
    {
        exec('which mysqldump', $output, $returnVar);
        return $returnVar === 0;
    }

    /**
     * PHP-based Database Backup (Fallback)
     */
    protected function backupDatabasePHP(string $filePath, UpdateLog $updateLog)
    {
        $handle = fopen($filePath, 'w+');
        if (!$handle) {
            throw new \Exception("Gagal membuat file backup di {$filePath}");
        }

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');

        foreach ($tables as $table) {
            $tableArray = (array)$table;
            $tableName = array_values($tableArray)[0];

            $createTable = DB::select("SHOW CREATE TABLE {$tableName}");
            $createTableSql = ((array)$createTable[0])['Create Table'];

            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($handle, $createTableSql . ";\n\n");

            DB::table($tableName)->orderByRaw('1')->chunk(200, function ($rows) use ($handle, $tableName) {
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        return is_null($value) ? "NULL" : "'" . addslashes($value) . "'";
                    }, (array)$row);
                    
                    $sql = "INSERT INTO `{$tableName}` VALUES (" . implode(',', $values) . ");\n";
                    fwrite($handle, $sql);
                }
            });
            
            fwrite($handle, "\n\n");
        }

        fclose($handle);
        $this->addProgressLog($updateLog, 'Backup database selesai (PHP) ✓', 15);
    }

    /**
     * Copy update files to application
     */
    protected function copyUpdateFiles(string $extractPath, UpdateLog $updateLog): void
    {
        $sourceDirs = [
            'app' => base_path('app'),
            'database' => base_path('database'),
            'resources' => base_path('resources'),
            'routes' => base_path('routes'),
            'public' => base_path('public'),
            'config' => base_path('config'),
        ];

        foreach ($sourceDirs as $dir => $targetPath) {
            $sourcePath = $extractPath . '/' . $dir;
            if (File::exists($sourcePath)) {
                File::copyDirectory($sourcePath, $targetPath);
            }
        }

        // Copy file individual jika ada
        $filesToCopy = [
            'composer.json',
            'package.json',
            '.env.example',
        ];

        foreach ($filesToCopy as $file) {
            $sourceFile = $extractPath . '/' . $file;
            if (File::exists($sourceFile)) {
                File::copy($sourceFile, base_path($file));
            }
        }
    }

    /**
     * Run migrations
     */
    protected function runMigrations(array $migrations, UpdateLog $updateLog): void
    {
        foreach ($migrations as $migration) {
            try {
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/' . $migration,
                    '--force' => true,
                ]);
            } catch (\Exception $e) {
                Log::error('Migration failed: ' . $migration . ' - ' . $e->getMessage());
            }
        }
    }

    /**
     * Run seeders
     */
    protected function runSeeders(array $seeders, UpdateLog $updateLog): void
    {
        foreach ($seeders as $seeder) {
            try {
                Artisan::call('db:seed', [
                    '--class' => $seeder,
                    '--force' => true,
                ]);
            } catch (\Exception $e) {
                Log::error('Seeder failed: ' . $seeder . ' - ' . $e->getMessage());
            }
        }
    }

    /**
     * Rollback update
     */
    protected function rollbackUpdate(UpdateLog $updateLog): void
    {
        // Implementasi rollback jika diperlukan
        // Bisa restore dari backup database
        Log::info('Rollback update untuk versi: ' . $updateLog->version);
    }
}

