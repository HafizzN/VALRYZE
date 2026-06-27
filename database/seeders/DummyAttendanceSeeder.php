<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\Division;
use App\Models\Position;
use App\Models\OfficeLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Aditya Pratama', 'Budi Santoso', 'Candra Wijaya', 'Dedi Kurniawan', 'Eko Prasetyo',
            'Fajar Nugroho', 'Gunawan Susanto', 'Hendra Wijaya', 'Irfan Hakim', 'Joko Susilo',
            'Krisna Bayu', 'Luthfi Hakim', 'Mulyadi', 'Nugroho Adhi', 'Oki Setiawan',
            'Prabowo Hidayat', 'Rian Setiadi', 'Slamet Riyadi', 'Taufik Hidayat', 'Umar Syarif',
            'Wahyu Hidayat', 'Yanto Permana', 'Zainal Abidin', 'Agus Harimurti', 'Bambang Pamungkas',
            'Dian Sastrowardoyo', 'Dewi Lestari', 'Elvira Devinamira', 'Fitri Carlina', 'Gisella Anastasia',
            'Happy Salma', 'Inul Daratista', 'Julia Perez', 'Kartika Putri', 'Luna Maya',
            'Maia Estianty', 'Nabila Syakieb', 'Ola Ramlan', 'Pevita Pearce', 'Raisa Andriana',
            'Sandra Dewi', 'Titi Kamal', 'Ussy Sulistiawaty', 'Vanesha Prescilla', 'Wulan Guritno',
            'Yuki Kato', 'Zaskia Adya Mecca', 'Anggun Sasmi', 'Agnez Monica', 'Chelsea Islan'
        ];

        $today = Carbon::today();
        $officeLocation = OfficeLocation::first() ?? OfficeLocation::create([
            'name' => 'Kantor Pusat VALRYZE',
            'latitude' => -0.925000,
            'longitude' => 100.360000,
            'radius' => 100,
            'address' => 'Jl. Sudirman No. 12, Padang, Sumatera Barat'
        ]);

        $shifts = Shift::pluck('id')->toArray();
        if (empty($shifts)) {
            $defaultShift = Shift::create([
                'name' => 'Shift Regular',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
            ]);
            $shifts = [$defaultShift->id];
        }

        // Map positions to their divisions
        $divisionPositions = [
            1 => [1, 2, 3],
            2 => [4, 5],
            3 => [6, 7]
        ];

        $createdUsers = [];

        foreach ($names as $index => $name) {
            $nikNum = 20269901 + $index;
            $nik = "NIK" . $nikNum;
            $email = "karyawan" . ($index + 1) . "@valryze.com";

            // Map division and position
            $divId = ($index % 3) + 1; // 1, 2, 3
            $posOptions = $divisionPositions[$divId] ?? [1];
            $posId = $posOptions[$index % count($posOptions)];

            $shiftId = $shifts[$index % count($shifts)];

            // Create User account
            $user = User::create([
                'nik' => $nik,
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($nik), // default password is their NIK
                'phone' => '0812' . rand(10000000, 99999999),
                'address' => 'Jl. Mawar No. ' . ($index + 1) . ', Kota Padang',
                'gender' => $index % 2 === 0 ? 'male' : 'female',
                'birth_date' => Carbon::now()->subYears(rand(22, 35))->format('Y-m-d'),
                'birth_place' => 'Padang',
                'religion' => 'Islam',
                'marital_status' => $index % 3 === 0 ? 'married' : 'single',
                'division_id' => $divId,
                'position_id' => $posId,
                'shift_id' => $shiftId,
                'join_date' => Carbon::now()->subMonths(rand(1, 24))->format('Y-m-d'),
                'employment_type' => 'permanent',
                'status' => 'active',
                'annual_leave_quota' => 12,
                'annual_leave_used' => 0
            ]);

            // Assign karyawan role
            $user->assignRole('karyawan');

            $createdUsers[] = $user;
        }

        // Create today's attendance for these 50 users
        // 40 Present, 10 Late
        shuffle($createdUsers);

        foreach ($createdUsers as $index => $user) {
            $isLate = ($index < 10); // First 10 are late, rest are present

            $shift = Shift::find($user->shift_id);
            $shiftStartTime = $shift ? Carbon::parse($shift->start_time) : Carbon::parse('08:00:00');

            if ($isLate) {
                // Check-in after start time (late by 5 to 45 minutes)
                $lateMinutes = rand(5, 45);
                $checkInTime = $shiftStartTime->copy()->addMinutes($lateMinutes);
                $status = 'late';
            } else {
                // Check-in before start time (present, early by 5 to 30 minutes)
                $earlyMinutes = rand(5, 30);
                $checkInTime = $shiftStartTime->copy()->subMinutes($earlyMinutes);
                $status = 'present';
                $lateMinutes = 0;
            }

            Attendance::create([
                'user_id' => $user->id,
                'office_location_id' => $officeLocation->id,
                'shift_id' => $user->shift_id,
                'date' => $today->format('Y-m-d'),
                'check_in_time' => $checkInTime->format('H:i:s'),
                'check_in_latitude' => $officeLocation->latitude + (rand(-50, 50) / 1000000), // Random offset near office
                'check_in_longitude' => $officeLocation->longitude + (rand(-50, 50) / 1000000),
                'check_in_address' => $officeLocation->address,
                'check_in_distance' => rand(5, 45),
                'is_fake_gps' => false,
                'status' => $status,
                'late_minutes' => $lateMinutes,
                'notes' => 'Dummy attendance record generated automatically'
            ]);
        }
    }
}
