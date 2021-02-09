<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messages;
use App\Models\Notifications;
use App\Models\FCM;

class MessagingController extends Controller
{
    public function create(Request $request){

        $message = new Messages();
        $message->from = $request->from;

        $message->to = $request->to;
        $message->message = $request->message;
        $message->time = $request->time;

        $message->is_read = 0;
        $message->save();

        $notification = new Notifications();
            $notification->title = "You have a new message";
            $notification->user_id = $request->to;
            $notification->content = $request->message;
            $notification->save();

            $curl = curl_init();

            $toId = FCM::where("user_id", $request->to)->pluck('token');

            $payload = [

                'registration_ids' => $toId,

                'notification' => [
                  'title' =>"You have a new message",
                  'body' => $request->message
                ]
                ];

            curl_setopt_array($curl, [
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Authorization: key=AAAAIhMcKng:APA91bFpWWPMkqDld-dV7RlbE_ZE8kqJbJLu9CP36QjM4-O8encnbWyIDJbtRis2fTHOOeTloCUi1hNEq4-sG9qXjt9iRDkhrufkPGxOyiftZxstbucqBpD380he4i479auew6WB12Ma",
                "Content-Type: application/json"],
            ]);

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);


        return response([
            'message' => 'success'
        ], 200);

    }

    public function getMessages(Request $request){

        $sender = $request->fromId;
        $receiver =  $request->toId;

        $messages = Messages::where( function ($query) use ($sender, $receiver){
            $query->where('from', $sender)->where('to', $receiver);
        })
        ->orWhere( function ($query) use ($sender, $receiver){
            $query->where('from', $receiver)->where('to', $sender);
        })->orderBy("created_at", 'asc')->get();

        return response(
            [
                'messages' => $messages
            ], 200
        );

    }

    public function delete(Request $request){
        try {
            Messages::where('from', $request->id)->delete();
        return response(
            ['messages' => 'success'], 200
        );
        } catch (\Throwable $th) {}
    }
}
