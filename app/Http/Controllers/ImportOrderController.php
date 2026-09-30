<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ImportOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImportOrderController extends Controller
{
    /**
     * Display a listing of import orders.
     */
    public function index(Request $request)
    {
        $query = ImportOrder::with('supplier')->latest('order_date');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('import_code', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $imports = $query->paginate(10)->withQueryString();
        $suppliers = Supplier::where('status', 'Active')->get();

        return view('trade.imports', compact('imports', 'suppliers'));
    }

    /**
     * Store new import order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'order_date' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after_or_equal:order_date'],
            'total_cost' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Draft', 'Pending', 'Processing', 'Arriving', 'Completed', 'Cancelled'])],
        ]);

        $count = ImportOrder::count() + 1;
        $validated['import_code'] = 'IMP-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->user()->name ?? 'Trade Specialist';

        $import = ImportOrder::create($validated);

        AuditLog::log('Created import order', 'Import Management', $import->id, null, $import->toArray());

        return back()->with('success', "Import order '{$import->import_code}' logged successfully.");
    }

    /**
     * Update import status.
     */
    public function updateStatus(Request $request, ImportOrder $importOrder)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Draft', 'Pending', 'Processing', 'Arriving', 'Completed', 'Cancelled'])],
        ]);

        $old = $importOrder->status;
        $newStatus = $validated['status'];

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'Completed') {
            $updateData['actual_arrival'] = now();
        }

        $importOrder->update($updateData);

        AuditLog::log("Updated import order status to {$newStatus}", 'Import Management', $importOrder->id, ['status' => $old], $updateData);

        return back()->with('success', "Import order '{$importOrder->import_code}' status updated to '{$newStatus}'.");
    }

    /**
     * Delete import order.
     */
    public function destroy(ImportOrder $importOrder)
    {
        $code = $importOrder->import_code;
        $importOrder->delete();

        AuditLog::log('Deleted import order', 'Import Management', $importOrder->id, ['import_code' => $code], null);

        return back()->with('success', "Import order '{$code}' deleted.");
    }
}
