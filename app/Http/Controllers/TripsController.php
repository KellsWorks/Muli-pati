<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trips;
use App\Models\User;
use DateTime;
use Carbon\Carbon;

class TripsController extends Controller
{
    public function create(Request $request, $id){

        $trip = new Trips();

        $user = User::findOrFail($id);

        $trip->start = $request->start;
        $trip->destination = $request->destination;
        $trip->start_time = $request->start_time;
        $trip->end_time = $request->end_time;
        $trip->pick_up_place = $request->pick_up_place;
        $trip->location = $request->location;
        $trip->number_of_passengers = $request->number_of_passengers;
        $trip->passenger_fare = $request->passenger_fare;
        $trip->car_type = $request->car_type;
        $trip->car_photo = $request->car_photo;

        $user->trip()->save($trip);

        return response([
            'message' => 'success'
        ], 200);
    }

    public function update(Request $request){

    }

    public function cancel(Request $request){

    }

    public function trips(Request $request){

        $id = $request->id;

        $user = User::findOrFail($id);
        
        $trips = Trips::find($id);

        $startTime = new Carbon($trips->end_time);
        $endTime = new Carbon($trips->start_time);

        $timeDiff = $endTime->diff($startTime);

        
        if($id == $trips->user_id){

            $trips = Trips::where('location', $trips->location)
               ->orderBy('id')
               ->get();

               return response(
                [
                    'trips' => $trips,
                    'timeDiff' => $timeDiff
                ], 200
            );
        }
        else{
           
            return response(
                [
                    'message' => 'failed'
                ], 500
            );

        }  

    }

    public function book(Request $request){

        $id = $request->id;

        $trip = Trips::findOrFail($id);

        if($id == $trip->user_id){
            $trip->status = 'booked';
            $trip->update();
        }

        return response([
            'message' => 'success'
        ], 200);
    }

    public function completed(Request $request){

        $id = $request->id;

        $trip = Trips::findOrFail($id);

        if($id == $trip->user_id){
            $trip->status = 'completed';
            $trip->update();
        }

        return response([
            'message' => 'success'
        ], 200);
    }

    public function cancelled(Request $request){

        $id = $request->id;

        $trip = Trips::findOrFail($id);

        if($id == $trip->user_id){
            $trip->status = 'cancelled';
            $trip->update();
        }

        return response([
            'message' => 'success'
        ], 200);
    }
}
