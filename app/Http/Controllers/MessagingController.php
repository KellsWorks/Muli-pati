<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messages;
use App\Models\User;
use App\Models\Profile;

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
            $messages = Messages::where('from', $request->id)->delete();
        return response(
            ['messages' => 'success'], 200
        );
        } catch (\Throwable $th) {}
    }
}
