<?php

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\FarmerActivity;
use App\Models\Fertilizer;
use App\Models\FertilizerDistribution;
use App\Models\FertilizerRequest;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FertilizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Initial Stock Inventory Items
        $invUrea = Inventory::create([
            'item_code' => 'INV-FERT-001',
            'item_name' => 'Urea (46-0-0) Fertilizer',
            'category' => 'Fertilizer',
            'item_type' => 'Nitrogen Granular',
            'quantity' => 1500.00,
            'unit' => 'Bags',
            'reorder_level' => 200.00,
            'unit_cost' => 1850.00,
            'status' => 'In Stock',
        ]);

        $invComplete = Inventory::create([
            'item_code' => 'INV-FERT-002',
            'item_name' => 'Complete Fertilizer (14-14-14)',
            'category' => 'Fertilizer',
            'item_type' => 'NPK Balanced',
            'quantity' => 1200.00,
            'unit' => 'Bags',
            'reorder_level' => 150.00,
            'unit_cost' => 1950.00,
            'status' => 'In Stock',
        ]);

        $invSulfate = Inventory::create([
            'item_code' => 'INV-FERT-003',
            'item_name' => 'Ammonium Sulfate (21-0-0)',
            'category' => 'Fertilizer',
            'item_type' => 'Nitrogen Sulfur',
            'quantity' => 800.00,
            'unit' => 'Bags',
            'reorder_level' => 100.00,
            'unit_cost' => 1400.00,
            'status' => 'In Stock',
        ]);

        $invPotash = Inventory::create([
            'item_code' => 'INV-FERT-004',
            'item_name' => 'Muriate of Potash (0-0-60)',
            'category' => 'Fertilizer',
            'item_type' => 'Potassium Granular',
            'quantity' => 50.00, // Set to low stock level for testing alerts
            'unit' => 'Bags',
            'reorder_level' => 100.00,
            'unit_cost' => 2100.00,
            'status' => 'Low Stock',
        ]);

        // Initial Stock In Transactions
        foreach ([$invUrea, $invComplete, $invSulfate, $invPotash] as $inv) {
            InventoryTransaction::create([
                'inventory_id' => $inv->id,
                'transaction_type' => 'STOCK_IN',
                'quantity' => $inv->quantity,
                'transaction_date' => now()->subMonths(3),
                'performed_by' => 'Warehouse Staff',
                'remarks' => 'Initial bulk stock-in procurement.',
            ]);
        }

        // 2. Create Fertilizer Catalog
        $f1 = Fertilizer::create([
            'fertilizer_code' => 'FERT-001',
            'name' => 'Urea (46-0-0)',
            'type' => 'Nitrogen High Grade',
            'unit' => 'Bags',
            'description' => 'High nitrogen content fertilizer essential for early vegetative sugarcane stalk elongation.',
            'inventory_id' => $invUrea->id,
            'status' => 'Active',
        ]);

        $f2 = Fertilizer::create([
            'fertilizer_code' => 'FERT-002',
            'name' => 'Complete Fertilizer (14-14-14)',
            'type' => 'Balanced NPK',
            'unit' => 'Bags',
            'description' => 'Balanced formula promoting early root establishment and tillering.',
            'inventory_id' => $invComplete->id,
            'status' => 'Active',
        ]);

        $f3 = Fertilizer::create([
            'fertilizer_code' => 'FERT-003',
            'name' => 'Ammonium Sulfate (21-0-0)',
            'type' => 'Sulfur Nitrogen',
            'unit' => 'Bags',
            'description' => 'Provides essential sulfur and nitrogen in alkaline Davao del Sur soils.',
            'inventory_id' => $invSulfate->id,
            'status' => 'Active',
        ]);

        $f4 = Fertilizer::create([
            'fertilizer_code' => 'FERT-004',
            'name' => 'Muriate of Potash (0-0-60)',
            'type' => 'Potash High Grade',
            'unit' => 'Bags',
            'description' => 'High potassium content to boost sucrose synthesis and sugar content (brix) in sugarcane stalks.',
            'inventory_id' => $invPotash->id,
            'status' => 'Active',
        ]);

        // 3. Create Sample Requests & Distributions across farmers
        $farmers = Farmer::limit(15)->get();
        $fertilizers = [$f1, $f2, $f3, $f4];
        $inventories = [$f1->id => $invUrea, $f2->id => $invComplete, $f3->id => $invSulfate, $f4->id => $invPotash];

        foreach ($farmers as $idx => $farmer) {
            $fert = $fertilizers[$idx % count($fertilizers)];
            $reqQty = 10 + (($idx % 4) * 10); // 10, 20, 30, 40 bags

            $reqCode = 'REQ-2026-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
            $isReleased = ($idx < 10); // First 10 are processed & released
            $status = $isReleased ? 'Released' : ($idx === 10 ? 'Approved' : 'Pending');

            $req = FertilizerRequest::create([
                'request_code' => $reqCode,
                'farmer_id' => $farmer->id,
                'fertilizer_id' => $fert->id,
                'requested_quantity' => $reqQty,
                'approved_quantity' => ($status === 'Pending') ? null : $reqQty,
                'request_date' => now()->subDays(15 - $idx),
                'approval_date' => ($status === 'Pending') ? null : now()->subDays(14 - $idx),
                'approved_by' => ($status === 'Pending') ? null : 'Agronomy Officer',
                'status' => $status,
                'remarks' => "Subsidy allocation for Davao Sugar Central contract planter.",
            ]);

            if ($isReleased) {
                $distCode = 'DIS-2026-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
                $targetInv = $inventories[$fert->id];

                // Deduct Inventory Stock
                $targetInv->quantity -= $reqQty;
                $targetInv->save();

                // Distribution Record
                FertilizerDistribution::create([
                    'distribution_code' => $distCode,
                    'request_id' => $req->id,
                    'farmer_id' => $farmer->id,
                    'fertilizer_id' => $fert->id,
                    'quantity' => $reqQty,
                    'distribution_date' => now()->subDays(12 - $idx),
                    'released_by' => 'Warehouse Staff',
                    'remarks' => "Released from Guihing Main Warehouse.",
                ]);

                // Inventory Transaction
                InventoryTransaction::create([
                    'inventory_id' => $targetInv->id,
                    'transaction_type' => 'FERTILIZER_RELEASE',
                    'quantity' => -$reqQty,
                    'reference_type' => 'FertilizerDistribution',
                    'transaction_date' => now()->subDays(12 - $idx),
                    'performed_by' => 'Warehouse Staff',
                    'remarks' => "Automated distribution release {$distCode}",
                ]);

                // Farmer Activity Log
                FarmerActivity::create([
                    'farmer_id' => $farmer->id,
                    'activity_type' => 'Fertilizer Distribution',
                    'description' => "Received distribution of {$reqQty} {$fert->unit} of {$fert->name} (Code: {$distCode}).",
                    'activity_date' => now()->subDays(12 - $idx),
                    'created_by' => 'Warehouse Staff',
                ]);
            }
        }
    }
}
