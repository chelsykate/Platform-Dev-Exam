@extends('layouts.app')

@section('title', 'Sugar Exports')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Sugar Export Shipments</h2>
            <p class="text-xs text-gray-500 mt-1">Davao Sugar Central Co., Inc. international sugar & by-product export shipments.</p>
        </div>
        <button onclick="document.getElementById('addExportModal').classList.toggle('hidden')" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-plane-departure flex"></i>
            <span>Log Export Order</span>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('export-orders.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search Export / Destination</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Export Code, Destination Country..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Statuses</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                    <option value="Shipped" {{ request('status') == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('export-orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Exports Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Export Code</th>
                        <th class="py-3 px-4">Foreign Customer</th>
                        <th class="py-3 px-4">Destination Country</th>
                        <th class="py-3 px-4">Order Date</th>
                        <th class="py-3 px-4">Shipment Date</th>
                        <th class="py-3 px-4">Export Value</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($exports as $exp)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-800">{{ $exp->export_code }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $exp->customer->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-800">
                                <i class="fa-solid fa-globe text-emerald-600 mr-1"></i> {{ $exp->destination }}
                            </td>
                            <td class="py-3 px-4 text-gray-500 font-mono">{{ $exp->order_date ? $exp->order_date->format('M d, Y') : '-' }}</td>
                            <td class="py-3 px-4 text-emerald-700 font-mono font-bold">{{ $exp->shipment_date ? $exp->shipment_date->format('M d, Y') : 'Pending Shipment' }}</td>
                            <td class="py-3 px-4 font-extrabold text-gray-900">₱{{ number_format($exp->total_amount, 2) }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded font-bold text-[10px] {{ $exp->status === 'Completed' || $exp->status === 'Delivered' ? 'bg-emerald-100 text-emerald-800' : ($exp->status === 'Shipped' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $exp->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <button onclick="openExportStatusModal({{ $exp->id }}, '{{ $exp->export_code }}', '{{ $exp->status }}')" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded font-semibold text-[11px] transition">
                                    Status
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 italic">No sugar export shipment orders recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exports->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $exports->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Export Order -->
<div id="addExportModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Log Sugar Export Order</h3>
            <button onclick="document.getElementById('addExportModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('export-orders.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Foreign Buyer / Customer *</label>
                <select name="customer_id" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">-- Choose Buyer --</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Order Date *</label>
                    <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Destination Country / Port *</label>
                    <input type="text" name="destination" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="e.g. United States / Port of Tokyo">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Export Total Amount (₱) *</label>
                    <input type="number" step="0.01" name="total_amount" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="e.g. 1250000">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status *</label>
                    <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                        <option value="Draft">Draft</option>
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addExportModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Save Export Order</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Update Status -->
<div id="exportStatusModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-blue-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Update Export Status</h3>
            <button onclick="document.getElementById('exportStatusModal').classList.add('hidden')" class="text-white hover:text-blue-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="exportStatusForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <p class="text-xs text-gray-600">Updating status for export <strong id="modalExpCode" class="text-emerald-800 font-mono"></strong></p>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status *</label>
                <select name="status" id="modalExpStatus" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                    <option value="Draft">Draft</option>
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('exportStatusModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-blue-700 text-white text-xs font-semibold rounded-lg shadow">Save Status</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openExportStatusModal(id, code, status) {
        document.getElementById('exportStatusForm').action = '/export-orders/' + id + '/status';
        document.getElementById('modalExpCode').innerText = code;
        document.getElementById('modalExpStatus').value = status;
        document.getElementById('exportStatusModal').classList.remove('hidden');
    }
</script>
@endsection
