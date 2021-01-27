<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Profile;
use App\Models\Subscriptions;
use App\Models\FCM;
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

        $user = new User();

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->password = $request->password;
        $user->remember_token = Str::random(10);

        $user->save();

        $profile = new Profile();

        $profile->photo = 'avatar.png';
        $profile->user_id = $user->id;
        
        $user->profile()->save($profile);

        $fcm = new FCM();
        $fcm->user_id = $user->id;
        $fcm->has_token= 0;
        $fcm->token = "token";
        $fcm->save();

        $token = $user->createToken('App Password Grant Client')->accessToken;
        $response = [
            'token' => $token,
         ];
        return response($response, 200);
    }

    public function login (Request $request) {
        
        $user = User::where('phone', $request->phone)->first();
        
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('appToken')->accessToken;

                $profile = Profile::where('user_id', $user->id)->get();

                $response = [
                    'token' => $token,
                    'name' => $user->name,
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'membership' => date("d M Y", strtotime($user->created_at)),
                    'profile' => $profile
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

    public function delete(Request $request){

        $user = User::findOrFail($request->id);

        $profile = Profile::where('user_id', $user->id)->delete();
        $user->delete();

        $response = ['message' => 'Account deleted!'];
        return response($response, 200);
    }

    public function logout (Request $request) {
        
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }


    //Updates

    public function updatePhoto(Request $request){

        $id = $request->id;

        $profile = Profile::findOrFail($id);

        // $request->validate([
        //     'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ]);
        
        if($request->photo != ''){

            $photo = time().'.jpg';

            // $request->photo->move(public_path('storage/profile/'), $photo);
            file_put_contents('storage/profile/'.$photo, base64_decode($request->photo));
            $profile->photo = $photo;

            $profile->save();

            return response([
                'message' => 'success',
                'photo' => $photo
            ], 200);
        }
        
    }

    public function updateLocation(Request $request){

        $profile = Profile::findOrFail($request->id);

        $profile->location = $request->location;
        $profile->update();

        return(
            [
                'message' => 'location updated'
            ]
            );
    }

    public function updateAccount(Request $request){

        $profile = Profile::findOrFail($request->id);
        $profile->email = $request->email;
        $profile->update();

        $user = User::findOrFail($profile->user_id);
        $user->name = $request->name;
        $user->phone = $request->phone;

        $user->update();

        
        return response([
            'user' => $user,
            'profile' => $profile
        ], 200);
        
    }



    // Functions for Agent APi's
    // For register

    public function registerAgent (Request $request) {
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

        $user = new User();

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->password = $request->password;
        $user->remember_token = Str::random(10);

        $user->save();

        $profile = new Profile();

        $profile->photo = 'avatar.png';
        $profile->user_id = $user->id;
        $profile->role = 'agent-user';
        
        $user->profile()->save($profile);

        $subscribe = new Subscriptions();

        $subscribe->agent_id = $user->id;
        $subscribe->expiry_date = $user->created_at;
        
        $subscribe->save();

        $subscribe->expiry_date = $subscribe->updated_at->addDays(30);
        $subscribe->update();


        $token = $user->createToken('App Password Grant Client')->accessToken;
        $response = [
            'token' => $token,
         ];
        return response($response, 200);
    }
}
