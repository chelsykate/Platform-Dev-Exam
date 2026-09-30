<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request)
    {
        $query = Supplier::withCount('inventoryItems')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('supplier_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Store new supplier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $count = Supplier::count() + 1;
        $validated['supplier_code'] = 'SUP-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $supplier = Supplier::create($validated);

        AuditLog::log('Created supplier record', 'Supplier Management', $supplier->id, null, $supplier->toArray());

        return back()->with('success', "Supplier '{$supplier->name}' ({$supplier->supplier_code}) added successfully.");
    }

    /**
     * Update supplier details.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $old = $supplier->toArray();
        $supplier->update($validated);

        AuditLog::log('Updated supplier details', 'Supplier Management', $supplier->id, $old, $supplier->toArray());

        return back()->with('success', "Supplier '{$supplier->name}' updated successfully.");
    }

    /**
     * Delete supplier.
     */
    public function destroy(Supplier $supplier)
    {
        $name = $supplier->name;
        $supplier->delete();

        AuditLog::log('Deleted supplier record', 'Supplier Management', $supplier->id, ['name' => $name], null);

        return back()->with('success', "Supplier '{$name}' deleted.");
    }
}
