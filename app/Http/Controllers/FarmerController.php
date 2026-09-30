<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Farmer;
use App\Models\FarmerActivity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FarmerController extends Controller
{
    /**
     * Display a listing of farmers.
     */
    public function index(Request $request)
    {
        $query = Farmer::withCount('farms')->latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('farmer_code', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('barangay', 'like', "%{$search}%")
                  ->orWhere('municipality', 'like', "%{$search}%");
            });
        }

        // Municipality filter
        if ($request->filled('municipality')) {
            $query->where('municipality', $request->input('municipality'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $farmers = $query->paginate(12)->withQueryString();

        // Unique municipalities for dropdown filter
        $municipalities = Farmer::distinct()->pluck('municipality')->filter()->values();

        return view('farmers.index', compact('farmers', 'municipalities'));
    }

    /**
     * Show form to add a new farmer.
     */
    public function create()
    {
        return view('farmers.create');
    }

    /**
     * Store new farmer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Pending', 'Under Verification'])],
        ]);

        // Auto-generate farmer_code: FAR-YYYY-XXXX
        $count = Farmer::count() + 1;
        $validated['farmer_code'] = 'FAR-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $farmer = Farmer::create($validated);

        // Record Activity
        FarmerActivity::create([
            'farmer_id' => $farmer->id,
            'activity_type' => 'Farmer Registered',
            'description' => "Farmer account created with code {$farmer->farmer_code}.",
            'activity_date' => now(),
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        // Record Audit Log
        AuditLog::log('Registered new farmer', 'Farmer Management', $farmer->id, null, $farmer->toArray());

        return redirect()->route('farmers.show', $farmer)->with('success', "Farmer '{$farmer->full_name}' ({$farmer->farmer_code}) registered successfully.");
    }

    /**
     * Display farmer profile.
     */
    public function show(Farmer $farmer)
    {
        $farmer->load(['farms.productions', 'productions.farm', 'activities']);
        return view('farmers.show', compact('farmer'));
    }

    /**
     * Show edit form.
     */
    public function edit(Farmer $farmer)
    {
        return view('farmers.edit', compact('farmer'));
    }

    /**
     * Update farmer details.
     */
    public function update(Request $request, Farmer $farmer)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Pending', 'Under Verification'])],
        ]);

        $oldValue = $farmer->toArray();
        $farmer->update($validated);

        // Activity Log
        FarmerActivity::create([
            'farmer_id' => $farmer->id,
            'activity_type' => 'Profile Updated',
            'description' => "Farmer profile details were updated.",
            'activity_date' => now(),
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        AuditLog::log('Updated farmer profile', 'Farmer Management', $farmer->id, $oldValue, $farmer->toArray());

        return redirect()->route('farmers.show', $farmer)->with('success', "Farmer '{$farmer->full_name}' updated successfully.");
    }

    /**
     * Delete farmer.
     */
    public function destroy(Farmer $farmer)
    {
        $name = $farmer->full_name;
        $code = $farmer->farmer_code;

        $farmer->delete();

        AuditLog::log('Deleted farmer record', 'Farmer Management', $farmer->id, ['farmer_code' => $code, 'name' => $name], null);

        return redirect()->route('farmers.index')->with('success', "Farmer '{$name}' ({$code}) deleted successfully.");
    }
}

