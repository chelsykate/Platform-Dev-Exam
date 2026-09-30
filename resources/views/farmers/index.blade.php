@extends('layouts.app')

@section('title', 'Farmer Management')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Sugarcane Farmers Management</h2>
            <p class="text-xs text-gray-500 mt-1">Davao Sugar Central Co., Inc. registered sugarcane planters database.</p>
        </div>
        <a href="{{ route('farmers.create') }}" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-user-plus"></i>
            <span>Register New Farmer</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('farmers.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search Farmer</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Code, Name, Barangay..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Municipality</label>
                <select name="municipality" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Municipalities</option>
                    @foreach($municipalities as $mun)
                        <option value="{{ $mun }}" {{ request('municipality') == $mun ? 'selected' : '' }}>{{ $mun }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
                    <option value="">All Statuses</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Under Verification" {{ request('status') == 'Under Verification' ? 'selected' : '' }}>Under Verification</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('farmers.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Farmers Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Farmer Code</th>
                        <th class="py-3 px-4">Farmer Name</th>
                        <th class="py-3 px-4">Location</th>
                        <th class="py-3 px-4">Contact</th>
                        <th class="py-3 px-4">Registered Farms</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($farmers as $farmer)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-800">
                                {{ $farmer->farmer_code }}
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-900">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[10px]">
                                        {{ strtoupper(substr($farmer->first_name, 0, 1) . substr($farmer->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $farmer->full_name }}</p>
                                        <p class="text-[11px] text-gray-400">{{ $farmer->email ?? 'No email' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                {{ $farmer->barangay }}, {{ $farmer->municipality }}
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                {{ $farmer->contact_number }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1 font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                    <i class="fa-solid fa-wheat-field text-emerald-600"></i> {{ $farmer->farms_count }} Farm(s)
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($farmer->status === 'Active')
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded">
                                        <i class="fa-solid fa-circle text-[8px]"></i> Active
                                    </span>
                                @elseif($farmer->status === 'Pending')
                                    <span class="inline-flex items-center gap-1 text-amber-700 font-semibold bg-amber-50 px-2 py-0.5 rounded">
                                        <i class="fa-solid fa-circle text-[8px]"></i> Pending
                                    </span>
                                @elseif($farmer->status === 'Under Verification')
                                    <span class="inline-flex items-center gap-1 text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded">
                                        <i class="fa-solid fa-circle text-[8px]"></i> Verifying
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-gray-600 font-semibold bg-gray-100 px-2 py-0.5 rounded">
                                        <i class="fa-solid fa-circle text-[8px]"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <a href="{{ route('farmers.show', $farmer) }}" class="p-1.5 text-gray-600 hover:text-emerald-700 transition" title="View Profile & Farms">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('farmers.edit', $farmer) }}" class="p-1.5 text-gray-600 hover:text-blue-700 transition" title="Edit Farmer">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <form method="POST" action="{{ route('farmers.destroy', $farmer) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this farmer profile?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-600 hover:text-red-700 transition" title="Delete Farmer">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <i class="fa-solid fa-users-slash text-2xl text-gray-300 mb-2"></i>
                                <p>No sugarcane farmers found matching the search criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($farmers->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $farmers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

