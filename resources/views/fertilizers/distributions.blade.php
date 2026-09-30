@extends('layouts.app')

@section('title', 'Fertilizer Distributions Log')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Fertilizer Distributions Log</h2>
            <p class="text-xs text-gray-500 mt-1">Warehouse release transactions and inventory deduction audit trail.</p>
        </div>
        <a href="{{ route('fertilizer-requests.index') }}" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition">
            <i class="fa-solid fa-list-check"></i>
            <span>View Pending Requests</span>
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('fertilizer-distributions.index') }}" class="flex gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Distribution Code, Farmer Name..." class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600">
            </div>
            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition">Search</button>
            <a href="{{ route('fertilizer-distributions.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs py-2 px-3 rounded-lg transition">Reset</a>
        </form>
    </div>

    <!-- Distributions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                        <th class="py-3 px-4">Distribution Code</th>
                        <th class="py-3 px-4">Request Code</th>
                        <th class="py-3 px-4">Farmer Name</th>
                        <th class="py-3 px-4">Fertilizer Released</th>
                        <th class="py-3 px-4">Quantity Released</th>
                        <th class="py-3 px-4">Release Timestamp</th>
                        <th class="py-3 px-4">Warehouse Officer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($distributions as $dist)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-800">{{ $dist->distribution_code }}</td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ $dist->request->request_code ?? 'N/A' }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">
                                {{ $dist->farmer->full_name }}
                                <span class="block text-[10px] text-gray-400 font-mono font-normal">{{ $dist->farmer->farmer_code }}</span>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-800">{{ $dist->fertilizer->name }}</td>
                            <td class="py-3 px-4 font-bold text-emerald-700">
                                <i class="fa-solid fa-box text-emerald-600 mr-1"></i> {{ $dist->quantity }} {{ $dist->fertilizer->unit }}
                            </td>
                            <td class="py-3 px-4 text-gray-500">{{ $dist->distribution_date ? $dist->distribution_date->format('M d, Y h:i A') : 'N/A' }}</td>
                            <td class="py-3 px-4 text-gray-700 font-medium">
                                <i class="fa-solid fa-id-badge text-gray-400 mr-1"></i> {{ $dist->released_by }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 italic">No fertilizer distribution release records logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($distributions->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $distributions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
