<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Statuskaryawan::updateOrCreate(['kode_status_karyawan' => 'K'], ['nama_status_karyawan' => 'Kontrak']);
        \App\Models\Statuskaryawan::updateOrCreate(['kode_status_karyawan' => 'T'], ['nama_status_karyawan' => 'Tetap']);
    }
}
