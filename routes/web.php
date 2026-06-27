<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CompanyDocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\Master\DivisionController;
use App\Http\Controllers\Master\OfficeLocationController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\PermissionRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// ─── Redirect root to dashboard ──────────────────────────────────────────────
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// ─── Auth routes (Breeze) ─────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ─── Authenticated routes ─────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Calendar & Holidays
    Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar/holidays', [\App\Http\Controllers\CalendarController::class, 'storeHoliday'])->name('calendar.holidays.store')->middleware('role:super_admin|hrd');
    Route::delete('/calendar/holidays/{holiday}', [\App\Http\Controllers\CalendarController::class, 'destroyHoliday'])->name('calendar.holidays.destroy')->middleware('role:super_admin|hrd');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Attendance ──────────────────────────────────────────────────────────
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/check-in', [AttendanceController::class, 'checkInForm'])->name('check-in');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in.store')->middleware('throttle:5,1');
        Route::get('/check-out', [AttendanceController::class, 'checkOutForm'])->name('check-out');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out.store')->middleware('throttle:5,1');
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
    });

    // ─── Leave (Cuti) ────────────────────────────────────────────────────────
    Route::resource('leave', LeaveRequestController::class);
    Route::match(['post', 'patch', 'put'], 'leave/{leave}/approve', [LeaveRequestController::class, 'approve'])->name('leave.approve');
    Route::match(['post', 'patch', 'put'], 'leave/{leave}/reject', [LeaveRequestController::class, 'reject'])->name('leave.reject');

    // ─── Permission (Izin) ───────────────────────────────────────────────────
    Route::resource('permission', PermissionRequestController::class);
    Route::match(['post', 'patch', 'put'], 'permission/{permission}/approve', [PermissionRequestController::class, 'approve'])->name('permission.approve');
    Route::match(['post', 'patch', 'put'], 'permission/{permission}/reject', [PermissionRequestController::class, 'reject'])->name('permission.reject');

    // ─── Overtime (Lembur) ───────────────────────────────────────────────────
    Route::resource('overtime', OvertimeController::class);
    Route::match(['post', 'patch', 'put'], 'overtime/{overtime}/approve-manager', [OvertimeController::class, 'approveManager'])->name('overtime.approve-manager');
    Route::match(['post', 'patch', 'put'], 'overtime/{overtime}/approve-hrd', [OvertimeController::class, 'approveHrd'])->name('overtime.approve-hrd');
    Route::match(['post', 'patch', 'put'], 'overtime/{overtime}/reject', [OvertimeController::class, 'reject'])->name('overtime.reject');

    // ─── Letters (Surat) ─────────────────────────────────────────────────────
    Route::resource('letters', LetterController::class);
    Route::match(['post', 'patch', 'put'], 'letters/{letter}/approve', [LetterController::class, 'approve'])->name('letters.approve');
    Route::match(['post', 'patch', 'put'], 'letters/{letter}/reject', [LetterController::class, 'reject'])->name('letters.reject');
    Route::get('letters/{letter}/download', [LetterController::class, 'download'])->name('letters.download');

    // ─── Documents ───────────────────────────────────────────────────────────
    Route::resource('documents', CompanyDocumentController::class);
    Route::get('documents/{document}/download', [CompanyDocumentController::class, 'download'])->name('documents.download');

    // ─── Announcements (Pengumuman) ──────────────────────────────────────────
    Route::middleware(['role:super_admin|hrd|manager'])->group(function () {
        Route::get('announcements/create', [\App\Http\Controllers\AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('announcements', [\App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('announcements/{announcement}/edit', [\App\Http\Controllers\AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('announcements/{announcement}', [\App\Http\Controllers\AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('announcements/{announcement}', [\App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });
    Route::get('announcements', [\App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('announcements/{announcement}', [\App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');

    // ─── Employees (Karyawan) ────────────────────────────────────────────────
    Route::middleware(['role:super_admin|hrd'])->group(function () {
        Route::resource('employees', EmployeeController::class);
    });

    // ─── Master Data ─────────────────────────────────────────────────────────
    Route::middleware(['role:super_admin|hrd'])->prefix('master')->name('master.')->group(function () {
        Route::resource('divisions', DivisionController::class);
        Route::resource('positions', \App\Http\Controllers\Master\PositionController::class);
        Route::resource('shifts', \App\Http\Controllers\Master\ShiftController::class);
        Route::resource('locations', OfficeLocationController::class);
    });

    // ─── Reports ─────────────────────────────────────────────────────────────
    Route::middleware(['role:super_admin|hrd|manager'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/export', [ReportController::class, 'exportAttendance'])->name('attendance.export');
        Route::get('/lateness', [ReportController::class, 'lateness'])->name('lateness');
        Route::get('/leave', [ReportController::class, 'leave'])->name('leave');
        Route::get('/permission', [ReportController::class, 'permission'])->name('permission');
        Route::get('/gps', [ReportController::class, 'gps'])->name('gps');
    });

    // ─── Settings ────────────────────────────────────────────────────────────
    Route::middleware(['role:super_admin'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/company', [SettingController::class, 'company'])->name('company');
        Route::put('/company', [SettingController::class, 'updateCompany'])->name('company.update');
        Route::get('/audit-logs', [SettingController::class, 'auditLogs'])->name('audit-logs');
        Route::resource('/users', \App\Http\Controllers\UserManagementController::class)->names([
            'index'   => 'users.index',
            'create'  => 'users.create',
            'store'   => 'users.store',
            'edit'    => 'users.edit',
            'update'  => 'users.update',
            'destroy' => 'users.destroy',
        ]);
    });

    // ─── Notifications API ───────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('read-all');
    });

    // ─── Global Search API ───────────────────────────────────────────────────
    Route::get('/global-search', [DashboardController::class, 'search'])->name('global-search');
});
