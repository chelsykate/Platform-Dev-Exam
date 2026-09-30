<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_list()
    {
        $role = Role::create(['name' => 'System Administrator', 'slug' => 'admin']);
        $admin = User::factory()->create([
            'role_id' => $role->id,
            'status' => 'Active',
        ]);

        $response = $this->actingAs($admin)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Users & Roles Management');
    }

    public function test_admin_can_create_new_user()
    {
        $adminRole = Role::create(['name' => 'System Administrator', 'slug' => 'admin']);
        $warehouseRole = Role::create(['name' => 'Warehouse Staff', 'slug' => 'warehouse']);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'status' => 'Active',
        ]);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'John Warehouse',
            'email' => 'john@davaosugar.com',
            'password' => 'password123',
            'role_id' => $warehouseRole->id,
            'status' => 'Active',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'email' => 'john@davaosugar.com',
            'role_id' => $warehouseRole->id,
        ]);
    }

    public function test_non_admin_cannot_access_user_management()
    {
        $farmerRole = Role::create(['name' => 'Farmer', 'slug' => 'farmer']);
        $farmer = User::factory()->create([
            'role_id' => $farmerRole->id,
            'status' => 'Active',
        ]);

        $response = $this->actingAs($farmer)->get('/users');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }
}
