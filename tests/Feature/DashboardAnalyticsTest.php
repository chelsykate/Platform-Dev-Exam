<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_dashboard_loads_with_kpis(): void
    {
        $role = Role::create(['name' => 'Management', 'slug' => 'management']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get('/analytics');

        $response->assertStatus(200);
        $response->assertSee('Analytics Overview');
        $response->assertSee('Total Farmers');
        $response->assertSee('Total Sales');
        $response->assertSee('Predictive Trends');
    }

    public function test_reports_and_audit_logs_pages_load(): void
    {
        $role = Role::create(['name' => 'Management', 'slug' => 'management']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $reportsResponse = $this->actingAs($user)->get('/reports');
        $reportsResponse->assertStatus(200);
        $reportsResponse->assertSee('Operational Reports');

        $auditResponse = $this->actingAs($user)->get('/audit-logs');
        $auditResponse->assertStatus(200);
        $auditResponse->assertSee('Audit Logs');
    }

    public function test_notifications_center_loads(): void
    {
        $role = Role::create(['name' => 'Management', 'slug' => 'management']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get('/notifications');

        $response->assertStatus(200);
        $response->assertSee('Notifications');
    }
}
