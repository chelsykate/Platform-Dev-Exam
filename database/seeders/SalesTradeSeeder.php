<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\ExportOrder;
use App\Models\ImportOrder;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SalesTradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplier = Supplier::firstOrCreate(
            ['supplier_code' => 'SUP-1001'],
            [
                'name' => 'AgriSupply Corporation',
                'contact_person' => 'Rene Dela Vega',
                'contact_number' => '09170001234',
                'email' => 'orders@agrisupply.com',
                'address' => 'General Santos City',
                'status' => 'Active',
            ]
        );

        $customer = Customer::firstOrCreate(
            ['customer_code' => 'CUST-1001'],
            [
                'name' => 'Davao Agro Foods',
                'contact_person' => 'Ana Ramos',
                'contact_number' => '09161234567',
                'email' => 'sales@davaoagro.com',
                'address' => 'Davao City',
                'status' => 'Active',
            ]
        );

        $order = SalesOrder::firstOrCreate(
            ['order_code' => 'SO-00001'],
            [
                'customer_id' => $customer->id,
                'order_date' => '2026-09-15',
                'total_amount' => 4100.00,
                'status' => 'Completed',
                'created_by' => 1,
            ]
        );

        SalesOrderItem::firstOrCreate(
            ['sales_order_id' => $order->id, 'product_name' => 'Raw Sugar'],
            ['quantity' => 120, 'unit_price' => 20.50, 'subtotal' => 2460.00]
        );

        ImportOrder::firstOrCreate(
            ['import_code' => 'IMP-00001'],
            [
                'supplier_id' => $supplier->id,
                'order_date' => '2026-09-10',
                'expected_arrival' => '2026-09-25',
                'actual_arrival' => '2026-09-24',
                'total_cost' => 25000.00,
                'status' => 'Completed',
                'created_by' => 1,
            ]
        );

        ExportOrder::firstOrCreate(
            ['export_code' => 'EXP-00001'],
            [
                'customer_id' => $customer->id,
                'order_date' => '2026-09-12',
                'destination' => 'Singapore',
                'total_amount' => 32500.00,
                'shipment_date' => '2026-09-20',
                'delivery_date' => '2026-09-28',
                'status' => 'Completed',
                'created_by' => 1,
            ]
        );
    }
}
