@extends('layouts.app')

@section('title', 'Inventory & Stock Management')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Inventory Stock & Materials</h2>
            <p class="text-xs text-gray-500 mt-1">Davao Sugar Central Co., Inc. warehouse stock monitoring and transaction ledger.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('inventory-transactions.index') }}" class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-xs px-3.5 py-2.5 rounded-lg border border-gray-300 transition">
                <i class="fa-solid fa-list-ul"></i>
                <span>Transaction Ledger</span>
            </a>
            <button onclick="document.getElementById('addItemModal').classList.toggle('hidden')" class="inline-flex items-center gap-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
                <i class="fa-solid fa-plus"></i>
                <span>Add Inventory Item</span>
            </button>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search Item / Code</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Code, Name, Category..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                <select name="category" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center pt-5">
                <label class="inline-flex items-center text-xs font-semibold text-gray-700 cursor-pointer">
                    <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <span class="ml-2 text-red-700 font-bold"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Show Low Stock Only</span>
                </label>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('inventory.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Item Code</th>
                        <th class="py-3 px-4">Item Name</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Stock Quantity</th>
                        <th class="py-3 px-4">Reorder Level</th>
                        <th class="py-3 px-4">Unit Cost</th>
                        <th class="py-3 px-4">Supplier</th>
                        <th class="py-3 px-4">Stock Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-800">{{ $item->item_code }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">
                                <a href="{{ route('inventory.show', $item) }}" class="hover:text-emerald-700 hover:underline">
                                    {{ $item->item_name }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-gray-600">{{ $item->category }}</td>
                            <td class="py-3 px-4 font-extrabold text-sm {{ $item->isLowStock() ? 'text-red-600' : 'text-emerald-800' }}">
                                {{ number_format($item->quantity, 2) }} {{ $item->unit }}
                            </td>
                            <td class="py-3 px-4 text-gray-500 font-medium">{{ number_format($item->reorder_level, 2) }} {{ $item->unit }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-700">₱{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $item->supplier->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4">
                                @if($item->isLowStock())
                                    <span class="inline-flex items-center gap-1 font-extrabold text-[10px] px-2.5 py-1 bg-red-100 text-red-800 rounded-full border border-red-300 animate-pulse">
                                        <i class="fa-solid fa-triangle-exclamation"></i> LOW STOCK
                                    </span>
                                @elseif($item->status === 'Out of Stock')
                                    <span class="inline-flex items-center gap-1 font-bold text-[10px] px-2.5 py-1 bg-gray-200 text-gray-800 rounded-full">
                                        OUT OF STOCK
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 font-bold text-[10px] px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full">
                                        <i class="fa-solid fa-circle text-[7px]"></i> IN STOCK
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <!-- Stock In Button -->
                                <button onclick="openStockModal('in', {{ $item->id }}, '{{ $item->item_name }}', '{{ $item->unit }}')" class="px-2 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded font-semibold text-[11px] transition" title="Stock-In Replenish">
                                    + Stock-In
                                </button>
                                <!-- Stock Out Button -->
                                <button onclick="openStockModal('out', {{ $item->id }}, '{{ $item->item_name }}', '{{ $item->unit }}')" class="px-2 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded font-semibold text-[11px] transition" title="Stock-Out Issue">
                                    - Stock-Out
                                </button>
                                <a href="{{ route('inventory.show', $item) }}" class="p-1.5 text-gray-600 hover:text-emerald-700 transition" title="View Ledger">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-500 italic">No inventory stock items recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Inventory Item -->
<div id="addItemModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Add Inventory Item</h3>
            <button onclick="document.getElementById('addItemModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('inventory.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Item Name *</label>
                <input type="text" name="item_name" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="e.g. Urea 46-0-0 Granular">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Category *</label>
                    <select name="category" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                        <option value="Fertilizer">Fertilizer</option>
                        <option value="Chemicals">Chemicals & Pesticides</option>
                        <option value="Raw Materials">Raw Materials</option>
                        <option value="Finished Product">Finished Product</option>
                        <option value="Equipment">Equipment & Spare Parts</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Unit of Measure *</label>
                    <input type="text" name="unit" value="Bags" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="Bags / Tons / Liters">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Initial Qty *</label>
                    <input type="number" step="0.5" name="quantity" value="100" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Reorder Level *</label>
                    <input type="number" step="0.5" name="reorder_level" value="20" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Unit Cost (₱) *</label>
                    <input type="number" step="0.01" name="unit_cost" value="1800" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Assigned Supplier</label>
                <select name="supplier_id" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">-- Optional Supplier --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addItemModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Save Stock Item</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Stock-In / Stock-Out -->
<div id="stockActionModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div id="stockModalHeader" class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 id="stockModalTitle" class="font-bold text-sm">Stock Action</h3>
            <button onclick="document.getElementById('stockActionModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="stockForm" method="POST" action="" class="p-6 space-y-4">
            @csrf

            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-xs">
                Target Stock Item: <strong id="modalItemName" class="text-gray-900"></strong>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Quantity (<span id="modalUnit"></span>) *</label>
                <input type="number" step="0.5" name="quantity" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="e.g. 50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Remarks / Reference</label>
                <textarea name="remarks" rows="2" class="w-full text-xs p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="Delivery receipt # / Usage note..."></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('stockActionModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" id="stockSubmitBtn" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Confirm</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openStockModal(type, id, itemName, unit) {
        const form = document.getElementById('stockForm');
        const header = document.getElementById('stockModalHeader');
        const title = document.getElementById('stockModalTitle');
        const btn = document.getElementById('stockSubmitBtn');

        document.getElementById('modalItemName').innerText = itemName;
        document.getElementById('modalUnit').innerText = unit;

        if (type === 'in') {
            form.action = '/inventory/' + id + '/stock-in';
            header.className = 'bg-emerald-900 text-white px-6 py-4 flex items-center justify-between';
            title.innerText = 'Process Stock-In (Replenish)';
            btn.className = 'px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-lg shadow';
            btn.innerText = 'Confirm Stock-In';
        } else {
            form.action = '/inventory/' + id + '/stock-out';
            header.className = 'bg-amber-800 text-white px-6 py-4 flex items-center justify-between';
            title.innerText = 'Process Stock-Out (Issue Stock)';
            btn.className = 'px-5 py-2 bg-amber-700 hover:bg-amber-800 text-white text-xs font-semibold rounded-lg shadow';
            btn.innerText = 'Confirm Stock-Out';
        }

        document.getElementById('stockActionModal').classList.remove('hidden');
    }
</script>
@endsection
