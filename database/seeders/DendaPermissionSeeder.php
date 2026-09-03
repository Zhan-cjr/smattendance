<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DendaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(
            ['name' => 'General Settings']
        );

        Permission::firstOrCreate(
            ['name' => 'denda.index'],
            ['id_permission_group' => $permissiongroup->id]
        );

        Permission::firstOrCreate(
            ['name' => 'denda.create'],
            ['id_permission_group' => $permissiongroup->id]
        );

        Permission::firstOrCreate(
            ['name' => 'denda.edit'],
            ['id_permission_group' => $permissiongroup->id]
        );

        Permission::firstOrCreate(
            ['name' => 'denda.delete'],
            ['id_permission_group' => $permissiongroup->id]
        );

        $permissions = Permission::whereIn('name', [
            'denda.index', 
            'denda.create', 
            'denda.edit', 
            'denda.delete'
        ])->get();

        // Assign to Super Admin (ID 1)
        $roleID = 1;
        $role = Role::findById($roleID);
        foreach ($permissions as $permission) {
             if ($role && !$role->hasPermissionTo($permission)) {
                 $role->givePermissionTo($permission);
             }
        }
        
        // Also assign to Admin role if it exists
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($permissions as $permission) {
                 if (!$adminRole->hasPermissionTo($permission)) {
                     $adminRole->givePermissionTo($permission);
                 }
            }
        }
    }
}
