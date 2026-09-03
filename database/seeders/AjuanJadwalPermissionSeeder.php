<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Permission_group;

class AjuanJadwalPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Permission Group
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Ajuan Jadwal']);

        // 2. Define Permissions
        $permissions = [
            'ajuanjadwal.index',   // View List
            'ajuanjadwal.create',  // Create Request
            'ajuanjadwal.edit',    // Edit Request
            'ajuanjadwal.delete',  // Delete Request
            'ajuanjadwal.approve', // Approve Request (HRD)
        ];

        // 3. Create Permissions if they don't exist
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName, 
                'id_permission_group' => $permissiongroup->id
            ]);
        }

        // 4. Assign Permissions to Roles
        
        // Role: Karyawan (Can create, view own, edit own, delete own)
        $roleKaryawan = Role::where('name', 'karyawan')->first();
        if ($roleKaryawan) {
            $permissionsKaryawan = [
                'ajuanjadwal.index',
                'ajuanjadwal.create',
                'ajuanjadwal.edit',
                'ajuanjadwal.delete',
            ];
            foreach ($permissionsKaryawan as $permission) {
                if (!$roleKaryawan->hasPermissionTo($permission)) {
                    $roleKaryawan->givePermissionTo($permission);
                }
            }
        }

        // Role: HRD / Super Admin (Can view all, approve)
        // Adjust role name as per your system 'hrd' or 'admin' or just 'super admin'
        // Assuming 'hrd' exists, if not code will skip.
        $roleHRD = Role::where('name', 'hrd')->first(); 
        if ($roleHRD) {
             $permissionsHRD = [
                'ajuanjadwal.index',
                'ajuanjadwal.approve',
            ];
            foreach ($permissionsHRD as $permission) {
                if (!$roleHRD->hasPermissionTo($permission)) {
                    $roleHRD->givePermissionTo($permission);
                }
            }
        }


        // Role: Super Admin (All Permissions)
        $roleSuperAdmin = Role::where('name', 'super admin')->first();
        if ($roleSuperAdmin) {
            foreach ($permissions as $permission) {
               $roleSuperAdmin->givePermissionTo($permission);
            }
        }
    }
}
