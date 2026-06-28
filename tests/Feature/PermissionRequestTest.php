<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PermissionRequest;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $karyawan;
    protected $manager;
    protected $division;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Create standard roles
        Role::create(['name' => 'karyawan']);
        Role::create(['name' => 'manager']);

        // Create division
        $this->division = Division::create(['name' => 'Sales Department']);

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
     * Test submitting a permission request with simulated file upload.
     */
    public function test_karyawan_can_submit_permission_request(): void
    {
        $response = $this->postJson('/api/permission-requests', [
            'permission_type' => 'sick',
            'date' => '2026-07-01',
            'reason' => 'Demam tinggi',
            'attachment' => UploadedFile::fake()->create('medical_letter.pdf', 500),
        ], [
            'Authorization' => 'Bearer karyawan-api-token'
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $this->assertDatabaseHas('permission_requests', [
            'user_id' => $this->karyawan->id,
            'permission_type' => 'sick',
            'status' => 'pending',
        ]);
    }
}
