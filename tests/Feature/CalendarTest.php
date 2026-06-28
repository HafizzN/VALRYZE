<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Holiday;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CalendarTest extends TestCase
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
     * Test HRD can create a national holiday.
     */
    public function test_hrd_can_create_holiday(): void
    {
        $response = $this->actingAs($this->hrd)->post('/calendar/holidays', [
            'name' => 'Tahun Baru',
            'date' => '2026-01-01',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $this->assertDatabaseHas('holidays', [
            'name' => 'Tahun Baru',
            'date' => '2026-01-01 00:00:00',
        ]);
    }

    /**
     * Test that standard karyawan is unauthorized to create holiday.
     */
    public function test_karyawan_cannot_create_holiday(): void
    {
        $response = $this->actingAs($this->karyawan)->post('/calendar/holidays', [
            'name' => 'Tahun Baru',
            'date' => '2026-01-01',
        ]);

        $response->assertStatus(403);
    }
}
