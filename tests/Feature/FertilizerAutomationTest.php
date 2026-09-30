<?php

namespace Tests\Feature;

use App\Models\Farmer;
use App\Models\Fertilizer;
use App\Models\FertilizerRequest;
use App\Models\Inventory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FertilizerAutomationTest extends TestCase
{
    use RefreshDatabase;

    public function test_automated_fertilizer_release_deducts_inventory_and_prevents_negative_stock()
    {
        $role = Role::create(['name' => 'Warehouse Staff', 'slug' => 'warehouse']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $inventory = Inventory::create([
            'item_code' => 'INV-TEST-001',
            'item_name' => 'Urea Test Stock',
            'category' => 'Fertilizer',
            'quantity' => 50, // 50 bags initial stock
            'unit' => 'Bags',
            'reorder_level' => 10,
            'unit_cost' => 1000,
            'status' => 'In Stock',
        ]);

        $farmer = Farmer::create([
            'farmer_code' => 'FAR-2026-TEST',
            'first_name' => 'Test',
            'last_name' => 'Planter',
            'contact_number' => '09170009999',
            'address' => 'Purok 1',
            'barangay' => 'Guihing',
            'municipality' => 'Hagonoy',
            'province' => 'Davao del Sur',
            'status' => 'Active',
        ]);

        $fertilizer = Fertilizer::create([
            'fertilizer_code' => 'FERT-TEST',
            'name' => 'Urea Test Stock',
            'type' => 'Nitrogen',
            'unit' => 'Bags',
            'inventory_id' => $inventory->id,
            'status' => 'Active',
        ]);

        $req = FertilizerRequest::create([
            'request_code' => 'REQ-2026-TEST',
            'farmer_id' => $farmer->id,
            'fertilizer_id' => $fertilizer->id,
            'requested_quantity' => 20,
            'approved_quantity' => 20,
            'request_date' => now(),
            'status' => 'Approved',
        ]);

        // 1. Process valid release of 20 bags
        $response = $this->actingAs($user)->post('/fertilizer-distributions', [
            'request_id' => $req->id,
            'quantity' => 20,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(30, $inventory->fresh()->quantity); // 50 - 20 = 30
        $this->assertDatabaseHas('fertilizer_distributions', [
            'request_id' => $req->id,
            'quantity' => 20,
        ]);

        // 2. Test Insufficient Stock Prevention (Request 40 bags when only 30 remain)
        $req2 = FertilizerRequest::create([
            'request_code' => 'REQ-2026-TEST2',
            'farmer_id' => $farmer->id,
            'fertilizer_id' => $fertilizer->id,
            'requested_quantity' => 40,
            'approved_quantity' => 40,
            'request_date' => now(),
            'status' => 'Approved',
        ]);

        $failResponse = $this->actingAs($user)->post('/fertilizer-distributions', [
            'request_id' => $req2->id,
            'quantity' => 40,
        ]);

        $failResponse->assertSessionHas('error', 'Insufficient fertilizer stock.');
        $this->assertEquals(30, $inventory->fresh()->quantity); // Stock remains unchanged at 30
    }
}
