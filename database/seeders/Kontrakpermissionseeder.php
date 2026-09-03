<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Kontrakpermissionseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate([
            'name' => 'Kontrak'
        ], [
            'name' => 'Kontrak'
        ]);

        $permissions = [
            'kontrak.index',
            'kontrak.create',
            'kontrak.edit',
            'kontrak.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['id_permission_group' => $permissiongroup->id]
            );
        }

        $role = Role::findById(1);
        $permissionsList = Permission::whereIn('name', $permissions)->get();
        if ($role) {
            foreach($permissionsList as $p) {
                if (!$role->hasPermissionTo($p)) {
                    $role->givePermissionTo($p);
                }
            }
        }
    }
}
