<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Permission_group;

class KpiPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Permission Group exists
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'KPI']);

        // Define permissions
        $permissionsToAssign = [
            'kpi.employee.index',
            'kpi.transaction.update',
        ];

        // Ensure permissions exist
        foreach ($permissionsToAssign as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName, 
                'id_permission_group' => $permissiongroup->id
            ]);
        }

        // Find the 'karyawan' role
        $roleName = 'karyawan';
        $role = Role::where('name', $roleName)->first();

        if ($role) {
            foreach ($permissionsToAssign as $permissionName) {
                if (!$role->hasPermissionTo($permissionName)) {
                    $role->givePermissionTo($permissionName);
                    $this->command->info("Permission '{$permissionName}' assigned to role '{$roleName}'.");
                } else {
                    $this->command->info("Permission '{$permissionName}' already exists for role '{$roleName}'. Skipped.");
                }
            }
        } else {
            $this->command->error("Role '{$roleName}' not found.");
        }
    }
}
