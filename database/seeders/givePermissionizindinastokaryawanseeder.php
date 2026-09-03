<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class givePermissionizindinastokaryawanseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'karyawan')->first();
        if ($role) {
             if (!$role->hasPermissionTo('izindinas.create')) {
                $role->givePermissionTo('izindinas.create');
             }
             if (!$role->hasPermissionTo('izindinas.delete')) {
                $role->givePermissionTo('izindinas.delete');
             }
        }
    }
}
