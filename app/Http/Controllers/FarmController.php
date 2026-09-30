<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Farm;
use App\Models\Farmer;
use App\Models\FarmerActivity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FarmController extends Controller
{
    /**
     * Store new farm attached to a farmer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => ['required', 'exists:farmers,id'],
            'farm_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'farm_size' => ['required', 'numeric', 'min:0.01'],
            'farm_size_unit' => ['required', 'string', 'max:50'],
            'soil_type' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::in(['Active', 'Fallow', 'Under Harvest', 'Inactive'])],
        ]);

        $farm = Farm::create($validated);
        $farmer = Farmer::findOrFail($farm->farmer_id);

        FarmerActivity::create([
            'farmer_id' => $farmer->id,
            'activity_type' => 'Farm Added',
            'description' => "Added farm '{$farm->farm_name}' ({$farm->farm_size} {$farm->farm_size_unit}, Soil: {$farm->soil_type}).",
            'activity_date' => now(),
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        AuditLog::log('Added new farm record', 'Farm Management', $farm->id, null, $farm->toArray());

        return back()->with('success', "Farm '{$farm->farm_name}' added to farmer profile successfully.");
    }

    /**
     * Update farm.
     */
    public function update(Request $request, Farm $farm)
    {
        $validated = $request->validate([
            'farm_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'farm_size' => ['required', 'numeric', 'min:0.01'],
            'farm_size_unit' => ['required', 'string', 'max:50'],
            'soil_type' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::in(['Active', 'Fallow', 'Under Harvest', 'Inactive'])],
        ]);

        $old = $farm->toArray();
        $farm->update($validated);

        FarmerActivity::create([
            'farmer_id' => $farm->farmer_id,
            'activity_type' => 'Farm Updated',
            'description' => "Farm '{$farm->farm_name}' parameters were updated.",
            'activity_date' => now(),
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        AuditLog::log('Updated farm details', 'Farm Management', $farm->id, $old, $farm->toArray());

        return back()->with('success', "Farm '{$farm->farm_name}' updated successfully.");
    }

    /**
     * Delete farm.
     */
    public function destroy(Farm $farm)
    {
        $name = $farm->farm_name;
        $farmerId = $farm->farmer_id;
        $farm->delete();

        FarmerActivity::create([
            'farmer_id' => $farmerId,
            'activity_type' => 'Farm Removed',
            'description' => "Farm '{$name}' was deleted.",
            'activity_date' => now(),
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        return back()->with('success', "Farm '{$name}' deleted successfully.");
    }
}

