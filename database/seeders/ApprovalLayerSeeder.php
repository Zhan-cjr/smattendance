<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovalLayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Rules for IZIN_ABSEN
        // Level 1: HRD
        DB::table('approval_layers')->updateOrInsert(
            [
                'feature' => 'IZIN_ABSEN',
                'level' => 1,
            ],
            [
                'role_name' => 'hrd', // Verify if your role is named 'hrd' or 'admin' or 'super admin'
                'kode_dept' => null, // All depts
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Level 2: Director (Sample)
        DB::table('approval_layers')->updateOrInsert(
            [
                'feature' => 'IZIN_ABSEN',
                'level' => 2,
            ],
            [
                'role_name' => 'director', 
                'kode_dept' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
