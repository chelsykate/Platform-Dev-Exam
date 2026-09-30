<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::withCount('salesOrders')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('customer_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(10)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    /**
     * Store customer.
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

        $count = Customer::count() + 1;
        $validated['customer_code'] = 'CUST-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $customer = Customer::create($validated);

        AuditLog::log('Created customer account', 'Sales Management', $customer->id, null, $customer->toArray());

        return back()->with('success', "Customer '{$customer->name}' ({$customer->customer_code}) created successfully.");
    }

    /**
     * Update customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $old = $customer->toArray();
        $customer->update($validated);

        AuditLog::log('Updated customer account', 'Sales Management', $customer->id, $old, $customer->toArray());

        return back()->with('success', "Customer '{$customer->name}' updated successfully.");
    }

    /**
     * Delete customer.
     */
    public function destroy(Customer $customer)
    {
        $name = $customer->name;
        $customer->delete();

        AuditLog::log('Deleted customer account', 'Sales Management', $customer->id, ['name' => $name], null);

        return back()->with('success', "Customer '{$name}' deleted.");
    }
}
