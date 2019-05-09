<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\Status;
use App\Models\Services;
use App\Models\Subscriptions;

class SubscriptionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $customer = Users::where('nro_cliente', $request->customer)->first();
        if (!$customer) {
            return response()->json('Customer not found', 404);
        }
        
        $service = Services::where('code', $request->service)->first();
        if (!$service) {
            return response()->json('Service not found', 404);
        }
        
        // the subscribe status
        $status = Status::where('status', 'subscribe')->first();
        
        $subsRepo = new \App\Repositories\SubscriptionRepository();
        $previous = $subsRepo->existSubscription($customer, $service);
        
        if ($previous!==false && $previous->status_id === $status->id) {
            return response()->json('Not Acceptable', 406);
        }
        
        $subscription = $subsRepo->save([
           'customer' => $customer, 
           'service' => $service, 
           'status' => $status, 
           'date' => $request->date, 
        ]);
        
        return response()->json($subscription, 201);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $customer
     * @param  string  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy($customer, $service)
    {
        $customer = Users::where('nro_cliente', $customer)->first();
        if (!$customer) {
            return response()->json('Customer not found', 404);
        }
        
        $service = Services::where('code', $service)->first();
        if (!$service) {
            return response()->json('Service not found', 404);
        }
        
        // the subscribe status
        $unsubscribe = Status::where('status', 'unsubscribe')->first();
        
        $subsRepo = new \App\Repositories\SubscriptionRepository();
        $subscription = $subsRepo->existSubscription($customer, $service);
        
        if ($subscription!==false && $subscription->status_id === $unsubscribe->id) {
            return response()->json('Not Acceptable, is unsubscribe.', 406);
        }
        
        $subscriptionDelete = $subsRepo->save([
           'customer' => $customer, 
           'service' => $service, 
           'status' => $unsubscribe,
           'previousStatus' => $subscription->status, 
           'date' => date('Y-m-d'), 
        ]);
        
        return response()->json($subscriptionDelete, 204);
    }
}
