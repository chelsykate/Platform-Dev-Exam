<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\FarmerActivity;
use App\Models\FertilizerDistribution;
use App\Models\FertilizerRequest;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FertilizerDistributionController extends Controller
{
    /**
     * Display listing of fertilizer distributions.
     */
    public function index(Request $request)
    {
        $query = FertilizerDistribution::with(['farmer', 'fertilizer', 'request'])->latest('distribution_date');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('distribution_code', 'like', "%{$search}%")
                  ->orWhereHas('farmer', function ($fq) use ($search) {
                      $fq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('farmer_code', 'like', "%{$search}%");
                  });
            });
        }

        $distributions = $query->paginate(10)->withQueryString();

        return view('fertilizers.distributions', compact('distributions'));
    }

    /**
     * Release fertilizer with automatic inventory deduction and stock validation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_id' => ['required', 'exists:fertilizer_requests,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string'],
        ]);

        $fertilizerRequest = FertilizerRequest::with(['farmer', 'fertilizer'])->findOrFail($validated['request_id']);

        if ($fertilizerRequest->status === 'Released') {
            return back()->with('error', 'This fertilizer request has already been released.');
        }

        $fertilizer = $fertilizerRequest->fertilizer;
        $releaseQuantity = (float) $validated['quantity'];

        // Find associated inventory item
        $inventory = null;
        if ($fertilizer->inventory_id) {
            $inventory = Inventory::find($fertilizer->inventory_id);
        }

        if (!$inventory) {
            $inventory = Inventory::where('category', 'Fertilizer')
                ->where('item_name', 'like', "%{$fertilizer->name}%")
                ->first();
        }

        if (!$inventory) {
            // Fallback to first available fertilizer stock item
            $inventory = Inventory::where('category', 'Fertilizer')->first();
        }

        // Check Inventory Stock Validation Rule
        if (!$inventory || $inventory->quantity < $releaseQuantity) {
            return back()->with('error', 'Insufficient fertilizer stock.');
        }

        // Execute Database Transaction for Atomic Updates
        DB::transaction(function () use ($fertilizerRequest, $fertilizer, $inventory, $releaseQuantity, $validated) {
            // 1. Deduct Inventory Quantity
            $inventory->quantity -= $releaseQuantity;
            if ($inventory->quantity <= $inventory->reorder_level) {
                $inventory->status = 'Low Stock';
            }
            $inventory->save();

            // 2. Create Distribution Code
            $count = FertilizerDistribution::count() + 1;
            $distCode = 'DIS-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // 3. Create Distribution Record
            $distribution = FertilizerDistribution::create([
                'distribution_code' => $distCode,
                'request_id' => $fertilizerRequest->id,
                'farmer_id' => $fertilizerRequest->farmer_id,
                'fertilizer_id' => $fertilizerRequest->fertilizer_id,
                'quantity' => $releaseQuantity,
                'distribution_date' => now(),
                'released_by' => auth()->user()->name ?? 'Warehouse Officer',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // 4. Create Inventory Transaction Log
            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'transaction_type' => 'FERTILIZER_RELEASE',
                'quantity' => -$releaseQuantity,
                'reference_type' => 'FertilizerDistribution',
                'reference_id' => $distribution->id,
                'transaction_date' => now(),
                'performed_by' => auth()->user()->name ?? 'Warehouse Officer',
                'remarks' => "Automated release for distribution {$distCode} (Request: {$fertilizerRequest->request_code})",
            ]);

            // 5. Update Fertilizer Request Status to Released
            $fertilizerRequest->update([
                'status' => 'Released',
                'approved_quantity' => $releaseQuantity,
            ]);

            // 6. Update Farmer Activity Log
            FarmerActivity::create([
                'farmer_id' => $fertilizerRequest->farmer_id,
                'activity_type' => 'Fertilizer Distribution',
                'description' => "Received distribution of {$releaseQuantity} {$fertilizer->unit} of {$fertilizer->name} under code {$distCode}.",
                'activity_date' => now(),
                'created_by' => auth()->user()->name ?? 'Warehouse Officer',
            ]);

            // 7. Send Account Notification
            AppNotification::send(
                null,
                $fertilizerRequest->farmer_id,
                'Fertilizer Released',
                "Distribution {$distCode} ({$releaseQuantity} {$fertilizer->unit} of {$fertilizer->name}) has been released by warehouse.",
                'success'
            );

            // 8. Log Audit Record
            AuditLog::log(
                'Released fertilizer distribution and deducted inventory stock',
                'Fertilizer Automation',
                $distribution->id,
                null,
                [
                    'distribution_code' => $distCode,
                    'inventory_item' => $inventory->item_name,
                    'deducted_quantity' => $releaseQuantity,
                    'remaining_stock' => $inventory->quantity,
                ]
            );
        });

        return back()->with('success', "Fertilizer distribution processed successfully. Inventory stock automatically updated.");
    }
}
