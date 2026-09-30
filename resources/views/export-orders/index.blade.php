@extends('layouts.app')

@section('title', 'Export Orders')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Export Orders</h2>
            <p class="text-xs text-gray-500 mt-1">Track outbound shipments and customer delivery commitments.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Create Export Order</h3>
            <form method="POST" action="{{ route('export-orders.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Customer</label>
                    <select name="customer_id" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" required>
                        <option value="">Select customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Order Date</label>
                    <input type="date" name="order_date" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Destination</label>
                    <input type="text" name="destination" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Total Amount</label>
                    <input type="number" step="0.01" name="total_amount" value="0" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Shipment Date</label>
                    <input type="date" name="shipment_date" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" required>
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg">Create Export</button>
            </form>
        </div>

        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase">
                            <th class="py-3 px-4">Export</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Destination</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exportOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-semibold text-gray-800">{{ $order->export_code }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $order->customer->name ?? '—' }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $order->destination }}</td>
                                <td class="py-3 px-4 font-semibold text-emerald-700">₱{{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-3 px-4"><span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold px-2 py-1">{{ $order->status }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">No export orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($exportOrders->hasPages())
                <div class="p-4 border-t border-gray-100">{{ $exportOrders->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
