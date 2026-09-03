<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Gruppermissionseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Grup']);

        Permission::firstOrCreate(['name' => 'grup.index'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'grup.create'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'grup.edit'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'grup.show'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'grup.delete'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'grup.detail'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'grup.setJamKerja'], ['id_permission_group' => $permissiongroup->id]);

        $permissions = Permission::where('id_permission_group', $permissiongroup->id)->get();
        $roleID = 1;
        $role = Role::findById($roleID);
        foreach ($permissions as $permission) {
             if ($role && !$role->hasPermissionTo($permission)) {
                 $role->givePermissionTo($permission);
             }
        }
    }
}
