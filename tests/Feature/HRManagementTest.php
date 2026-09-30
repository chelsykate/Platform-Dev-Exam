<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HRManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_departments_and_employees()
    {
        $role = Role::create(['name' => 'HR / Employee', 'slug' => 'hr']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $dept = Department::create([
            'department_name' => 'Agronomy',
            'description' => 'Field staff',
        ]);

        $emp = Employee::create([
            'employee_code' => 'EMP-0001',
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'department_id' => $dept->id,
            'position' => 'Agronomist',
            'contact_number' => '09170001111',
            'address' => 'Guihing',
            'date_hired' => now(),
            'salary' => 30000,
            'employment_status' => 'Regular',
        ]);

        $response = $this->actingAs($user)->get('/employees');

        $response->assertStatus(200);
        $response->assertSee('EMP-0001');
        $response->assertSee('Juan Cruz');
    }

    public function test_payroll_generation_net_salary_calculation_and_payslip()
    {
        $role = Role::create(['name' => 'HR / Employee', 'slug' => 'hr']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $dept = Department::create(['department_name' => 'Operations']);
        $emp = Employee::create([
            'employee_code' => 'EMP-0002',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'department_id' => $dept->id,
            'position' => 'Inspector',
            'contact_number' => '09180002222',
            'address' => 'Hagonoy',
            'date_hired' => now(),
            'salary' => 20000,
            'employment_status' => 'Regular',
        ]);

        // Basic 20000 + Allowances 2000 + Overtime 1000 - Deductions 3000 = Net 20000
        $response = $this->actingAs($user)->post('/payroll', [
            'employee_id' => $emp->id,
            'pay_period_start' => '2026-09-01',
            'pay_period_end' => '2026-09-30',
            'basic_salary' => 20000,
            'allowances' => 2000,
            'overtime' => 1000,
            'deductions' => 3000,
            'status' => 'Processed',
        ]);

        $response->assertSessionHas('success');
        $payroll = Payroll::where('employee_id', $emp->id)->first();
        $this->assertNotNull($payroll);
        $this->assertEquals(20000, $payroll->net_salary);

        // Payslip view test
        $payslipResponse = $this->actingAs($user)->get("/payslips/{$payroll->id}");
        $payslipResponse->assertStatus(200);
        $payslipResponse->assertSee('Official Payslip');
        $payslipResponse->assertSee('Maria Santos');
    }
}
