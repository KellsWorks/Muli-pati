<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\FCM;
use App\Models\Messages;
use Illuminate\Http\Request;
use App\Models\Trips;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Notifications;
use DateTime;

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


        $curl = curl_init();

        $toId = FCM::where("user_id", $id)->pluck('token');
        $start = $request->start;
        $destination = $request->destination;
        $start_time = $request->start_time;

        $title = "Trip added successfully!";
        $content ="You have successfully added a trip from $start to $destination on $start_time";


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

        $notification = new Notifications();
            $notification->title = "Trip added successfully!";
            $notification->user_id = $id;
            $notification->content = $content;
            $notification->save();

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

        // $user = User::findOrFail($id);

        $trips = Trips::find($id);

        // $startTime = new Carbon($trips->end_time);
        // $endTime = new Carbon($trips->start_time);

        // $timeDiff = $endTime->diff($startTime);


        if($trips->location != ""){

            $trips = Trips::all();

               return response(
                [
                    'trips' => $trips
                    // 'timeDiff' => $timeDiff
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

    public function statusDelete(Request $request){

        $trips = Trips::findOrFail($request->id);
        $creationTime = date('Y-m-d h:m:s', strtotime($trips->created_at));
        $startTime = date('Y-m-d h:m:s', strtotime($trips->start_time));
        $id = $trips->user_id;


        $datetime1 = new DateTime($creationTime);
        $datetime2 = new DateTime($startTime);
        $interval = $datetime1->diff($datetime2);

        $hour = $interval->d;
        $minutes = $interval->h;

        if($hour == 0 && $minutes > 5){

            $curl = curl_init();

            $toId = FCM::where("user_id", $id)->pluck('token');

            $title = "Trip schedules";
            $content ="Your trip is scheduled to start in the next $minutes minutes";


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

            $notification = new Notifications();
            $notification->title = $title;
            $notification->user_id = $id;
            $notification->content = $content;
            $notification->save();

            return response(
                [
                    "notification" => $response,
                    "message" => "success"
                ], 200
            );
        }

        else if($hour == 0 && $minutes == 0){

            $curl = curl_init();

            $toId = FCM::where("user_id", $id)->pluck('token');

            $title = "Trip schedules";
            $content ="Trip start time has reached";


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

            $notification = new Notifications();
            $notification->title = $title;
            $notification->user_id = $id;
            $notification->content = $content;
            $notification->save();

            return response(
                [
                    "notification" => $response,
                    "message" => "success"
                ], 200
            );
        }

        else if($hour == 0 && $minutes >= -20){

            $curl = curl_init();

            $toId = FCM::where("user_id", $id)->pluck('token');

            $title = "Trip schedules";
            $content ="Trip has reached it's start time, deleted";


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

            $trips->delete();
            Messages::where("trip_id", $trips->id)->delete();
            Bookings::where("trip_id", $trips->id)->delete();

            $notification = new Notifications();
            $notification->title = "Trip deleted!";
            $notification->user_id = $id;
            $notification->content = $content;
            $notification->save();

            return response(
                [
                    "notification" => $response,
                    "message" => "message"
                ], 200
            );
        }else{
            return response(
                [
                    "message" => $hour
                ], 200
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
        $userId = $request->userId;

        $trip = Trips::findOrFail($id);
        $start = $trip->start;
        $destination = $trip->destination;

        $bookings = Bookings::where("trip_id", $trip->id)->get();

        foreach($bookings as $book){

            $notification = new Notifications();
            $notification->title = "Booking cancelled";
            $notification->user_id = $book->user_id;
            $notification->content = "Your trip from $start to $destination has been cancelled by the driver!";
            $notification->save();

            $curl = curl_init();

            $toId = FCM::where("user_id", $book->user_id)->pluck('token');

            $payload = [

                'registration_ids' => $toId,

                'notification' => [
                  'title' => "Booking cancelled",
                  'body' => "A trip you booked has been cancelled by the driver!!"
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

        }

        Bookings::where("trip_id", $trip->id)->delete();

        if($userId == $trip->user_id){

            $trip->delete();

            Messages::where('from', $userId)->delete();

            $curl = curl_init();

            $toId = FCM::where("user_id", $id)->pluck('token');

            $payload = [

                'registration_ids' => $toId,

                'notification' => [
                  'title' => "Trip schedules",
                  'body' => "You have deleted a trip successfully!"
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

            $notification = new Notifications();
            $notification->title = "Trip schedules";
            $notification->user_id = $id;
            $notification->content = "You have deleted a trip successfully!";
            $notification->save();
        }else{
            $trip->delete();

            Messages::where('from', $userId)->delete();

            $curl = curl_init();

            $toId = FCM::where("user_id", $id)->pluck('token');

            $payload = [

                'registration_ids' => $toId,

                'notification' => [
                  'title' => "Trip schedules",
                  'body' => "You have deleted a trip successfully!"
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

            $notification = new Notifications();
            $notification->title = "Trip schedules";
            $notification->user_id = $id;
            $notification->content = "You have deleted a trip successfully!";
            $notification->save();
        }

        return response([
            'message' => 'success'
        ], 200);
    }

    //Getting all trips

    public function allTrips(){

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

    public function getBookings(){

        $bookings = Bookings::all();

        return response(
            [
                "bookings" => $bookings
            ], 200
        );
    }

}
