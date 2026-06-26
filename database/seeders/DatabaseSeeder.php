<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Division;
use App\Models\OfficeLocation;
use App\Models\Position;
use App\Models\Shift;
use App\Models\User;
use App\Models\Announcement;
use App\Models\LeaveRequest;
use App\Models\PermissionRequest;
use App\Models\OvertimeRequest;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Company ────────────────────────────────────────────
        Company::create([
            'name'    => 'PT. Smart Teknologi Indonesia',
            'address' => 'Jl. HR. Rasuna Said No. 1, Jakarta Selatan',
            'phone'   => '021-12345678',
            'email'   => 'info@smarttech.co.id',
            'website' => 'https://smarttech.co.id',
        ]);

        // ─── Roles ──────────────────────────────────────────────
        $superAdmin = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $hrd        = Role::create(['name' => 'hrd', 'guard_name' => 'web']);
        $manager    = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $karyawan   = Role::create(['name' => 'karyawan', 'guard_name' => 'web']);

        // ─── Permissions ─────────────────────────────────────────
        $permissions = [
            // Employee
            'manage employees', 'view employees',
            // Attendance
            'manage attendance', 'view attendance', 'export attendance',
            // Leave
            'manage leave', 'approve leave', 'view leave',
            // Permission requests
            'manage permission', 'approve permission', 'view permission',
            // Overtime
            'manage overtime', 'approve overtime', 'view overtime',
            // Letters
            'manage letters', 'approve letters', 'view letters',
            // Documents
            'manage documents', 'view documents',
            // Announcements
            'manage announcements', 'view announcements',
            // Reports
            'view reports', 'export reports',
            // Settings
            'manage settings', 'manage users', 'manage roles', 'manage locations',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all permissions to super_admin
        $superAdmin->givePermissionTo(Permission::all());

        // HRD permissions
        $hrd->givePermissionTo([
            'manage employees', 'view employees',
            'manage attendance', 'view attendance', 'export attendance',
            'manage leave', 'approve leave', 'view leave',
            'manage permission', 'approve permission', 'view permission',
            'manage overtime', 'approve overtime', 'view overtime',
            'manage letters', 'approve letters', 'view letters',
            'manage documents', 'view documents',
            'manage announcements', 'view announcements',
            'view reports', 'export reports',
        ]);

        // Manager permissions
        $manager->givePermissionTo([
            'view employees',
            'view attendance',
            'approve leave', 'view leave',
            'approve permission', 'view permission',
            'approve overtime', 'view overtime',
            'view letters',
            'view documents',
            'view announcements',
            'view reports',
        ]);

        // Karyawan permissions
        $karyawan->givePermissionTo([
            'view attendance',
            'view leave',
            'view permission',
            'view overtime',
            'view letters',
            'view documents',
            'view announcements',
        ]);

        // ─── Divisions ──────────────────────────────────────────
        $divIT     = Division::create(['name' => 'Information Technology', 'code' => 'IT']);
        $divHR     = Division::create(['name' => 'Human Resources', 'code' => 'HR']);
        $divFin    = Division::create(['name' => 'Finance & Accounting', 'code' => 'FIN']);
        $divOps    = Division::create(['name' => 'Operations', 'code' => 'OPS']);
        $divSales  = Division::create(['name' => 'Sales & Marketing', 'code' => 'SALES']);

        // ─── Positions ──────────────────────────────────────────
        $posItMgr  = Position::create(['division_id' => $divIT->id, 'name' => 'IT Manager', 'level' => 'manager']);
        $posDevSr  = Position::create(['division_id' => $divIT->id, 'name' => 'Senior Developer', 'level' => 'supervisor']);
        $posDev    = Position::create(['division_id' => $divIT->id, 'name' => 'Developer', 'level' => 'staff']);
        
        $posHrdMgr = Position::create(['division_id' => $divHR->id, 'name' => 'HRD Manager', 'level' => 'manager']);
        $posHrdStf = Position::create(['division_id' => $divHR->id, 'name' => 'HRD Staff', 'level' => 'staff']);
        
        $posFinMgr = Position::create(['division_id' => $divFin->id, 'name' => 'Finance Manager', 'level' => 'manager']);
        $posAccStf = Position::create(['division_id' => $divFin->id, 'name' => 'Accounting Staff', 'level' => 'staff']);
        
        $posOpsStf = Position::create(['division_id' => $divOps->id, 'name' => 'Operations Staff', 'level' => 'staff']);
        $posSales  = Position::create(['division_id' => $divSales->id, 'name' => 'Sales Executive', 'level' => 'staff']);

        // ─── Shifts ─────────────────────────────────────────────
        $shiftPagi   = Shift::create(['name' => 'Pagi', 'start_time' => '08:00', 'end_time' => '17:00', 'late_tolerance_minutes' => 15, 'color' => '#3B82F6']);
        $shiftSiang  = Shift::create(['name' => 'Siang', 'start_time' => '13:20', 'end_time' => '21:00', 'late_tolerance_minutes' => 15, 'color' => '#F59E0B']);
        $shiftMalam  = Shift::create(['name' => 'Malam', 'start_time' => '21:00', 'end_time' => '06:00', 'late_tolerance_minutes' => 15, 'is_overnight' => true, 'color' => '#8B5CF6']);

        // ─── Office Location ────────────────────────────────────
        OfficeLocation::create([
            'name'          => 'Kantor Pusat',
            'address'       => 'Jl. HR. Rasuna Said No. 1, Jakarta Selatan',
            'latitude'      => -6.207474,
            'longitude'     => 106.831914,
            'radius_meters' => 100,
        ]);
        
        OfficeLocation::create([
            'name'          => 'Kantor Cabang',
            'address'       => 'Padang Utara, Kota Padang, Sumatera Barat',
            'latitude'      => -0.9183988,
            'longitude'     => 100.3681470,
            'radius_meters' => 200,
        ]);

        // ─── Users ──────────────────────────────────────────────
        // Super Admin
        $admin = User::create([
            'name'             => 'Super Administrator',
            'email'            => 'admin@smarthr.com',
            'password'         => Hash::make('password'),
            'nik'              => 'SA001',
            'phone'            => '081234567890',
            'division_id'      => $divIT->id,
            'position_id'      => $posItMgr->id,
            'shift_id'         => $shiftPagi->id,
            'join_date'        => '2020-01-01',
            'employment_type'  => 'permanent',
            'status'           => 'active',
            'annual_leave_quota' => 12,
        ]);
        $admin->assignRole('super_admin');

        // HRD
        $hrdUser = User::create([
            'name'             => 'Siti Rahayu',
            'email'            => 'hrd@smarthr.com',
            'password'         => Hash::make('password'),
            'nik'              => 'HR001',
            'phone'            => '081234567891',
            'division_id'      => $divHR->id,
            'position_id'      => $posHrdMgr->id,
            'shift_id'         => $shiftPagi->id,
            'join_date'        => '2020-03-01',
            'employment_type'  => 'permanent',
            'status'           => 'active',
            'annual_leave_quota' => 12,
        ]);
        $hrdUser->assignRole('hrd');

        // Manager
        $managerUser = User::create([
            'name'             => 'Budi Santoso',
            'email'            => 'manager@smarthr.com',
            'password'         => Hash::make('password'),
            'nik'              => 'IT002',
            'phone'            => '081234567892',
            'division_id'      => $divIT->id,
            'position_id'      => $posDevSr->id,
            'shift_id'         => $shiftPagi->id,
            'join_date'        => '2019-06-01',
            'employment_type'  => 'permanent',
            'status'           => 'active',
            'annual_leave_quota' => 12,
        ]);
        $managerUser->assignRole('manager');

        // Karyawan 1 (Ahmad Fauzi)
        $employee1 = User::create([
            'name'             => 'Ahmad Fauzi',
            'email'            => 'karyawan@smarthr.com',
            'password'         => Hash::make('password'),
            'nik'              => 'IT003',
            'phone'            => '081234567893',
            'division_id'      => $divIT->id,
            'position_id'      => $posDev->id,
            'shift_id'         => $shiftSiang->id, // Menggunakan Shift Siang agar cocok dengan uji coba absensi
            'join_date'        => '2021-09-01',
            'employment_type'  => 'permanent',
            'status'           => 'active',
            'annual_leave_quota' => 12,
        ]);
        $employee1->assignRole('karyawan');

        // Karyawan 2 (Rian Hidayat)
        $employee2 = User::create([
            'name'             => 'Rian Hidayat',
            'email'            => 'rian@smarthr.com',
            'password'         => Hash::make('password'),
            'nik'              => 'IT004',
            'phone'            => '081234567894',
            'division_id'      => $divIT->id,
            'position_id'      => $posDev->id,
            'shift_id'         => $shiftPagi->id,
            'join_date'        => '2022-01-15',
            'employment_type'  => 'contract',
            'status'           => 'active',
            'annual_leave_quota' => 12,
        ]);
        $employee2->assignRole('karyawan');

        // Karyawan 3 (Diana Putri)
        $employee3 = User::create([
            'name'             => 'Diana Putri',
            'email'            => 'diana@smarthr.com',
            'password'         => Hash::make('password'),
            'nik'              => 'FIN001',
            'phone'            => '081234567895',
            'division_id'      => $divFin->id,
            'position_id'      => $posAccStf->id,
            'shift_id'         => $shiftPagi->id,
            'join_date'        => '2022-05-10',
            'employment_type'  => 'permanent',
            'status'           => 'active',
            'annual_leave_quota' => 12,
        ]);
        $employee3->assignRole('karyawan');

        // Karyawan 4 (Reza Pratama)
        $employee4 = User::create([
            'name'             => 'Reza Pratama',
            'email'            => 'reza@smarthr.com',
            'password'         => Hash::make('password'),
            'nik'              => 'OPS001',
            'phone'            => '081234567896',
            'division_id'      => $divOps->id,
            'position_id'      => $posOpsStf->id,
            'shift_id'         => $shiftPagi->id,
            'join_date'        => '2023-02-01',
            'employment_type'  => 'contract',
            'status'           => 'active',
            'annual_leave_quota' => 12,
        ]);
        $employee4->assignRole('karyawan');

        // Karyawan 5 (Anita Sari)
        $employee5 = User::create([
            'name'             => 'Anita Sari',
            'email'            => 'anita@smarthr.com',
            'password'         => Hash::make('password'),
            'nik'              => 'SALES001',
            'phone'            => '081234567897',
            'division_id'      => $divSales->id,
            'position_id'      => $posSales->id,
            'shift_id'         => $shiftPagi->id,
            'join_date'        => '2023-06-01',
            'employment_type'  => 'permanent',
            'status'           => 'active',
            'annual_leave_quota' => 12,
        ]);
        $employee5->assignRole('karyawan');

        // ─── Announcements (Pengumuman) ────────────────────────
        Announcement::create([
            'user_id'      => $admin->id,
            'title'        => 'Pemberitahuan Sistem Baru: Smart HR Portal',
            'content'      => 'Halo Rekan-Rekan Karyawan. Hari ini kami meresmikan Smart HR Portal untuk mempermudah absensi berbasis GPS, pengajuan cuti, izin, dan lembur secara real-time. Harap download aplikasinya dan laporkan jika ada kendala sistem.',
            'category'     => 'info',
            'is_pinned'    => true,
            'published_at' => now(),
        ]);

        Announcement::create([
            'user_id'      => $hrdUser->id,
            'title'        => 'Rapat Bulanan Evaluasi Kinerja Karyawan',
            'content'      => 'Diberitahukan kepada seluruh Manager Divisi bahwa Rapat Koordinasi Evaluasi Kinerja Kuartal akan diadakan pada hari Senin depan jam 10:00 WIB via Zoom. Mohon menyiapkan materi laporan divisi masing-masing.',
            'category'     => 'meeting',
            'is_pinned'    => false,
            'published_at' => now(),
        ]);

        Announcement::create([
            'user_id'      => $hrdUser->id,
            'title'        => 'Kebijakan Libur Hari Raya Keagamaan',
            'content'      => 'Merujuk pada keputusan direksi, kantor PT. Smart Teknologi Indonesia akan diliburkan selama periode cuti bersama hari raya keagamaan nasional. Operasional kantor akan berjalan kembali seperti biasa pasca periode libur berakhir.',
            'category'     => 'holiday',
            'is_pinned'    => false,
            'published_at' => now(),
        ]);

        // ─── Pending Requests (Cuti, Izin, Lembur) ─────────────
        // 1. Cuti
        LeaveRequest::create([
            'user_id'      => $employee3->id, // Diana
            'leave_type'   => 'annual',
            'start_date'   => Carbon::today()->addDays(5),
            'end_date'     => Carbon::today()->addDays(7),
            'total_days'   => 3,
            'reason'       => 'Acara pernikahan keluarga di luar kota',
            'status'       => 'pending',
        ]);

        // 2. Izin
        PermissionRequest::create([
            'user_id'         => $employee2->id, // Rian
            'permission_type' => 'personal',
            'date'            => Carbon::today()->addDay(),
            'start_time'      => '09:00',
            'end_time'        => '12:00',
            'reason'          => 'Mengantar orang tua kontrol kesehatan berkala ke rumah sakit',
            'status'          => 'pending',
        ]);

        // 3. Lembur
        OvertimeRequest::create([
            'user_id'     => $employee4->id, // Reza
            'date'        => Carbon::today()->subDay(),
            'start_time'  => '17:30',
            'end_time'    => '20:30',
            'total_hours' => 3.00,
            'reason'      => 'Menyelesaikan setup perangkat server untuk persiapan rilis sistem baru',
            'status'      => 'pending',
        ]);

        // ─── Attendance History for the past 7 days ───────────
        $usersToSeed = [$hrdUser, $managerUser, $employee1, $employee2, $employee3, $employee4, $employee5];

        for ($i = 7; $i >= 1; $i--) {
            $date = Carbon::today('Asia/Jakarta')->subDays($i);
            if ($date->isWeekend()) {
                continue; // Jangan isi absen di hari Sabtu dan Minggu
            }

            foreach ($usersToSeed as $u) {
                // Probabilitas kehadiran: 80% Hadir Tepat Waktu, 12% Terlambat, 8% Mangkir/Absen
                $rand = rand(1, 100);

                if ($rand <= 80) {
                    // Hadir Tepat Waktu
                    $checkInMin  = rand(5, 45); // Absen 5-45 menit sebelum jam mulai
                    $checkInTime = Carbon::parse($u->shift->start_time)->subMinutes($checkInMin);
                    $checkOutMin = rand(10, 40);
                    $checkOutTime = Carbon::parse($u->shift->end_time)->addMinutes($checkOutMin);

                    Attendance::create([
                        'user_id'              => $u->id,
                        'office_location_id'   => rand(1, 2),
                        'shift_id'             => $u->shift_id,
                        'date'                 => $date,
                        'check_in_time'        => $checkInTime->format('H:i:s'),
                        'check_out_time'       => $checkOutTime->format('H:i:s'),
                        'check_in_latitude'    => $u->shift_id == 2 ? -0.9183988 : -6.207474,
                        'check_in_longitude'   => $u->shift_id == 2 ? 100.3681470 : 106.831914,
                        'check_in_address'     => $u->shift_id == 2 ? 'Padang Utara, Sumatera Barat' : 'Jakarta Selatan, DKI Jakarta',
                        'check_in_distance'    => rand(5, 30),
                        'check_out_latitude'   => $u->shift_id == 2 ? -0.9183988 : -6.207474,
                        'check_out_longitude'  => $u->shift_id == 2 ? 100.3681470 : 106.831914,
                        'check_out_address'    => $u->shift_id == 2 ? 'Padang Utara, Sumatera Barat' : 'Jakarta Selatan, DKI Jakarta',
                        'check_out_distance'   => rand(5, 30),
                        'is_fake_gps'          => false,
                        'status'               => 'present',
                        'late_minutes'         => 0,
                    ]);
                } elseif ($rand <= 92) {
                    // Hadir Terlambat
                    $lateMinutes = rand(16, 60); // Terlambat 16-60 menit
                    $checkInTime = Carbon::parse($u->shift->start_time)->addMinutes($lateMinutes);
                    $checkOutMin = rand(10, 40);
                    $checkOutTime = Carbon::parse($u->shift->end_time)->addMinutes($checkOutMin);

                    Attendance::create([
                        'user_id'              => $u->id,
                        'office_location_id'   => rand(1, 2),
                        'shift_id'             => $u->shift_id,
                        'date'                 => $date,
                        'check_in_time'        => $checkInTime->format('H:i:s'),
                        'check_out_time'       => $checkOutTime->format('H:i:s'),
                        'check_in_latitude'    => $u->shift_id == 2 ? -0.9183988 : -6.207474,
                        'check_in_longitude'   => $u->shift_id == 2 ? 100.3681470 : 106.831914,
                        'check_in_address'     => $u->shift_id == 2 ? 'Padang Utara, Sumatera Barat' : 'Jakarta Selatan, DKI Jakarta',
                        'check_in_distance'    => rand(5, 30),
                        'check_out_latitude'   => $u->shift_id == 2 ? -0.9183988 : -6.207474,
                        'check_out_longitude'  => $u->shift_id == 2 ? 100.3681470 : 106.831914,
                        'check_out_address'    => $u->shift_id == 2 ? 'Padang Utara, Sumatera Barat' : 'Jakarta Selatan, DKI Jakarta',
                        'check_out_distance'   => rand(5, 30),
                        'is_fake_gps'          => false,
                        'status'               => 'late',
                        'late_minutes'         => $lateMinutes,
                    ]);
                } else {
                    // Mangkir / Absen (Tanpa Check-in/out)
                    Attendance::create([
                        'user_id'              => $u->id,
                        'office_location_id'   => rand(1, 2),
                        'shift_id'             => $u->shift_id,
                        'date'                 => $date,
                        'status'               => 'absent',
                        'late_minutes'         => 0,
                    ]);
                }
            }
        }
    }
}
