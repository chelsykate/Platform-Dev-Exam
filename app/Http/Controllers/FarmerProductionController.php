<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\FarmerActivity;
use App\Models\FarmerProduction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FarmerProductionController extends Controller
{
    /**
     * Store new farmer production record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => ['required', 'exists:farmers,id'],
            'farm_id' => ['required', 'exists:farms,id'],
            'crop_year' => ['required', 'string', 'max:50'],
            'planting_date' => ['required', 'date'],
            'harvest_date' => ['nullable', 'date', 'after_or_equal:planting_date'],
            'estimated_yield' => ['required', 'numeric', 'min:0'],
            'actual_yield' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Planted', 'Growing', 'Harvested', 'Completed'])],
        ]);

        $production = FarmerProduction::create($validated);

        FarmerActivity::create([
            'farmer_id' => $production->farmer_id,
            'activity_type' => 'Production Record Created',
            'description' => "Logged sugarcane crop year {$production->crop_year} (Est. Yield: {$production->estimated_yield} Tons).",
            'activity_date' => now(),
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        AuditLog::log('Created production record', 'Farmer Production', $production->id, null, $production->toArray());

        return back()->with('success', "Production record for crop year '{$production->crop_year}' added successfully.");
    }

    /**
     * Update farmer production record.
     */
    public function update(Request $request, FarmerProduction $production)
    {
        $validated = $request->validate([
            'crop_year' => ['required', 'string', 'max:50'],
            'planting_date' => ['required', 'date'],
            'harvest_date' => ['nullable', 'date', 'after_or_equal:planting_date'],
            'estimated_yield' => ['required', 'numeric', 'min:0'],
            'actual_yield' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Planted', 'Growing', 'Harvested', 'Completed'])],
        ]);

        $old = $production->toArray();
        $production->update($validated);

        FarmerActivity::create([
            'farmer_id' => $production->farmer_id,
            'activity_type' => 'Production Update',
            'description' => "Updated production crop year {$production->crop_year} (Actual Yield: " . ($production->actual_yield ?? 'Pending') . " Tons).",
            'activity_date' => now(),
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        AuditLog::log('Updated production record', 'Farmer Production', $production->id, $old, $production->toArray());

        return back()->with('success', "Production record for crop year '{$production->crop_year}' updated successfully.");
    }

    /**
     * Delete farmer production record.
     */
    public function destroy(FarmerProduction $production)
    {
        $cropYear = $production->crop_year;
        $farmerId = $production->farmer_id;
        $production->delete();

        FarmerActivity::create([
            'farmer_id' => $farmerId,
            'activity_type' => 'Production Record Deleted',
            'description' => "Deleted production record for crop year {$cropYear}.",
            'activity_date' => now(),
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        return back()->with('success', "Production record for crop year '{$cropYear}' deleted.");
    }
}

