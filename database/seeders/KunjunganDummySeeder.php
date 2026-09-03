<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Kunjungan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KunjunganDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil data karyawan yang dibuat dari AdditionalDummyDataSeeder
        // Kita asumsikan karyawan dummy memiliki NIK yg diawali 2501
        $karyawans = Karyawan::where('nik', 'like', '2603%')->get();

        if ($karyawans->isEmpty()) {
            $this->command->warn('Tidak ada data karyawan dummy (NIK 2501...). Jalankan AdditionalDummyDataSeeder terlebih dahulu.');
            return;
        }

        $faker = \Faker\Factory::create('id_ID');

        // Koordinat Tasikmalaya
        // Lat: -7.3274, Long: 108.2207
        $baseLat = -7.3274;
        $baseLng = 108.2207;

        foreach ($karyawans as $karyawan) {
            // Buat 5-10 kunjungan per karyawan
            $jumlahKunjungan = rand(5, 10);

            for ($i = 0; $i < $jumlahKunjungan; $i++) {
                // Random offset untuk lokasi (sekitar 5-10km radius)
                // 0.01 derajat lat/lng kira-kira 1.1km
                $latOffset = (rand(-50, 50) / 1000); // +/- 0.05 derajat
                $lngOffset = (rand(-50, 50) / 1000);

                $lat = $baseLat + $latOffset;
                $lng = $baseLng + $lngOffset;

                $tanggal = $faker->dateTimeBetween('-2 months', 'now');

                Kunjungan::create([
                    'nik' => $karyawan->nik,
                    // Note: Check if destination/tujuan field exists. Migration had 'tujuan_kunjungan'.
                    // Based on 2025_10_11_144625_create_kunjungan_table.php there is 'tujuan_kunjungan'.
                    // But check if there was a remove migration?
                    // List of files showed: 2025_10_11_145253_remove_tujuan_kunjungan_from_kunjungan_table.php
                    // So likely 'tujuan_kunjungan' was removed. We stick to deskripsi.
                    'deskripsi' => 'Kunjungan ke ' . $faker->company,
                    'foto' => null, // Atau path default jika ada
                    'lokasi' => $lat . ',' . $lng,
                    'tanggal_kunjungan' => $tanggal->format('Y-m-d'),
                ]);
            }
        }

        $this->command->info('Berhasil membuat data dummy kunjungan di sekitar Tasikmalaya.');
    }
}
