<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\OvertimeRequest;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OvertimeTest extends TestCase
{
    use RefreshDatabase;

    protected $karyawan;
    protected $manager;
    protected $division;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles
        Role::create(['name' => 'karyawan']);
        Role::create(['name' => 'manager']);

        // Create division
        $this->division = Division::create(['name' => 'Finance Department']);

        // Create test users
        $this->karyawan = User::factory()->create([
            'division_id' => $this->division->id,
            'api_token' => 'karyawan-api-token',
        ]);
        $this->karyawan->assignRole('karyawan');

        $this->manager = User::factory()->create([
            'division_id' => $this->division->id,
            'api_token' => 'manager-api-token',
        ]);
        $this->manager->assignRole('manager');
    }

    /**
     * Test submitting an overtime request.
     */
    public function test_karyawan_can_submit_overtime_request(): void
    {
        $response = $this->postJson('/api/overtime-requests', [
            'date' => '2026-07-01',
            'start_time' => '17:00',
            'end_time' => '20:00',
            'reason' => 'Penyusunan laporan bulanan',
        ], [
            'Authorization' => 'Bearer karyawan-api-token'
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $this->assertDatabaseHas('overtime_requests', [
            'user_id' => $this->karyawan->id,
            'reason' => 'Penyusunan laporan bulanan',
            'status' => 'pending',
        ]);
    }
}
