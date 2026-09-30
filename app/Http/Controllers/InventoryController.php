<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request)
    {
        $query = Inventory::with('supplier')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('low_stock') && $request->boolean('low_stock')) {
            $query->whereColumn('quantity', '<=', 'reorder_level');
        }

        $items = $query->paginate(10)->withQueryString();
        $suppliers = Supplier::where('status', 'Active')->get();
        $categories = Inventory::distinct()->pluck('category')->filter()->values();

        return view('inventory.index', compact('items', 'suppliers', 'categories'));
    }

    /**
     * Store a new inventory item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'item_type' => ['nullable', 'string', 'max:100'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $count = Inventory::count() + 1;
        $validated['item_code'] = 'INV-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validated['status'] = $validated['quantity'] <= $validated['reorder_level'] ? ($validated['quantity'] == 0 ? 'Out of Stock' : 'Low Stock') : 'In Stock';

        $item = DB::transaction(function () use ($validated) {
            $item = Inventory::create($validated);

            if ($item->quantity > 0) {
                InventoryTransaction::create([
                    'inventory_id' => $item->id,
                    'transaction_type' => 'STOCK_IN',
                    'quantity' => $item->quantity,
                    'transaction_date' => now(),
                    'performed_by' => auth()->user()->name ?? 'System',
                    'remarks' => 'Initial stock inventory opening balance.',
                ]);
            }

            return $item;
        });

        AuditLog::log('Added inventory item', 'Inventory Management', $item->id, null, $item->toArray());

        return back()->with('success', "Inventory item '{$item->item_name}' ({$item->item_code}) added successfully.");
    }

    /**
     * View inventory item details and transaction ledger.
     */
    public function show(Inventory $inventory)
    {
        $inventory->load(['supplier', 'transactions']);
        return view('inventory.show', compact('inventory'));
    }

    /**
     * Process Stock-In.
     */
    public function stockIn(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string'],
        ]);

        $qty = (float) $validated['quantity'];

        DB::transaction(function () use ($inventory, $qty, $validated) {
            $inventory->quantity += $qty;
            $inventory->status = $inventory->quantity <= $inventory->reorder_level ? 'Low Stock' : 'In Stock';
            $inventory->save();

            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'transaction_type' => 'STOCK_IN',
                'quantity' => $qty,
                'transaction_date' => now(),
                'performed_by' => auth()->user()->name ?? 'Warehouse Staff',
                'remarks' => $validated['remarks'] ?? 'Manual Stock-In replenishment',
            ]);
        });

        AuditLog::log('Replenished inventory stock (Stock-In)', 'Inventory Management', $inventory->id, null, ['add_quantity' => $qty, 'new_balance' => $inventory->quantity]);

        return back()->with('success', "Stock-In of {$qty} {$inventory->unit} processed for '{$inventory->item_name}'.");
    }

    /**
     * Process Stock-Out with Non-Negative Validation Guard.
     */
    public function stockOut(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string'],
        ]);

        $qty = (float) $validated['quantity'];

        if ($inventory->quantity < $qty) {
            return back()->with('error', "Insufficient inventory stock. Available: {$inventory->quantity} {$inventory->unit}. Requested: {$qty} {$inventory->unit}.");
        }

        DB::transaction(function () use ($inventory, $qty, $validated) {
            $inventory->quantity -= $qty;
            if ($inventory->quantity == 0) {
                $inventory->status = 'Out of Stock';
            } elseif ($inventory->quantity <= $inventory->reorder_level) {
                $inventory->status = 'Low Stock';
            } else {
                $inventory->status = 'In Stock';
            }
            $inventory->save();

            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'transaction_type' => 'STOCK_OUT',
                'quantity' => -$qty,
                'transaction_date' => now(),
                'performed_by' => auth()->user()->name ?? 'Warehouse Staff',
                'remarks' => $validated['remarks'] ?? 'Manual Stock-Out issuance',
            ]);
        });

        // Trigger Alert Notification if Low Stock
        if ($inventory->isLowStock()) {
            AppNotification::send(null, null, 'Low Stock Alert', "Inventory item '{$inventory->item_name}' reached low-stock level ({$inventory->quantity} {$inventory->unit} remaining).", 'warning');
        }

        AuditLog::log('Issued inventory stock (Stock-Out)', 'Inventory Management', $inventory->id, null, ['deducted' => $qty, 'remaining' => $inventory->quantity]);

        return back()->with('success', "Stock-Out of {$qty} {$inventory->unit} processed for '{$inventory->item_name}'.");
    }

    /**
     * Process Stock Adjustment.
     */
    public function adjustStock(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'new_quantity' => ['required', 'numeric', 'min:0'],
            'remarks' => ['required', 'string'],
        ]);

        $newQty = (float) $validated['new_quantity'];
        $diff = $newQty - $inventory->quantity;

        DB::transaction(function () use ($inventory, $newQty, $diff, $validated) {
            $inventory->quantity = $newQty;
            $inventory->status = $inventory->quantity <= $inventory->reorder_level ? ($inventory->quantity == 0 ? 'Out of Stock' : 'Low Stock') : 'In Stock';
            $inventory->save();

            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'transaction_type' => 'ADJUSTMENT',
                'quantity' => $diff,
                'transaction_date' => now(),
                'performed_by' => auth()->user()->name ?? 'Warehouse Manager',
                'remarks' => $validated['remarks'],
            ]);
        });

        AuditLog::log('Adjusted inventory stock balance', 'Inventory Management', $inventory->id, null, ['new_balance' => $newQty]);

        return back()->with('success', "Inventory balance for '{$inventory->item_name}' adjusted to {$newQty} {$inventory->unit}.");
    }

    /**
     * Display all inventory transactions.
     */
    public function transactions(Request $request)
    {
        $query = InventoryTransaction::with('inventory')->latest('transaction_date');

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->input('type'));
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('inventory.transactions', compact('transactions'));
    }

    /**
     * Delete inventory item.
     */
    public function destroy(Inventory $inventory)
    {
        $name = $inventory->item_name;
        $inventory->delete();

        AuditLog::log('Deleted inventory item', 'Inventory Management', $inventory->id, ['item_name' => $name], null);

        return redirect()->route('inventory.index')->with('success', "Inventory item '{$name}' deleted.");
    }
}
