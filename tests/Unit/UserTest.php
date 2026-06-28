<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test remaining leave quota calculation helper.
     */
    public function test_remaining_leave_quota_calculation(): void
    {
        $user = new User([
            'annual_leave_quota' => 12,
            'annual_leave_used' => 4,
        ]);

        $this->assertEquals(8, $user->remaining_leave);

        // Test boundary check (remaining leave cannot be negative)
        $user->annual_leave_used = 15;
        $this->assertEquals(0, $user->remaining_leave);
    }

    /**
     * Test name initials generation helper.
     */
    public function test_initials_generation(): void
    {
        $user = new User(['name' => 'Hafizul Hanif']);
        $this->assertEquals('HH', $user->initials);

        $user2 = new User(['name' => 'Fauzi']);
        $this->assertEquals('FA', $user2->initials);

        $user3 = new User(['name' => 'Ahmad Budi Prasetyo']);
        $this->assertEquals('AB', $user3->initials);
    }

    /**
     * Test role labels translation helper.
     */
    public function test_role_label_translation(): void
    {
        $user = new User(['name' => 'John Doe']);
        
        // Default role label when user has no roles assigned
        $this->assertEquals('Karyawan', $user->role_label);
    }
}
