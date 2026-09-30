@extends('layouts.app')

@section('title', 'Operational Reports')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Operational Reports</h2>
        <p class="text-xs text-gray-500 mt-1">Summary view of the current ERP performance and operational status.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Farmers</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $reportData['total_farmers'] }}</p>
            <p class="text-xs text-emerald-600 mt-1">{{ $reportData['active_farmers'] }} active</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Sales Value</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">₱{{ number_format($reportData['sales_value'], 2) }}</p>
            <p class="text-xs text-amber-600 mt-1">Across active sales orders</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Fertilizer Distributed</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($reportData['fertilizer_distributed'], 0) }}</p>
            <p class="text-xs text-purple-600 mt-1">Units issued to farmers</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Low Stock Items</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $reportData['low_stock_items'] }}</p>
            <p class="text-xs text-red-600 mt-1">Needs attention</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Employees</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $reportData['employees'] }}</p>
            <p class="text-xs text-blue-600 mt-1">Tracked workforce</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Trade Activity</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $reportData['imports'] + $reportData['exports'] }}</p>
            <p class="text-xs text-sky-600 mt-1">Imports + exports</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Key Report Highlights</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($highlights as $label => $value)
                <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                    <p class="text-[10px] uppercase tracking-wide text-gray-500 font-bold">{{ $label }}</p>
                    <p class="mt-2 text-lg font-bold text-gray-900">{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
