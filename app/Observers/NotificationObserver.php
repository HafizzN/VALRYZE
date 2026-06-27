<?php

namespace App\Observers;

use App\Models\Notification;
use App\Services\FcmService;

class NotificationObserver
{
    /**
     * Handle the Notification "created" event.
     */
    public function created(Notification $notification): void
    {
        $user = $notification->user;
        if ($user) {
            FcmService::sendPushNotification(
                $user,
                $notification->title,
                $notification->message,
                [
                    'notification_id' => $notification->id,
                    'type' => $notification->type,
                ]
            );
        }
    }
}
