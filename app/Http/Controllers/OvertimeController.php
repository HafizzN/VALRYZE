<?php

namespace App\Http\Controllers;

use App\Models\OvertimeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendEmailJob;

class OvertimeController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $query = OvertimeRequest::with('user');

        if (!$user->hasRole(['super_admin', 'hrd', 'manager'])) {
            $query->where('user_id', $user->id);
        }
        if ($user->hasRole('manager')) {
            $ids = User::where('division_id', $user->division_id)->pluck('id');
            $query->whereIn('user_id', $ids);
        }

        $overtimes = $query->orderBy('date', 'desc')->paginate(15);
        return view('overtime.index', compact('overtimes'));
    }

    public function create()
    {
        return view('overtime.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'reason'     => 'required|string|min:10',
        ]);

        $start  = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        $end    = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
        $totalHours = $start->diffInMinutes($end) / 60;

        OvertimeRequest::create([
            'user_id'     => Auth::id(),
            'date'        => $request->date,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'total_hours' => round($totalHours, 2),
            'reason'      => $request->reason,
            'status'      => 'pending',
        ]);

        return redirect()->route('overtime.index')->with('success', 'Pengajuan lembur berhasil dikirim.');
    }

    public function show(OvertimeRequest $overtime)
    {
        return view('overtime.show', compact('overtime'));
    }

    public function approveManager(OvertimeRequest $overtime)
    {
        $overtime->update([
            'status'              => 'approved_manager',
            'approved_by_manager' => Auth::id(),
            'manager_approved_at' => now(),
        ]);

        try {
            SendEmailJob::dispatch(
                $overtime->user->email,
                "Pengajuan Lembur Disetujui Manager - Menunggu HRD",
                "Halo {$overtime->user->name},\n\nPengajuan lembur Anda untuk tanggal {$overtime->date} ({$overtime->total_hours} jam) telah disetujui oleh Manager dan sedang menunggu persetujuan akhir dari HRD.\n\nSalam,\nSmart HR Portal"
            );
        } catch (\Exception $e) {
            // Ignore
        }

        return back()->with('success', 'Lembur disetujui Manager.');
    }

    public function approveHrd(OvertimeRequest $overtime)
    {
        $overtime->update([
            'status'          => 'approved',
            'approved_by_hrd' => Auth::id(),
            'hrd_approved_at' => now(),
        ]);

        try {
            SendEmailJob::dispatch(
                $overtime->user->email,
                "Pengajuan Lembur Disetujui",
                "Halo {$overtime->user->name},\n\nKabar baik! Pengajuan lembur Anda untuk tanggal {$overtime->date} ({$overtime->total_hours} jam) telah disetujui sepenuhnya oleh HRD.\n\nSalam,\nSmart HR Portal"
            );
        } catch (\Exception $e) {
            // Ignore
        }

        return back()->with('success', 'Lembur disetujui HRD.');
    }

    public function reject(Request $request, OvertimeRequest $overtime)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $overtime->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);

        try {
            SendEmailJob::dispatch(
                $overtime->user->email,
                "Pengajuan Lembur Ditolak",
                "Halo {$overtime->user->name},\n\nPengajuan lembur Anda untuk tanggal {$overtime->date} telah ditolak.\n\nAlasan penolakan: {$request->rejection_reason}\n\nSalam,\nSmart HR Portal"
            );
        } catch (\Exception $e) {
            // Ignore
        }

        return back()->with('success', 'Lembur ditolak.');
    }
}
