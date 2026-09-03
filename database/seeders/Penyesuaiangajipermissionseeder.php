<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Penyesuaiangajipermissionseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Penyesuaian Gaji']);

        Permission::firstOrCreate(['name' => 'penyesuaiangaji.index'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'penyesuaiangaji.create'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'penyesuaiangaji.edit'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'penyesuaiangaji.show'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'penyesuaiangaji.delete'], ['id_permission_group' => $permissiongroup->id]);

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
