<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'System Administrator', 'slug' => 'admin', 'description' => 'Full administrative control across all modules.'],
            ['name' => 'Warehouse Staff', 'slug' => 'warehouse', 'description' => 'Manages inventory stock and releases fertilizer.'],
            ['name' => 'HR / Employee', 'slug' => 'hr', 'description' => 'Manages personnel, employee records, and payroll.'],
            ['name' => 'Sales and Distribution Staff', 'slug' => 'sales', 'description' => 'Handles customer sales orders and distribution.'],
            ['name' => 'Farmer', 'slug' => 'farmer', 'description' => 'Accesses farm records, production, and requests fertilizer.'],
            ['name' => 'Finance', 'slug' => 'finance', 'description' => 'Monitors financial transactions, order balances, and payslips.'],
            ['name' => 'Management', 'slug' => 'management', 'description' => 'Accesses high-level analytics, reports, and operational trends.'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['slug' => $r['slug']], $r);
        }

        // Create Default Users with Roles
        $adminRole = Role::where('slug', 'admin')->first();
        $warehouseRole = Role::where('slug', 'warehouse')->first();
        $hrRole = Role::where('slug', 'hr')->first();
        $salesRole = Role::where('slug', 'sales')->first();
        $farmerRole = Role::where('slug', 'farmer')->first();
        $financeRole = Role::where('slug', 'finance')->first();
        $managementRole = Role::where('slug', 'management')->first();

        User::updateOrCreate(
            ['email' => 'admin@davaosugar.com'],
            ['name' => 'System Administrator', 'password' => Hash::make('password'), 'role_id' => $adminRole->id, 'status' => 'Active']
        );

        User::updateOrCreate(
            ['email' => 'warehouse@davaosugar.com'],
            ['name' => 'Warehouse Staff', 'password' => Hash::make('password'), 'role_id' => $warehouseRole->id, 'status' => 'Active']
        );

        User::updateOrCreate(
            ['email' => 'hr@davaosugar.com'],
            ['name' => 'HR Manager', 'password' => Hash::make('password'), 'role_id' => $hrRole->id, 'status' => 'Active']
        );

        User::updateOrCreate(
            ['email' => 'sales@davaosugar.com'],
            ['name' => 'Sales Lead', 'password' => Hash::make('password'), 'role_id' => $salesRole->id, 'status' => 'Active']
        );

        User::updateOrCreate(
            ['email' => 'farmer@davaosugar.com'],
            ['name' => 'Juan Farmer', 'password' => Hash::make('password'), 'role_id' => $farmerRole->id, 'status' => 'Active']
        );

        User::updateOrCreate(
            ['email' => 'finance@davaosugar.com'],
            ['name' => 'Finance Controller', 'password' => Hash::make('password'), 'role_id' => $financeRole->id, 'status' => 'Active']
        );

        User::updateOrCreate(
            ['email' => 'management@davaosugar.com'],
            ['name' => 'Plant General Manager', 'password' => Hash::make('password'), 'role_id' => $managementRole->id, 'status' => 'Active']
        );
    }
}
