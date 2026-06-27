<?php

use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Http\Controllers\Api\ManagerApiController;
use App\Http\Controllers\Api\HrApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Authentication Route with brute-force protection (max 10 logins per minute)
Route::post('/login', [AttendanceApiController::class, 'login'])->middleware('throttle:10,1');

// Protected Mobile API Routes (using custom token-based API authentication & rate limiting)
Route::middleware(['auth.api', 'throttle:120,1'])->group(function () {
    Route::get('/profile', [AttendanceApiController::class, 'profile']);
    Route::post('/attendance/check-in', [AttendanceApiController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceApiController::class, 'checkOut']);
    Route::get('/attendance/history', [AttendanceApiController::class, 'history']);
    Route::get('/attendance/export', [AttendanceApiController::class, 'export']);
    Route::get('/notifications', [AttendanceApiController::class, 'notifications']);
    Route::post('/notifications/{id}/read', [AttendanceApiController::class, 'markNotificationRead']);
    Route::post('/notifications/read-all', [AttendanceApiController::class, 'markAllNotificationsRead']);
    Route::post('/profile/photo', [AttendanceApiController::class, 'updateProfilePhoto']);
    Route::post('/profile/fcm-token', [AttendanceApiController::class, 'updateFcmToken']);

    // New Employee Feature Routes
    Route::get('/announcements', [EmployeeApiController::class, 'announcements']);
    Route::get('/leave-requests', [EmployeeApiController::class, 'leaveRequests']);
    Route::post('/leave-requests', [EmployeeApiController::class, 'storeLeaveRequest']);
    Route::get('/permission-requests', [EmployeeApiController::class, 'permissionRequests']);
    Route::post('/permission-requests', [EmployeeApiController::class, 'storePermissionRequest']);
    Route::get('/overtime-requests', [EmployeeApiController::class, 'overtimeRequests']);
    Route::post('/overtime-requests', [EmployeeApiController::class, 'storeOvertimeRequest']);

    // Manager API Routes
    Route::prefix('manager')->group(function () {
        Route::get('/dashboard-stats', [ManagerApiController::class, 'dashboardStats']);
        Route::get('/my-team', [ManagerApiController::class, 'myTeam']);
        Route::get('/team-attendance', [ManagerApiController::class, 'teamAttendance']);
        Route::get('/approvals', [ManagerApiController::class, 'approvals']);
        Route::post('/approvals/{type}/{id}', [ManagerApiController::class, 'processApproval']);
    });

    // HR API Routes
    Route::prefix('hr')->group(function () {
        Route::get('/employees', [HrApiController::class, 'employees']);
        Route::get('/recruitment-onboarding', [HrApiController::class, 'recruitmentOnboarding']);
        Route::get('/payroll-summary', [HrApiController::class, 'payrollSummary']);
        Route::get('/approvals', [HrApiController::class, 'approvals']);
        Route::post('/approvals/{type}/{id}', [HrApiController::class, 'processApproval']);
        Route::get('/form-meta', [HrApiController::class, 'formMeta']);
        Route::post('/employees', [HrApiController::class, 'storeEmployee']);
        Route::get('/divisions', [HrApiController::class, 'divisions']);
        Route::post('/divisions', [HrApiController::class, 'storeDivision']);
    });
});

