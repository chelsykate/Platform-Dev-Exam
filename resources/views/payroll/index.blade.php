@extends('layouts.app')

@section('title', 'Payroll & Payslips')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Payroll & Payslips Management</h2>
            <p class="text-xs text-gray-500 mt-1">Davao Sugar Central Co., Inc. employee compensation and Net Salary calculations.</p>
        </div>
        <button onclick="document.getElementById('generatePayrollModal').classList.toggle('hidden')" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-calculator"></i>
            <span>Generate Payroll</span>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('payroll.index') }}" class="flex gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Employee Code or Name..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">Search</button>
            <a href="{{ route('payroll.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
        </form>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Employee</th>
                        <th class="py-3 px-4">Department</th>
                        <th class="py-3 px-4">Pay Period</th>
                        <th class="py-3 px-4">Basic Salary</th>
                        <th class="py-3 px-4">Allowances</th>
                        <th class="py-3 px-4">Overtime</th>
                        <th class="py-3 px-4">Deductions</th>
                        <th class="py-3 px-4">Net Salary</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payrolls as $pay)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-bold text-gray-900">
                                {{ $pay->employee->full_name }}
                                <span class="block text-[10px] text-gray-400 font-mono font-normal">{{ $pay->employee->employee_code }}</span>
                            </td>
                            <td class="py-3 px-4 text-gray-600">{{ $pay->employee->department->department_name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 font-mono text-gray-600">
                                {{ $pay->pay_period_start ? $pay->pay_period_start->format('M d') : '' }} - {{ $pay->pay_period_end ? $pay->pay_period_end->format('M d, Y') : '' }}
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-700">₱{{ number_format($pay->basic_salary, 2) }}</td>
                            <td class="py-3 px-4 text-emerald-700">+₱{{ number_format($pay->allowances, 2) }}</td>
                            <td class="py-3 px-4 text-emerald-700">+₱{{ number_format($pay->overtime, 2) }}</td>
                            <td class="py-3 px-4 text-red-600">-₱{{ number_format($pay->deductions, 2) }}</td>
                            <td class="py-3 px-4 font-extrabold text-sm text-emerald-800">₱{{ number_format($pay->net_salary, 2) }}</td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('payroll.payslip', $pay) }}" target="_blank" class="px-2.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded font-bold text-[11px] transition inline-flex items-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-print"></i> Payslip
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-500 italic">No payroll records generated yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payrolls->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Generate Payroll -->
<div id="generatePayrollModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Generate Employee Payroll</h3>
            <button onclick="document.getElementById('generatePayrollModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('payroll.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Select Employee *</label>
                <select name="employee_id" id="empSelect" required onchange="updateBasicSalary()" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">-- Choose Employee --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}">{{ $emp->employee_code }} - {{ $emp->full_name }} (₱{{ number_format($emp->salary, 2) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pay Period Start *</label>
                    <input type="date" name="pay_period_start" value="{{ date('Y-m-01') }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pay Period End *</label>
                    <input type="date" name="pay_period_end" value="{{ date('Y-m-t') }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Basic Salary (₱) *</label>
                    <input type="number" step="0.01" name="basic_salary" id="basicSalaryInput" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="25000">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Allowances (₱)</label>
                    <input type="number" step="0.01" name="allowances" value="0" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Overtime Pay (₱)</label>
                    <input type="number" step="0.01" name="overtime" value="0" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Deductions (SSS/PhilHealth/Tax) (₱)</label>
                    <input type="number" step="0.01" name="deductions" value="0" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status *</label>
                <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="Processed">Processed</option>
                    <option value="Paid">Paid</option>
                    <option value="Draft">Draft</option>
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('generatePayrollModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Generate & Save Payroll</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateBasicSalary() {
        const select = document.getElementById('empSelect');
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.dataset.salary) {
            document.getElementById('basicSalaryInput').value = selectedOption.dataset.salary;
        }
    }
</script>
@endsection
