<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = Employee::with('department')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->input('employment_status'));
        }

        $employees = $query->paginate(12)->withQueryString();
        $departments = Department::all();

        return view('employees.index', compact('employees', 'departments'));
    }

    /**
     * Show form to create employee.
     */
    public function create()
    {
        $departments = Department::all();
        return view('employees.create', compact('departments'));
    }

    /**
     * Store new employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'position' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'date_hired' => ['required', 'date'],
            'salary' => ['required', 'numeric', 'min:0'],
            'employment_status' => ['required', Rule::in(['Regular', 'Probationary', 'Contractual', 'Resigned', 'Terminated'])],
        ]);

        $count = Employee::count() + 1;
        $validated['employee_code'] = 'EMP-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $employee = Employee::create($validated);

        AuditLog::log('Registered new employee', 'Employee Management', $employee->id, null, $employee->toArray());

        return redirect()->route('employees.index')->with('success', "Employee '{$employee->full_name}' ({$employee->employee_code}) registered successfully.");
    }

    /**
     * Show employee profile.
     */
    public function show(Employee $employee)
    {
        $employee->load(['department', 'payrolls']);
        return view('employees.show', compact('employee'));
    }

    /**
     * Show edit employee form.
     */
    public function edit(Employee $employee)
    {
        $departments = Department::all();
        return view('employees.edit', compact('employee', 'departments'));
    }

    /**
     * Update employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'position' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'date_hired' => ['required', 'date'],
            'salary' => ['required', 'numeric', 'min:0'],
            'employment_status' => ['required', Rule::in(['Regular', 'Probationary', 'Contractual', 'Resigned', 'Terminated'])],
        ]);

        $old = $employee->toArray();
        $employee->update($validated);

        AuditLog::log('Updated employee profile', 'Employee Management', $employee->id, $old, $employee->toArray());

        return redirect()->route('employees.index')->with('success', "Employee '{$employee->full_name}' updated successfully.");
    }

    /**
     * Delete employee.
     */
    public function destroy(Employee $employee)
    {
        $name = $employee->full_name;
        $employee->delete();

        AuditLog::log('Deleted employee profile', 'Employee Management', $employee->id, ['name' => $name], null);

        return redirect()->route('employees.index')->with('success', "Employee '{$name}' deleted.");
    }
}
