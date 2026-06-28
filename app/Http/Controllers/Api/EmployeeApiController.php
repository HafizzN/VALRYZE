<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\LeaveRequest;
use App\Models\PermissionRequest;
use App\Models\OvertimeRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeApiController extends Controller
{
    /**
     * Get active company announcements.
     */
    public function announcements()
    {
        $announcements = Announcement::active()
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'content' => $a->content,
                    'category' => $a->category,
                    'category_name' => $a->category_name,
                    'is_pinned' => (bool) $a->is_pinned,
                    'attachment_url' => $a->attachment ? asset('storage/' . $a->attachment) : null,
                    'published_at' => $a->published_at ? $a->published_at->format('Y-m-d H:i:s') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'announcements' => $announcements
        ]);
    }

    /**
     * Get employee leave requests history.
     */
    public function leaveRequests()
    {
        $user = Auth::user();
        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($lr) {
                return [
                    'id' => $lr->id,
                    'leave_type' => $lr->leave_type,
                    'leave_type_name' => $lr->leave_type_name,
                    'start_date' => $lr->start_date ? $lr->start_date->format('Y-m-d') : null,
                    'end_date' => $lr->end_date ? $lr->end_date->format('Y-m-d') : null,
                    'total_days' => $lr->total_days,
                    'reason' => $lr->reason,
                    'attachment_url' => $lr->attachment ? asset('storage/' . $lr->attachment) : null,
                    'status' => $lr->status,
                    'status_name' => $lr->status_name,
                    'rejection_reason' => $lr->rejection_reason,
                    'created_at' => $lr->created_at ? $lr->created_at->format('Y-m-d H:i:s') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'leave_requests' => $leaveRequests
        ]);
    }

    /**
     * Submit a new leave request.
     */
    public function storeLeaveRequest(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string|in:annual,maternity,paternity,wedding,big_leave,sick,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $user = Auth::user();
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        // Bug fix #6: count only working days (Mon-Fri), not full calendar days
        $totalDays = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if ($current->isWeekday()) {
                $totalDays++;
            }
            $current->addDay();
        }
        // Fallback to at least 1 day even if entire range is weekend
        $totalDays = max(1, $totalDays);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments/leaves', 'public');
        }

        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type' => $request->leave_type,
            'start_date' => $start,
            'end_date' => $end,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan cuti berhasil dikirim.',
            'leave_request' => [
                'id' => $leaveRequest->id,
                'leave_type_name' => $leaveRequest->leave_type_name,
                'total_days' => $leaveRequest->total_days,
                'status_name' => $leaveRequest->status_name,
            ]
        ]);
    }

    /**
     * Get employee permission/permit requests history.
     */
    public function permissionRequests()
    {
        $user = Auth::user();
        $permissionRequests = PermissionRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pr) {
                return [
                    'id' => $pr->id,
                    'permission_type' => $pr->permission_type,
                    'permission_type_name' => $pr->permission_type_name,
                    'date' => $pr->date ? $pr->date->format('Y-m-d') : null,
                    'end_date' => $pr->end_date ? $pr->end_date->format('Y-m-d') : null,
                    'start_time' => $pr->start_time ? Carbon::parse($pr->start_time)->format('H:i') : null,
                    'end_time' => $pr->end_time ? Carbon::parse($pr->end_time)->format('H:i') : null,
                    'reason' => $pr->reason,
                    'attachment_url' => $pr->attachment ? asset('storage/' . $pr->attachment) : null,
                    'status' => $pr->status,
                    'rejection_reason' => $pr->rejection_reason,
                    'created_at' => $pr->created_at ? $pr->created_at->format('Y-m-d H:i:s') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'permission_requests' => $permissionRequests
        ]);
    }

    /**
     * Submit a new permission/permit request.
     */
    public function storePermissionRequest(Request $request)
    {
        $request->validate([
            'permission_type' => 'required|string|in:sick,family,field_duty,personal',
            'date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $user = Auth::user();
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments/permissions', 'public');
        }

        $permissionRequest = PermissionRequest::create([
            'user_id' => $user->id,
            'permission_type' => $request->permission_type,
            'date' => Carbon::parse($request->date),
            'end_date' => $request->end_date ? Carbon::parse($request->end_date) : null,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan izin berhasil dikirim.',
            'permission_request' => [
                'id' => $permissionRequest->id,
                'permission_type_name' => $permissionRequest->permission_type_name,
                'status' => $permissionRequest->status,
            ]
        ]);
    }

    /**
     * Get employee overtime requests history.
     */
    public function overtimeRequests()
    {
        $user = Auth::user();
        $overtimeRequests = OvertimeRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($or) {
                return [
                    'id' => $or->id,
                    'date' => $or->date ? $or->date->format('Y-m-d') : null,
                    'start_time' => $or->start_time ? Carbon::parse($or->start_time)->format('H:i') : null,
                    'end_time' => $or->end_time ? Carbon::parse($or->end_time)->format('H:i') : null,
                    'total_hours' => (float) $or->total_hours,
                    'reason' => $or->reason,
                    'status' => $or->status,
                    'rejection_reason' => $or->rejection_reason,
                    'created_at' => $or->created_at ? $or->created_at->format('Y-m-d H:i:s') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'overtime_requests' => $overtimeRequests
        ]);
    }

    /**
     * Submit a new overtime request.
     */
    public function storeOvertimeRequest(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'reason' => 'required|string',
        ]);

        $user = Auth::user();
        $start = Carbon::parse($request->date . ' ' . $request->start_time);
        $end = Carbon::parse($request->date . ' ' . $request->end_time);
        
        if ($end->lt($start)) {
            $end->addDay();
        }
        
        $totalHours = $start->diffInMinutes($end) / 60;

        $overtimeRequest = OvertimeRequest::create([
            'user_id' => $user->id,
            'date' => Carbon::parse($request->date),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_hours' => $totalHours,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan lembur berhasil dikirim.',
            'overtime_request' => [
                'id' => $overtimeRequest->id,
                'total_hours' => (float) $overtimeRequest->total_hours,
                'status' => $overtimeRequest->status,
            ]
        ]);
    }
}
