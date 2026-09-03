<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Presensipermissionseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Presensi']);

        // Permission::firstOrCreate(['name' => 'presensi.index'], ['id_permission_group' => $permissiongroup->id]);

        Permission::firstOrCreate(['name' => 'presensi.create'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'presensi.edit'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'presensi.delete'], ['id_permission_group' => $permissiongroup->id]);

        $permissions = Permission::where('id_permission_group', $permissiongroup->id)->get();
        
        $roleID = 1;
        $role = Role::findById($roleID);
        foreach ($permissions as $p) {
             if ($role && !$role->hasPermissionTo($p)) $role->givePermissionTo($p);
        }

        $roleKaryawan = 3;
        $rolekar = Role::findById($roleKaryawan);
        foreach ($permissions as $p) {
             if ($rolekar && !$rolekar->hasPermissionTo($p)) $rolekar->givePermissionTo($p);
        }
    }
}
