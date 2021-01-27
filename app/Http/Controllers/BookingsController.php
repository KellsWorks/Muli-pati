<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use App\Models\Trips;
use App\Models\User;

class BookingsController extends Controller
{
    public function create(Request $request){

        $id = $request->booker_id;
        $trip_id = $request->trip_id;

        $bookings = new Bookings();
        $user = User::findOrFail($id);

        $bookings->status = 'booked';
        $bookings->trip_id = $trip_id;

        $user->bookings()->save($bookings);

        return response(
            [
                'message' => 'success'
            ], 200
        );
    }

    public function cancel(Request $request){

        $trip = Bookings::findOrFail($request->id);
        $trip->status = 'cancelled';
        $trip->update();

        return response(
            [
                'message' => 'trip cancelled'
            ],
            200
        );
    }

    public function delete(Request $request){

        $trip = Bookings::findOrFail($request->id);
        $trip->delete();

        return response(
            [
                'message' => 'trip deleted'
            ],
            200
        );
    }
}
