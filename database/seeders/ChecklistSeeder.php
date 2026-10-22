<?php

namespace Database\Seeders;

use App\Models\ChecklistCheckpoint;
use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Permission Group & Permissions
        $group = Permission_group::firstOrCreate(['name' => 'Checklist Toko']);

        $permIndex = Permission::firstOrCreate(['name' => 'checklist.index'], ['id_permission_group' => $group->id]);
        $permCreate = Permission::firstOrCreate(['name' => 'checklist.create'], ['id_permission_group' => $group->id]);
        $permAdmin = Permission::firstOrCreate(['name' => 'checklist.admin'], ['id_permission_group' => $group->id]);
        $permDelete = Permission::firstOrCreate(['name' => 'checklist.delete'], ['id_permission_group' => $group->id]);

        $superAdmin = Role::where('name', 'super admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo([$permIndex, $permCreate, $permAdmin, $permDelete]);
        }

        $administrator = Role::where('name', 'administrator')->first();
        if ($administrator) {
            $administrator->givePermissionTo([$permIndex, $permCreate, $permAdmin, $permDelete]);
        }

        $adminCabang = Role::where('name', 'admin cabang')->first();
        if ($adminCabang) {
            $adminCabang->givePermissionTo([$permIndex, $permCreate, $permAdmin]);
        }

        $karyawanRole = Role::where('name', 'karyawan')->first();
        if ($karyawanRole) {
            $karyawanRole->givePermissionTo([$permIndex, $permCreate]);
        }

        // 2. Default Retail Checkpoints (8 Closing)
        $closingPoints = [
            [
                'nama_checkpoint' => 'Toilet & Kamar Mandi',
                'deskripsi' => 'Buka pintu toilet, periksa belakang pintu dan kolong. Pastikan steril dari orang/penyusup lalu kunci dari luar.',
                'tipe' => 'closing',
                'urutan' => 1,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Gudang / Ruang Stok Barang',
                'deskripsi' => 'Periksa celah rak stok & tumpukan kardus barang. Pastikan tidak ada orang bersembunyi lalu gembok pintu gudang.',
                'tipe' => 'closing',
                'urutan' => 2,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Kolong Meja Kasir & Kolong Display',
                'deskripsi' => 'Periksa kolong meja kasir tertutup & area blindspot CCTV. Pastikan steril dari orang atau barang mencurigakan.',
                'tipe' => 'closing',
                'urutan' => 3,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Pintu Belakang, Jendela & Teralis Besi',
                'deskripsi' => 'Pastikan semua grendel, slot kunci, dan teralis jendela belakang terkunci rapat dan kokoh dari dalam.',
                'tipe' => 'closing',
                'urutan' => 4,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Laci Kasir & Brankas Toko',
                'deskripsi' => 'Laci kasir ditarik/terbuka tanda uang kasir sudah disetor/masuk brankas, dan brankas terkunci rapat.',
                'tipe' => 'closing',
                'urutan' => 5,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Etalase Kaca & Rak Barang Berharga',
                'deskripsi' => 'Etalase kaca display terkunci rapat dan aman dari jangkauan.',
                'tipe' => 'closing',
                'urutan' => 6,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Saklar Lampu, AC & Komputer Kasir',
                'deskripsi' => 'AC dan komputer kasir sudah dimatikan (shutdown), hanya lampu malam/keamanan yang menyala.',
                'tipe' => 'closing',
                'urutan' => 7,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Rolling Door & Gembok Pintu Depan',
                'deskripsi' => 'Foto tampak luar: rolling door/pintu utama tertutup rapat dan gembok ganda terpasang kuat.',
                'tipe' => 'closing',
                'urutan' => 8,
                'is_active' => true,
                'all_cabang' => true,
            ],
        ];

        // 3. Default Retail Checkpoints (8 Opening)
        $openingPoints = [
            [
                'nama_checkpoint' => 'Pintu Masuk & Gembok Depan',
                'deskripsi' => 'Periksa kondisi fisik gembok dan pintu masuk. Pastikan utuh tanpa bekas congkelan/pembobolan malam hari.',
                'tipe' => 'opening',
                'urutan' => 1,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Area Kasir & Brankas',
                'deskripsi' => 'Periksa kondisi fisik laci kasir dan brankas toko. Pastikan utuh dan aman seperti saat closing kemarin.',
                'tipe' => 'opening',
                'urutan' => 2,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Komputer POS / Kasir, Printer & EDC',
                'deskripsi' => 'Nyalakan komputer POS, printer struk, dan mesin EDC. Pastikan semua menyala normal dan siap melayani transaksi.',
                'tipe' => 'opening',
                'urutan' => 3,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Lampu Toko, AC & Monitor CCTV',
                'deskripsi' => 'Lampu toko menyala terang, AC berfungsi sejuk, dan layar CCTV online aktif merekam.',
                'tipe' => 'opening',
                'urutan' => 4,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Kebersihan Lantai & Lorong Toko',
                'deskripsi' => 'Lantai area penjualan disapu dan dipel bersih, lorong (aisle) bebas dari tumpukan kardus/sampah.',
                'tipe' => 'opening',
                'urutan' => 5,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Kerapian Rak Display & Gondola',
                'deskripsi' => 'Produk di rak tertata rapi menghadap ke depan (facing), label harga jelas dan terpasang rapi.',
                'tipe' => 'opening',
                'urutan' => 6,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Kebersihan Kaca Etalase & Pintu',
                'deskripsi' => 'Kaca pintu utama dan etalase bersih dari noda/debu, siap menyambut pelanggan toko.',
                'tipe' => 'opening',
                'urutan' => 7,
                'is_active' => true,
                'all_cabang' => true,
            ],
            [
                'nama_checkpoint' => 'Fasilitas Toilet & Area Karyawan',
                'deskripsi' => 'Toilet bersih, air mengalir normal, sabun/tisu terisi siap pakai.',
                'tipe' => 'opening',
                'urutan' => 8,
                'is_active' => true,
                'all_cabang' => true,
            ],
        ];

        foreach (array_merge($closingPoints, $openingPoints) as $item) {
            ChecklistCheckpoint::updateOrCreate(
                [
                    'nama_checkpoint' => $item['nama_checkpoint'],
                    'tipe' => $item['tipe'],
                ],
                $item
            );
        }
    }
}
