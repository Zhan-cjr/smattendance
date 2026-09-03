<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GrupSetJamKerjaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari permission group Grup
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Grup']);

        $permission = Permission::firstOrCreate([
            'name' => 'grup.setJamKerja'
        ], [
            'id_permission_group' => $permissiongroup->id
        ]);

        $this->command->info('Permission grup.setJamKerja ensured.');

        // Berikan permission ke role ID 1 (admin)
        $roleID = 1;
        $role = Role::findById($roleID);
        if ($role && !$role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
            $this->command->info('Permission grup.setJamKerja diberikan ke role admin');
        } else {
             $this->command->info('Permission grup.setJamKerja sudah ada atau role tidak ditemukan');
        }
    }
}



































