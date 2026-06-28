<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles for testing
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'hrd']);
        Role::create(['name' => 'karyawan']);
    }

    /**
     * Test that guests are redirected to login.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test that Super Admin can access the admin dashboard successfully.
     */
    public function test_super_admin_can_access_dashboard(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get('/dashboard');
        $response->assertStatus(200);
    }
}
