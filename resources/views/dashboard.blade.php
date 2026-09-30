@extends('layouts.app')

@section('title', 'ERP Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 text-white rounded-2xl p-6 shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Davao Sugar Central Co., Inc. ERP</h2>
            <p class="text-emerald-200 text-sm mt-1">Fertilizer Distribution Automation Module & Sugarcane Farmers Analytics</p>
            <div class="flex flex-wrap items-center gap-4 text-xs text-emerald-300 mt-3">
                <span><i class="fa-solid fa-building mr-1"></i> Parent: Pacific Sugar Holdings Corp. / Filinvest</span>
                <span><i class="fa-solid fa-industry mr-1"></i> Est. 1970</span>
                <span><i class="fa-solid fa-location-dot mr-1"></i> Salutillo St., Brgy. Guihing, Hagonoy, Davao del Sur</span>
            </div>
        </div>
        <div class="bg-emerald-950/60 backdrop-blur border border-emerald-700/50 px-4 py-3 rounded-xl text-center min-w-[160px]">
            <p class="text-xs text-emerald-300 font-medium">System Status</p>
            <p class="text-sm font-bold text-emerald-400 mt-0.5"><i class="fa-solid fa-circle text-xs text-emerald-400 mr-1 animate-pulse"></i> Operational</p>
        </div>
    </div>

    <!-- Quick Overview KPI Grid Header -->
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-800">System Overview KPIs</h3>
        <span class="text-xs text-gray-500 font-medium">Live operational metrics</span>
    </div>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Farmers</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg text-sm"><i class="fa-solid fa-users"></i></span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $metrics['total_farmers'] }}</p>
            <p class="text-xs text-emerald-600 font-medium mt-1"><i class="fa-solid fa-arrow-up text-xs mr-0.5"></i> Active Register</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Farmers</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg text-sm"><i class="fa-solid fa-user-check"></i></span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $metrics['active_farmers'] }}</p>
            <p class="text-xs text-gray-500 font-medium mt-1">Verified Sugarcane Planters</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Fertilizer Requests</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-lg text-sm"><i class="fa-solid fa-file-signature"></i></span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $metrics['fertilizer_requests'] }}</p>
            <p class="text-xs text-amber-600 font-medium mt-1">{{ $metrics['approved_requests'] }} approved</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Fertilizer Released</span>
                <span class="p-2 bg-purple-50 text-purple-600 rounded-lg text-sm"><i class="fa-solid fa-truck-ramp-box"></i></span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($metrics['fertilizer_distributed'], 0) }}</p>
            <p class="text-xs text-purple-600 font-medium mt-1">Auto deducted</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Low Stock Items</span>
                <span class="p-2 bg-red-50 text-red-600 rounded-lg text-sm"><i class="fa-solid fa-triangle-exclamation"></i></span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $metrics['low_stock_items'] }}</p>
            <p class="text-xs text-red-600 font-medium mt-1">Requires reorder</p>
        </div>
    </div>

    <!-- Analytics & System Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Container -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-gray-800 text-base">Monthly Operational Overview</h4>
                <span class="text-xs bg-emerald-50 text-emerald-700 font-semibold px-2.5 py-1 rounded-md">Live metrics</span>
            </div>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg border border-dashed border-gray-300">
                <canvas id="overviewChart"></canvas>
            </div>
        </div>

        <!-- Plant Outputs & Information -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
            <h4 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-2">Primary Plant Outputs</h4>
            
            <ul class="space-y-3">
                <li class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2 text-gray-700"><i class="fa-solid fa-cube text-amber-600"></i> Raw Sugar</span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-medium">Core Product</span>
                </li>
                <li class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2 text-gray-700"><i class="fa-solid fa-box text-emerald-600"></i> Refined Sugar</span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-medium">Core Product</span>
                </li>
                <li class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2 text-gray-700"><i class="fa-solid fa-flask text-purple-600"></i> Molasses</span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-medium">By-Product</span>
                </li>
                <li class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2 text-gray-700"><i class="fa-solid fa-recycle text-green-600"></i> Bagasse</span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-medium">By-Product</span>
                </li>
            </ul>

            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs text-gray-500">
                    <i class="fa-solid fa-circle-info text-emerald-600 mr-1"></i>
                    Modules: Farmer Management, Fertilizer Automation, Inventory, Sales, Trade, HR Payroll, Analytics.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('overviewChart').getContext('2d');
        const salesLabels = @json($chartData['salesLabels'] ?? []);
        const salesValues = @json($chartData['salesValues'] ?? []);
        const distributionLabels = @json($chartData['distributionLabels'] ?? []);
        const distributionValues = @json($chartData['distributionValues'] ?? []);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: distributionLabels.length ? distributionLabels : ['No data'],
                datasets: [{
                    label: 'Fertilizer Distribution',
                    data: distributionValues.length ? distributionValues : [0],
                    backgroundColor: '#059669',
                    borderRadius: 6
                }, {
                    label: 'Sales',
                    data: salesValues.length ? salesValues : [0],
                    backgroundColor: '#d97706',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endsection
