<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovalFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\ApprovalFeature::firstOrCreate(
            ['feature' => 'IZIN'],
            [
                'name' => 'IZIN',
                'description' => 'Konfigurasi persetujuan untuk modul perizinan karyawan (Absen, Sakit, Cuti, Dinas).'
            ]
        );
    
    }
}
