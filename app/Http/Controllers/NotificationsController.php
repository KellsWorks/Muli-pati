<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifications;

class NotificationsController extends Controller
{
    public function userNotification(Request $request){

        $notifications = Notifications::all();

        return response(
            [
                "notifications" => $notifications
            ], 200
        );
    }

    public function markAsRead(Request $request){
        $notification = Notifications::findOrFail($request->id);
        $notification->status = "marked";
        $notification->update();

        return response([
            "message" => "success"
        ], 200);
    }

    public function delete(Request $request){
        $notification = Notifications::findOrFail($request->id);
        $notification->delete();

        return response([
            "message" => "success"
        ], 200);
    }
}
