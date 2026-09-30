@extends('layouts.app')

@section('title', 'Customers Directory')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Customers Directory</h2>
            <p class="text-xs text-gray-500 mt-1">Davao Sugar Central Co., Inc. domestic & commercial buyer accounts.</p>
        </div>
        <button onclick="document.getElementById('addCustomerModal').classList.toggle('hidden')" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-user-tie flex"></i>
            <span>Add Customer Account</span>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('customers.index') }}" class="flex gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Customer Code, Company Name, Contact Person..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">Search</button>
            <a href="{{ route('customers.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Code</th>
                        <th class="py-3 px-4">Customer / Buyer Name</th>
                        <th class="py-3 px-4">Contact Person</th>
                        <th class="py-3 px-4">Contact Number</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Sales Orders</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $cust)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-800">{{ $cust->customer_code }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $cust->name }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $cust->contact_person }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $cust->contact_number }}</td>
                            <td class="py-3 px-4 text-gray-500">{{ $cust->email ?? 'N/A' }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-700">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-gray-100 text-gray-700">{{ $cust->sales_orders_count }} Order(s)</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $cust->status === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $cust->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <form method="POST" action="{{ route('customers.destroy', $cust) }}" class="inline-block" onsubmit="return confirm('Delete customer account?');">
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
                            <td colspan="8" class="py-8 text-center text-gray-500 italic">No customer buyer records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Customer -->
<div id="addCustomerModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Add Customer Buyer Account</h3>
            <button onclick="document.getElementById('addCustomerModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('customers.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Company / Buyer Name *</label>
                <input type="text" name="name" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="e.g. Universal Robina Corp / Coca-Cola Philippines">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Contact Person *</label>
                    <input type="text" name="contact_person" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="Ms. Elena Torres">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Contact Number *</label>
                    <input type="text" name="contact_number" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="0917-888-9900">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email Address</label>
                <input type="email" name="email" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="procurement@buyer.com">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Business Address *</label>
                <input type="text" name="address" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="Davao City Industrial District">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status *</label>
                <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addCustomerModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Save Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
