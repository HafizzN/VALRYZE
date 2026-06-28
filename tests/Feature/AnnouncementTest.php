<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AnnouncementTest extends TestCase
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

        $this->karyawan = User::factory()->create([
            'api_token' => 'karyawan-api-token',
        ]);
        $this->karyawan->assignRole('karyawan');
    }

    /**
     * Test HRD can create an announcement.
     */
    public function test_hrd_can_create_announcement(): void
    {
        $response = $this->actingAs($this->hrd)->post('/announcements', [
            'title' => 'Pengumuman Libur Lebaran',
            'content' => 'Libur dimulai dari tanggal 1 April.',
            'category' => 'holiday',
        ]);

        $response->assertRedirect('/announcements');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('announcements', [
            'title' => 'Pengumuman Libur Lebaran',
            'content' => 'Libur dimulai dari tanggal 1 April.',
            'category' => 'holiday',
        ]);
    }

    /**
     * Test Karyawan can retrieve announcements list via API.
     */
    public function test_karyawan_can_retrieve_announcements_via_api(): void
    {
        Announcement::create([
            'user_id' => $this->hrd->id,
            'title' => 'Rapat Bulanan',
            'content' => 'Rapat akan diadakan jam 10 pagi.',
            'category' => 'meeting',
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/announcements', [
            'Authorization' => 'Bearer karyawan-api-token'
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertCount(1, $response->json('announcements'));
    }
}
