<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Seeder;

class HrPayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['department_name' => 'Mill Operations', 'description' => 'Sugar milling and production teams.'],
            ['department_name' => 'Administration', 'description' => 'Administrative support and office coordination.'],
            ['department_name' => 'Warehouse', 'description' => 'Inventory control and fertilizer release.'],
            ['department_name' => 'Sales', 'description' => 'Customer and order servicing.'],
        ];

        foreach ($departments as $departmentData) {
            Department::firstOrCreate(['department_name' => $departmentData['department_name']], $departmentData);
        }

        $mill = Department::where('department_name', 'Mill Operations')->first();
        $admin = Department::where('department_name', 'Administration')->first();
        $warehouse = Department::where('department_name', 'Warehouse')->first();
        $sales = Department::where('department_name', 'Sales')->first();

        $employees = [
            ['employee_code' => 'EMP-1001', 'first_name' => 'Maria', 'middle_name' => 'L', 'last_name' => 'Santos', 'department_id' => $mill->id, 'position' => 'Field Supervisor', 'contact_number' => '09171234567', 'address' => 'Guihing, Hagonoy', 'date_hired' => '2024-01-10', 'salary' => 26000, 'employment_status' => 'Regular'],
            ['employee_code' => 'EMP-1002', 'first_name' => 'Renato', 'middle_name' => 'A', 'last_name' => 'Dela Cruz', 'department_id' => $admin->id, 'position' => 'HR Officer', 'contact_number' => '09182345678', 'address' => 'Digos City', 'date_hired' => '2023-06-01', 'salary' => 23000, 'employment_status' => 'Regular'],
            ['employee_code' => 'EMP-1003', 'first_name' => 'Cecilia', 'middle_name' => 'B', 'last_name' => 'Gomez', 'department_id' => $warehouse->id, 'position' => 'Warehouse Clerk', 'contact_number' => '09193456789', 'address' => 'Hagonoy', 'date_hired' => '2022-09-18', 'salary' => 21000, 'employment_status' => 'Regular'],
            ['employee_code' => 'EMP-1004', 'first_name' => 'Jerome', 'middle_name' => 'P', 'last_name' => 'Manuel', 'department_id' => $sales->id, 'position' => 'Sales Specialist', 'contact_number' => '09204567890', 'address' => 'Padada', 'date_hired' => '2024-03-15', 'salary' => 24000, 'employment_status' => 'Regular'],
        ];

        foreach ($employees as $employeeData) {
            $employee = Employee::firstOrCreate(['employee_code' => $employeeData['employee_code']], $employeeData);

            Payroll::firstOrCreate(
                ['employee_id' => $employee->id, 'pay_period_start' => '2026-09-01', 'pay_period_end' => '2026-09-30'],
                [
                    'basic_salary' => $employee->salary,
                    'allowances' => 1200,
                    'overtime' => 800,
                    'deductions' => 1000,
                    'net_salary' => Payroll::calculateNetSalary($employee->salary, 1200, 800, 1000),
                    'status' => 'Approved',
                ]
            );
        }
    }
}
