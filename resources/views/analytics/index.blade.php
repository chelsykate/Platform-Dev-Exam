@extends('layouts.app')

@section('title', 'Analytics Overview')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Analytics Overview</h2>
            <p class="text-xs text-gray-500 mt-1">Descriptive performance analytics for sugarcane farming and operations.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Total Farmers</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $metrics['total_farmers'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Active Farmers</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $metrics['active_farmers'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Total Sales</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">₱{{ number_format($metrics['total_sales'], 2) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Fertilizer Distributed</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($metrics['fertilizer_distributed'], 2) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Low Stock Items</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $metrics['low_stock_items'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] uppercase text-gray-500 font-bold">Employees</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $metrics['total_employees'] }}</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-800">Predictive Trends</h3>
            <span class="text-[10px] uppercase tracking-wide bg-amber-50 text-amber-700 font-semibold px-2 py-1 rounded-full">Forecast</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-4">
                <p class="text-[11px] uppercase text-emerald-700 font-bold">Projected monthly sales</p>
                <p class="mt-2 text-2xl font-bold text-emerald-900">₱{{ number_format($forecast['sales_forecast'], 2) }}</p>
                <p class="mt-1 text-xs text-emerald-700">Based on rolling sales trend</p>
            </div>
            <div class="bg-amber-50 border border-amber-100 rounded-lg p-4">
                <p class="text-[11px] uppercase text-amber-700 font-bold">Projected fertilizer demand</p>
                <p class="mt-2 text-2xl font-bold text-amber-900">{{ number_format($forecast['fertilizer_forecast'], 2) }}</p>
                <p class="mt-1 text-xs text-amber-700">Demand forecast for next cycle</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-lg p-4">
                <p class="text-[11px] uppercase text-red-700 font-bold">Stock risk index</p>
                <p class="mt-2 text-2xl font-bold text-red-900">{{ $forecast['stock_risk'] }}%</p>
                <p class="mt-1 text-xs text-red-700">Items nearing reorder threshold</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Monthly Sales</h3>
            <canvas id="salesChart" height="180"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Fertilizer Distribution</h3>
            <canvas id="distributionChart" height="180"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Farmers by Municipality</h3>
            <canvas id="municipalityChart" height="180"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Inventory Quantity by Item</h3>
            <canvas id="inventoryChart" height="180"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const salesLabels = @json($monthlySales->pluck('month')->all());
        const salesValues = @json($monthlySales->pluck('total')->map(fn($value) => (float) $value)->all());
        const distributionLabels = @json($monthlyDistributions->pluck('month')->all());
        const distributionValues = @json($monthlyDistributions->pluck('total')->map(fn($value) => (float) $value)->all());
        const municipalityLabels = @json($farmerByMunicipality->pluck('municipality')->all());
        const municipalityValues = @json($farmerByMunicipality->pluck('total')->map(fn($value) => (int) $value)->all());
        const inventoryLabels = @json($inventoryMovement->pluck('item_name')->all());
        const inventoryValues = @json($inventoryMovement->pluck('quantity')->map(fn($value) => (float) $value)->all());

        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: salesLabels.length ? salesLabels : ['No data'],
                datasets: [{
                    label: 'Sales Amount',
                    data: salesValues.length ? salesValues : [0],
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.15)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        new Chart(document.getElementById('distributionChart'), {
            type: 'bar',
            data: {
                labels: distributionLabels.length ? distributionLabels : ['No data'],
                datasets: [{
                    label: 'Distributed Qty',
                    data: distributionValues.length ? distributionValues : [0],
                    backgroundColor: '#f59e0b'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        new Chart(document.getElementById('municipalityChart'), {
            type: 'doughnut',
            data: {
                labels: municipalityLabels.length ? municipalityLabels : ['No data'],
                datasets: [{
                    data: municipalityValues.length ? municipalityValues : [1],
                    backgroundColor: ['#059669', '#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ef4444']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        new Chart(document.getElementById('inventoryChart'), {
            type: 'bar',
            data: {
                labels: inventoryLabels.length ? inventoryLabels : ['No data'],
                datasets: [{
                    label: 'Inventory Quantity',
                    data: inventoryValues.length ? inventoryValues : [0],
                    backgroundColor: '#0ea5e9'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    });
</script>
@endsection
