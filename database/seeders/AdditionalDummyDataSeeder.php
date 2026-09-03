<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdditionalDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Additional Cabang
        $cabangs = [
            [
                'kode_cabang' => 'BDG',
                'nama_cabang' => 'BANDUNG',
                'alamat_cabang' => 'Jl. Asia Afrika No. 10, Bandung',
                'telepon_cabang' => '0224201000',
                'lokasi_cabang' => '-6.917464,107.619122',
                'radius_cabang' => '30',
            ],
            [
                'kode_cabang' => 'JKT',
                'nama_cabang' => 'JAKARTA',
                'alamat_cabang' => 'Jl. Sudirman No. 1, Jakarta',
                'telepon_cabang' => '0215701000',
                'lokasi_cabang' => '-6.208763,106.845599',
                'radius_cabang' => '30',
            ],
            [
                'kode_cabang' => 'SBY',
                'nama_cabang' => 'SURABAYA',
                'alamat_cabang' => 'Jl. Pemuda No. 1, Surabaya',
                'telepon_cabang' => '0315311000',
                'lokasi_cabang' => '-7.265757,112.750373',
                'radius_cabang' => '30',
            ],
        ];

        foreach ($cabangs as $cabang) {
            Cabang::updateOrCreate(
                ['kode_cabang' => $cabang['kode_cabang']],
                $cabang
            );
        }

        $this->command->info('Additional branches seeded (BDG, JKT, SBY).');

        // 2. Ensure Departments Exist
        $deptData = [
            ['kode_dept' => 'PRD', 'nama_dept' => 'PRODUKSI'],
            ['kode_dept' => 'SDM', 'nama_dept' => 'SUMBER DAYA MANUSIA'],
            ['kode_dept' => 'KUA', 'nama_dept' => 'KEUANGAN'],
        ];

        foreach ($deptData as $d) {
            \App\Models\Departemen::updateOrCreate(
                ['kode_dept' => $d['kode_dept']],
                $d
            );
        }

        // 3. Ensure Jabatan Exists
        $jabatanData = [
            ['kode_jabatan' => 'J01', 'nama_jabatan' => 'Manager'],
            ['kode_jabatan' => 'J02', 'nama_jabatan' => 'Supervisor'],
            ['kode_jabatan' => 'J03', 'nama_jabatan' => 'Staff'],
            ['kode_jabatan' => 'J04', 'nama_jabatan' => 'Operator'],
            ['kode_jabatan' => 'J05', 'nama_jabatan' => 'Helper'],
        ];

        foreach ($jabatanData as $j) {
            \App\Models\Jabatan::updateOrCreate(
                ['kode_jabatan' => $j['kode_jabatan']],
                $j
            );
        }

        // 4. Create 30 Dummy Karyawan
        $depts = ['PRD', 'SDM', 'KUA'];
        $jabatan_codes = ['J01', 'J02', 'J03', 'J04', 'J05'];
        $cabang_codes = ['BDG', 'JKT', 'SBY'];
        
        $first_names = ['Adi', 'Budi', 'Candra', 'Dedi', 'Eko', 'Fajar', 'Gilang', 'Hendra', 'Indra', 'Joko', 'Kurnia', 'Lukman', 'Mahendra', 'Nanda', 'Oki', 'Prasetyo', 'Qori', 'Rizki', 'Sandi', 'Taufik', 'Utama', 'Vicky', 'Wahyu', 'Xaverius', 'Yudi', 'Zainal', 'Putri', 'Siti', 'Dewi', 'Rina'];
        $last_names = ['Santoso', 'Wijaya', 'Saputra', 'Hidayat', 'Pratama', 'Nugraha', 'Setiawan', 'Kusuma', 'Lestari', 'Pertiwi', 'Ramadhan', 'Utami', 'Wulandari', 'Susanti', 'Mulyani', 'Rahayu', 'Handayani', 'Sari', 'Wati', 'Nur', 'Cahyani', 'Indah', 'Kurniasari', 'Puspita', 'Anggraeni', 'Novita', 'Yuliana', 'Astuti', 'Hasanah', 'Fitri'];

        // Start NIK from 250100013
        $start_nik = 250100013;

        for ($i = 0; $i < 30; $i++) {
            $nik = (string) ($start_nik + $i);
            $nama = $first_names[$i % count($first_names)] . ' ' . $last_names[$i % count($last_names)];
            $dept = $depts[array_rand($depts)];
            $jabatan = $jabatan_codes[array_rand($jabatan_codes)];
            $cabang = $cabang_codes[array_rand($cabang_codes)];
            
            Karyawan::updateOrCreate(
                ['nik' => $nik],
                [
                    'no_ktp' => '320101010101' . str_pad($i + 13, 4, '0', STR_PAD_LEFT),
                    'nama_karyawan' => $nama,
                    'tempat_lahir' => 'Kota ' . $cabang,
                    'tanggal_lahir' => '199' . rand(0, 9) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'alamat' => 'Jl. Contoh No. ' . ($i + 1) . ', ' . $cabang,
                    'no_hp' => '0812' . str_pad($i + 13, 8, '0', STR_PAD_LEFT),
                    'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
                    'kode_status_kawin' => 'TK',
                    'pendidikan_terakhir' => 'S1',
                    'kode_cabang' => $cabang,
                    'kode_dept' => $dept,
                    'kode_jabatan' => $jabatan,
                    'tanggal_masuk' => '2021-01-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'status_karyawan' => 'T',
                    'lock_location' => '1',
                    'status_aktif_karyawan' => '1',
                    'password' => Hash::make('12345'),
                ]
            );
        }

        $this->command->info('30 Additional Dummy Karyawan seeded successfully.');
    }
}
