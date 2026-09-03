<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\Kunjungan;
use Illuminate\Support\Carbon;

class GenerateDummyKunjunganAdam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kunjungan:dummy-adam';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate 10 dummy kunjungan records for Amnal in Tasikmalaya for today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nameTarget = 'Amnal';
        $today = Carbon::today()->format('Y-m-d');
        
        $this->info("Searching for employee: {$nameTarget}...");

        // 1. Find Adam
        $karyawan = Karyawan::where('nama_karyawan', 'like', "%{$nameTarget}%")->first();

        if (!$karyawan) {
            $this->error("Employee '{$nameTarget}' not found!");
            return 1;
        }

        $this->info("Found: {$karyawan->nama_karyawan} (NIK: {$karyawan->nik})");

        // 2. Base Location (Tasikmalaya Center)
        // -7.3176, 108.1993
        $centerLat = -7.3176;
        $centerLon = 108.1993;

        $count = 10;
        $success = 0;

        $this->info("Generating {$count} dummy visits...");

        for ($i = 1; $i <= $count; $i++) {
            // Generate distinct but close locations
            // Variation: roughly 50m to 500m
            // 0.0005 deg ~= 55m
            // 0.005 deg ~= 550m
            
            $offsetLat = (rand(-500, 500) / 100000); // +/- 0.00500
            $offsetLon = (rand(-500, 500) / 100000);

            $lat = $centerLat + $offsetLat;
            $lon = $centerLon + $offsetLon;
            $location = "{$lat},{$lon}";

            Kunjungan::create([
                'nik' => $karyawan->nik,
                'tanggal_kunjungan' => $today,
                'lokasi' => $location,
                'deskripsi' => "Kunjungan ke Client Tasikmalaya #{$i} " . ($i % 2 == 0 ? "(Follow Up)" : "(Prospecting)"),
                'foto' => 'default_kunjungan.png', // Dummy
            ]);

            $this->info("[{$i}/{$count}] Created visit at {$location}");
            $success++;
        }

        $this->info("Done! Successfully generated {$success} visits for {$karyawan->nama_karyawan}.");
        return 0;
    }
}
