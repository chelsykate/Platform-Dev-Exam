@extends('layouts.app')

@section('title', 'Sugarcane Imports')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Sugarcane Procurement & Import Orders</h2>
            <p class="text-xs text-gray-500 mt-1">Davao Sugar Central Co., Inc. bulk sugarcane imports & external farm procurement tracking.</p>
        </div>
        <button onclick="document.getElementById('addImportModal').classList.toggle('hidden')" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-ship flex"></i>
            <span>Log Import Order</span>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('import-orders.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search Import / Supplier</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Import Code, Supplier Name..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Statuses</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                    <option value="Arriving" {{ request('status') == 'Arriving' ? 'selected' : '' }}>Arriving</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('import-orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Imports Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Import Code</th>
                        <th class="py-3 px-4">Supplier</th>
                        <th class="py-3 px-4">Order Date</th>
                        <th class="py-3 px-4">Expected Arrival</th>
                        <th class="py-3 px-4">Actual Arrival</th>
                        <th class="py-3 px-4">Total Cost</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($imports as $imp)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-800">{{ $imp->import_code }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $imp->supplier->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-gray-500 font-mono">{{ $imp->order_date ? $imp->order_date->format('M d, Y') : '-' }}</td>
                            <td class="py-3 px-4 text-gray-600 font-mono">{{ $imp->expected_arrival ? $imp->expected_arrival->format('M d, Y') : '-' }}</td>
                            <td class="py-3 px-4 text-emerald-700 font-mono font-bold">{{ $imp->actual_arrival ? $imp->actual_arrival->format('M d, Y') : 'In Transit' }}</td>
                            <td class="py-3 px-4 font-extrabold text-gray-900">₱{{ number_format($imp->total_cost, 2) }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded font-bold text-[10px] {{ $imp->status === 'Completed' ? 'bg-emerald-100 text-emerald-800' : ($imp->status === 'Arriving' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $imp->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <button onclick="openImportStatusModal({{ $imp->id }}, '{{ $imp->import_code }}', '{{ $imp->status }}')" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded font-semibold text-[11px] transition">
                                    Status
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 italic">No sugarcane import procurement orders recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($imports->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $imports->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Import Order -->
<div id="addImportModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Log Sugarcane Import Order</h3>
            <button onclick="document.getElementById('addImportModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('import-orders.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Supplier *</label>
                <select name="supplier_id" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">-- Choose Supplier --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Order Date *</label>
                    <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Expected Arrival *</label>
                    <input type="date" name="expected_arrival" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Total Procurement Cost (₱) *</label>
                    <input type="number" step="0.01" name="total_cost" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="e.g. 750000">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status *</label>
                    <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                        <option value="Draft">Draft</option>
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Arriving">Arriving</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addImportModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Save Import Order</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Update Status -->
<div id="importStatusModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-blue-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Update Import Status</h3>
            <button onclick="document.getElementById('importStatusModal').classList.add('hidden')" class="text-white hover:text-blue-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="importStatusForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <p class="text-xs text-gray-600">Updating status for import <strong id="modalImpCode" class="text-emerald-800 font-mono"></strong></p>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status *</label>
                <select name="status" id="modalImpStatus" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                    <option value="Draft">Draft</option>
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Arriving">Arriving</option>
                    <option value="Completed">Completed (Received)</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('importStatusModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-blue-700 text-white text-xs font-semibold rounded-lg shadow">Save Status</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openImportStatusModal(id, code, status) {
        document.getElementById('importStatusForm').action = '/import-orders/' + id + '/status';
        document.getElementById('modalImpCode').innerText = code;
        document.getElementById('modalImpStatus').value = status;
        document.getElementById('importStatusModal').classList.remove('hidden');
    }
</script>
@endsection
