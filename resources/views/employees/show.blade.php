@extends('layouts.app')

@section('title', $employee->full_name . ' - Employee Profile')

@section('content')
<div class="space-y-6">
    
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 px-3 py-2 rounded-lg transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Employees
            </a>
            <span class="text-xs font-mono bg-emerald-100 text-emerald-800 font-bold px-2.5 py-1 rounded">
                {{ $employee->employee_code }}
            </span>
        </div>
        <a href="{{ route('employees.edit', $employee) }}" class="text-xs font-semibold bg-emerald-700 hover:bg-emerald-800 text-white px-3.5 py-2 rounded-lg shadow transition">
            <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Profile
        </a>
    </div>

    <!-- Profile Header Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-emerald-800 text-white font-bold text-xl flex items-center justify-center shadow">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $employee->full_name }}</h2>
                    <p class="text-xs text-gray-500">{{ $employee->position }} | <strong class="text-emerald-800">{{ $employee->department->department_name ?? 'N/A' }}</strong></p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-3 border-t border-gray-100 text-xs">
                <div>
                    <span class="text-gray-400 block font-medium">Contact Number</span>
                    <span class="font-semibold text-gray-800">{{ $employee->contact_number }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block font-medium">Date Hired</span>
                    <span class="font-semibold text-gray-800">{{ $employee->date_hired ? $employee->date_hired->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block font-medium">Employment Status</span>
                    <span class="inline-block px-2 py-0.5 rounded font-bold text-[11px] bg-emerald-100 text-emerald-800">
                        {{ $employee->employment_status }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-emerald-950 text-white p-4 rounded-xl flex flex-col justify-between shadow-inner">
            <span class="text-xs text-emerald-300 font-semibold uppercase tracking-wider">Salary Compensation</span>
            <div class="my-2">
                <p class="text-3xl font-extrabold text-white">₱{{ number_format($employee->salary, 2) }}</p>
                <p class="text-xs text-emerald-300">Monthly Basic Salary</p>
            </div>
            <div class="text-[11px] text-emerald-200 border-t border-emerald-800/80 pt-2 flex justify-between">
                <span>Address:</span>
                <span class="truncate ml-2 text-white">{{ $employee->address }}</span>
            </div>
        </div>
    </div>

    <!-- Employee Payroll History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <h3 class="font-bold text-gray-900 text-base border-b border-gray-100 pb-3"><i class="fa-solid fa-receipt text-emerald-700 mr-2"></i> Employee Payroll History & Payslips</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-2.5 px-3">Pay Period</th>
                        <th class="py-2.5 px-3">Basic Salary</th>
                        <th class="py-2.5 px-3">Allowances</th>
                        <th class="py-2.5 px-3">Overtime</th>
                        <th class="py-2.5 px-3">Deductions</th>
                        <th class="py-2.5 px-3">Net Salary</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employee->payrolls as $pay)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2.5 px-3 font-bold text-gray-900">
                                {{ $pay->pay_period_start ? $pay->pay_period_start->format('M d') : '' }} - {{ $pay->pay_period_end ? $pay->pay_period_end->format('M d, Y') : '' }}
                            </td>
                            <td class="py-2.5 px-3 font-medium text-gray-700">₱{{ number_format($pay->basic_salary, 2) }}</td>
                            <td class="py-2.5 px-3 text-emerald-700">+₱{{ number_format($pay->allowances, 2) }}</td>
                            <td class="py-2.5 px-3 text-emerald-700">+₱{{ number_format($pay->overtime, 2) }}</td>
                            <td class="py-2.5 px-3 text-red-600">-₱{{ number_format($pay->deductions, 2) }}</td>
                            <td class="py-2.5 px-3 font-extrabold text-emerald-800">₱{{ number_format($pay->net_salary, 2) }}</td>
                            <td class="py-2.5 px-3 font-bold text-[10px] text-emerald-700">{{ $pay->status }}</td>
                            <td class="py-2.5 px-3 text-right">
                                <a href="{{ route('payroll.payslip', $pay) }}" target="_blank" class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white rounded font-semibold text-[11px] transition">
                                    <i class="fa-solid fa-print mr-1"></i> Print Payslip
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-gray-500 italic">No payroll records logged for this employee.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
