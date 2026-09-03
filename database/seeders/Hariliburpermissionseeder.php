<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Hariliburpermissionseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Haril Libur']);

        Permission::firstOrCreate(['name' => 'harilibur.index'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'harilibur.create'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'harilibur.edit'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'harilibur.show'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'harilibur.delete'], ['id_permission_group' => $permissiongroup->id]);

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
