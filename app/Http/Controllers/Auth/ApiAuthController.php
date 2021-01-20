<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        
        $request['password']=Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $token = $user->createToken('App Password Grant Client')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);
    }

    public function login (Request $request) {
        
        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('appToken')->accessToken;
                $response = [
                    'token' => $token,
                    'name' => $user->name,
                    'phone' => $user->phone,
                 ];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }


    //Updates

    public function updatePhoto(Request $request, $id){

        $user = User::findOrFail($id);

        $profile = new Profile();
        $profile->email = '';
        $profile->location = '';
        
        if($request->photo != ''){

            $photo = time().'.jpg';
            file_put_contents('storage/profile/'.$photo, base64_decode($request->photo));
            $profile->photo = $photo;
        }
        
        $user->profile()->save($profile);

        return response([
            'message' => 'success'
        ], 200);
    }
}
