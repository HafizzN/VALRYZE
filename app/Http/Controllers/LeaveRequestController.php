<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendEmailJob;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = LeaveRequest::with('user');

        if (!$user->hasRole(['super_admin', 'hrd', 'manager'])) {
            $query->where('user_id', $user->id);
        }

        if ($user->hasRole('manager')) {
            $divisionUserIds = User::where('division_id', $user->division_id)->pluck('id');
            $query->whereIn('user_id', $divisionUserIds);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('leave.index', compact('leaves'));
    }

    public function create()
    {
        return view('leave.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type'  => 'required|in:annual,maternity,paternity,wedding,big_leave,sick,other',
            'start_date'  => 'required|date|after_or_equal:today',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'required|string|min:10',
            'attachment'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'child_name'       => 'required_if:leave_type,maternity|nullable|string',
            'child_birth_date' => 'required_if:leave_type,maternity|nullable|date',
            'wedding_date'     => 'required_if:leave_type,wedding|nullable|date',
        ]);

        $user = Auth::user();

        // Check annual leave quota
        if ($request->leave_type === 'annual') {
            $startDate = Carbon::parse($request->start_date);
            $endDate   = Carbon::parse($request->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;

            if ($user->remaining_leave < $totalDays) {
                return back()->with('error', "Sisa cuti Anda ({$user->remaining_leave} hari) tidak mencukupi untuk {$totalDays} hari.");
            }
        }

        $totalDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        LeaveRequest::create([
            'user_id'         => $user->id,
            'leave_type'      => $request->leave_type,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'total_days'      => $totalDays,
            'reason'          => $request->reason,
            'attachment'      => $attachmentPath,
            'child_name'      => $request->child_name,
            'child_birth_date'=> $request->child_birth_date,
            'wedding_date'    => $request->wedding_date,
            'status'          => 'pending',
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function show(LeaveRequest $leave)
    {
        $this->authorizeAccess($leave);
        return view('leave.show', compact('leave'));
    }

    public function approve(Request $request, LeaveRequest $leave)
    {
        $user = Auth::user();

        if ($user->hasRole('manager') && $leave->status === 'pending') {
            $leave->update([
                'status'             => 'approved_manager',
                'approved_by_manager'=> $user->id,
                'manager_approved_at'=> now(),
            ]);

            try {
                SendEmailJob::dispatch(
                    $leave->user->email,
                    "Pengajuan Cuti Disetujui Manager - Menunggu HRD",
                    "Halo {$leave->user->name},\n\nPengajuan cuti Anda (tanggal {$leave->start_date} s/d {$leave->end_date}) telah disetujui oleh Manager dan saat ini sedang menunggu persetujuan akhir dari HRD.\n\nSalam,\nSmart HR Portal"
                );
            } catch (\Exception $e) {
                // Ignore mail sending errors to prevent blocking the flow
            }

            return back()->with('success', 'Cuti disetujui oleh Manager. Menunggu persetujuan HRD.');
        }

        if ($user->hasRole(['super_admin', 'hrd']) && in_array($leave->status, ['pending', 'approved_manager'])) {
            // Update leave quota
            if ($leave->leave_type === 'annual') {
                $leave->user->increment('annual_leave_used', $leave->total_days);
            }

            $leave->update([
                'status'          => 'approved',
                'approved_by_hrd' => $user->id,
                'hrd_approved_at' => now(),
            ]);

            // Auto-generate attendance records for all approved leave dates
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                \App\Models\Attendance::updateOrCreate(
                    ['user_id' => $leave->user_id, 'date' => $date->format('Y-m-d')],
                    [
                        'status' => 'leave',
                        'notes'  => 'Cuti: ' . match($leave->leave_type) {
                            'annual' => 'Tahunan',
                            'maternity' => 'Melahirkan',
                            'paternity' => 'Menemani Melahirkan',
                            'wedding' => 'Pernikahan',
                            'big_leave' => 'Besar',
                            'sick' => 'Sakit',
                            default => 'Lainnya'
                        },
                    ]
                );
            }

            try {
                SendEmailJob::dispatch(
                    $leave->user->email,
                    "Pengajuan Cuti Disetujui",
                    "Halo {$leave->user->name},\n\nKabar baik! Pengajuan cuti Anda (tanggal {$leave->start_date} s/d {$leave->end_date}) telah disetujui sepenuhnya oleh HRD.\n\nSelamat berlibur!\n\nSalam,\nSmart HR Portal"
                );
            } catch (\Exception $e) {
                // Ignore
            }

            return back()->with('success', 'Cuti berhasil disetujui.');
        }

        return back()->with('error', 'Anda tidak memiliki izin untuk menyetujui cuti ini.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $request->validate(['rejection_reason' => 'required|string|min:5']);

        $leave->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        try {
            SendEmailJob::dispatch(
                $leave->user->email,
                "Pengajuan Cuti Ditolak",
                "Halo {$leave->user->name},\n\nPengajuan cuti Anda (tanggal {$leave->start_date} s/d {$leave->end_date}) telah ditolak.\n\nAlasan penolakan: {$request->rejection_reason}\n\nSalam,\nSmart HR Portal"
            );
        } catch (\Exception $e) {
            // Ignore
        }

        return back()->with('success', 'Cuti berhasil ditolak.');
    }

    private function authorizeAccess(LeaveRequest $leave): void
    {
        $user = Auth::user();
        if (!$user->hasRole(['super_admin', 'hrd', 'manager']) && $leave->user_id !== $user->id) {
            abort(403);
        }
    }
}
