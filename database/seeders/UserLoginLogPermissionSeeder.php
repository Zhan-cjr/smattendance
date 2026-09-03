<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserLoginLogPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Log Login User']);

        Permission::firstOrCreate(['name' => 'userloginlog.index'], ['id_permission_group' => $permissiongroup->id]);

        $permissions = Permission::where('id_permission_group', $permissiongroup->id)->get();
        
        // Find Super Admin role by name or ID (assuming 1 is Super Admin as seen in other seeders)
        $role = Role::where('name', 'super admin')->orWhere('id', 1)->first();
        
        if ($role) {
            foreach ($permissions as $permission) {
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}
