@extends('layouts.app')

@section('title', 'Fertilizer Catalog')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Fertilizer Types & Catalog</h2>
            <p class="text-xs text-gray-500 mt-1">Davao Sugar Central Co., Inc. approved fertilizer formulas & inventory links.</p>
        </div>
        <button onclick="document.getElementById('addFertilizerModal').classList.toggle('hidden')" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-plus"></i>
            <span>Add Fertilizer Formula</span>
        </button>
    </div>

    <!-- Catalog Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Code</th>
                        <th class="py-3 px-4">Formula / Name</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Unit</th>
                        <th class="py-3 px-4">Linked Stock Item</th>
                        <th class="py-3 px-4">Available Quantity</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($fertilizers as $fert)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-800">{{ $fert->fertilizer_code }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $fert->name }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $fert->type }}</td>
                            <td class="py-3 px-4 font-medium text-gray-700">{{ $fert->unit }}</td>
                            <td class="py-3 px-4 text-gray-600">
                                {{ $fert->inventory->item_name ?? 'Auto Matched Stock' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-emerald-700">
                                {{ $fert->inventory->quantity ?? '0' }} {{ $fert->unit }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $fert->status === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $fert->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <form method="POST" action="{{ route('fertilizers.destroy', $fert) }}" class="inline-block" onsubmit="return confirm('Delete fertilizer item?');">
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
                            <td colspan="8" class="py-8 text-center text-gray-500 italic">No fertilizer catalog entries recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($fertilizers->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $fertilizers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Fertilizer -->
<div id="addFertilizerModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-emerald-900 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Add Fertilizer Formula</h3>
            <button onclick="document.getElementById('addFertilizerModal').classList.add('hidden')" class="text-white hover:text-emerald-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('fertilizers.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Fertilizer Name *</label>
                <input type="text" name="name" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="e.g. Urea (46-0-0)">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Formula Type *</label>
                    <input type="text" name="type" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="Nitrogen">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Packaging Unit *</label>
                    <input type="text" name="unit" value="Bags" required class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Link to Inventory Stock Item</label>
                <select name="inventory_id" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">-- Select Inventory Stock Item --</option>
                    @foreach($inventories as $inv)
                        <option value="{{ $inv->id }}">{{ $inv->item_name }} (Current Stock: {{ $inv->quantity }} {{ $inv->unit }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Description / Application Note</label>
                <textarea name="description" rows="2" class="w-full text-xs p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600" placeholder="Recommended for early sugarcane vegetative growth..."></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status *</label>
                <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addFertilizerModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow">Save Fertilizer</button>
            </div>
        </form>
    </div>
</div>
@endsection
