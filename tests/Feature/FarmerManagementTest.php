<?php

namespace Tests\Feature;

use App\Models\Farm;
use App\Models\Farmer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_farmer_list()
    {
        $role = Role::create(['name' => 'System Administrator', 'slug' => 'admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        Farmer::create([
            'farmer_code' => 'FAR-2026-0001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'contact_number' => '09170001111',
            'address' => 'Purok 1',
            'barangay' => 'Guihing',
            'municipality' => 'Hagonoy',
            'province' => 'Davao del Sur',
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user)->get('/farmers');

        $response->assertStatus(200);
        $response->assertSee('FAR-2026-0001');
        $response->assertSee('Juan Dela Cruz');
    }

    public function test_admin_can_create_farmer_and_add_farm()
    {
        $role = Role::create(['name' => 'System Administrator', 'slug' => 'admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post('/farmers', [
            'first_name' => 'Pedro',
            'last_name' => 'Penduko',
            'contact_number' => '09180002222',
            'address' => 'Sitio Central',
            'barangay' => 'Guihing',
            'municipality' => 'Hagonoy',
            'province' => 'Davao del Sur',
            'status' => 'Active',
        ]);

        $farmer = Farmer::where('first_name', 'Pedro')->first();
        $this->assertNotNull($farmer);

        $response->assertRedirect('/farmers/' . $farmer->id);

        $farmResponse = $this->actingAs($user)->post('/farms', [
            'farmer_id' => $farmer->id,
            'farm_name' => 'Pedro Plot A',
            'location' => 'Guihing, Hagonoy',
            'farm_size' => 4.5,
            'farm_size_unit' => 'Hectares',
            'soil_type' => 'Loam',
            'status' => 'Active',
        ]);

        $this->assertDatabaseHas('farms', [
            'farmer_id' => $farmer->id,
            'farm_name' => 'Pedro Plot A',
        ]);
    }
}

