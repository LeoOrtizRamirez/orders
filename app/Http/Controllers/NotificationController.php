<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->unreadNotifications;
        return response()->json($notifications);
    }

    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        $notification = $request->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['status' => 'success']);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success']);
    }
}
