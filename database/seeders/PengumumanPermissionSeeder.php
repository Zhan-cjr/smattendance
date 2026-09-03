<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PengumumanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Permission Group
        $permissiongroup = Permission_group::firstOrCreate(
            ['name' => 'Pengumuman']
        );

        // 2. Create Permissions linked to Group
        $permissionsList = [
            'pengumuman.index',
            'pengumuman.create',
            'pengumuman.store',
            'pengumuman.delete'
        ];

        foreach ($permissionsList as $permName) {
            Permission::firstOrCreate(
                ['name' => $permName],
                ['id_permission_group' => $permissiongroup->id]
            );
        }

        // 3. Assign Permissions to Role (Super Admin)
        $role_super_admin = Role::findByName('super admin');
        if ($role_super_admin) {
            foreach ($permissionsList as $permName) {
                if (!$role_super_admin->hasPermissionTo($permName)) {
                    $role_super_admin->givePermissionTo($permName);
                }
            }
        }
    }
}
