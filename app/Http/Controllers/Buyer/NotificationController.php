<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        
        return view('buyer.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Invalidate notification cache so dropdown reflects the change
        $userId = $request->user()->id;
        Cache::forget("notif_unread_{$userId}");
        Cache::forget("notif_recent_{$userId}");
        
        return redirect()->back();
    }
}
