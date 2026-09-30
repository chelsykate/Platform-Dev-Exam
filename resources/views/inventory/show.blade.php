@extends('layouts.app')

@section('title', $inventory->item_name . ' - Inventory Ledger')

@section('content')
<div class="space-y-6">
    
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 px-3 py-2 rounded-lg transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Inventory
            </a>
            <span class="text-xs font-mono bg-emerald-100 text-emerald-800 font-bold px-2.5 py-1 rounded">
                {{ $inventory->item_code }}
            </span>
        </div>
    </div>

    <!-- Stock Item Summary Header Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-700 text-white font-bold text-lg flex items-center justify-center shadow">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $inventory->item_name }}</h2>
                    <p class="text-xs text-gray-500">Category: <strong class="text-gray-700">{{ $inventory->category }}</strong> | Unit Cost: <strong class="text-emerald-800">₱{{ number_format($inventory->unit_cost, 2) }}</strong></p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-3 border-t border-gray-100 text-xs">
                <div>
                    <span class="text-gray-400 block">Reorder Level</span>
                    <span class="font-bold text-gray-800">{{ number_format($inventory->reorder_level, 2) }} {{ $inventory->unit }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Supplier</span>
                    <span class="font-bold text-gray-800">{{ $inventory->supplier->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Stock Condition</span>
                    @if($inventory->isLowStock())
                        <span class="inline-block px-2.5 py-0.5 rounded font-extrabold text-[10px] bg-red-100 text-red-800 animate-pulse">
                            <i class="fa-solid fa-triangle-exclamation"></i> LOW STOCK
                        </span>
                    @else
                        <span class="inline-block px-2.5 py-0.5 rounded font-bold text-[10px] bg-emerald-100 text-emerald-800">
                            IN STOCK
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stock Gauge Box -->
        <div class="bg-emerald-950 text-white p-4 rounded-xl flex flex-col justify-between shadow-inner">
            <span class="text-xs text-emerald-300 font-semibold uppercase tracking-wider">Current Stock Balance</span>
            <div class="my-2">
                <p class="text-3xl font-extrabold text-white">{{ number_format($inventory->quantity, 2) }}</p>
                <p class="text-xs text-emerald-300">{{ $inventory->unit }} Available</p>
            </div>
            <div class="text-[11px] text-emerald-200 border-t border-emerald-800/80 pt-2 flex justify-between">
                <span>Total Inventory Value:</span>
                <strong class="text-white">₱{{ number_format($inventory->quantity * $inventory->unit_cost, 2) }}</strong>
            </div>
        </div>
    </div>

    <!-- Inventory Transaction Audit Ledger -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <h3 class="font-bold text-gray-900 text-base border-b border-gray-100 pb-3">
            <i class="fa-solid fa-clock-rotate-left text-emerald-700 mr-2"></i> Stock Movement Transaction Ledger
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-2.5 px-3">Date & Time</th>
                        <th class="py-2.5 px-3">Type</th>
                        <th class="py-2.5 px-3">Quantity Movement</th>
                        <th class="py-2.5 px-3">Performed By</th>
                        <th class="py-2.5 px-3">Remarks / Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($inventory->transactions as $trx)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2.5 px-3 text-gray-500 font-mono">{{ $trx->transaction_date ? $trx->transaction_date->format('M d, Y h:i A') : 'N/A' }}</td>
                            <td class="py-2.5 px-3">
                                @if($trx->transaction_type === 'STOCK_IN')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-emerald-100 text-emerald-800">+ STOCK IN</span>
                                @elseif($trx->transaction_type === 'FERTILIZER_RELEASE' || $trx->transaction_type === 'STOCK_OUT')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-amber-100 text-amber-800">- {{ $trx->transaction_type }}</span>
                                @else
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-blue-100 text-blue-800">{{ $trx->transaction_type }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 font-bold {{ $trx->quantity > 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ $trx->quantity > 0 ? '+' : '' }}{{ number_format($trx->quantity, 2) }} {{ $inventory->unit }}
                            </td>
                            <td class="py-2.5 px-3 font-medium text-gray-700">{{ $trx->performed_by }}</td>
                            <td class="py-2.5 px-3 text-gray-600">{{ $trx->remarks ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500 italic">No transaction history recorded for this stock item yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
