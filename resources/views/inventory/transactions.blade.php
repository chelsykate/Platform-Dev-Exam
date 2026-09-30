@extends('layouts.app')

@section('title', 'Inventory Transactions Ledger')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">System Inventory Transactions Ledger</h2>
            <p class="text-xs text-gray-500 mt-1">Audit log of all stock receipts, issuances, fertilizer releases, and adjustments.</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Back to Stock Items</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('inventory-transactions.index') }}" class="flex gap-3">
            <div class="flex-1">
                <select name="type" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Transaction Types</option>
                    <option value="STOCK_IN" {{ request('type') == 'STOCK_IN' ? 'selected' : '' }}>STOCK_IN</option>
                    <option value="STOCK_OUT" {{ request('type') == 'STOCK_OUT' ? 'selected' : '' }}>STOCK_OUT</option>
                    <option value="FERTILIZER_RELEASE" {{ request('type') == 'FERTILIZER_RELEASE' ? 'selected' : '' }}>FERTILIZER_RELEASE</option>
                    <option value="ADJUSTMENT" {{ request('type') == 'ADJUSTMENT' ? 'selected' : '' }}>ADJUSTMENT</option>
                </select>
            </div>
            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">Filter</button>
            <a href="{{ route('inventory-transactions.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Date & Time</th>
                        <th class="py-3 px-4">Stock Item</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Transaction Type</th>
                        <th class="py-3 px-4">Quantity Movement</th>
                        <th class="py-3 px-4">Performed By</th>
                        <th class="py-3 px-4">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 text-gray-500 font-mono">{{ $trx->transaction_date ? $trx->transaction_date->format('M d, Y h:i A') : 'N/A' }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $trx->inventory->item_name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $trx->inventory->category ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if($trx->transaction_type === 'STOCK_IN')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-emerald-100 text-emerald-800">+ STOCK_IN</span>
                                @elseif($trx->transaction_type === 'FERTILIZER_RELEASE')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-purple-100 text-purple-800">- FERTILIZER_RELEASE</span>
                                @elseif($trx->transaction_type === 'STOCK_OUT')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-amber-100 text-amber-800">- STOCK_OUT</span>
                                @else
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-blue-100 text-blue-800">{{ $trx->transaction_type }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold {{ $trx->quantity > 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ $trx->quantity > 0 ? '+' : '' }}{{ number_format($trx->quantity, 2) }} {{ $trx->inventory->unit ?? '' }}
                            </td>
                            <td class="py-3 px-4 text-gray-700 font-medium">{{ $trx->performed_by }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $trx->remarks ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 italic">No inventory transaction logs recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
