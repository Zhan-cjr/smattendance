<?php

namespace App\Console\Commands;

use App\Models\LiveLocation;
use App\Models\Karyawan;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateTestLocationData extends Command
{
    protected $signature = 'location:generate-test {nik?} {--clean}';
    protected $description = 'Generate test location data for employees with spy enabled. Use --clean to delete old data first.';

    /**
     * Predefined location areas with their coordinates
     * Format: 'area_name' => ['lat' => latitude, 'lng' => longitude, 'lokasi' => location_name]
     */
    private $predefinedLocations = [
        'cibeber_cianjur' => [
            ['lat' => -6.8044, 'lng' => 107.1439, 'lokasi' => 'Cibeber Cianjur - Pusat'],
            ['lat' => -6.8050, 'lng' => 107.1445, 'lokasi' => 'Cibeber Cianjur - Timur'],
            ['lat' => -6.8040, 'lng' => 107.1430, 'lokasi' => 'Cibeber Cianjur - Barat'],
            ['lat' => -6.8035, 'lng' => 107.1435, 'lokasi' => 'Cibeber Cianjur - Utara'],
            ['lat' => -6.8055, 'lng' => 107.1440, 'lokasi' => 'Cibeber Cianjur - Selatan'],
        ],
        'jakarta' => [
            ['lat' => -6.2088, 'lng' => 106.8456, 'lokasi' => 'Jakarta Pusat'],
            ['lat' => -6.2100, 'lng' => 106.8470, 'lokasi' => 'Jalan Merdeka Barat'],
            ['lat' => -6.2110, 'lng' => 106.8480, 'lokasi' => 'Kota Tua Jakarta'],
            ['lat' => -6.2120, 'lng' => 106.8490, 'lokasi' => 'Monas'],
            ['lat' => -6.2130, 'lng' => 106.8500, 'lokasi' => 'Plaza Indonesia'],
        ]
    ];

    public function handle()
    {
        $nik = $this->argument('nik');
        $clean = $this->option('clean');

        if ($nik) {
            $employees = Karyawan::where('nik', $nik)->where('spy', 1)->get();
        } else {
            $employees = Karyawan::where('spy', 1)->where('status_aktif_karyawan', 1)->limit(5)->get();
        }

        if ($employees->isEmpty()) {
            $this->error('Tidak ada karyawan dengan spy=1 ditemukan');
            return;
        }

        foreach ($employees as $employee) {
            if ($clean) {
                // Delete old location data
                LiveLocation::where('nik', $employee->nik)->delete();
                $this->line("  - Deleted old location data for {$employee->nik}");
            }

            // Determine which location area to use based on employee details
            $locationArea = $this->determineLocationArea($employee);
            $locations = $this->predefinedLocations[$locationArea] ?? $this->predefinedLocations['jakarta'];

            $now = Carbon::now();
            $count = 0;

            foreach ($locations as $index => $loc) {
                LiveLocation::create([
                    'nik' => $employee->nik,
                    'latitude' => $loc['lat'] + (rand(-50, 50) / 10000),
                    'longitude' => $loc['lng'] + (rand(-50, 50) / 10000),
                    'lokasi' => $loc['lokasi'],
                    'accuracy' => rand(5, 30),
                    'tracked_at' => $now->subMinutes($index * 5),
                ]);
                $count++;
            }

            $this->info("✓ Generated {$count} location records for {$employee->nama_karyawan} ({$employee->nik}) - Area: {$locationArea}");
        }

        $this->info('Test location data generated successfully!');
    }

    /**
     * Determine which location area to use based on employee details
     */
    private function determineLocationArea($employee)
    {
        // Check if employee has specific location preference in alamat field
        if ($employee->alamat) {
            $alamatLower = strtolower($employee->alamat);
            
            if (str_contains($alamatLower, 'cibeber') || str_contains($alamatLower, 'cianjur')) {
                return 'cibeber_cianjur';
            }
            
            if (str_contains($alamatLower, 'jakarta') || str_contains($alamatLower, 'jkt') || str_contains($alamatLower, 'dki')) {
                return 'jakarta';
            }
        }

        // Default fallback
        return 'jakarta';
    }
}
