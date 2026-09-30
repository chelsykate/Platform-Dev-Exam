<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_inventory_and_low_stock_status()
    {
        $role = Role::create(['name' => 'Warehouse Staff', 'slug' => 'warehouse']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $item = Inventory::create([
            'item_code' => 'INV-0001',
            'item_name' => 'Complete Fertilizer 14-14-14',
            'category' => 'Fertilizer',
            'quantity' => 15,
            'unit' => 'Bags',
            'reorder_level' => 20, // 15 <= 20 triggers low stock
            'unit_cost' => 1900,
            'status' => 'Low Stock',
        ]);

        $response = $this->actingAs($user)->get('/inventory');

        $response->assertStatus(200);
        $response->assertSee('INV-0001');
        $response->assertSee('LOW STOCK');
    }

    public function test_stock_in_and_stock_out_operations()
    {
        $role = Role::create(['name' => 'Warehouse Staff', 'slug' => 'warehouse']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $item = Inventory::create([
            'item_code' => 'INV-0002',
            'item_name' => 'Ammonium Sulfate',
            'category' => 'Fertilizer',
            'quantity' => 100,
            'unit' => 'Bags',
            'reorder_level' => 10,
            'unit_cost' => 1400,
            'status' => 'In Stock',
        ]);

        // Stock-In +50 bags
        $this->actingAs($user)->post("/inventory/{$item->id}/stock-in", [
            'quantity' => 50,
            'remarks' => 'Procurement stock-in',
        ]);

        $this->assertEquals(150, $item->fresh()->quantity);

        // Stock-Out -30 bags
        $this->actingAs($user)->post("/inventory/{$item->id}/stock-out", [
            'quantity' => 30,
            'remarks' => 'Field distribution',
        ]);

        $this->assertEquals(120, $item->fresh()->quantity);

        // Failed Stock-Out -200 bags (Excessive)
        $failResponse = $this->actingAs($user)->post("/inventory/{$item->id}/stock-out", [
            'quantity' => 200,
            'remarks' => 'Excessive issue attempt',
        ]);

        $failResponse->assertSessionHas('error');
        $this->assertEquals(120, $item->fresh()->quantity); // Quantity remains at 120
    }

    public function test_supplier_creation()
    {
        $role = Role::create(['name' => 'System Administrator', 'slug' => 'admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post('/suppliers', [
            'name' => 'Davao Agrivet',
            'contact_person' => 'Juan Santos',
            'contact_number' => '09170001111',
            'address' => 'Digos City',
            'status' => 'Active',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Davao Agrivet',
        ]);
    }
}
