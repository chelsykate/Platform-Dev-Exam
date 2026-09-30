<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_can_manage_employee_and_payroll_records(): void
    {
        $role = Role::create(['name' => 'HR / Employee', 'slug' => 'hr', 'description' => 'Human resources management']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $department = Department::create([
            'department_name' => 'Mill Operations',
            'description' => 'Sugar milling operations',
        ]);

        $response = $this->actingAs($user)->get('/employees');
        $response->assertStatus(200);

        $createEmployeeResponse = $this->actingAs($user)->post('/employees', [
            'employee_code' => 'EMP-1001',
            'first_name' => 'Maria',
            'middle_name' => 'L.',
            'last_name' => 'Santos',
            'department_id' => $department->id,
            'position' => 'Field Supervisor',
            'contact_number' => '09171234567',
            'address' => 'Guihing, Hagonoy',
            'date_hired' => '2024-01-10',
            'salary' => 25000,
            'employment_status' => 'Regular',
        ]);

        $createEmployeeResponse->assertRedirect('/employees');
        $this->assertDatabaseHas('employees', ['employee_code' => 'EMP-0001']);

        $employee = Employee::where('employee_code', 'EMP-0001')->first();

        $payrollResponse = $this->actingAs($user)->from('/payroll')->post('/payroll', [
            'employee_id' => $employee->id,
            'pay_period_start' => '2026-09-01',
            'pay_period_end' => '2026-09-30',
            'basic_salary' => 25000,
            'allowances' => 1500,
            'overtime' => 750,
            'deductions' => 1200,
            'status' => 'Paid',
        ]);

        $payrollResponse->assertRedirect('/payroll');
        $this->assertDatabaseHas('payroll', ['employee_id' => $employee->id]);
        $this->assertDatabaseHas('payroll', ['net_salary' => 26050]);

        $payslipResponse = $this->actingAs($user)->get('/payslips/' . Payroll::first()->id);
        $payslipResponse->assertStatus(200);
    }
}
