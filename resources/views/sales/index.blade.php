@extends('layouts.app')

@section('title', 'Sales Orders')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Sugar Sales Orders</h2>
            <p class="text-xs text-gray-500 mt-1">Davao Sugar Central Co., Inc. raw sugar, refined sugar, molasses & bagasse sales ledger.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-xs px-3.5 py-2.5 rounded-lg border border-gray-300 transition">
                <i class="fa-solid fa-users"></i>
                <span>Customer Accounts</span>
            </a>
            <button onclick="document.getElementById('addSalesOrderModal').classList.toggle('hidden')" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
                <i class="fa-solid fa-cart-plus"></i>
                <span>Create Sales Order</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('sales-orders.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search Order / Customer</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="SO Code, Customer Name..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('sales-orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Sales Orders Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Order Code</th>
                        <th class="py-3 px-4">Customer Name</th>
                        <th class="py-3 px-4">Order Date</th>
                        <th class="py-3 px-4">Line Items Breakdown</th>
                        <th class="py-3 px-4">Grand Total</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-800">{{ $order->order_code }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $order->customer->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-gray-500 font-mono">{{ $order->order_date ? $order->order_date->format('M d, Y') : 'N/A' }}</td>
                            <td class="py-3 px-4 space-y-1">
                                @foreach($order->items as $item)
                                    <div class="text-[11px] text-gray-700">
                                        • <strong>{{ $item->product_name }}</strong>: {{ $item->quantity }} @ ₱{{ number_format($item->unit_price, 2) }} = <span class="font-bold text-emerald-800">₱{{ number_format($item->subtotal, 2) }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="py-3 px-4 font-extrabold text-sm text-emerald-900">₱{{ number_format($order->total_amount, 2) }}</td>
                            <td class="py-3 px-4">
                                @if($order->status === 'Completed')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-emerald-100 text-emerald-800">Completed</span>
                                @elseif($order->status === 'Processing')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-blue-100 text-blue-800">Processing</span>
                                @elseif($order->status === 'Pending')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-amber-100 text-amber-800">Pending</span>
                                @else
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-red-100 text-red-800">Cancelled</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <button onclick="openStatusModal({{ $order->id }}, '{{ $order->order_code }}', '{{ $order->status }}')" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded font-semibold text-[11px] transition">
                                    Status
                                </button>
                                <form method="POST" action="{{ route('sales-orders.destroy', $order) }}" class="inline-block" onsubmit="return confirm('Delete sales order?');">
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
                            <td colspan="7" class="py-8 text-center text-gray-500 italic">No sales orders recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Create Sales Order -->
<div id="addSalesOrderModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between flex-shrink-0">
            <h3 class="font-bold text-sm">Create Sugar Sales Order</h3>
            <button onclick="document.getElementById('addSalesOrderModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('sales-orders.store') }}" class="p-6 space-y-4 overflow-y-auto">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Customer Account *</label>
                    <select name="customer_id" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Order Date *</label>
                    <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Order Status *</label>
                <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div class="border-t border-gray-100 pt-3">
                <h4 class="font-bold text-xs text-gray-800 uppercase mb-2">Line Items (Subtotal = Qty × Unit Price)</h4>
                
                <div class="space-y-3">
                    <div class="grid grid-cols-12 gap-2 bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <div class="col-span-5">
                            <label class="block text-[10px] text-gray-500 font-bold mb-1">Product</label>
                            <select name="items[0][product_name]" required class="w-full text-xs p-1.5 border border-gray-300 rounded">
                                <option value="Raw Sugar (LTO)">Raw Sugar (50kg Bag)</option>
                                <option value="Refined Sugar (Grade A)">Refined Sugar (50kg Bag)</option>
                                <option value="Molasses (Industrial)">Molasses (Metric Ton)</option>
                                <option value="Bagasse (Biofuel)">Bagasse (Metric Ton)</option>
                            </select>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-[10px] text-gray-500 font-bold mb-1">Quantity</label>
                            <input type="number" step="0.5" name="items[0][quantity]" value="100" required class="w-full text-xs p-1.5 border border-gray-300 rounded">
                        </div>
                        <div class="col-span-4">
                            <label class="block text-[10px] text-gray-500 font-bold mb-1">Unit Price (₱)</label>
                            <input type="number" step="0.01" name="items[0][unit_price]" value="2800" required class="w-full text-xs p-1.5 border border-gray-300 rounded">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addSalesOrderModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Generate Sales Order</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Update Order Status -->
<div id="statusModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-blue-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Update Order Status</h3>
            <button onclick="document.getElementById('statusModal').classList.add('hidden')" class="text-white hover:text-blue-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="statusForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <p class="text-xs text-gray-600">Updating status for order <strong id="modalSOCode" class="text-emerald-800 font-mono"></strong></p>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status *</label>
                <select name="status" id="modalSOStatus" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('statusModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-blue-700 text-white text-xs font-semibold rounded-lg shadow">Save Status</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openStatusModal(id, code, status) {
        document.getElementById('statusForm').action = '/sales-orders/' + id + '/status';
        document.getElementById('modalSOCode').innerText = code;
        document.getElementById('modalSOStatus').value = status;
        document.getElementById('statusModal').classList.remove('hidden');
    }
</script>
@endsection
