<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        $managePatients = Permission::firstOrCreate(['name' => 'manage patients']);
        $manageDoctors = Permission::firstOrCreate(['name' => 'manage doctors']);
        $manageAppointments = Permission::firstOrCreate(['name' => 'manage appointments']);
        $manageBills = Permission::firstOrCreate(['name' => 'manage bills']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $doctor = Role::firstOrCreate(['name' => 'doctor']);
        $receptionist = Role::firstOrCreate(['name' => 'receptionist']);

        // Admin: full access to everything
        $admin->syncPermissions([
            $managePatients,
            $manageDoctors,
            $manageAppointments,
            $manageBills,
        ]);

        // Receptionist: can manage day-to-day records, but not doctors
        $receptionist->syncPermissions([
            $managePatients,
            $manageAppointments,
            $manageBills,
        ]);

        // Doctor: view-only role, no manage permissions assigned
    }
}