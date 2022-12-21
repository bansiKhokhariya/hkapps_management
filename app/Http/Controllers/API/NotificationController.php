<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getAllNotification()
    {
        $notification = auth()->user()->notifications()->get();
        return response()->json($notification);
    }

    public function getUnreadNotification()
    {
        $notification = auth()->user()->unreadNotifications;
        return response()->json($notification);
    }

    public function markAsRead()
    {
        $notification = auth()->user()->unreadNotifications->markAsRead();
        return response()->json($notification);
    }


}
