<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Fertilizer;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FertilizerController extends Controller
{
    /**
     * Display listing of fertilizers.
     */
    public function index()
    {
        $fertilizers = Fertilizer::with('inventory')->latest()->paginate(10);
        $inventories = Inventory::where('category', 'Fertilizer')->get();

        return view('fertilizers.index', compact('fertilizers', 'inventories'));
    }

    /**
     * Store new fertilizer type in catalog.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'inventory_id' => ['nullable', 'exists:inventory,id'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $count = Fertilizer::count() + 1;
        $validated['fertilizer_code'] = 'FERT-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $fertilizer = Fertilizer::create($validated);

        AuditLog::log('Created fertilizer catalog entry', 'Fertilizers', $fertilizer->id, null, $fertilizer->toArray());

        return back()->with('success', "Fertilizer '{$fertilizer->name}' ({$fertilizer->fertilizer_code}) added successfully.");
    }

    /**
     * Update fertilizer type.
     */
    public function update(Request $request, Fertilizer $fertilizer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'inventory_id' => ['nullable', 'exists:inventory,id'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $old = $fertilizer->toArray();
        $fertilizer->update($validated);

        AuditLog::log('Updated fertilizer catalog entry', 'Fertilizers', $fertilizer->id, $old, $fertilizer->toArray());

        return back()->with('success', "Fertilizer '{$fertilizer->name}' updated successfully.");
    }

    /**
     * Delete fertilizer entry.
     */
    public function destroy(Fertilizer $fertilizer)
    {
        $name = $fertilizer->name;
        $fertilizer->delete();

        AuditLog::log('Deleted fertilizer entry', 'Fertilizers', $fertilizer->id, ['name' => $name], null);

        return back()->with('success', "Fertilizer '{$name}' deleted.");
    }
}
