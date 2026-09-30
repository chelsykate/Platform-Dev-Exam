<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Farmer;
use App\Models\FarmerProduction;
use App\Models\FertilizerDistribution;
use App\Models\FertilizerRequest;
use App\Models\ImportOrder;
use App\Models\Inventory;
use App\Models\ExportOrder;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_farmers' => Farmer::count(),
            'active_farmers' => Farmer::where('status', 'Active')->count(),
            'total_sales' => SalesOrder::sum('total_amount') ?? 0,
            'fertilizer_distributed' => FertilizerDistribution::sum('quantity') ?? 0,
            'low_stock_items' => Inventory::whereRaw('quantity <= reorder_level')->count(),
            'total_employees' => Employee::count(),
        ];

        $monthlySales = SalesOrder::selectRaw('strftime("%Y-%m", order_date) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get();

        $monthlyDistributions = FertilizerDistribution::selectRaw('strftime("%Y-%m", distribution_date) as month, SUM(quantity) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get();

        $farmerByMunicipality = Farmer::selectRaw('municipality, COUNT(*) as total')
            ->groupBy('municipality')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $farmSizeDistribution = Farmer::selectRaw('COALESCE(SUM(farm_size), 0) as total_size')
            ->join('farms', 'farmers.id', '=', 'farms.farmer_id')
            ->first();

        $productionByFarmer = FarmerProduction::selectRaw('farmer_id, SUM(actual_yield) as total_yield')
            ->groupBy('farmer_id')
            ->orderByDesc('total_yield')
            ->limit(5)
            ->get();

        $inventoryMovement = Inventory::selectRaw('item_name, quantity')
            ->orderBy('quantity')
            ->limit(8)
            ->get();

        $tradeMix = [
            'imports' => ImportOrder::count(),
            'exports' => ExportOrder::count(),
            'sales' => SalesOrder::count(),
        ];

        $recentSalesValues = $monthlySales->pluck('total')->map(fn ($value) => (float) $value)->all();
        $recentDistributionValues = $monthlyDistributions->pluck('total')->map(fn ($value) => (float) $value)->all();

        $averageSales = $recentSalesValues ? array_sum($recentSalesValues) / count($recentSalesValues) : 0;
        $averageDistribution = $recentDistributionValues ? array_sum($recentDistributionValues) / count($recentDistributionValues) : 0;
        $inventoryCount = Inventory::count();
        $stockRisk = $inventoryCount > 0
            ? min(100, max(0, (($metrics['low_stock_items'] / $inventoryCount) * 100) + 20))
            : 0;

        $forecast = [
            'sales_forecast' => $averageSales * 1.12,
            'fertilizer_forecast' => $averageDistribution * 1.08,
            'stock_risk' => round($stockRisk, 1),
        ];

        return view('analytics.index', compact(
            'metrics',
            'monthlySales',
            'monthlyDistributions',
            'farmerByMunicipality',
            'farmSizeDistribution',
            'productionByFarmer',
            'inventoryMovement',
            'tradeMix',
            'forecast'
        ));
    }
}
