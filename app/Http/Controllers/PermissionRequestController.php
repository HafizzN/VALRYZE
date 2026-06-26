<?php

namespace App\Http\Controllers;

use App\Models\PermissionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendEmailJob;

class PermissionRequestController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $query = PermissionRequest::with('user');

        if (!$user->hasRole(['super_admin', 'hrd', 'manager'])) {
            $query->where('user_id', $user->id);
        }
        if ($user->hasRole('manager')) {
            $ids = User::where('division_id', $user->division_id)->pluck('id');
            $query->whereIn('user_id', $ids);
        }

        $permissions = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('permission.index', compact('permissions'));
    }

    public function create()
    {
        return view('permission.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'permission_type' => 'required|in:sick,family,field_duty,personal',
            'date'            => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:date',
            'start_time'      => 'nullable|date_format:H:i',
            'end_time'        => 'nullable|date_format:H:i|after:start_time',
            'reason'          => 'required|string|min:10',
            'attachment'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('permission-attachments', 'public');
        }

        PermissionRequest::create([
            'user_id'         => Auth::id(),
            'permission_type' => $request->permission_type,
            'date'            => $request->date,
            'end_date'        => $request->end_date,
            'start_time'      => $request->start_time,
            'end_time'        => $request->end_time,
            'reason'          => $request->reason,
            'attachment'      => $attachmentPath,
            'status'          => 'pending',
        ]);

        return redirect()->route('permission.index')->with('success', 'Pengajuan izin berhasil dikirim.');
    }

    public function show(PermissionRequest $permission)
    {
        return view('permission.show', compact('permission'));
    }

    public function approve(PermissionRequest $permission)
    {
        $permission->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Auto-generate attendance records for all approved permission dates
        $start = \Carbon\Carbon::parse($permission->date);
        $end = $permission->end_date ? \Carbon\Carbon::parse($permission->end_date) : $start;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            \App\Models\Attendance::updateOrCreate(
                ['user_id' => $permission->user_id, 'date' => $date->format('Y-m-d')],
                [
                    'status' => $permission->permission_type === 'sick' ? 'sick' : 'permission',
                    'notes'  => 'Izin: ' . match($permission->permission_type) {
                        'sick' => 'Sakit',
                        'family' => 'Keperluan Keluarga',
                        'field_duty' => 'Tugas Lapangan',
                        'personal' => 'Keperluan Pribadi',
                        default => 'Lainnya'
                    },
                ]
            );
        }

        try {
            SendEmailJob::dispatch(
                $permission->user->email,
                "Pengajuan Izin Disetujui",
                "Halo {$permission->user->name},\n\nKabar baik! Pengajuan izin Anda untuk tanggal {$permission->date} telah disetujui.\n\nSalam,\nSmart HR Portal"
            );
        } catch (\Exception $e) {
            // Ignore
        }

        return back()->with('success', 'Izin berhasil disetujui.');
    }

    public function reject(Request $request, PermissionRequest $permission)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $permission->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);

        try {
            SendEmailJob::dispatch(
                $permission->user->email,
                "Pengajuan Izin Ditolak",
                "Halo {$permission->user->name},\n\nPengajuan izin Anda untuk tanggal {$permission->date} telah ditolak.\n\nAlasan penolakan: {$request->rejection_reason}\n\nSalam,\nSmart HR Portal"
            );
        } catch (\Exception $e) {
            // Ignore
        }

        return back()->with('success', 'Izin berhasil ditolak.');
    }
}
