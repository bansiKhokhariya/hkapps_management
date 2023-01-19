<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use App\Models\Notification;


class NotificationController extends Controller
{
    public function getAllNotification()
    {
        $notification = auth()->user()->notifications()->where('deleted_at',null)->get();
        return NotificationResource::collection($notification);
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

    public function deleteNotification($id)
    {
        $notification = Notification::where('id', $id)
            ->get()
            ->first()
            ->delete();
        return 'Notification delete Succesfully';
    }


}
