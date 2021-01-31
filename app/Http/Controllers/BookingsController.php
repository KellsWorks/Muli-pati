<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use App\Models\Trips;
use App\Models\User;
use App\Models\Notifications;
use App\Models\FCM;
use Illuminate\Support\Facades\DB;
use DateTime;

class BookingsController extends Controller
{
    public function create(Request $request){

        $id = $request->booker_id;
        $trip_id = $request->trip_id;

        if(Bookings::where('trip_id', $trip_id)->exists() && Bookings::where('user_id', $id)->exists()){
            return response(
                [
                    'message' => 'You already booked this trip'
                ], 202
            );
        }
        else{
            $bookings = new Bookings();
            $user = User::findOrFail($id);

            $bookings->status = 'booked';
            $bookings->trip_id = $trip_id;

            $user->bookings()->save($bookings);

            $tripsTable = DB::table('trips')
                         ->where('id', $trip_id)
                         ->get()[0];

            $start = $tripsTable->start;
            $destination = $tripsTable->destination;

            $date = new DateTime($tripsTable->start_time);
            $new_date_format = $date->format('l, F d Y h:i:s');
            $start_time = $new_date_format;

            $title = "Booking successfully!";
            $content ="You have successfully booked a trip from $start to $destination on $start_time";

            $notification = new Notifications();
            $notification->title = "Booking successfully!";
            $notification->user_id = $id;
            $notification->content = $content;
            $notification->save();

            $curl = curl_init();

            $toId = FCM::where("user_id", $id)->pluck('token');

            $payload = [

                'registration_ids' => $toId,

                'notification' => [
                  'title' => $title,
                  'body' => $content
                ]
              ];

            curl_setopt_array($curl, [
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Authorization: key=AAAAIhMcKng:APA91bFpWWPMkqDld-dV7RlbE_ZE8kqJbJLu9CP36QjM4-O8encnbWyIDJbtRis2fTHOOeTloCUi1hNEq4-sG9qXjt9iRDkhrufkPGxOyiftZxstbucqBpD380he4i479auew6WB12Ma",
                "Content-Type: application/json"
            ],
            ]);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            $response = curl_exec($curl);

            return response(
                [
                    'notification' => $response,
                    'message' => 'success'
                ], 200
            );
        }
    }

    public function getBookedTrips(Request $request){

        $status = Bookings::where("user_id", $request->id)->pluck('status');

        foreach ($status as $item) {
            if($item == "booked"){
                $bookings = Bookings::where("status", $item)->pluck('trip_id');
                $trips = Trips::findOrFail($bookings);
                return response(
                    [
                        "userTrips" => $trips
                    ], 200
                );
            }
        }

    }

    public function getCancelledTrips(Request $request){

        $status = Bookings::where("user_id", $request->id)->pluck('status');

        foreach ($status as $item) {
            if($item == "cancelled"){
                $bookings = Bookings::where("status", $item)->pluck('trip_id');
                $trips = Trips::findOrFail($bookings);
                return response(
                    [
                        "userTrips" => $trips
                    ], 200
                );
            }
        }

    }

    public function getUserBookings(Request $request){
        $bookings = Bookings::where("user_id", $request->user_id)->get();
        return response(
            [
                "userBookings" => $bookings
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

    public function getToken(Request $request){
        $start = DB::table('trips')
                         ->where('user_id', $request->id)
                         ->where('id', $request->tripId)
                         ->get()[0];

                         return response([
                            'token' => $start
                        ], 200);

    }
}
