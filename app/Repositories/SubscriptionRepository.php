<?php

namespace App\Repositories;

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
}