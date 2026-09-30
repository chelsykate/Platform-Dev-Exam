<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of sales orders.
     */
    public function index(Request $request)
    {
        $query = SalesOrder::with(['customer', 'items'])->latest('order_date');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->paginate(10)->withQueryString();
        $customers = Customer::where('status', 'Active')->get();

        return view('sales.index', compact('orders', 'customers'));
    }

    /**
     * Store new sales order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['Pending', 'Processing', 'Completed', 'Cancelled'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $order = DB::transaction(function () use ($validated) {
            $count = SalesOrder::count() + 1;
            $orderCode = 'SO-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $totalAmount = 0;
            $itemsToCreate = [];

            foreach ($validated['items'] as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $subtotal = $qty * $price;
                $totalAmount += $subtotal;

                $itemsToCreate[] = [
                    'product_name' => $item['product_name'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                ];
            }

            $salesOrder = SalesOrder::create([
                'order_code' => $orderCode,
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'created_by' => auth()->user()->name ?? 'Sales Staff',
            ]);

            foreach ($itemsToCreate as $itemData) {
                $salesOrder->items()->create($itemData);
            }

            return $salesOrder;
        });

        AuditLog::log('Created sales order', 'Sales Management', $order->id, null, ['order_code' => $order->order_code, 'total_amount' => $order->total_amount]);

        return back()->with('success', "Sales order '{$order->order_code}' (Total: ₱" . number_format($order->total_amount, 2) . ") created successfully.");
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, SalesOrder $salesOrder)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Pending', 'Processing', 'Completed', 'Cancelled'])],
        ]);

        $oldStatus = $salesOrder->status;
        $salesOrder->update(['status' => $validated['status']]);

        AuditLog::log("Updated sales order status to {$validated['status']}", 'Sales Management', $salesOrder->id, ['status' => $oldStatus], ['status' => $validated['status']]);

        return back()->with('success', "Order '{$salesOrder->order_code}' status updated to '{$salesOrder->status}'.");
    }

    /**
     * Delete sales order.
     */
    public function destroy(SalesOrder $salesOrder)
    {
        $code = $salesOrder->order_code;
        $salesOrder->delete();

        AuditLog::log('Deleted sales order', 'Sales Management', $salesOrder->id, ['order_code' => $code], null);

        return back()->with('success', "Sales order '{$code}' deleted.");
    }
}
