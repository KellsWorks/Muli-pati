<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifications;
use App\Models\FCM;

class FCMController extends Controller
{
    
    public function saveToken(Request $request){

        $token = FCM::findOrFail($request->id);

        $token->has_token = 1;
        $token->token = $request->token;

        $token->update();

        return response([
            'message' => 'token saved'
        ], 200);
    }
}
