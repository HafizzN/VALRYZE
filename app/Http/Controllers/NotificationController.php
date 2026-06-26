<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->orderBy('created_at', 'desc')->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id === Auth::id()) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
