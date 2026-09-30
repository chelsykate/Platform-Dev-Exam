<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee->full_name }} ({{ $payroll->payroll_code ?? 'PAY-'.$payroll->id }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .container-box { border: none !important; shadow: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans p-6">

    <!-- Print Action Bar -->
    <div class="max-w-3xl mx-auto mb-4 flex justify-between items-center no-print">
        <a href="{{ route('payroll.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-300 px-3 py-2 rounded-lg shadow-sm">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Payroll
        </a>
        <button onclick="window.print()" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs px-4 py-2 rounded-lg shadow flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Print Payslip Document
        </button>
    </div>

    <!-- Official Payslip Document -->
    <div class="max-w-3xl mx-auto bg-white border border-gray-300 rounded-2xl shadow-xl overflow-hidden container-box p-8 space-y-6">
        
        <!-- Company Header -->
        <div class="border-b-2 border-emerald-900 pb-4 flex justify-between items-start">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded bg-emerald-800 text-white flex items-center justify-center font-bold text-lg">
                        <i class="fa-solid fa-wheat-awn text-sm"></i>
                    </div>
                    <h1 class="text-xl font-bold text-emerald-950 uppercase tracking-tight">Davao Sugar Central Co., Inc.</h1>
                </div>
                <p class="text-xs text-gray-600 font-semibold">Pacific Sugar Holdings Corp. / Filinvest Development Corp.</p>
                <p class="text-[11px] text-gray-500">
                    <i class="fa-solid fa-location-dot text-emerald-700 mr-1"></i> Salutillo St., Brgy. Guihing, Hagonoy, Davao del Sur, Philippines
                </p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-emerald-900 text-white font-extrabold text-xs uppercase tracking-wider rounded-md">Official Payslip</span>
                <p class="text-xs text-gray-500 mt-2 font-mono">Ref: PAY-{{ str_pad($payroll->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="text-xs font-bold text-emerald-800 mt-0.5">Status: {{ strtoupper($payroll->status) }}</p>
            </div>
        </div>

        <!-- Employee Info Grid -->
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-gray-400 block font-medium">Employee Name</span>
                <strong class="text-gray-900 text-sm">{{ $payroll->employee->full_name }}</strong>
            </div>
            <div>
                <span class="text-gray-400 block font-medium">Employee Code</span>
                <strong class="text-emerald-800 font-mono">{{ $payroll->employee->employee_code }}</strong>
            </div>
            <div>
                <span class="text-gray-400 block font-medium">Department</span>
                <strong class="text-gray-800">{{ $payroll->employee->department->department_name ?? 'N/A' }}</strong>
            </div>
            <div>
                <span class="text-gray-400 block font-medium">Job Position</span>
                <strong class="text-gray-800">{{ $payroll->employee->position }}</strong>
            </div>
            <div class="sm:col-span-2 pt-2 border-t border-gray-200">
                <span class="text-gray-400 block font-medium">Pay Period</span>
                <strong class="text-gray-900">{{ $payroll->pay_period_start ? $payroll->pay_period_start->format('F d, Y') : '' }} – {{ $payroll->pay_period_end ? $payroll->pay_period_end->format('F d, Y') : '' }}</strong>
            </div>
            <div class="sm:col-span-2 pt-2 border-t border-gray-200">
                <span class="text-gray-400 block font-medium">Payment Date</span>
                <strong class="text-gray-900">{{ date('F d, Y') }}</strong>
            </div>
        </div>

        <!-- Earnings & Deductions Breakdown Tables -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            
            <!-- Earnings -->
            <div class="border border-emerald-200 rounded-xl overflow-hidden">
                <div class="bg-emerald-800 text-white px-4 py-2 font-bold text-xs uppercase tracking-wider">
                    Earnings Breakdown
                </div>
                <table class="w-full text-xs text-left divide-y divide-gray-100">
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-2.5 px-3 text-gray-700">Basic Monthly Salary</td>
                            <td class="py-2.5 px-3 text-right font-medium text-gray-900">₱{{ number_format($payroll->basic_salary, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-gray-700">Allowances</td>
                            <td class="py-2.5 px-3 text-right font-medium text-emerald-700">+₱{{ number_format($payroll->allowances, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-gray-700">Overtime Pay</td>
                            <td class="py-2.5 px-3 text-right font-medium text-emerald-700">+₱{{ number_format($payroll->overtime, 2) }}</td>
                        </tr>
                        <tr class="bg-emerald-50 font-bold">
                            <td class="py-2.5 px-3 text-emerald-950">Total Gross Earnings</td>
                            <td class="py-2.5 px-3 text-right text-emerald-950">₱{{ number_format($payroll->basic_salary + $payroll->allowances + $payroll->overtime, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Deductions -->
            <div class="border border-red-200 rounded-xl overflow-hidden">
                <div class="bg-red-800 text-white px-4 py-2 font-bold text-xs uppercase tracking-wider">
                    Deductions Breakdown
                </div>
                <table class="w-full text-xs text-left divide-y divide-gray-100">
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-2.5 px-3 text-gray-700">SSS / PhilHealth / Pag-IBIG & Tax</td>
                            <td class="py-2.5 px-3 text-right font-medium text-red-600">-₱{{ number_format($payroll->deductions, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-gray-400 italic">Other Adjustments</td>
                            <td class="py-2.5 px-3 text-right text-gray-400">₱0.00</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-gray-400 italic">Loan Amortization</td>
                            <td class="py-2.5 px-3 text-right text-gray-400">₱0.00</td>
                        </tr>
                        <tr class="bg-red-50 font-bold">
                            <td class="py-2.5 px-3 text-red-950">Total Deductions</td>
                            <td class="py-2.5 px-3 text-right text-red-950">-₱{{ number_format($payroll->deductions, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Net Salary Summary Box -->
        <div class="bg-emerald-950 text-white p-5 rounded-2xl flex items-center justify-between shadow-lg">
            <div>
                <span class="text-xs text-emerald-300 uppercase font-semibold tracking-wider">Net Take-Home Pay</span>
                <p class="text-[11px] text-emerald-200">Basic + Allowances + Overtime - Deductions</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-black text-white">₱{{ number_format($payroll->net_salary, 2) }}</p>
                <p class="text-xs text-emerald-300 font-bold">Philippine Pesos (PHP)</p>
            </div>
        </div>

        <!-- Signature Authorization Footer -->
        <div class="pt-8 border-t border-gray-200 grid grid-cols-2 gap-12 text-xs">
            <div class="text-center space-y-8">
                <div class="h-10 border-b border-gray-400 w-3/4 mx-auto"></div>
                <div>
                    <p class="font-bold text-gray-800 uppercase">{{ $payroll->employee->full_name }}</p>
                    <p class="text-[11px] text-gray-500">Employee Signature & Date</p>
                </div>
            </div>

            <div class="text-center space-y-8">
                <div class="h-10 border-b border-gray-400 w-3/4 mx-auto flex items-end justify-center pb-1">
                    <span class="font-bold text-emerald-900">HR & Finance Officer</span>
                </div>
                <div>
                    <p class="font-bold text-gray-800 uppercase">Davao Sugar Central Co., Inc. Finance</p>
                    <p class="text-[11px] text-gray-500">Authorized Officer Signature</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
