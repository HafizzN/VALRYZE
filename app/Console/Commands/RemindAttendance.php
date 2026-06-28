<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Notification;
use Carbon\Carbon;

class RemindAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automatic push notification reminders for checking in and out based on shifts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->info("Running attendance reminders at {$now->toDateTimeString()}...");

        $users = User::where('status', 'active')->get();

        foreach ($users as $user) {
            $shift = $user->shift;
            if (!$shift) {
                continue;
            }

            // --- Check-in Reminder ---
            $shiftStart = Carbon::parse($shift->start_time, 'Asia/Jakarta');
            $shiftStartToday = $now->copy()->setTime($shiftStart->hour, $shiftStart->minute, 0);

            // If current time is after the shift start, but within 30 minutes after it
            $isTimeForCheckInReminder = $now->greaterThanOrEqualTo($shiftStartToday) && $now->diffInMinutes($shiftStartToday) <= 30;

            if ($isTimeForCheckInReminder) {
                // Check if user has checked in today
                $hasCheckedIn = Attendance::where('user_id', $user->id)
                    ->whereDate('date', Carbon::today('Asia/Jakarta'))
                    ->exists();

                if (!$hasCheckedIn) {
                    // Check if already reminded today for check-in
                    $alreadyReminded = Notification::where('user_id', $user->id)
                        ->where('type', 'attendance_reminder_in')
                        ->whereDate('created_at', Carbon::today('Asia/Jakarta'))
                        ->exists();

                    if (!$alreadyReminded) {
                        $this->info("Sending check-in reminder to {$user->name}...");
                        Notification::create([
                            'user_id' => $user->id,
                            'type'    => 'attendance_reminder_in',
                            'title'   => 'Pengingat Presensi Masuk ⏰',
                            'message' => 'Halo ' . explode(' ', $user->name)[0] . ', Anda belum melakukan presensi masuk untuk shift ' . $shift->name . ' hari ini. Segera lakukan presensi ya!',
                            'icon'    => 'alarm',
                            'color'   => '#F59E0B', // Amber
                        ]);
                    }
                }
            }

            // --- Check-out Reminder ---
            $shiftEnd = Carbon::parse($shift->end_time, 'Asia/Jakarta');
            $shiftEndToday = $now->copy()->setTime($shiftEnd->hour, $shiftEnd->minute, 0);

            // If current time is after the shift end, but within 30 minutes after it
            $isTimeForCheckOutReminder = $now->greaterThanOrEqualTo($shiftEndToday) && $now->diffInMinutes($shiftEndToday) <= 30;

            if ($isTimeForCheckOutReminder) {
                // Check if user checked in but has not checked out today
                $attendanceToday = Attendance::where('user_id', $user->id)
                    ->whereDate('date', Carbon::today('Asia/Jakarta'))
                    ->first();

                $needsCheckOut = $attendanceToday && is_null($attendanceToday->check_out_time);

                if ($needsCheckOut) {
                    // Check if already reminded today for check-out
                    $alreadyReminded = Notification::where('user_id', $user->id)
                        ->where('type', 'attendance_reminder_out')
                        ->whereDate('created_at', Carbon::today('Asia/Jakarta'))
                        ->exists();

                    if (!$alreadyReminded) {
                        $this->info("Sending check-out reminder to {$user->name}...");
                        Notification::create([
                            'user_id' => $user->id,
                            'type'    => 'attendance_reminder_out',
                            'title'   => 'Pengingat Presensi Pulang ⏰',
                            'message' => 'Halo ' . explode(' ', $user->name)[0] . ', jangan lupa untuk melakukan presensi pulang hari ini. Hati-hati di jalan!',
                            'icon'    => 'alarm_on',
                            'color'   => '#10B981', // Emerald
                        ]);
                    }
                }
            }
        }

        $this->info('Attendance reminders completed.');
        return 0;
    }
}
