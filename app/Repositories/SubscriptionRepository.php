<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

use App\Models\Subscriptions;
use App\Models\Users;
use App\Models\Status;
use App\Models\Services;

class SubscriptionRepository {
  protected $model;
  private $subscription;
  
  public function __construct(Subscriptions $subscription = null)
  {
      $this->subscription = $subscription;
  }
  
  public function existSubscription(Users $customer, Services $service) {
      $subscription = Subscriptions::where([
          'user_id' => $customer->id,
          'service_id' => $service->id
      ])->orderBy('id', 'desc')->first();
      
      return $subscription?$subscription:false;
  }
  
  public function save($data) {
    $subscription = new Subscriptions();
        
    $subscription->user()->associate($data['customer']);
    $subscription->service()->associate($data['service']);
    $subscription->status()->associate($data['status']);
    $subscription->status_change = $data['date'];
    
    if (isset($data['previousStatus'])) {
      $subscription->previousStatus()->associate($data['previousStatus']);
    }
    
    $subscription->save();

    return $subscription;
  }
  
  public function getSubscriptions($date) {
    $start = $date . ' 00:00:00';
    $end = $date . ' 23:59:59';
    $subscriptions = Subscriptions::query()
        ->whereHas('status', function($query) {
            $query->where('status', '=', 'subscribe');
        })
        ->where('status_change', '>=', $start)
        ->where('status_change', '<=', $end)->count();
        
    return $subscriptions;
  }
  
  public function getUnsubscriptions($date) {
    $start = $date . ' 00:00:00';
    $end = $date . ' 23:59:59';
    
    $subscriptions = Subscriptions::query()
        ->whereHas('status', function($query) {
            $query->where('status', '=', 'unsubscribe');
        })
        ->where('status_change', '>=', $start)
        ->where('status_change', '<=', $end)->count();
    
    return $subscriptions;
  }
  
  public function getActives($date) {
    $start = $date . ' 23:59:59';
    $subscriptionsAll = DB::table('subscriptions')
        ->select(DB::raw('MAX(id) as id'))
        ->where('status_change', '<=', $start)
        ->groupBy('user_id');
    
    $subscriptionsActives = DB::table('subscriptions')
        ->join('status', function ($join) {
            $join->on('status.id', '=', 'subscriptions.status_id');
        })
        ->joinSub($subscriptionsAll, 'sa', function ($join) {
            $join->on('subscriptions.id', '=', 'sa.id');
        })
        ->where('subscriptions.status_change', '<=', $start)
        ->count();
    return $subscriptionsActives;
  }
}
