<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Cabang;
use App\Models\Statuskawin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Jabatan (5 data)
        $jabatans = [
            ['kode_jabatan' => 'J01', 'nama_jabatan' => 'Manager'],
            ['kode_jabatan' => 'J02', 'nama_jabatan' => 'Supervisor'],
            ['kode_jabatan' => 'J03', 'nama_jabatan' => 'Staff'],
            ['kode_jabatan' => 'J04', 'nama_jabatan' => 'Operator'],
            ['kode_jabatan' => 'J05', 'nama_jabatan' => 'Admin'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::updateOrCreate(
                ['kode_jabatan' => $jabatan['kode_jabatan']],
                $jabatan
            );
        }

        $this->command->info('Jabatan berhasil di-seed (5 data)');

        // 2. Seed Departemen (3 data)
        $departemens = [
            ['kode_dept' => 'PRD', 'nama_dept' => 'Produksi'],
            ['kode_dept' => 'SDM', 'nama_dept' => 'Sumber Daya Manusia'],
            ['kode_dept' => 'KUA', 'nama_dept' => 'Keuangan & Akuntansi'],
        ];

        foreach ($departemens as $departemen) {
            Departemen::updateOrCreate(
                ['kode_dept' => $departemen['kode_dept']],
                $departemen
            );
        }

        $this->command->info('Departemen berhasil di-seed (3 data)');

        // 3. Pastikan ada Cabang
        $cabang = Cabang::first();
        if (!$cabang) {
            $cabang = Cabang::create([
                'kode_cabang' => 'TSM',
                'nama_cabang' => 'TASIKMALAYA',
                'alamat_cabang' => 'Jln. Perintis Kemerdekaan No. 80 Kawalu Tasikmalaya',
                'telepon_cabang' => '0265311766',
                'lokasi_cabang' => '-7.317623346580317,108.19935815408388',
                'radius_cabang' => '30',
            ]);
        }

        // 4. Pastikan ada Status Kawin
        $statusKawin = Statuskawin::first();
        if (!$statusKawin) {
            Statuskawin::create(['kode_status_kawin' => 'TK', 'status_kawin' => 'Tidak Kawin']);
            Statuskawin::create(['kode_status_kawin' => 'K0', 'status_kawin' => 'Kawin Belum Punya Tanggungan']);
            Statuskawin::create(['kode_status_kawin' => 'K1', 'status_kawin' => 'Kawin Punya Tanggungan 1']);
            Statuskawin::create(['kode_status_kawin' => 'K2', 'status_kawin' => 'Kawin Punya Tanggungan 2']);
        }

        // 4. Seed Karyawan (12 data)
        $karyawans = [
            [
                'nik' => '250100001',
                'no_ktp' => '3201010101010001',
                'nama_karyawan' => 'Ahmad Rizki',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1990-01-15',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta Pusat',
                'no_hp' => '081234567890',
                'jenis_kelamin' => 'L',
                'kode_status_kawin' => 'K1',
                'pendidikan_terakhir' => 'S1',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'PRD',
                'kode_jabatan' => 'J01',
                'tanggal_masuk' => '2020-01-01',
                'status_karyawan' => 'T',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100002',
                'no_ktp' => '3201010101010002',
                'nama_karyawan' => 'Siti Nurhaliza',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1992-03-20',
                'alamat' => 'Jl. Sudirman No. 45, Bandung',
                'no_hp' => '081234567891',
                'jenis_kelamin' => 'P',
                'kode_status_kawin' => 'TK',
                'pendidikan_terakhir' => 'S1',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'SDM',
                'kode_jabatan' => 'J02',
                'tanggal_masuk' => '2020-02-15',
                'status_karyawan' => 'T',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100003',
                'no_ktp' => '3201010101010003',
                'nama_karyawan' => 'Budi Santoso',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1988-05-10',
                'alamat' => 'Jl. Gajah Mada No. 78, Surabaya',
                'no_hp' => '081234567892',
                'jenis_kelamin' => 'L',
                'kode_status_kawin' => 'K2',
                'pendidikan_terakhir' => 'S1',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'KUA',
                'kode_jabatan' => 'J03',
                'tanggal_masuk' => '2020-03-01',
                'status_karyawan' => 'T',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100004',
                'no_ktp' => '3201010101010004',
                'nama_karyawan' => 'Dewi Lestari',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1993-07-25',
                'alamat' => 'Jl. Malioboro No. 12, Yogyakarta',
                'no_hp' => '081234567893',
                'jenis_kelamin' => 'P',
                'kode_status_kawin' => 'K1',
                'pendidikan_terakhir' => 'D3',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'PRD',
                'kode_jabatan' => 'J04',
                'tanggal_masuk' => '2020-04-10',
                'status_karyawan' => 'K',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100005',
                'no_ktp' => '3201010101010005',
                'nama_karyawan' => 'Eko Prasetyo',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1991-09-12',
                'alamat' => 'Jl. Pemuda No. 56, Semarang',
                'no_hp' => '081234567894',
                'jenis_kelamin' => 'L',
                'kode_status_kawin' => 'TK',
                'pendidikan_terakhir' => 'SMA',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'PRD',
                'kode_jabatan' => 'J04',
                'tanggal_masuk' => '2020-05-20',
                'status_karyawan' => 'K',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100006',
                'no_ktp' => '3201010101010006',
                'nama_karyawan' => 'Fitri Handayani',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1994-11-08',
                'alamat' => 'Jl. Gatot Subroto No. 34, Medan',
                'no_hp' => '081234567895',
                'jenis_kelamin' => 'P',
                'kode_status_kawin' => 'TK',
                'pendidikan_terakhir' => 'S1',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'SDM',
                'kode_jabatan' => 'J03',
                'tanggal_masuk' => '2020-06-05',
                'status_karyawan' => 'T',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100007',
                'no_ktp' => '3201010101010007',
                'nama_karyawan' => 'Gunawan Wijaya',
                'tempat_lahir' => 'Makassar',
                'tanggal_lahir' => '1989-02-18',
                'alamat' => 'Jl. Ahmad Yani No. 67, Makassar',
                'no_hp' => '081234567896',
                'jenis_kelamin' => 'L',
                'kode_status_kawin' => 'K2',
                'pendidikan_terakhir' => 'S1',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'KUA',
                'kode_jabatan' => 'J02',
                'tanggal_masuk' => '2020-07-12',
                'status_karyawan' => 'T',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100008',
                'no_ktp' => '3201010101010008',
                'nama_karyawan' => 'Hani Kartika',
                'tempat_lahir' => 'Palembang',
                'tanggal_lahir' => '1992-04-30',
                'alamat' => 'Jl. Sudirman No. 89, Palembang',
                'no_hp' => '081234567897',
                'jenis_kelamin' => 'P',
                'kode_status_kawin' => 'K1',
                'pendidikan_terakhir' => 'D3',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'PRD',
                'kode_jabatan' => 'J03',
                'tanggal_masuk' => '2020-08-25',
                'status_karyawan' => 'T',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100009',
                'no_ktp' => '3201010101010009',
                'nama_karyawan' => 'Indra Permana',
                'tempat_lahir' => 'Denpasar',
                'tanggal_lahir' => '1990-06-14',
                'alamat' => 'Jl. Gajah Mada No. 23, Denpasar',
                'no_hp' => '081234567898',
                'jenis_kelamin' => 'L',
                'kode_status_kawin' => 'TK',
                'pendidikan_terakhir' => 'SMA',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'SDM',
                'kode_jabatan' => 'J05',
                'tanggal_masuk' => '2020-09-10',
                'status_karyawan' => 'K',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100010',
                'no_ktp' => '3201010101010010',
                'nama_karyawan' => 'Jihan Safitri',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1993-08-22',
                'alamat' => 'Jl. Ijen No. 45, Malang',
                'no_hp' => '081234567899',
                'jenis_kelamin' => 'P',
                'kode_status_kawin' => 'TK',
                'pendidikan_terakhir' => 'S1',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'KUA',
                'kode_jabatan' => 'J03',
                'tanggal_masuk' => '2020-10-15',
                'status_karyawan' => 'T',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100011',
                'no_ktp' => '3201010101010011',
                'nama_karyawan' => 'Kurniawan Adi',
                'tempat_lahir' => 'Solo',
                'tanggal_lahir' => '1991-12-05',
                'alamat' => 'Jl. Slamet Riyadi No. 78, Solo',
                'no_hp' => '081234567900',
                'jenis_kelamin' => 'L',
                'kode_status_kawin' => 'K1',
                'pendidikan_terakhir' => 'S1',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'PRD',
                'kode_jabatan' => 'J02',
                'tanggal_masuk' => '2020-11-20',
                'status_karyawan' => 'T',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
            [
                'nik' => '250100012',
                'no_ktp' => '3201010101010012',
                'nama_karyawan' => 'Lina Marlina',
                'tempat_lahir' => 'Bogor',
                'tanggal_lahir' => '1994-10-28',
                'alamat' => 'Jl. Raya Pajajaran No. 12, Bogor',
                'no_hp' => '081234567901',
                'jenis_kelamin' => 'P',
                'kode_status_kawin' => 'K0',
                'pendidikan_terakhir' => 'D3',
                'kode_cabang' => $cabang->kode_cabang,
                'kode_dept' => 'SDM',
                'kode_jabatan' => 'J04',
                'tanggal_masuk' => '2020-12-01',
                'status_karyawan' => 'K',
                'lock_location' => '1',
                'status_aktif_karyawan' => '1',
                'password' => Hash::make('12345'),
            ],
        ];

        foreach ($karyawans as $karyawan) {
            Karyawan::updateOrCreate(
                ['nik' => $karyawan['nik']],
                $karyawan
            );
        }

        $this->command->info('Karyawan berhasil di-seed (12 data)');
        $this->command->info('Semua data dummy berhasil di-seed!');
    }
}

