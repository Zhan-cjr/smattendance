<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\Kunjungan;
use App\Models\AktivitasKaryawan;
use App\Models\ChecklistLaporan;

class PruneOldPhotosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photos:prune {--days= : Batas usia foto dalam hari (opsional, default dari pengaturan/60)} {--force : Paksa eksekusi meski toggle otomatis nonaktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan file fisik foto presensi, kunjungan, aktivitas, dan checklist yang berumur lebih dari batas hari (data rekapitulasi database tetap 100% aman)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $setting = Pengaturanumum::first();

        $force = (bool) $this->option('force');
        $autoActive = $setting ? (bool) $setting->auto_hapus_foto : false;

        if (!$force && !$autoActive) {
            $this->warn('Fitur auto bersihkan foto saat ini NONAKTIF di Pengaturan Umum. Gunakan --force untuk memaksa eksekusi.');
            return 0;
        }

        $days = (int) ($this->option('days') ?: ($setting->auto_hapus_foto_hari ?? 60));
        if ($days <= 0) {
            $days = 60;
        }

        $cutoffDate = Carbon::now()->subDays($days)->toDateString();
        $this->info("Memulai pembersihan foto lebih lama dari {$days} hari (sebelum atau sama dengan tanggal {$cutoffDate})...");

        $jenisStr = $setting->auto_hapus_foto_jenis ?? 'presensi,kunjungan,aktivitas,checklist';
        $jenisList = array_map('trim', explode(',', $jenisStr));

        $totalDeleted = 0;
        $stats = [
            'presensi' => 0,
            'kunjungan' => 0,
            'aktivitas' => 0,
            'checklist' => 0,
        ];

        $disk = Storage::disk('public');

        // 1. PRESENSI
        if (in_array('presensi', $jenisList)) {
            $this->line("Memeriksa foto Presensi...");
            Presensi::where('tanggal', '<=', $cutoffDate)
                ->where(function ($q) {
                    $q->whereNotNull('foto_in')->where('foto_in', '!=', '')
                        ->orWhereNotNull('foto_out')->where('foto_out', '!=', '')
                        ->orWhereNotNull('foto_istirahat_in')->where('foto_istirahat_in', '!=', '')
                        ->orWhereNotNull('foto_istirahat_out')->where('foto_istirahat_out', '!=', '');
                })
                ->chunkById(100, function ($presensis) use ($disk, &$stats, &$totalDeleted) {
                    foreach ($presensis as $p) {
                        foreach (['foto_in', 'foto_out', 'foto_istirahat_in', 'foto_istirahat_out'] as $field) {
                            $filename = $p->$field;
                            if ($filename && $disk->exists('uploads/absensi/' . $filename)) {
                                $disk->delete('uploads/absensi/' . $filename);
                                $stats['presensi']++;
                                $totalDeleted++;
                            }
                        }
                    }
                });
        }

        // 2. KUNJUNGAN
        if (in_array('kunjungan', $jenisList)) {
            $this->line("Memeriksa foto Kunjungan...");
            Kunjungan::where('tanggal_kunjungan', '<=', $cutoffDate)
                ->whereNotNull('foto')->where('foto', '!=', '')
                ->chunkById(100, function ($kunjungans) use ($disk, &$stats, &$totalDeleted) {
                    foreach ($kunjungans as $k) {
                        if ($disk->exists('uploads/kunjungan/' . $k->foto)) {
                            $disk->delete('uploads/kunjungan/' . $k->foto);
                            $stats['kunjungan']++;
                            $totalDeleted++;
                        }
                    }
                });
        }

        // 3. AKTIVITAS
        if (in_array('aktivitas', $jenisList)) {
            $this->line("Memeriksa foto Aktivitas Karyawan...");
            AktivitasKaryawan::whereDate('created_at', '<=', $cutoffDate)
                ->whereNotNull('foto')->where('foto', '!=', '')
                ->chunkById(100, function ($aktivitasList) use ($disk, &$stats, &$totalDeleted) {
                    foreach ($aktivitasList as $a) {
                        if ($disk->exists('uploads/aktivitas/' . $a->foto)) {
                            $disk->delete('uploads/aktivitas/' . $a->foto);
                            $stats['aktivitas']++;
                            $totalDeleted++;
                        }
                    }
                });
        }

        // 4. CHECKLIST (Individual checkpoints & 4-in-1 collages)
        if (in_array('checklist', $jenisList)) {
            $this->line("Memeriksa foto Checklist & Grid 4-in-1...");
            ChecklistLaporan::with(['details', 'collages'])
                ->where('tanggal', '<=', $cutoffDate)
                ->chunkById(50, function ($laporans) use ($disk, &$stats, &$totalDeleted) {
                    foreach ($laporans as $lap) {
                        // Checkpoint detail photos
                        foreach ($lap->details as $det) {
                            if ($det->foto) {
                                $path = str_starts_with($det->foto, 'checklist/') ? $det->foto : 'checklist/photos/' . $det->foto;
                                if ($disk->exists($path)) {
                                    $disk->delete($path);
                                    $stats['checklist']++;
                                    $totalDeleted++;
                                }
                            }
                        }

                        // Collages
                        foreach ($lap->collages as $col) {
                            if ($col->foto_collage) {
                                $colPath = str_starts_with($col->foto_collage, 'checklist/') ? $col->foto_collage : 'checklist/collages/' . $col->foto_collage;
                                if ($disk->exists($colPath)) {
                                    $disk->delete($colPath);
                                    $stats['checklist']++;
                                    $totalDeleted++;
                                }
                            }
                        }
                    }
                });
        }

        $summary = "Selesai! Berhasil membersihkan {$totalDeleted} file foto berumur > {$days} hari (" .
            "Presensi: {$stats['presensi']}, " .
            "Kunjungan: {$stats['kunjungan']}, " .
            "Aktivitas: {$stats['aktivitas']}, " .
            "Checklist: {$stats['checklist']}). Data tabel database tetap aman.";

        $this->info($summary);
        Log::info('[Auto Prune Photos] ' . $summary);

        return 0;
    }
}
