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
use App\Models\Messages;

class BookingsController extends Controller
{
    public function create(Request $request){

        $id = $request->booker_id;
        $trip_id = $request->trip_id;

        if(Bookings::where('trip_id', $trip_id)->exists() && Bookings::where('user_id', $id)->exists()){
            return response(
                [
                    'notification' => 'null',
                    'message' => 'You already booked this trip'
                ], 202
            );
        }
        else{
            $bookings = new Bookings();
            $user = User::findOrFail($id);

            $tripDetails = Trips::findOrFail($trip_id);

            $bookings->status = 'booked';
            $bookings->trip_id = $trip_id;
            $bookings->start = $tripDetails->start;
            $bookings->destination = $tripDetails->destination;

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

            $message = new Messages();
            $message->from = $tripsTable->user_id;

            $message->to = $id;
            $message->message = "Thank you for booking this trip. Let's stay in touch in this chat room.";
            $message->time = "00:00";

            $message->is_read = 0;
            $message->save();

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

        $status = Bookings::where("user_id", $request->id)
                            ->where("status", "booked")
                            ->pluck('trip_id');
        foreach($status as $item){
            $trips = Trips::findOrFail($status);
            return response([
                "userTrips" => $trips
            ], 200);
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
        $bookings = Bookings::all();
        return response(
            [
                "userBookings" => $bookings
            ], 200
        );
    }

    public function cancel(Request $request){

        $id = Bookings::where("user_id", $request->id)->where("trip_id", $request->trip_id)->pluck('id');

        $bookings = Bookings::where('id', $id)
                   ->update([
                       'status' => 'cancelled'
                   ]);

        return response(
            [
                'message' => $bookings
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
