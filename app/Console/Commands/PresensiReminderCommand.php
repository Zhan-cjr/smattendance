<?php

namespace App\Console\Commands;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PresensiReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:reminder {--type=all : in, out, or all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automated Web Push notifications for clock-in and clock-out reminders';

    /**
     * Execute the console command.
     */
    public function handle(WebPushService $webPushService): int
    {
        $type = $this->option('type');
        $now = Carbon::now(config('app.timezone'));
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        $this->info("Running Presensi Reminder for {$today} at {$currentTime} [Type: {$type}]");

        if ($type === 'in' || $type === 'all') {
            $this->sendClockInReminders($webPushService, $today);
        }

        if ($type === 'out' || $type === 'all') {
            $this->sendClockOutReminders($webPushService, $today, $now);
        }

        return Command::SUCCESS;
    }

    /**
     * Option A: Morning Clock-In Reminder for active employees who haven't clocked in today
     */
    protected function sendClockInReminders(WebPushService $webPushService, string $today): void
    {
        // Get active employees
        $activeEmployees = Karyawan::where('status_aktif_karyawan', '1')->get();

        // Get NIKs already clocked in today
        $alreadyClockedIn = Presensi::where('tanggal', $today)
            ->whereNotNull('jam_in')
            ->pluck('nik')
            ->toArray();

        // Get NIKs with approved leave/cuti/sakit/dinas today
        $onLeaveAbsen = DB::table('presensi_izinabsen')
            ->where('status', 1)
            ->where('dari', '<=', $today)
            ->where('sampai', '>=', $today)
            ->pluck('nik')->toArray();

        $onLeaveCuti = DB::table('presensi_izincuti')
            ->where('status', 1)
            ->where('dari', '<=', $today)
            ->where('sampai', '>=', $today)
            ->pluck('nik')->toArray();

        $onLeaveSakit = DB::table('presensi_izinsakit')
            ->where('status', 1)
            ->where('dari', '<=', $today)
            ->where('sampai', '>=', $today)
            ->pluck('nik')->toArray();

        $onLeaveDinas = DB::table('presensi_izindinas')
            ->where('status', 1)
            ->where('dari', '<=', $today)
            ->where('sampai', '>=', $today)
            ->pluck('nik')->toArray();

        $exemptNiks = array_unique(array_merge(
            $alreadyClockedIn,
            $onLeaveAbsen,
            $onLeaveCuti,
            $onLeaveSakit,
            $onLeaveDinas
        ));

        $targetEmployees = $activeEmployees->filter(function ($karyawan) use ($exemptNiks) {
            return !in_array($karyawan->nik, $exemptNiks);
        });

        $count = 0;
        foreach ($targetEmployees as $emp) {
            $firstName = explode(' ', $emp->nama_karyawan)[0];
            $webPushService->sendToNik(
                $emp->nik,
                '☀️ Selamat Pagi!',
                "Semangat beraktivitas hari ini, {$firstName}. Jangan lupa lakukan presensi masuk saat tiba di lokasi kerja ya!",
                url('/presensi/create')
            );
            $count++;
        }

        $this->info("Clock-in reminders sent to {$count} employees.");
    }

    /**
     * Clock-Out Reminder based on the shift chosen when employee clocked in
     */
    protected function sendClockOutReminders(WebPushService $webPushService, string $today, Carbon $now): void
    {
        // Find employees who clocked in today but have not clocked out yet
        $presensis = Presensi::where('tanggal', $today)
            ->whereNotNull('jam_in')
            ->whereNull('jam_out')
            ->join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->select('presensi.*', 'presensi_jamkerja.nama_jam_kerja', 'presensi_jamkerja.jam_masuk', 'presensi_jamkerja.jam_pulang', 'karyawan.nama_karyawan')
            ->get();

        $count = 0;
        foreach ($presensis as $p) {
            if (empty($p->jam_pulang)) {
                continue;
            }

            $jamPulangCarbon = Carbon::parse($today . ' ' . $p->jam_pulang, config('app.timezone'));
            $diffMinutes = $now->diffInMinutes($jamPulangCarbon, false); // Negative if past jam_pulang

            // If current time is within 15 minutes before jam_pulang OR up to 90 minutes after jam_pulang
            if ($diffMinutes <= 15 && $diffMinutes >= -90) {
                $firstName = explode(' ', $p->nama_karyawan)[0];
                $webPushService->sendToNik(
                    $p->nik,
                    '⏰ Pengingat Presensi Pulang',
                    "Halo {$firstName}, shift kerja Anda ({$p->jam_masuk} - {$p->jam_pulang}) telah selesai. Jangan lupa lakukan presensi pulang ya!",
                    url('/presensi/create')
                );
                $count++;
            }
        }

        $this->info("Clock-out reminders sent to {$count} employees.");
    }
}
