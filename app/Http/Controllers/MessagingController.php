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

        $message->is_read = 0;
        $message->save();

        return response([
            'message' => 'success'
        ], 200);
        
    }

    public function getMessages(Request $request){

        $user = User::findOrFail($request->id);
        $sender = Profile::where('user_id', $user->id)->get();

        $messages = Messages::where('from', $user->id)
                    ->get();

        return response(
            [
                'sender' => $sender,
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
