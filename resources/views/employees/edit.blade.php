@extends('layouts.app')

@section('title', 'Edit Employee Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Edit Employee Profile</h2>
            <p class="text-xs text-gray-500 mt-1">Code: <span class="font-mono font-bold text-emerald-800">{{ $employee->employee_code }}</span></p>
        </div>
        <a href="{{ route('employees.show', $employee) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 px-3 py-2 rounded-lg transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Profile
        </a>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $employee->middle_name) }}" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Department *</label>
                    <select name="department_id" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Position / Job Title *</label>
                    <input type="text" name="position" value="{{ old('position', $employee->position) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Contact Number *</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $employee->contact_number) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Date Hired *</label>
                    <input type="date" name="date_hired" value="{{ old('date_hired', $employee->date_hired ? $employee->date_hired->format('Y-m-d') : '') }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Home Address *</label>
                <input type="text" name="address" value="{{ old('address', $employee->address) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Monthly Basic Salary (₱) *</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary', $employee->salary) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Employment Status *</label>
                    <select name="employment_status" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                        <option value="Regular" {{ old('employment_status', $employee->employment_status) == 'Regular' ? 'selected' : '' }}>Regular</option>
                        <option value="Probationary" {{ old('employment_status', $employee->employment_status) == 'Probationary' ? 'selected' : '' }}>Probationary</option>
                        <option value="Contractual" {{ old('employment_status', $employee->employment_status) == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                        <option value="Resigned" {{ old('employment_status', $employee->employment_status) == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="Terminated" {{ old('employment_status', $employee->employment_status) == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <a href="{{ route('employees.show', $employee) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">Cancel</a>
                <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-lg shadow transition">
                    Update Employee Details
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
