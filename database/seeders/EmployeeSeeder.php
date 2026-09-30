<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Departments
        $d1 = Department::create(['department_name' => 'Executive & Management', 'description' => 'Plant executive administration & general management.']);
        $d2 = Department::create(['department_name' => 'Agronomy & Field Operations', 'description' => 'Sugarcane cultivation, farmer relations, and fertilizer distribution.']);
        $d3 = Department::create(['department_name' => 'Sugar Milling & Processing', 'description' => 'Raw & refined sugar, molasses, and bagasse production operations.']);
        $d4 = Department::create(['department_name' => 'Warehouse & Supply Chain', 'description' => 'Inventory control, stock management, and fertilizer storage.']);
        $d5 = Department::create(['department_name' => 'Sales, Logistics & Export', 'description' => 'Sugar sales orders, domestic trade, import/export shipments.']);
        $d6 = Department::create(['department_name' => 'Finance & Accounting', 'description' => 'Financial reporting, payroll, and audit compliance.']);

        // 2. Create Employees
        $sampleEmps = [
            ['first_name' => 'Roberto', 'last_name' => 'Valdez', 'dept' => $d1->id, 'pos' => 'General Plant Manager', 'salary' => 85000],
            ['first_name' => 'Carlos', 'last_name' => 'Mendoza', 'dept' => $d2->id, 'pos' => 'Chief Agronomist', 'salary' => 45000],
            ['first_name' => 'Lilia', 'last_name' => 'Santos', 'dept' => $d2->id, 'pos' => 'Field Assistance Inspector', 'salary' => 28000],
            ['first_name' => 'Eduardo', 'last_name' => 'Torres', 'dept' => $d3->id, 'pos' => 'Mill Operations Engineer', 'salary' => 52000],
            ['first_name' => 'Grace', 'last_name' => 'Villanueva', 'dept' => $d4->id, 'pos' => 'Head Warehouse Manager', 'salary' => 38000],
            ['first_name' => 'Ramon', 'last_name' => 'Castillo', 'dept' => $d4->id, 'pos' => 'Fertilizer Release Officer', 'salary' => 24000],
            ['first_name' => 'Ana', 'last_name' => 'Fernandez', 'dept' => $d5->id, 'pos' => 'Sales & Trade Lead', 'salary' => 42000],
            ['first_name' => 'David', 'last_name' => 'Alcantara', 'dept' => $d6->id, 'pos' => 'Finance Controller', 'salary' => 48000],
        ];

        foreach ($sampleEmps as $idx => $se) {
            $empCode = 'EMP-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
            $emp = Employee::create([
                'employee_code' => $empCode,
                'first_name' => $se['first_name'],
                'last_name' => $se['last_name'],
                'department_id' => $se['dept'],
                'position' => $se['pos'],
                'contact_number' => '0917-555-' . str_pad($idx + 10, 4, '0', STR_PAD_LEFT),
                'address' => 'Brgy. Guihing, Hagonoy, Davao del Sur',
                'date_hired' => date('Y-m-d', strtotime("-".(1 + $idx)." years")),
                'salary' => $se['salary'],
                'employment_status' => 'Regular',
            ]);

            // Create Sample Payroll Entries
            $allowances = 2500;
            $overtime = ($idx % 2 === 0) ? 1800 : 0;
            $deductions = round($se['salary'] * 0.10, 2);
            $netSalary = Payroll::calculateNetSalary($se['salary'], $allowances, $overtime, $deductions);

            Payroll::create([
                'employee_id' => $emp->id,
                'pay_period_start' => date('Y-m-01'),
                'pay_period_end' => date('Y-m-t'),
                'basic_salary' => $se['salary'],
                'allowances' => $allowances,
                'overtime' => $overtime,
                'deductions' => $deductions,
                'net_salary' => $netSalary,
                'status' => 'Processed',
            ]);
        }
    }
}
