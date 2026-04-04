<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = AdminNotification::orderByDesc('id')->limit(100)->get();
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead(AdminNotification $notification)
    {
        $notification->markAsRead();
        if ($notification->link) {
            return redirect($notification->link);
        }
        return redirect()->route('admin.notifications.index')->with('status', 'Marked as read.');
    }

    public function markAllRead()
    {
        AdminNotification::unread()->update(['read_at' => now()]);
        return redirect()->route('admin.notifications.index')->with('status', 'All marked as read.');
    }
}
