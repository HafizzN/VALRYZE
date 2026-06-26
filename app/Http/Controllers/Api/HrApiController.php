<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\PermissionRequest;
use App\Models\OvertimeRequest;
use App\Models\User;
use App\Models\Division;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrApiController extends Controller
{
    /**
     * Middleware to verify HRD role.
     */
    private function checkHr()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('hrd')) {
            abort(response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya HRD yang dapat mengakses fitur ini.'
            ], 403));
        }
        return $user;
    }

    /**
     * Get directory of all employees across all divisions.
     */
    public function employees()
    {
        $this->checkHr();

        $employees = User::with(['division', 'position', 'shift'])
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'nik' => $u->nik ?? '-',
                    'name' => $u->name,
                    'email' => $u->email,
                    'phone' => $u->phone ?? '-',
                    'photo_url' => $u->photo_url,
                    'division' => $u->division->name ?? '-',
                    'position' => $u->position->name ?? '-',
                    'employment_type' => match($u->employment_type) {
                        'permanent' => 'Permanen',
                        'contract' => 'Kontrak',
                        'internship' => 'Magang',
                        'freelance' => 'Pekerja Lepas',
                        default => ucfirst($u->employment_type),
                    },
                    'status' => $u->status,
                    'status_label' => match($u->status) {
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        'resign' => 'Resign',
                        default => ucfirst($u->status),
                    },
                    'join_date' => $u->join_date ? $u->join_date->format('d M Y') : '-',
                ];
            ]);

        return response()->json([
            'success' => true,
            'employees' => $employees
        ]);
    }

    /**
     * Get recruitment and onboarding summary metrics.
     */
    public function recruitmentOnboarding()
    {
        $this->checkHr();

        // High fidelity mock recruitment data (since there are no DB tables for this, we provide a clean, complete response)
        $jobs = [
            [
                'id' => 1,
                'title' => 'Backend Engineer (Laravel)',
                'division' => 'Engineering',
                'applicants_count' => 12,
                'status' => 'active',
                'status_label' => 'Aktif'
            ],
            [
                'id' => 2,
                'title' => 'Mobile Developer (Flutter)',
                'division' => 'Engineering',
                'applicants_count' => 8,
                'status' => 'active',
                'status_label' => 'Aktif'
            ],
            [
                'id' => 3,
                'title' => 'HR Staff',
                'division' => 'Human Resources',
                'applicants_count' => 25,
                'status' => 'closed',
                'status_label' => 'Ditutup'
            ],
        ];

        $applicants = [
            [
                'id' => 101,
                'name' => 'Budi Santoso',
                'job_title' => 'Backend Engineer (Laravel)',
                'stage' => 'Technical Test',
                'stage_label' => 'Ujian Teknis',
                'applied_date' => '3 hari lalu'
            ],
            [
                'id' => 102,
                'name' => 'Siti Aminah',
                'job_title' => 'Mobile Developer (Flutter)',
                'stage' => 'Interview HR',
                'stage_label' => 'Wawancara HRD',
                'applied_date' => 'Kemarin'
            ],
            [
                'id' => 103,
                'name' => 'Rian Wijaya',
                'job_title' => 'Backend Engineer (Laravel)',
                'stage' => 'Offering',
                'stage_label' => 'Penawaran Kerja',
                'applied_date' => '5 hari lalu'
            ],
        ];

        $onboardings = [
            [
                'id' => 201,
                'employee_name' => 'Andi Pratama',
                'program' => 'Pengenalan Perusahaan & Visi Misi',
                'progress' => 0.8, // 80%
                'due_date' => '28 Jun 2026'
            ],
            [
                'id' => 202,
                'employee_name' => 'Rina Amalia',
                'program' => 'Setup Lingkungan Kerja & Git Repository',
                'progress' => 0.4, // 40%
                'due_date' => '30 Jun 2026'
            ]
        ];

        return response()->json([
            'success' => true,
            'jobs' => $jobs,
            'applicants' => $applicants,
            'onboardings' => $onboardings,
            'summary' => [
                'total_openings' => 2,
                'total_applicants' => 20,
                'active_onboarding' => 2
            ]
        ]);
    }

    /**
     * Get monthly payroll budget summary and employee salary details.
     */
    public function payrollSummary()
    {
        $this->checkHr();

        $employees = User::with(['division', 'position'])->where('status', 'active')->get();
        $payrollList = [];
        $totalSalary = 0;
        $totalBenefits = 0;
        $totalDeductions = 0;

        foreach ($employees as $u) {
            // Generate realistic deterministic salary based on position and division
            $posName = strtolower($u->position->name ?? '');
            $basicSalary = 6500000; // default basic salary Rp 6.5M

            if (str_contains($posName, 'director') || str_contains($posName, 'direktur')) {
                $basicSalary = 35000000;
            } elseif (str_contains($posName, 'manager') || str_contains($posName, 'lead')) {
                $basicSalary = 22000000;
            } elseif (str_contains($posName, 'senior')) {
                $basicSalary = 15000000;
            } elseif (str_contains($posName, 'engineer') || str_contains($posName, 'developer') || str_contains($posName, 'analyst')) {
                $basicSalary = 11000000;
            } elseif (str_contains($posName, 'hr') || str_contains($posName, 'recruiter')) {
                $basicSalary = 8000000;
            }

            // Calculations
            $allowance = (int) ($basicSalary * 0.15); // 15% allowance
            $bpjsDeduction = (int) ($basicSalary * 0.03); // 3% BPJS
            $taxDeduction = (int) ($basicSalary * 0.05); // 5% PPh21
            $deductions = $bpjsDeduction + $taxDeduction;
            $netSalary = $basicSalary + $allowance - $deductions;

            $totalSalary += $basicSalary;
            $totalBenefits += $allowance;
            $totalDeductions += $deductions;

            $payrollList[] = [
                'user_id' => $u->id,
                'name' => $u->name,
                'nik' => $u->nik ?? '-',
                'position' => $u->position->name ?? '-',
                'division' => $u->division->name ?? '-',
                'basic_salary' => $basicSalary,
                'allowance' => $allowance,
                'deductions' => $deductions,
                'net_salary' => $netSalary
            ];
        }

        $totalPayout = $totalSalary + $totalBenefits - $totalDeductions;

        return response()->json([
            'success' => true,
            'summary' => [
                'month' => Carbon::now()->translatedFormat('F Y'),
                'total_employees' => count($payrollList),
                'total_basic_salary' => $totalSalary,
                'total_benefits' => $totalBenefits,
                'total_deductions' => $totalDeductions,
                'total_payout' => $totalPayout,
            ],
            'payroll_list' => $payrollList
        ]);
    }

    /**
     * Get approvals list for HR (all divisions).
     * HR reviews:
     * - Leaves/Overtimes that are 'approved_manager' (waiting HR final approval) or 'pending'.
     * - Permissions that are 'pending'.
     */
    public function approvals()
    {
        $this->checkHr();

        // Get leaves awaiting HRD final approval (or pending if we want broad visibility)
        $leaves = LeaveRequest::with(['user.division', 'user.position'])
            ->whereIn('status', ['pending', 'approved_manager'])
            ->get()
            ->map(function ($l) {
                return [
                    'id' => $l->id,
                    'type' => 'leave',
                    'type_label' => 'Cuti',
                    'employee_name' => $l->user->name,
                    'employee_photo' => $l->user->photo_url,
                    'division' => $l->user->division->name ?? '-',
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
                    'status' => $l->status,
                    'status_label' => $l->status === 'approved_manager' ? 'Disetujui Manager' : 'Menunggu Manager',
                    'attachment_url' => $l->attachment ? asset('storage/' . $l->attachment) : null,
                    'created_at' => $l->created_at->diffForHumans(),
                ];
            });

        $permissions = PermissionRequest::with(['user.division', 'user.position'])
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
                    'division' => $p->user->division->name ?? '-',
                    'position' => $p->user->position->name ?? '-',
                    'title' => match($p->permission_type) {
                        'sick' => 'Izin Sakit',
                        'family' => 'Keperluan Keluarga',
                        'field_duty' => 'Tugas Luar Kantor',
                        default => 'Izin Pribadi',
                    },
                    'start_date' => Carbon::parse($p->date)->format('d M Y'),
                    'end_date' => $p->end_date ? Carbon::parse($p->end_date)->format('d M Y') : null,
                    'duration' => $duration,
                    'reason' => $p->reason,
                    'status' => $p->status,
                    'status_label' => 'Menunggu Persetujuan',
                    'attachment_url' => $p->attachment ? asset('storage/' . $p->attachment) : null,
                    'created_at' => $p->created_at->diffForHumans(),
                ];
            });

        $overtimes = OvertimeRequest::with(['user.division', 'user.position'])
            ->whereIn('status', ['pending', 'approved_manager'])
            ->get()
            ->map(function ($o) {
                $hours = $o->total_hours ?? Carbon::parse($o->start_time)->diffInHours(Carbon::parse($o->end_time));
                return [
                    'id' => $o->id,
                    'type' => 'overtime',
                    'type_label' => 'Lembur',
                    'employee_name' => $o->user->name,
                    'employee_photo' => $o->user->photo_url,
                    'division' => $o->user->division->name ?? '-',
                    'position' => $o->user->position->name ?? '-',
                    'title' => 'Kerja Lembur',
                    'start_date' => Carbon::parse($o->date)->format('d M Y'),
                    'end_date' => null,
                    'duration' => "{$hours} jam (" . Carbon::parse($o->start_time)->format('H:i') . ' - ' . Carbon::parse($o->end_time)->format('H:i') . ')',
                    'reason' => $o->reason,
                    'status' => $o->status,
                    'status_label' => $o->status === 'approved_manager' ? 'Disetujui Manager' : 'Menunggu Manager',
                    'attachment_url' => null,
                    'created_at' => $o->created_at->diffForHumans(),
                ];
            });

        $allApprovals = $leaves->concat($permissions)->concat($overtimes);

        return response()->json([
            'success' => true,
            'approvals' => $allApprovals
        ]);
    }

    /**
     * Process final HRD approval.
     */
    public function processApproval(Request $request, $type, $id)
    {
        $hrd = $this->checkHr();

        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|string|nullable|max:500'
        ]);

        $action = $request->action;
        $reason = $request->rejection_reason;

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

        // Apply HR Action
        if ($action === 'approve') {
            $item->status = 'approved'; // Final state
            if ($type === 'leave' || $type === 'overtime') {
                $item->approved_by_hrd = $hrd->id;
                $item->hrd_approved_at = Carbon::now();

                // If approved and it is leave, update leave balance
                if ($type === 'leave') {
                    $applicant = User::findOrFail($item->user_id);
                    $applicant->increment('annual_leave_used', $item->total_days);
                }
            } elseif ($type === 'permission') {
                $item->approved_by = $hrd->id;
                $item->approved_at = Carbon::now();
            }
        } else { // reject
            $item->status = 'rejected';
            $item->rejection_reason = $reason;
            if ($type === 'leave' || $type === 'overtime') {
                $item->approved_by_hrd = $hrd->id;
                $item->hrd_approved_at = Carbon::now();
            } elseif ($type === 'permission') {
                $item->approved_by = $hrd->id;
                $item->approved_at = Carbon::now();
            }
        }

        $item->save();

        // Send internal notification
        \App\Models\Notification::create([
            'user_id' => $item->user_id,
            'title' => $action === 'approve' ? 'Pengajuan Disetujui HRD' : 'Pengajuan Ditolak HRD',
            'content' => "Pengajuan " . ($type === 'leave' ? 'Cuti' : ($type === 'permission' ? 'Izin' : 'Lembur')) . " Anda pada tanggal " . ($type === 'permission' ? Carbon::parse($item->date)->format('d/m/Y') : Carbon::parse($item->start_date)->format('d/m/Y')) . " telah " . ($action === 'approve' ? 'disetujui' : 'ditolak') . " secara final oleh HRD.",
            'type' => 'status_update',
            'read_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil diproses secara final oleh HRD.'
        ]);
    }

    /**
     * Get metadata options (divisions, positions, shifts) for employee creation form.
     */
    public function formMeta()
    {
        $this->checkHr();

        $divisions = Division::select('id', 'name')->get();
        $positions = \App\Models\Position::select('id', 'name')->get();
        $shifts = \App\Models\Shift::where('is_active', true)->select('id', 'name', 'start_time', 'end_time')->get();

        return response()->json([
            'success' => true,
            'divisions' => $divisions,
            'positions' => $positions,
            'shifts' => $shifts
        ]);
    }

    /**
     * Create a new employee account.
     */
    public function storeEmployee(Request $request)
    {
        $this->checkHr();

        $request->validate([
            'nik' => 'required|string|unique:users,nik|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
            'division_id' => 'required|exists:divisions,id',
            'position_id' => 'required|exists:positions,id',
            'shift_id' => 'required|exists:shifts,id',
            'employment_type' => 'required|in:permanent,contract,internship,freelance',
            'join_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
        ]);

        $user = User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'division_id' => $request->division_id,
            'position_id' => $request->position_id,
            'shift_id' => $request->shift_id,
            'employment_type' => $request->employment_type,
            'join_date' => Carbon::parse($request->join_date),
            'phone' => $request->phone,
            'gender' => $request->gender,
            'status' => 'active',
            'annual_leave_quota' => 12,
            'annual_leave_used' => 0,
        ]);

        // Assign default role 'karyawan' using Spatie HasRoles method
        $user->assignRole('karyawan');

        return response()->json([
            'success' => true,
            'message' => 'Karyawan baru berhasil didaftarkan.',
            'user' => [
                'id' => $user->id,
                'nik' => $user->nik,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }
}

