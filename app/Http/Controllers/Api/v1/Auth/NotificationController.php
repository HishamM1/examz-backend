<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request) {
        return $request->user()->notifications;
    }

    public function markAsRead(Request $request, $notificationId) {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
    }

    public function markAllAsRead(Request $request) {
        $request->user()->unreadNotifications->markAsRead();
    }

    public function clearAll(Request $request) {
        $request->user()->notifications()->delete();
    }


}
