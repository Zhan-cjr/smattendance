<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\Cabang;
use App\Models\Presensi;
use App\Models\Jamkerja;
use Illuminate\Support\Carbon;

class GenerateDummyPresensiTasikmalaya extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:dummy-tasik';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy presensi data for Tasikmalaya employees for today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        $this->info("Generating dummy data for Date: {$today}");

        // 1. Get Cabang Tasikmalaya
        $cabang = Cabang::where('nama_cabang', 'like', '%TASIKMALAYA%')->first();

        if (!$cabang) {
            $this->error('Cabang Tasikmalaya not found!');
            return 1;
        }

        $this->info("Found Branch: {$cabang->nama_cabang} (Code: {$cabang->kode_cabang})");
        
        // Parse Branch Location
        $locParts = explode(',', $cabang->lokasi_cabang);
        if (count($locParts) != 2) {
             $this->error('Invalid lokasi_cabang format for branch!');
             return 1;
        }
        $centerLat = floatval($locParts[0]);
        $centerLon = floatval($locParts[1]);
        $radiusMeters = $cabang->radius_cabang;

        // 2. Get a valid Jam Kerja (Just take the first one or a default)
        // Adjust this query if you need a specific shift
        $jamKerja = Jamkerja::first();
        if (!$jamKerja) {
            $this->error('No Jam Kerja found!');
            return 1;
        }
        $kodeJamKerja = $jamKerja->kode_jam_kerja;
        $namaJamKerja = $jamKerja->nama_jam_kerja ?? $kodeJamKerja;
        $this->info("Using Jam Kerja: {$namaJamKerja}");


        // 3. Get Employees of this Branch
        $karyawans = Karyawan::where('kode_cabang', $cabang->kode_cabang)
                             ->where('status_aktif_karyawan', '1') // Only active
                             ->get();

        if ($karyawans->isEmpty()) {
            $this->info('No active employees found for this branch.');
            return 0;
        }

        $this->info("Found {$karyawans->count()} employees.");

        $countSuccess = 0;
        $countSkip = 0;

        foreach ($karyawans as $karyawan) {
            // Check if presensi already exists
            $exists = Presensi::where('nik', $karyawan->nik)
                              ->where('tanggal', $today)
                              ->exists();

            if ($exists) {
                $this->warn("Skipping {$karyawan->nama_karyawan} - Presensi already exists.");
                $countSkip++;
                continue;
            }

            // Randomize Time: 07:00:00 - 08:00:00
            $randomHour = 7;
            $randomMinute = rand(0, 59);
            $randomSecond = rand(0, 59);
            $jamIn = sprintf('%02d:%02d:%02d', $randomHour, $randomMinute, $randomSecond);

            // Randomize Location Status (e.g., 70% inside, 30% outside)
            // Note: This is an approximation. "Outside" here means we intentionally
            // generate a point that MIGHT be outside.
            // A simple way is to generate distance.
            
            $isInside = (rand(1, 100) <= 70); 
            
            // Generate random distance in meters
            if ($isInside) {
                // 0 to radius - 5 meters (safe zone)
                $dist = rand(0, max(0, $radiusMeters - 5));
                $statusNote = "Inside Radius";
            } else {
                // radius + 10 to radius + 500 meters
                $dist = rand($radiusMeters + 10, $radiusMeters + 500);
                $statusNote = "Outside Radius";
            }

            // Convert distance to degrees (Roughly)
            // 1 degree lat ~= 111,320 meters
            // 1 degree lon ~= 40075000 * cos(lat) / 360
            
            $angle = rand(0, 360);
            $dx = $dist * cos(deg2rad($angle));
            $dy = $dist * sin(deg2rad($angle));

            $deltaLat = $dy / 111320;
            $deltaLon = $dx / (40075000 * cos(deg2rad($centerLat)) / 360);

            $newLat = $centerLat + $deltaLat;
            $newLon = $centerLon + $deltaLon;
            
            $lokasiIn = "{$newLat},{$newLon}";

            // Insert
            Presensi::create([
                'nik' => $karyawan->nik,
                'tanggal' => $today,
                'jam_in' => $today . ' ' . $jamIn,
                'foto_in' => 'default.png', // Dummy
                'lokasi_in' => $lokasiIn,
                'kode_jam_kerja' => $kodeJamKerja,
                'status' => 'h', // Hadir
            ]);
            
            $this->info("Created for {$karyawan->nama_karyawan} ({$statusNote} - {$dist}m)");
            $countSuccess++;
        }

        $this->info("Done! Created: {$countSuccess}, Skipped: {$countSkip}");
        return 0;
    }
}
