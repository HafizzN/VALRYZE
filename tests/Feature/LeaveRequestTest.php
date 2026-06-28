<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
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

        // Create a test division
        $this->division = Division::create(['name' => 'IT Department']);

        // Create test users
        $this->karyawan = User::factory()->create([
            'division_id' => $this->division->id,
            'annual_leave_quota' => 12,
            'annual_leave_used' => 0,
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
     * Test submitting a valid leave request via API.
     */
    public function test_karyawan_can_submit_leave_request(): void
    {
        $response = $this->postJson('/api/leave-requests', [
            'leave_type' => 'annual',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-03',
            'reason' => 'Liburan keluarga',
        ], [
            'Authorization' => 'Bearer karyawan-api-token'
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $this->karyawan->id,
            'leave_type' => 'annual',
            'status' => 'pending',
            'total_days' => 3,
        ]);
    }

    /**
     * Test that manager can approve leave request for division member.
     */
    public function test_manager_can_approve_leave_request(): void
    {
        // 1. Create a pending leave request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $this->karyawan->id,
            'leave_type' => 'annual',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-03',
            'total_days' => 3,
            'reason' => 'Liburan',
            'status' => 'pending',
        ]);

        // 2. Manager approves
        $response = $this->postJson("/api/manager/approvals/leave/{$leaveRequest->id}", [
            'action' => 'approve',
        ], [
            'Authorization' => 'Bearer manager-api-token'
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $this->assertDatabaseHas('leave_requests', [
            'id' => $leaveRequest->id,
            'status' => 'approved_manager',
            'approved_by_manager' => $this->manager->id,
        ]);
    }
}
