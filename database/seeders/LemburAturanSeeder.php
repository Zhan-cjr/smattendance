<?php

namespace Database\Seeders;

use App\Models\LemburAturan;
use Illuminate\Database\Seeder;

class LemburAturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aturan Hari Kerja
        LemburAturan::create([
            'tipe_hari' => '1', // Hari Kerja
            'jam_dari' => 0,
            'jam_sampai' => 1,
            'faktor' => 1.5,
        ]);
        LemburAturan::create([
            'tipe_hari' => '1', // Hari Kerja
            'jam_dari' => 1,
            'jam_sampai' => 99,
            'faktor' => 2.0,
        ]);

        // Aturan Hari Libur (Contoh Standar 6 Hari Kerja)
        LemburAturan::create([
            'tipe_hari' => '2', // Hari Libur
            'jam_dari' => 0,
            'jam_sampai' => 7,
            'faktor' => 2.0,
        ]);
        LemburAturan::create([
            'tipe_hari' => '2', // Hari Libur
            'jam_dari' => 7,
            'jam_sampai' => 8,
            'faktor' => 3.0,
        ]);
        LemburAturan::create([
            'tipe_hari' => '2', // Hari Libur
            'jam_dari' => 8,
            'jam_sampai' => 99,
            'faktor' => 4.0,
        ]);
    }
}
