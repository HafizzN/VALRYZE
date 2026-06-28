<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;

class NotifyBirthdays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automatic push notifications for birthdays (today and tomorrow)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today('Asia/Jakarta');
        $tomorrow = Carbon::tomorrow('Asia/Jakarta');

        $this->info("Checking birthdays for Today ($today) and Tomorrow ($tomorrow)...");

        // 1. Check who has a birthday TODAY
        $todayUsers = User::whereMonth('birth_date', $today->month)
            ->whereDay('birth_date', $today->day)
            ->get();

        foreach ($todayUsers as $user) {
            $this->info("Today is {$user->name}'s birthday!");

            // Send congratulations directly to the birthday user
            Notification::create([
                'user_id' => $user->id,
                'type' => 'birthday',
                'title' => 'Selamat Ulang Tahun! 🎂',
                'message' => 'Selamat hari ulang tahun, ' . explode(' ', $user->name)[0] . '! Semoga sehat, panjang umur, dan sukses selalu dalam karir Anda.',
                'icon' => 'cake',
                'color' => 'pink',
            ]);
        }

        // 2. Check who has a birthday TOMORROW
        $tomorrowUsers = User::whereMonth('birth_date', $tomorrow->month)
            ->whereDay('birth_date', $tomorrow->day)
            ->get();

        if ($tomorrowUsers->isNotEmpty()) {
            // Find HRD & managers to notify them that a colleague has a birthday tomorrow
            $hrdAndManagers = User::role(['hrd', 'super_admin'])->get();

            foreach ($tomorrowUsers as $user) {
                $this->info("Tomorrow is {$user->name}'s birthday!");

                // Notify HRD and Super Admins
                foreach ($hrdAndManagers as $recipient) {
                    Notification::create([
                        'user_id' => $recipient->id,
                        'type' => 'birthday_alert',
                        'title' => 'Besok Rekan Kerja Ulang Tahun! 🎉',
                        'message' => 'Besok adalah hari ulang tahun ' . $user->name . ' (' . ($user->division->name ?? 'Karyawan') . '). Jangan lupa untuk memberikan ucapan selamat!',
                        'icon' => 'calendar_today',
                        'color' => 'blue',
                    ]);
                }
            }
        }

        $this->info('Birthday notification checks completed.');
        return 0;
    }
}
