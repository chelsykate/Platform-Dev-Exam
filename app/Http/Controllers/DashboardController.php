<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Farmer;
use App\Models\FertilizerDistribution;
use App\Models\FertilizerRequest;
use App\Models\ImportOrder;
use App\Models\Inventory;
use App\Models\ExportOrder;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main ERP dashboard.
     */
    public function index()
    {
        $metrics = [
            'total_farmers' => Farmer::count(),
            'active_farmers' => Farmer::where('status', 'Active')->count(),
            'fertilizer_requests' => FertilizerRequest::count(),
            'approved_requests' => FertilizerRequest::whereIn('status', ['Approved', 'Ready for Release', 'Released'])->count(),
            'fertilizer_distributed' => FertilizerDistribution::sum('quantity') ?? 0,
            'low_stock_items' => Inventory::whereRaw('quantity <= reorder_level')->count(),
            'total_sales' => SalesOrder::sum('total_amount') ?? 0,
            'import_orders' => ImportOrder::count(),
            'export_orders' => ExportOrder::count(),
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

        $chartData = [
            'salesLabels' => $monthlySales->pluck('month')->all(),
            'salesValues' => $monthlySales->pluck('total')->map(fn ($value) => (float) $value)->all(),
            'distributionLabels' => $monthlyDistributions->pluck('month')->all(),
            'distributionValues' => $monthlyDistributions->pluck('total')->map(fn ($value) => (float) $value)->all(),
        ];

        return view('dashboard', compact('metrics', 'chartData'));
    }
}
