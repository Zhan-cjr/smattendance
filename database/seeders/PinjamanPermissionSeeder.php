<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PinjamanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(
            ['name' => 'Pinjaman Karyawan']
        );

        $permissionNames = [
            'pinjaman.index',
            'pinjaman.create',
            'pinjaman.edit',
            'pinjaman.delete',
            'pinjaman.generate',
            'pinjaman.pembayaran',
        ];

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['id_permission_group' => $permissiongroup->id]
            );
        }

        $permissions = Permission::whereIn('name', $permissionNames)->get();

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
