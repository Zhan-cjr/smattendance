<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GrupDetailPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari permission group Grup
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Grup']);

        // Pastikan permission grup.detail ada
        $permission = Permission::firstOrCreate([
            'name' => 'grup.detail'
        ], [
            'id_permission_group' => $permissiongroup->id
        ]);

        // Berikan permission ke role admin (ID 1)
        $roleID = 1;
        $role = Role::findById($roleID);
        if ($role && !$role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
        }
    }
}
