<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAllAsRead(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
        }

        return back()->with('info', 'All notifications marked as read.');
    }

    public function destroy(string $id): RedirectResponse
    {
        if (Auth::check()) {
            $notification = Auth::user()->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->delete();
            }
        }

        return back()->with('info', 'Notification dismissed.');
    }
}

