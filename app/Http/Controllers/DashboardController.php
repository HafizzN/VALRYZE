<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\PermissionRequest;
use App\Models\OvertimeRequest;
use App\Models\User;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole(['super_admin', 'hrd'])) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('manager')) {
            return $this->managerDashboard();
        }

        return $this->employeeDashboard();
    }

    private function adminDashboard()
    {
        $today = Carbon::today();

        $totalEmployees   = User::where('status', 'active')->count();
        
        // Optimize today's stats into a single database query
        $todayStats = Attendance::whereDate('date', $today)
            ->selectRaw("
                COUNT(CASE WHEN status IN ('present', 'late') THEN 1 END) as present,
                COUNT(CASE WHEN status = 'late' THEN 1 END) as late,
                COUNT(CASE WHEN status = 'leave' THEN 1 END) as leave_count,
                COUNT(CASE WHEN status = 'permission' THEN 1 END) as permission
            ")
            ->first();

        $presentToday      = $todayStats->present ?? 0;
        $lateToday         = $todayStats->late ?? 0;
        $onLeaveToday      = $todayStats->leave_count ?? 0;
        $onPermissionToday = $todayStats->permission ?? 0;

        // Check if today is a weekend or a registered holiday
        $isWeekendOrHoliday = $today->isWeekend() || \App\Models\Holiday::whereDate('date', $today)->exists();
        if ($isWeekendOrHoliday) {
            $absentToday = 0;
        } else {
            $absentToday = max(0, $totalEmployees - $presentToday - $onLeaveToday - $onPermissionToday);
        }

        $pendingLeave      = LeaveRequest::where('status', 'pending')->count();
        $pendingPermission = PermissionRequest::where('status', 'pending')->count();
        $pendingOvertime   = OvertimeRequest::where('status', 'pending')->count();

        // Optimize 7 days chart data into a single database query
        $startDate = Carbon::today()->subDays(6);
        $attendanceStats = Attendance::selectRaw("
                date,
                COUNT(CASE WHEN status IN ('present', 'late') THEN 1 END) as present_count,
                COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent_count,
                COUNT(CASE WHEN status = 'late' THEN 1 END) as late_count
            ")
            ->whereDate('date', '>=', $startDate)
            ->groupBy('date')
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $stats = $attendanceStats->get($dateStr);

            $chartData[] = [
                'date'    => $date->format('d/m'),
                'present' => $stats ? $stats->present_count : 0,
                'absent'  => $stats ? $stats->absent_count : 0,
                'late'    => $stats ? $stats->late_count : 0,
            ];
        }

        // Recent activities
        $recentAttendances = Attendance::with('user')
            ->whereDate('date', $today)
            ->orderBy('check_in_time', 'desc')
            ->limit(10)
            ->get();

        $announcements = Announcement::active()->orderBy('is_pinned', 'desc')->orderBy('published_at', 'desc')->limit(5)->get();

        // Queue monitoring status
        $queueHeartbeat = \Illuminate\Support\Facades\Cache::get('queue_worker_heartbeat');
        $queueStatus = 'offline';
        if ($queueHeartbeat && (now()->timestamp - $queueHeartbeat) < 150) {
            $queueStatus = 'online';
        }

        $failedJobsCount = 0;
        try {
            $failedJobsCount = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            // Graceful fallback if table doesn't exist
        }

        return view('dashboard.admin', compact(
            'totalEmployees', 'presentToday', 'lateToday',
            'onLeaveToday', 'onPermissionToday', 'absentToday',
            'pendingLeave', 'pendingPermission', 'pendingOvertime',
            'chartData', 'recentAttendances', 'announcements',
            'queueStatus', 'failedJobsCount'
        ));
    }

    private function managerDashboard()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Division-based stats for manager
        $divisionUsers = User::where('division_id', $user->division_id)->where('status', 'active')->pluck('id');
        $presentToday  = Attendance::whereIn('user_id', $divisionUsers)->whereDate('date', $today)->whereIn('status', ['present', 'late'])->count();

        $pendingLeave    = LeaveRequest::whereIn('user_id', $divisionUsers)->where('status', 'pending')->count();
        $pendingOvertime = OvertimeRequest::whereIn('user_id', $divisionUsers)->where('status', 'pending')->count();

        $announcements = Announcement::active()->limit(5)->get();

        return view('dashboard.manager', compact('presentToday', 'pendingLeave', 'pendingOvertime', 'announcements'));
    }

    private function employeeDashboard()
    {
        $user  = Auth::user();
        $today = Carbon::today();

        $todayAttendance = $user->todayAttendance();

        // Recent attendance (last 7 records)
        $recentAttendances = $user->attendances()
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        // Monthly stats
        $monthPresent = $user->attendances()
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->whereIn('status', ['present', 'late'])
            ->count();

        $monthLate = $user->attendances()
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->where('status', 'late')
            ->count();

        $announcements = Announcement::active()
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        $pendingLeaves = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved_manager'])
            ->get()
            ->map(function($item) {
                $item->type_label = 'Cuti - ' . match($item->leave_type) {
                    'annual' => 'Tahunan',
                    'maternity' => 'Melahirkan',
                    'paternity' => 'Menemani Melahirkan',
                    'wedding' => 'Pernikahan',
                    'big_leave' => 'Cuti Besar',
                    'sick' => 'Sakit',
                    default => 'Lainnya'
                };
                return $item;
            });

        $pendingPermissions = PermissionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->get()
            ->map(function($item) {
                $item->type_label = 'Izin - ' . match($item->permission_type) {
                    'sick' => 'Sakit',
                    'family' => 'Keperluan Keluarga',
                    'field_duty' => 'Tugas Lapangan',
                    'personal' => 'Keperluan Pribadi',
                    default => 'Lainnya'
                };
                return $item;
            });

        $pendingRequests = $pendingLeaves->concat($pendingPermissions)->sortByDesc('created_at');

        return view('dashboard.employee', compact(
            'todayAttendance', 'recentAttendances',
            'monthPresent', 'monthLate',
            'announcements', 'pendingRequests'
        ));
    }

    public function search(\Illuminate\Http\Request $request)
    {
        $q = $request->query('q');
        if (empty($q)) {
            return response()->json([]);
        }

        $results = [];

        // Search Employees (Accessible by admin/hrd/manager)
        if (auth()->user()->hasRole(['super_admin', 'hrd', 'manager'])) {
            $employees = \App\Models\User::where('status', 'active')
                ->where(function($query) use ($q) {
                    $query->where('name', 'LIKE', "%{$q}%")
                          ->orWhere('nik', 'LIKE', "%{$q}%")
                          ->orWhere('email', 'LIKE', "%{$q}%");
                })
                ->limit(5)
                ->get()
                ->map(function($u) {
                    return [
                        'type' => 'Karyawan',
                        'title' => $u->name,
                        'sub' => $u->nik . ' · ' . ($u->division->name ?? 'Staff'),
                        'url' => route('employees.edit', $u->id)
                    ];
                });
            $results = array_merge($results, $employees->toArray());
        }

        // Search Divisions (Admin/HRD only)
        if (auth()->user()->hasRole(['super_admin', 'hrd'])) {
            $divisions = \App\Models\Division::where('name', 'LIKE', "%{$q}%")
                ->limit(3)
                ->get()
                ->map(function($d) {
                    return [
                        'type' => 'Divisi',
                        'title' => $d->name,
                        'sub' => 'Master Data Divisi',
                        'url' => route('master.divisions.index')
                    ];
                });
            $results = array_merge($results, $divisions->toArray());
        }

        // Search Leave Requests (Admin/HRD/Manager see all, employee sees own)
        $leavesQuery = \App\Models\LeaveRequest::with('user');
        if (!auth()->user()->hasRole(['super_admin', 'hrd', 'manager'])) {
            $leavesQuery->where('user_id', auth()->id());
        }
        $leaves = $leavesQuery->where(function($query) use ($q) {
            $query->whereHas('user', function($uq) use ($q) {
                $uq->where('name', 'LIKE', "%{$q}%");
            })->orWhere('reason', 'LIKE', "%{$q}%");
        })
        ->limit(3)
        ->get()
        ->map(function($l) {
            return [
                'type' => 'Pengajuan Cuti',
                'title' => ($l->user->name ?? 'Karyawan') . ' (' . $l->reason . ')',
                'sub' => $l->start_date->format('d M Y') . ' s/d ' . $l->end_date->format('d M Y') . ' · Status: ' . strtoupper($l->status),
                'url' => route('leave.index')
            ];
        });
        $results = array_merge($results, $leaves->toArray());

        // Search Announcements
        $announcements = \App\Models\Announcement::where('title', 'LIKE', "%{$q}%")
            ->orWhere('content', 'LIKE', "%{$q}%")
            ->limit(3)
            ->get()
            ->map(function($a) {
                return [
                    'type' => 'Pengumuman',
                    'title' => $a->title,
                    'sub' => 'Kategori: ' . strtoupper($a->category) . ' · Dipublikasikan: ' . ($a->published_at ? $a->published_at->format('d M Y') : '-'),
                    'url' => route('announcements.index')
                ];
            });
        $results = array_merge($results, $announcements->toArray());

        return response()->json($results);
    }
}
