<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    /**
     * Display payroll list.
     */
    public function index(Request $request)
    {
        $query = Payroll::with('employee.department')->latest('pay_period_end');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        $payrolls = $query->paginate(12)->withQueryString();
        $employees = Employee::where('employment_status', 'Regular')->orWhere('employment_status', 'Probationary')->get();

        return view('payroll.index', compact('payrolls', 'employees'));
    }

    /**
     * Store new payroll record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'pay_period_start' => ['required', 'date'],
            'pay_period_end' => ['required', 'date', 'after_or_equal:pay_period_start'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'overtime' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Draft', 'Processed', 'Paid', 'Cancelled'])],
        ]);

        $basic = (float) $validated['basic_salary'];
        $allowances = (float) ($validated['allowances'] ?? 0);
        $overtime = (float) ($validated['overtime'] ?? 0);
        $deductions = (float) ($validated['deductions'] ?? 0);

        // Net Salary = Basic Salary + Allowances + Overtime - Deductions
        $validated['net_salary'] = Payroll::calculateNetSalary($basic, $allowances, $overtime, $deductions);

        $payroll = Payroll::create($validated);
        $employee = Employee::findOrFail($payroll->employee_id);

        AuditLog::log('Generated employee payroll', 'Payroll Management', $payroll->id, null, $payroll->toArray());

        return back()->with('success', "Payroll generated for employee '{$employee->full_name}'. Net Salary: ₱" . number_format($payroll->net_salary, 2));
    }

    /**
     * Display printable payslip.
     */
    public function payslip(Payroll $payroll)
    {
        $payroll->load('employee.department');
        return view('payroll.payslip', compact('payroll'));
    }
}
