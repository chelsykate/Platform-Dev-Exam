<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesTradeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_and_trade_pages_load_and_orders_can_be_created(): void
    {
        $role = Role::create(['name' => 'Sales and Distribution Staff', 'slug' => 'sales']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $customersResponse = $this->actingAs($user)->get('/customers');
        $customersResponse->assertStatus(200);

        $salesResponse = $this->actingAs($user)->get('/sales-orders');
        $salesResponse->assertStatus(200);

        $importResponse = $this->actingAs($user)->get('/import-orders');
        $importResponse->assertStatus(200);

        $exportResponse = $this->actingAs($user)->get('/export-orders');
        $exportResponse->assertStatus(200);

        $customer = Customer::create([
            'customer_code' => 'CUST-1001',
            'name' => 'Davao Agro Foods',
            'contact_person' => 'Ana Ramos',
            'contact_number' => '09161234567',
            'email' => 'sales@davaoagro.com',
            'address' => 'Davao City',
            'status' => 'Active',
        ]);

        $supplier = Supplier::create([
            'supplier_code' => 'SUP-1001',
            'name' => 'Agrimax Trading',
            'contact_person' => 'Rene Dela Vega',
            'contact_number' => '09170001234',
            'email' => 'supplier@agrimax.com',
            'address' => 'General Santos City',
            'status' => 'Active',
        ]);

        $salesOrderResponse = $this->actingAs($user)->from('/sales-orders')->post('/sales-orders', [
            'customer_id' => $customer->id,
            'order_date' => '2026-09-25',
            'status' => 'Pending',
            'items' => [
                ['product_name' => 'Raw Sugar', 'quantity' => 120, 'unit_price' => 20.5],
                ['product_name' => 'Molasses', 'quantity' => 80, 'unit_price' => 15],
            ],
        ]);

        $salesOrderResponse->assertRedirect('/sales-orders');
        $this->assertDatabaseHas('sales_orders', ['customer_id' => $customer->id, 'status' => 'Pending']);

        $importOrderResponse = $this->actingAs($user)->from('/import-orders')->post('/import-orders', [
            'supplier_id' => $supplier->id,
            'order_date' => '2026-09-20',
            'expected_arrival' => '2026-10-05',
            'status' => 'Pending',
            'total_cost' => 25000,
        ]);

        $importOrderResponse->assertRedirect('/import-orders');

        $exportOrderResponse = $this->actingAs($user)->from('/export-orders')->post('/export-orders', [
            'customer_id' => $customer->id,
            'order_date' => '2026-09-21',
            'destination' => 'Singapore',
            'shipment_date' => '2026-10-02',
            'status' => 'Pending',
            'total_amount' => 32500,
        ]);

        $exportOrderResponse->assertRedirect('/export-orders');
    }
}
