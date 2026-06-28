<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shift;
use App\Models\OfficeLocation;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $shift;
    protected $office;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Create standard roles
        Role::create(['name' => 'karyawan']);

        // Create a shift (starts 9 AM, ends 5 PM)
        $this->shift = Shift::create([
            'name' => 'Shift Pagi',
            'start_time' => '00:00:00', // Set to 00:00 to 23:59 to bypass allowed time validations during testing
            'end_time' => '23:59:00',
            'late_tolerance_minutes' => 15,
            'is_active' => true,
        ]);

        // Create an office location (Central Jakarta)
        $this->office = OfficeLocation::create([
            'name' => 'HQ Jakarta',
            'latitude' => -6.200000,
            'longitude' => 106.800000,
            'radius_meters' => 100,
            'is_active' => true,
        ]);

        // Create a user
        $this->user = User::factory()->create([
            'shift_id' => $this->shift->id,
            'api_token' => 'mock-api-token-12345',
        ]);
        $this->user->assignRole('karyawan');
    }

    /**
     * Test check-in fails when outside geofence.
     */
    public function test_check_in_fails_outside_geofence(): void
    {
        $response = $this->postJson('/api/attendance/check-in', [
            'latitude' => -7.200000, // far away from office
            'longitude' => 106.800000,
            'accuracy' => 10,
            'shift_id' => $this->shift->id,
            'photo' => UploadedFile::fake()->image('selfie.jpg'),
        ], [
            'Authorization' => 'Bearer mock-api-token-12345'
        ]);

        $response->assertStatus(400);
        $this->assertFalse($response->json('success'));
    }

    /**
     * Test successful check-in within office geofence.
     */
    public function test_check_in_success_within_geofence(): void
    {
        $response = $this->postJson('/api/attendance/check-in', [
            'latitude' => -6.200000, // HQ Jakarta coordinates
            'longitude' => 106.800000,
            'accuracy' => 10,
            'shift_id' => $this->shift->id,
            'photo' => UploadedFile::fake()->image('selfie.jpg'),
        ], [
            'Authorization' => 'Bearer mock-api-token-12345'
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        // Verify record in database
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'office_location_id' => $this->office->id,
        ]);
    }
}
