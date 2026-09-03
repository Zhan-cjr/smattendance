<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan worker queue tiap menit untuk memproses job antrian
        $schedule->command('queue:work --queue=default --sleep=3 --tries=3 --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping();

        // Hapus data lokasi yang lebih dari 7 hari setiap hari pukul 02:00
        $schedule->command('location:delete-old --days=7')
            ->dailyAt('02:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
