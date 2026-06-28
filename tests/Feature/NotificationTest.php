<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test notification creation and unread count helper.
     */
    public function test_user_unread_notifications_count(): void
    {
        $user = User::factory()->create();

        // Initially 0 unread
        $this->assertEquals(0, $user->unreadNotificationsCount());

        // Create 2 notifications
        Notification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Title 1',
            'message' => 'Body 1',
            'read_at' => null,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Title 2',
            'message' => 'Body 2',
            'read_at' => now(),
        ]);

        // Should be 1 unread since one is already read
        $this->assertEquals(1, $user->unreadNotificationsCount());
    }
}
