<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\FcmService;

class TestPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test Firebase FCM push notification to all registered tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->get();

        if ($users->isEmpty()) {
            $this->error('No users found with a registered FCM token.');
            return 1;
        }

        $this->info("Found {$users->count()} user(s) with registered FCM token(s). Sending test notifications...");

        foreach ($users as $user) {
            $this->info("Sending push notification to User: {$user->name} ({$user->email})...");
            
            $success = FcmService::sendPushNotification(
                $user,
                'VALRYZE Tes Notifikasi 🔔',
                'Halo ' . explode(' ', $user->name)[0] . ', ini adalah push notification uji coba dari Laravel Cloud ke HP Anda!'
            );

            if ($success) {
                $this->info("Successfully sent notification to {$user->name}!");
            } else {
                $this->error("Failed to send notification to {$user->name}. Check Laravel logs for details.");
            }
        }

        $this->info('Done!');
        return 0;
    }
}
