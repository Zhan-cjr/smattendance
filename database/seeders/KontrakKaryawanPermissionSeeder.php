<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class KontrakKaryawanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan group permission ada
        $permissionGroup = \App\Models\Permission_group::firstOrCreate(['name' => 'Kontrak']);

        // Pastikan permission kontrak.index ada
        $permission = Permission::firstOrCreate(
            ['name' => 'kontrak.index'],
            ['id_permission_group' => $permissionGroup->id]
        );

        // Ambil role karyawan
        $role = Role::where('name', 'karyawan')->first();

        if ($role) {
            if ($role->hasPermissionTo($permission)) {
                //$this->command->info('Permission kontrak.index already exists for role karyawan.');
            } else {
                // Berikan permission ke role karyawan
                $role->givePermissionTo($permission);
                $this->command->info('Permission kontrak.index successfully added to role karyawan.');
            }
        } else {
            $this->command->error('Role karyawan not found.');
        }
    }
}
