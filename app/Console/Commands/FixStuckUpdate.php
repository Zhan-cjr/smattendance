<?php

namespace App\Console\Commands;

use App\Models\UpdateLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class FixStuckUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:fix-stuck {--force : Force fix tanpa konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix update yang stuck atau loading terus';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Memperbaiki update yang stuck...');
        $this->newLine();

        // 1. Cek update log yang stuck
        $stuckLogs = UpdateLog::whereIn('status', ['downloading', 'installing'])
            ->whereNull('completed_at')
            ->get();

        if ($stuckLogs->count() > 0) {
            $this->warn("Ditemukan {$stuckLogs->count()} update yang stuck:");
            foreach ($stuckLogs as $log) {
                $this->line("  - Version: {$log->version}, Status: {$log->status}, Created: {$log->created_at}");
            }
            $this->newLine();

            if (!$this->option('force')) {
                if (!$this->confirm('Apakah Anda yakin ingin mark update ini sebagai failed?')) {
                    $this->info('Dibatalkan.');
                    return 0;
                }
            }

            foreach ($stuckLogs as $log) {
                $log->update([
                    'status' => 'failed',
                    'message' => 'Update terhenti karena proses terputus atau refresh',
                    'completed_at' => now(),
                ]);
                $this->info("✓ Update {$log->version} marked as failed");
            }
        } else {
            $this->info('✓ Tidak ada update yang stuck');
        }

        $this->newLine();

        // 2. Clear cache
        $this->info('🧹 Membersihkan cache...');
        try {
            Artisan::call('optimize:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            $this->info('✓ Cache berhasil dibersihkan');
        } catch (\Exception $e) {
            $this->error('✗ Gagal membersihkan cache: ' . $e->getMessage());
        }

        $this->newLine();

        // 3. Cleanup file temporary
        $this->info('🗑️  Membersihkan file temporary...');
        try {
            $updatesDir = storage_path('app/updates');
            
            if (File::exists($updatesDir)) {
                // Hapus folder extract
                $extractDirs = File::directories($updatesDir);
                foreach ($extractDirs as $dir) {
                    if (strpos($dir, 'extract_') !== false) {
                        File::deleteDirectory($dir);
                        $this->line("  ✓ Dihapus: " . basename($dir));
                    }
                }

                // Hapus file ZIP yang mungkin corrupt
                $zipFiles = File::glob($updatesDir . '/update_*.zip');
                foreach ($zipFiles as $zipFile) {
                    File::delete($zipFile);
                    $this->line("  ✓ Dihapus: " . basename($zipFile));
                }
            }
            $this->info('✓ File temporary berhasil dibersihkan');
        } catch (\Exception $e) {
            $this->error('✗ Gagal membersihkan file: ' . $e->getMessage());
        }

        $this->newLine();

        // 4. Rebuild cache
        $this->info('🔨 Rebuild cache...');
        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            $this->info('✓ Cache berhasil di-rebuild');
        } catch (\Exception $e) {
            $this->warn('⚠ Cache rebuild gagal: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('✅ Selesai! Aplikasi seharusnya sudah normal.');
        $this->info('💡 Jika masih loading, coba restart web server Anda.');

        return 0;
    }
}
