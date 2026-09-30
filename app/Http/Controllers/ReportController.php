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

class ReportController extends Controller
{
    public function index()
    {
        $reportData = [
            'total_farmers' => Farmer::count(),
            'active_farmers' => Farmer::where('status', 'Active')->count(),
            'fertilizer_requests' => FertilizerRequest::count(),
            'fertilizer_distributed' => FertilizerDistribution::sum('quantity') ?? 0,
            'sales_value' => SalesOrder::sum('total_amount') ?? 0,
            'low_stock_items' => Inventory::whereRaw('quantity <= reorder_level')->count(),
            'employees' => Employee::count(),
            'imports' => ImportOrder::count(),
            'exports' => ExportOrder::count(),
        ];

        $highlights = [
            'Farmers registered this cycle' => $reportData['total_farmers'],
            'Active farmers' => $reportData['active_farmers'],
            'Fertilizer requests' => $reportData['fertilizer_requests'],
            'Fertilizer distributed' => number_format($reportData['fertilizer_distributed'], 0),
            'Total sales' => '₱' . number_format($reportData['sales_value'], 2),
            'Low stock alerts' => $reportData['low_stock_items'],
        ];

        return view('reports.index', compact('reportData', 'highlights'));
    }
}
