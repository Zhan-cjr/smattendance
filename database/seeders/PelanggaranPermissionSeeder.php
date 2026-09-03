<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PelanggaranPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(
            ['name' => 'Pelanggaran']
        );

        Permission::firstOrCreate(
            ['name' => 'pelanggaran.index'],
            ['id_permission_group' => $permissiongroup->id]
        );

        Permission::firstOrCreate(
            ['name' => 'pelanggaran.create'],
            ['id_permission_group' => $permissiongroup->id]
        );

        Permission::firstOrCreate(
            ['name' => 'pelanggaran.edit'],
            ['id_permission_group' => $permissiongroup->id]
        );

        Permission::firstOrCreate(
            ['name' => 'pelanggaran.delete'],
            ['id_permission_group' => $permissiongroup->id]
        );

        $permissions = Permission::where('id_permission_group', $permissiongroup->id)->get();
        $roleID = 1;
        $role = Role::findById($roleID);
        foreach ($permissions as $permission) {
             if ($role && !$role->hasPermissionTo($permission)) {
                 $role->givePermissionTo($permission);
             }
        }

        // Assign only index and create permissions to karyawan role
        $karyawanRole = Role::where('name', 'karyawan')->first();
        if ($karyawanRole) {
            $kams = ['pelanggaran.index', 'pelanggaran.create'];
            foreach($kams as $pname) {
                if (!$karyawanRole->hasPermissionTo($pname)) {
                   $karyawanRole->givePermissionTo($pname);
                }
            }
        }
    }
}
