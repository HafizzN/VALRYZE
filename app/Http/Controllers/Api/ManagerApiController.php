<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\PermissionRequest;
use App\Models\OvertimeRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerApiController extends Controller
{
    /**
     * Middleware to verify manager role.
     */
    private function checkManager()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('manager')) {
            abort(response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Manager yang dapat mengakses fitur ini.'
            ], 403));
        }
        return $user;
    }

    /**
     * Get dashboard stats for manager's team.
     */
    public function dashboardStats()
    {
        $manager = $this->checkManager();
        $divisionId = $manager->division_id;

        if (!$divisionId) {
            return response()->json([
                'success' => true,
                'stats' => [
                    'team_size' => 0,
                    'present_today' => 0,
                    'late_today' => 0,
                    'on_leave_today' => 0,
                    'pending_approvals_count' => 0
                ]
            ]);
        }

        // Get team members (excluding manager themselves)
        $teamUserIds = User::where('division_id', $divisionId)
            ->where('id', '!=', $manager->id)
            ->pluck('id')
            ->toArray();

        $teamSize = count($teamUserIds);

        // Attendance stats today
        $presentToday = Attendance::whereIn('user_id', $teamUserIds)
            ->whereDate('date', Carbon::today())
            ->whereIn('status', ['present', 'late'])
            ->count();

        $lateToday = Attendance::whereIn('user_id', $teamUserIds)
            ->whereDate('date', Carbon::today())
            ->where('status', 'late')
            ->count();

        // Active leaves today
        $onLeaveToday = LeaveRequest::whereIn('user_id', $teamUserIds)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->count();

        // Pending approvals count for this division
        $pendingLeaves = LeaveRequest::whereIn('user_id', $teamUserIds)
            ->where('status', 'pending')
            ->count();

        $pendingPermissions = PermissionRequest::whereIn('user_id', $teamUserIds)
            ->where('status', 'pending')
            ->count();

        $pendingOvertimes = OvertimeRequest::whereIn('user_id', $teamUserIds)
            ->where('status', 'pending')
            ->count();

        $pendingApprovalsCount = $pendingLeaves + $pendingPermissions + $pendingOvertimes;

        return response()->json([
            'success' => true,
            'stats' => [
                'team_size' => $teamSize,
                'present_today' => $presentToday,
                'late_today' => $lateToday,
                'on_leave_today' => $onLeaveToday,
                'pending_approvals_count' => $pendingApprovalsCount,
            ]
        ]);
    }

    /**
     * Get manager's team directory.
     */
    public function myTeam()
    {
        $manager = $this->checkManager();
        $divisionId = $manager->division_id;

        if (!$divisionId) {
            return response()->json([
                'success' => true,
                'team' => []
            ]);
        }

        $team = User::with(['position', 'shift'])
            ->where('division_id', $divisionId)
            ->where('id', '!=', $manager->id)
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'nik' => $u->nik ?? '-',
                    'name' => $u->name,
                    'email' => $u->email,
                    'phone' => $u->phone ?? '-',
                    'photo_url' => $u->photo_url,
                    'position' => $u->position->name ?? '-',
                    'employment_type' => match($u->employment_type) {
                        'permanent' => 'Permanen',
                        'contract' => 'Kontrak',
                        'internship' => 'Magang',
                        'freelance' => 'Pekerja Lepas',
                        default => ucfirst($u->employment_type),
                    },
                    'shift_name' => $u->shift->name ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'team' => $team
        ]);
    }

    /**
     * Get real-time team attendance status for today.
     */
    public function teamAttendance()
    {
        $manager = $this->checkManager();
        $divisionId = $manager->division_id;

        if (!$divisionId) {
            return response()->json([
                'success' => true,
                'attendance' => []
            ]);
        }

        $teamUsers = User::with(['position'])
            ->where('division_id', $divisionId)
            ->where('id', '!=', $manager->id)
            ->get();

        $today = Carbon::today();
        $userIds = $teamUsers->pluck('id');

        // Bug fix #2: load all related data in bulk (3 queries) instead of N*3 queries
        $allAttendances  = Attendance::whereIn('user_id', $userIds)
            ->whereDate('date', $today)->get()->keyBy('user_id');
        $allLeaves       = LeaveRequest::whereIn('user_id', $userIds)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()->keyBy('user_id');
        $allPermissions  = PermissionRequest::whereIn('user_id', $userIds)
            ->where('status', 'approved')
            ->whereDate('date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->get()->keyBy('user_id');

        $attendanceData = [];

        foreach ($teamUsers as $u) {
            $att          = $allAttendances->get($u->id);
            $onLeave      = $allLeaves->get($u->id);
            $onPermission = $allPermissions->get($u->id);

            $status = 'absent';
            $statusLabel = 'Belum Absen';

            if ($att) {
                $status = $att->status;
                $statusLabel = match($att->status) {
                    'present'    => 'Hadir',
                    'late'       => 'Terlambat',
                    'absent'     => 'Mangkir',
                    'leave'      => 'Cuti',
                    'permission' => 'Izin',
                    'sick'       => 'Sakit',
                    default      => ucfirst($att->status),
                };
            } elseif ($onLeave) {
                $status = 'leave';
                $statusLabel = 'Cuti (' . match($onLeave->leave_type) {
                    'annual' => 'Tahunan',
                    'sick'   => 'Sakit',
                    default  => 'Khusus',
                } . ')';
            } elseif ($onPermission) {
                $status = 'permission';
                $statusLabel = 'Izin (' . match($onPermission->permission_type) {
                    'sick'       => 'Sakit',
                    'family'     => 'Keperluan Keluarga',
                    'field_duty' => 'Dinas Luar',
                    'personal'   => 'Pribadi',
                    default      => 'Lainnya',
                } . ')';
            }

            $attendanceData[] = [
                'user_id'          => $u->id,
                'name'             => $u->name,
                'photo_url'        => $u->photo_url,
                'position'         => $u->position->name ?? '-',
                'status'           => $status,
                'status_label'     => $statusLabel,
                'check_in'         => $att && $att->check_in_time  ? Carbon::parse($att->check_in_time)->format('H:i')  : null,
                'check_out'        => $att && $att->check_out_time ? Carbon::parse($att->check_out_time)->format('H:i') : null,
                'check_in_photo'   => $att && $att->check_in_photo  ? asset('storage/' . $att->check_in_photo)  : null,
                'check_out_photo'  => $att && $att->check_out_photo ? asset('storage/' . $att->check_out_photo) : null,
                'check_in_address'  => $att ? $att->check_in_address  : null,
                'check_out_address' => $att ? $att->check_out_address : null,
            ];
        }

        return response()->json([
            'success'    => true,
            'attendance' => $attendanceData
        ]);
    }

    /**
     * Get pending approvals for manager's division.
     */
    public function approvals()
    {
        $manager = $this->checkManager();
        $divisionId = $manager->division_id;

        if (!$divisionId) {
            return response()->json([
                'success' => true,
                'approvals' => []
            ]);
        }

        $teamUserIds = User::where('division_id', $divisionId)
            ->where('id', '!=', $manager->id)
            ->pluck('id')
            ->toArray();

        $leaves = LeaveRequest::with(['user.position'])
            ->whereIn('user_id', $teamUserIds)
            ->where('status', 'pending')
            ->get()
            ->map(function ($l) {
                return [
                    'id' => $l->id,
                    'type' => 'leave',
                    'type_label' => 'Cuti',
                    'employee_name' => $l->user->name,
                    'employee_photo' => $l->user->photo_url,
                    'position' => $l->user->position->name ?? '-',
                    'title' => match($l->leave_type) {
                        'annual' => 'Cuti Tahunan',
                        'sick' => 'Cuti Sakit',
                        'maternity' => 'Cuti Melahirkan',
                        default => 'Cuti Khusus',
                    },
                    'start_date' => Carbon::parse($l->start_date)->format('d M Y'),
                    'end_date' => Carbon::parse($l->end_date)->format('d M Y'),
                    'duration' => "{$l->total_days} hari",
                    'reason' => $l->reason,
                    'attachment_url' => $l->attachment ? asset('storage/' . $l->attachment) : null,
                    'created_at' => $l->created_at->diffForHumans(),
                ];
            });

        $permissions = PermissionRequest::with(['user.position'])
            ->whereIn('user_id', $teamUserIds)
            ->where('status', 'pending')
            ->get()
            ->map(function ($p) {
                $duration = '1 hari';
                if ($p->end_date) {
                    $diff = Carbon::parse($p->date)->diffInDays(Carbon::parse($p->end_date)) + 1;
                    $duration = "{$diff} hari";
                }
                return [
                    'id' => $p->id,
                    'type' => 'permission',
                    'type_label' => 'Izin',
                    'employee_name' => $p->user->name,
                    'employee_photo' => $p->user->photo_url,
                    'position' => $p->user->position->name ?? '-',
                    'title' => match($p->permission_type) {
                        'sick' => 'Izin Sakit',
                        'family' => 'Izin Keluarga',
                        'field_duty' => 'Tugas Luar Kantor',
                        default => 'Izin Pribadi',
                    },
                    'start_date' => Carbon::parse($p->date)->format('d M Y'),
                    'end_date' => $p->end_date ? Carbon::parse($p->end_date)->format('d M Y') : null,
                    'duration' => $duration,
                    'reason' => $p->reason,
                    'attachment_url' => $p->attachment ? asset('storage/' . $p->attachment) : null,
                    'created_at' => $p->created_at->diffForHumans(),
                ];
            });

        $overtimes = OvertimeRequest::with(['user.position'])
            ->whereIn('user_id', $teamUserIds)
            ->where('status', 'pending')
            ->get()
            ->map(function ($o) {
                $hours = $o->total_hours ?? Carbon::parse($o->start_time)->diffInHours(Carbon::parse($o->end_time));
                return [
                    'id' => $o->id,
                    'type' => 'overtime',
                    'type_label' => 'Lembur',
                    'employee_name' => $o->user->name,
                    'employee_photo' => $o->user->photo_url,
                    'position' => $o->user->position->name ?? '-',
                    'title' => 'Kerja Lembur',
                    'start_date' => Carbon::parse($o->date)->format('d M Y'),
                    'end_date' => null,
                    'duration' => "{$hours} jam (" . Carbon::parse($o->start_time)->format('H:i') . ' - ' . Carbon::parse($o->end_time)->format('H:i') . ')',
                    'reason' => $o->reason,
                    'attachment_url' => null,
                    'created_at' => $o->created_at->diffForHumans(),
                ];
            });

        // Merge and sort by created_at (simulate sort by combining list)
        $allApprovals = $leaves->concat($permissions)->concat($overtimes);

        return response()->json([
            'success' => true,
            'approvals' => $allApprovals
        ]);
    }

    /**
     * Process approval (approve or reject) for leave/permit/overtime requests.
     */
    public function processApproval(Request $request, $type, $id)
    {
        $manager = $this->checkManager();
        $divisionId = $manager->division_id;

        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|string|nullable|max:500'
        ]);

        $action = $request->action;
        $reason = $request->rejection_reason;

        // Fetch request based on type
        if ($type === 'leave') {
            $item = LeaveRequest::findOrFail($id);
        } elseif ($type === 'permission') {
            $item = PermissionRequest::findOrFail($id);
        } elseif ($type === 'overtime') {
            $item = OvertimeRequest::findOrFail($id);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tipe pengajuan tidak valid.'
            ], 400);
        }

        // Bug fix #5: Guard against IDOR — reject if already processed
        if ($item->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini sudah diproses sebelumnya (status saat ini: ' . $item->status . ').'
            ], 409);
        }

        // Verify request belongs to someone in manager's division
        $applicant = User::findOrFail($item->user_id);
        if ($applicant->division_id !== $divisionId || $applicant->id === $manager->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki wewenang untuk menyetujui pengajuan karyawan ini.'
            ], 403);
        }

        // Apply action
        if ($action === 'approve') {
            if ($type === 'leave') {
                $item->status = 'approved_manager';
                $item->approved_by_manager = $manager->id;
                $item->manager_approved_at = Carbon::now();
            } elseif ($type === 'permission') {
                $item->status = 'approved';
                $item->approved_by = $manager->id;
                $item->approved_at = Carbon::now();
            } elseif ($type === 'overtime') {
                $item->status = 'approved_manager';
                $item->approved_by_manager = $manager->id;
                $item->manager_approved_at = Carbon::now();
            }
        } else { // reject
            $item->status = 'rejected';
            $item->rejection_reason = $reason;
            if ($type === 'leave' || $type === 'overtime') {
                $item->approved_by_manager = $manager->id;
                $item->manager_approved_at = Carbon::now();
            } elseif ($type === 'permission') {
                $item->approved_by = $manager->id;
                $item->approved_at = Carbon::now();
            }
        }

        $item->save();

        // Send internal notification
        $dateField = $type === 'overtime' ? $item->date : ($type === 'permission' ? $item->date : $item->start_date);
        \App\Models\Notification::create([
            'user_id' => $item->user_id,
            'type'    => 'status_update',
            'title'   => $action === 'approve' ? 'Pengajuan Disetujui Manager' : 'Pengajuan Ditolak Manager',
            'message' => "Pengajuan " . ($type === 'leave' ? 'Cuti' : ($type === 'permission' ? 'Izin' : 'Lembur')) . " Anda pada tanggal " . Carbon::parse($dateField)->format('d/m/Y') . " telah " . ($action === 'approve' ? 'disetujui' : 'ditolak') . " oleh Manager.",
            'icon'    => $action === 'approve' ? 'check-circle' : 'x-circle',
            'color'   => $action === 'approve' ? '#10B981' : '#EF4444',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil diproses.'
        ]);
    }
}
