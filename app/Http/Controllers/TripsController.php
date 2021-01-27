<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trips;
use App\Models\User;
use App\Models\Profile;
use DateTime;
use Carbon\Carbon;
use App\Models\Notifications;

class TripsController extends Controller
{
    public function create(Request $request){

        $id = $request->id;
        $trip = new Trips();

        $user = User::findOrFail($id);
    
        $trip->start = $request->start;
        $trip->destination = $request->destination;
        $trip->start_time = $request->start_time;
        $trip->end_time = $request->end_time;
        $trip->pick_up_place = $request->pick_up_place;
        $trip->location = $request->get('location');
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

        $id = $request->id;

        $trip = Trips::finOrFail($id);

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

        $trip->update();

        return response([
            'message' => 'success'
        ], 200);
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
        // $startTime = $trip->start_time;

        // $notify = new Notifications();

        // $notify->title = 'Booking successful!';
        // $notify->content = 'You have booked a trip on '.$startTime;
        
        // $user->notify()->save($notify);

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

    //trips preference

    public function delete(Request $request){

        $id = $request->id;

        $trip = Trips::findOrFail($id);

        if($id == $trip->user_id){
            $trip->delete();
        }

        return response([
            'message' => 'success'
        ], 200);
    }

    //Getting all trips

    public function allTrips(Request $request){

        $trips = Trips::all();

            return response([
                'trips' => $trips
            ], 200);
    
    }

    public function allTripsLocation(Request $request, $location){

        $trips = Trips::where(
            'location', $location
        )->get();

            return response([
                'trips' => $trips
            ], 200);
    
    }
    
}
