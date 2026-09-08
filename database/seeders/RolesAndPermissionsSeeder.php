<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'doctor']);
        Role::firstOrCreate(['name' => 'receptionist']);

        Permission::firstOrCreate(['name' => 'manage patients']);
        Permission::firstOrCreate(['name' => 'manage doctors']);
        Permission::firstOrCreate(['name' => 'manage appointments']);
        Permission::firstOrCreate(['name' => 'manage bills']);
    }
}
