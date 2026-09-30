<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'supplier_code' => 'SUP-001',
                'name' => 'Davao Agrivet & Chemical Supply Co.',
                'contact_person' => 'Engr. Manuel Santos',
                'contact_number' => '0917-888-1122',
                'email' => 'sales@davaoagrivet.com',
                'address' => 'Lapu-Lapu St., Digos City, Davao del Sur',
                'status' => 'Active',
            ],
            [
                'supplier_code' => 'SUP-002',
                'name' => 'Mindanao Fertilizer & Agricultural Products Inc.',
                'contact_person' => 'Ms. Rebecca Cruz',
                'contact_number' => '0918-999-2233',
                'email' => 'orders@minfertilizer.com.ph',
                'address' => 'Sasa Industrial Complex, Davao City',
                'status' => 'Active',
            ],
            [
                'supplier_code' => 'SUP-003',
                'name' => 'Pacific Sugar Milling Spare Parts & Hardware Corp.',
                'contact_person' => 'Mr. Arthur Gonzales',
                'contact_number' => '0920-555-4433',
                'email' => 'spares@pacificsugarhardware.com',
                'address' => 'Guihing Highway, Hagonoy, Davao del Sur',
                'status' => 'Active',
            ],
            [
                'supplier_code' => 'SUP-004',
                'name' => 'Southern Mindanao Crop Protection Products',
                'contact_person' => 'Dr. Fernando Reyes',
                'contact_number' => '0922-333-6677',
                'email' => 'info@cropprotectmin.com',
                'address' => 'McArthur Highway, Matanao, Davao del Sur',
                'status' => 'Active',
            ],
        ];

        foreach ($suppliers as $sup) {
            $created = Supplier::firstOrCreate(['supplier_code' => $sup['supplier_code']], $sup);
            
            // Attach supplier to inventory items
            if ($created->supplier_code === 'SUP-001' || $created->supplier_code === 'SUP-002') {
                Inventory::where('category', 'Fertilizer')->update(['supplier_id' => $created->id]);
            }
        }
    }
}
