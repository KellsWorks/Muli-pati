<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trips;

class TripsController extends Controller
{
    public function create(Request $request, $id){

        $trip = new Trip();

        $user = User::findOrFail($id);

        $trip->start = $request->start;
        $trip->destination = $request->destination;
        $trip->start_time = $request->start_time;
        $trip->end_time = $request->end_time;
        $trip->pick_up_place = $request->pick_up_place;
        $trip->location = $request->location;
        $trip->number_of_passengers = $request->number_of_passengers;
        $trip->passenger_fare = $request->passenger_fare;
        $trip->car_type = $trip->car_type;

        $user->trip()->save($profile);

        return response([
            'message' => 'success'
        ], 200);
    }

    public function update(Request $request){

    }

    public function cancel(Request $request){

    }
}
