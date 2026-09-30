<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\ExportOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExportOrderController extends Controller
{
    /**
     * Display a listing of export orders.
     */
    public function index(Request $request)
    {
        $query = ExportOrder::with('customer')->latest('order_date');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('export_code', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $exports = $query->paginate(10)->withQueryString();
        $customers = Customer::where('status', 'Active')->get();

        return view('trade.exports', compact('exports', 'customers'));
    }

    /**
     * Store export order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'destination' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Draft', 'Pending', 'Processing', 'Shipped', 'Delivered', 'Completed', 'Cancelled'])],
        ]);

        $count = ExportOrder::count() + 1;
        $validated['export_code'] = 'EXP-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->user()->name ?? 'Export Manager';

        $export = ExportOrder::create($validated);

        AuditLog::log('Created export order', 'Export Management', $export->id, null, $export->toArray());

        return back()->with('success', "Export order '{$export->export_code}' to {$export->destination} logged successfully.");
    }

    /**
     * Update export status.
     */
    public function updateStatus(Request $request, ExportOrder $exportOrder)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Draft', 'Pending', 'Processing', 'Shipped', 'Delivered', 'Completed', 'Cancelled'])],
        ]);

        $old = $exportOrder->status;
        $newStatus = $validated['status'];

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'Shipped' && !$exportOrder->shipment_date) {
            $updateData['shipment_date'] = now();
        } elseif (($newStatus === 'Delivered' || $newStatus === 'Completed') && !$exportOrder->delivery_date) {
            $updateData['delivery_date'] = now();
        }

        $exportOrder->update($updateData);

        AuditLog::log("Updated export order status to {$newStatus}", 'Export Management', $exportOrder->id, ['status' => $old], $updateData);

        return back()->with('success', "Export order '{$exportOrder->export_code}' status updated to '{$newStatus}'.");
    }

    /**
     * Delete export order.
     */
    public function destroy(ExportOrder $exportOrder)
    {
        $code = $exportOrder->export_code;
        $exportOrder->delete();

        AuditLog::log('Deleted export order', 'Export Management', $exportOrder->id, ['export_code' => $code], null);

        return back()->with('success', "Export order '{$code}' deleted.");
    }
}
