<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::with(['user', 'farmer'])
            ->where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->latest()
            ->get();

        $unreadCount = $notifications->where('is_read', false)->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(AppNotification $notification)
    {
        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }
}
