<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected $hrd;
    protected $karyawan;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles
        Role::create(['name' => 'hrd']);
        Role::create(['name' => 'karyawan']);

        $this->hrd = User::factory()->create();
        $this->hrd->assignRole('hrd');

        $this->karyawan = User::factory()->create();
        $this->karyawan->assignRole('karyawan');
    }

    /**
     * Test HRD can access reports index pages.
     */
    public function test_hrd_can_access_reports_pages(): void
    {
        $response = $this->actingAs($this->hrd)->get('/reports/attendance');
        $response->assertStatus(200);

        $responseLateness = $this->actingAs($this->hrd)->get('/reports/lateness');
        $responseLateness->assertStatus(200);
    }

    /**
     * Test standard karyawan is unauthorized to access reports.
     */
    public function test_karyawan_cannot_access_reports_pages(): void
    {
        $response = $this->actingAs($this->karyawan)->get('/reports/attendance');
        $response->assertStatus(403);
    }
}
