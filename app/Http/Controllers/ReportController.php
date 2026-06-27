<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\PermissionRequest;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $month);

        $query = Attendance::with('user.division')
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum);

        if ($request->filled('division_id')) {
            $userIds = User::where('division_id', $request->division_id)->pluck('id');
            $query->whereIn('user_id', $userIds);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Clone the query builder before applying sorting to prevent SQL Group By syntax errors
        $summaryQuery = $query->clone();

        $attendances = $query->orderBy('date')->orderBy('user_id')->paginate(30);
        $users = User::where('status', 'active')->orderBy('name')->get();
        $divisions = \App\Models\Division::where('is_active', true)->get();

        // Summary stats optimized into a single database query
        $summaryCounts = $summaryQuery
            ->selectRaw("
                COUNT(CASE WHEN status = 'present' THEN 1 END) as present,
                COUNT(CASE WHEN status = 'late' THEN 1 END) as late,
                COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent,
                COUNT(CASE WHEN status = 'leave' THEN 1 END) as leave_count,
                COUNT(CASE WHEN status = 'permission' THEN 1 END) as permission
            ")
            ->first();

        $summary = [
            'present'    => $summaryCounts->present ?? 0,
            'late'       => $summaryCounts->late ?? 0,
            'absent'     => $summaryCounts->absent ?? 0,
            'leave'      => $summaryCounts->leave_count ?? 0,
            'permission' => $summaryCounts->permission ?? 0,
        ];

        return view('reports.attendance', compact('attendances', 'users', 'divisions', 'month', 'summary'));
    }

    public function exportAttendance(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $month);

        $query = Attendance::with(['user.division', 'user.position'])
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum);

        if ($request->filled('division_id')) {
            $userIds = User::where('division_id', $request->division_id)->pluck('id');
            $query->whereIn('user_id', $userIds);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $attendances = $query->orderBy('date')->orderBy('user_id')->get();

        if ($request->get('format') === 'pdf') {
            $pdf = Pdf::loadView('reports.attendance-pdf', compact('attendances', 'month'))
                ->setPaper('a4', 'landscape');
            return $pdf->download("laporan-kehadiran-{$month}.pdf");
        }

        return Excel::download(
            new \App\Exports\AttendanceExport($attendances),
            "laporan-kehadiran-{$month}.xlsx"
        );
    }

    public function lateness(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $month);

        $lateRecords = Attendance::with('user.division')
            ->where('status', 'late')
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->orderBy('late_minutes', 'desc')
            ->paginate(20);

        return view('reports.lateness', compact('lateRecords', 'month'));
    }

    public function leave(Request $request)
    {
        $year = $request->get('year', now()->year);
        $leaves = LeaveRequest::with('user.division')
            ->whereYear('start_date', $year)
            ->where('status', 'approved')
            ->orderBy('start_date', 'desc')
            ->paginate(20);

        // Leave balance summary
        $leaveBalance = User::where('status', 'active')
            ->select(['id', 'name', 'nik', 'division_id', 'annual_leave_quota', 'annual_leave_used'])
            ->with('division')
            ->get()
            ->map(fn($u) => ['user' => $u, 'remaining' => $u->remaining_leave]);

        return view('reports.leave', compact('leaves', 'leaveBalance', 'year'));
    }

    public function permission(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $month);

        $permissions = PermissionRequest::with('user.division')
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->orderBy('date', 'desc')
            ->paginate(20);

        $stats = [
            'sick'       => PermissionRequest::whereYear('date', $year)->whereMonth('date', $monthNum)->where('permission_type', 'sick')->count(),
            'family'     => PermissionRequest::whereYear('date', $year)->whereMonth('date', $monthNum)->where('permission_type', 'family')->count(),
            'field_duty' => PermissionRequest::whereYear('date', $year)->whereMonth('date', $monthNum)->where('permission_type', 'field_duty')->count(),
            'personal'   => PermissionRequest::whereYear('date', $year)->whereMonth('date', $monthNum)->where('permission_type', 'personal')->count(),
        ];

        return view('reports.permission', compact('permissions', 'stats', 'month'));
    }

    public function gps(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $attendances = Attendance::with('user')
            ->whereDate('date', $date)
            ->whereNotNull('check_in_latitude')
            ->get();

        $googleMapsKey = config('services.google_maps.key');
        return view('reports.gps', compact('attendances', 'date', 'googleMapsKey'));
    }
}
