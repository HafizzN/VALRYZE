<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Division;
use App\Models\OfficeLocation;
use App\Models\Position;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

        // ─── Shifts ─────────────────────────────────────────────
        $shiftPagi   = Shift::create(['name' => 'Pagi', 'start_time' => '08:00', 'end_time' => '17:00', 'late_tolerance_minutes' => 15, 'color' => '#3B82F6']);
        $shiftSiang  = Shift::create(['name' => 'Siang', 'start_time' => '12:00', 'end_time' => '21:00', 'late_tolerance_minutes' => 15, 'color' => '#F59E0B']);
        $shiftMalam  = Shift::create(['name' => 'Malam', 'start_time' => '21:00', 'end_time' => '06:00', 'late_tolerance_minutes' => 15, 'is_overnight' => true, 'color' => '#8B5CF6']);

        // ─── Office Location ────────────────────────────────────
        OfficeLocation::create([
            'name'          => 'Kantor Pusat',
            'address'       => 'Jl. HR. Rasuna Said No. 1, Jakarta Selatan',
            'latitude'      => -6.207474,
            'longitude'     => 106.831914,
            'radius_meters' => 100,
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

        // Karyawan
        $employee = User::create([
            'name'             => 'Ahmad Fauzi',
            'email'            => 'karyawan@smarthr.com',
            'password'         => Hash::make('password'),
            'nik'              => 'IT003',
            'phone'            => '081234567893',
            'division_id'      => $divIT->id,
            'position_id'      => $posDev->id,
            'shift_id'         => $shiftPagi->id,
            'join_date'        => '2021-09-01',
            'employment_type'  => 'permanent',
            'status'           => 'active',
            'annual_leave_quota' => 12,
        ]);
        $employee->assignRole('karyawan');
    }
}
