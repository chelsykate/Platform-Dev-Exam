<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display departments listing.
     */
    public function index()
    {
        $departments = Department::withCount('employees')->latest()->get();
        return view('departments.index', compact('departments'));
    }

    /**
     * Store department.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_name' => ['required', 'string', 'max:255', 'unique:departments'],
            'description' => ['nullable', 'string'],
        ]);

        $dept = Department::create($validated);

        AuditLog::log('Created department', 'HR Management', $dept->id, null, $dept->toArray());

        return back()->with('success', "Department '{$dept->department_name}' created successfully.");
    }

    /**
     * Update department.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'department_name' => ['required', 'string', 'max:255', 'unique:departments,department_name,' . $department->id],
            'description' => ['nullable', 'string'],
        ]);

        $old = $department->toArray();
        $department->update($validated);

        AuditLog::log('Updated department', 'HR Management', $department->id, $old, $department->toArray());

        return back()->with('success', "Department '{$department->department_name}' updated successfully.");
    }

    /**
     * Delete department.
     */
    public function destroy(Department $department)
    {
        $name = $department->department_name;
        $department->delete();

        AuditLog::log('Deleted department', 'HR Management', $department->id, ['name' => $name], null);

        return back()->with('success', "Department '{$name}' deleted.");
    }
}
