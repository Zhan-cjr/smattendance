<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LaporanCutiPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Laporan']);

        $permission = Permission::firstOrCreate(
            ['name' => 'laporan.cuti'], 
            ['id_permission_group' => $permissiongroup->id]
        );

        $roleID = 1;
        $role = Role::findById($roleID);

        if ($role) {
            if (!$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
                $this->command->info('Permission laporan.cuti successfully added to role ID 1.');
            } else {
                //$this->command->info('Permission laporan.cuti already exists for role ID 1.');
            }
        }
    }
}
