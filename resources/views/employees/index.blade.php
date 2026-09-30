@extends('layouts.app')

@section('title', 'Employees Directory')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Employees Directory</h2>
            <p class="text-xs text-gray-500 mt-1">Davao Sugar Central Co., Inc. active personnel & staff roster.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('departments.index') }}" class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-xs px-3.5 py-2.5 rounded-lg border border-gray-300 transition">
                <i class="fa-solid fa-sitemap"></i>
                <span>Manage Departments</span>
            </a>
            <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
                <i class="fa-solid fa-user-plus"></i>
                <span>Add Employee</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('employees.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search Employee</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Code, Name, Position..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Department</label>
                <select name="department_id" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="employment_status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Statuses</option>
                    <option value="Regular" {{ request('employment_status') == 'Regular' ? 'selected' : '' }}>Regular</option>
                    <option value="Probationary" {{ request('employment_status') == 'Probationary' ? 'selected' : '' }}>Probationary</option>
                    <option value="Contractual" {{ request('employment_status') == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                    <option value="Resigned" {{ request('employment_status') == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                    <option value="Terminated" {{ request('employment_status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('employees.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Code</th>
                        <th class="py-3 px-4">Employee Name</th>
                        <th class="py-3 px-4">Department</th>
                        <th class="py-3 px-4">Position</th>
                        <th class="py-3 px-4">Contact</th>
                        <th class="py-3 px-4">Date Hired</th>
                        <th class="py-3 px-4">Basic Salary</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-800">{{ $emp->employee_code }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900 font-medium">
                                <a href="{{ route('employees.show', $emp) }}" class="hover:text-emerald-700 hover:underline">
                                    {{ $emp->full_name }}
                                </a>
                            </td>
                            <td class="py-3 px-4 font-semibold text-gray-700">{{ $emp->department->department_name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $emp->position }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $emp->contact_number }}</td>
                            <td class="py-3 px-4 text-gray-500">{{ $emp->date_hired ? $emp->date_hired->format('M d, Y') : 'N/A' }}</td>
                            <td class="py-3 px-4 font-bold text-emerald-800">₱{{ number_format($emp->salary, 2) }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded font-bold text-[10px] {{ $emp->employment_status === 'Regular' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $emp->employment_status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <a href="{{ route('employees.show', $emp) }}" class="p-1.5 text-gray-600 hover:text-emerald-700 transition" title="View Profile">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('employees.edit', $emp) }}" class="p-1.5 text-gray-600 hover:text-blue-700 transition" title="Edit Employee">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <form method="POST" action="{{ route('employees.destroy', $emp) }}" class="inline-block" onsubmit="return confirm('Delete employee record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-600 hover:text-red-700 transition" title="Delete">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-500 italic">No employee records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
