<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionKpiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'KPI']);

        // KPI Periods
        Permission::firstOrCreate(['name' => 'kpi.period.index', 'id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'kpi.period.create', 'id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'kpi.period.edit', 'id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'kpi.period.delete', 'id_permission_group' => $permissiongroup->id]);

        // KPI Indicators
        Permission::firstOrCreate(['name' => 'kpi.indicator.index', 'id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'kpi.indicator.create', 'id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'kpi.indicator.edit', 'id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'kpi.indicator.delete', 'id_permission_group' => $permissiongroup->id]);
        

        // KPI Transactions
        Permission::firstOrCreate(['name' => 'kpi.employee.index', 'id_permission_group' => $permissiongroup->id]); // View List
        Permission::firstOrCreate(['name' => 'kpi.transaction.create', 'id_permission_group' => $permissiongroup->id]); // Set Target
        Permission::firstOrCreate(['name' => 'kpi.transaction.update', 'id_permission_group' => $permissiongroup->id]); // Input Realization
        Permission::firstOrCreate(['name' => 'kpi.transaction.approve', 'id_permission_group' => $permissiongroup->id]); // Approve

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
