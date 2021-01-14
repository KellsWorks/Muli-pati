<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function create(Request $request, $id){

        $profile = new Profile();

        $profile->role = $request->role;
        $profile->photo = $request->photo;
        $profile->email = $request->email;
        $profile->location = $request->location;

        $user = User::findOrFail($id);
        $user->profile()->save($profile);

        $response = ['message' => 'success'];

        return response($response)
    }
}
