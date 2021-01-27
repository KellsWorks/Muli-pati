<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Subscriptions;
use App\Models\Plans;

class SubscriptionsController extends Controller
{
    public function subscribe(Request $request){

        $sub = Subscriptions::where('agent_id', $request->agent_id)->get()[0];

        $subscription = Subscriptions::findOrFail($sub->id);

        $subscription->subscribed = 1;
        $subscription->plan = $request->plan;

        $subscription->expiry_date = $sub->updated_at->addDays(30);
        $subscription->update();

        $plan = Plans::where('plan', $request->plan)->get();

        return response(
            [
            'subscription' => $subscription,
            'plan' => $plan
            ], 200
        );
    }

    public function endSubscription(Request $request){

        $subscription = Subscriptions::findOrFail($request->id);

        $expiry = $subscription->expiry_date;
        $created = $subscription->updated_at;

        if ($expiry == $created) {

            $subscription->subscribed = 0;
            $subscription->update();

            return response([
                'message' => 'Subscription ended'
            ], 200);
        }else{
            return response([
                'message' => 'Subscription still on'
            ], 200);
        }
    }
}
