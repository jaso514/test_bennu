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
  
  /**
   * Verify if the customer have a service, if have it return the subscription
   * if not, return false
   * @param Users $customer
   * @param Services $service
   * @return bool|Subscriptions
   */
  public function existSubscription(Users $customer, Services $service) {
      $subscription = Subscriptions::where([
          'user_id' => $customer->id,
          'service_id' => $service->id
      ])->orderBy('id', 'desc')->first();
      
      return $subscription?$subscription:false;
  }
  
  /**
   * create and save a Subscriptions object
   * @param array $data
   * @return Subscriptions
   */
  public function save(array $data) {
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
  
  /**
   * Return the count of Subscriptions of the given date
   * @param string $date
   * @return int
   */
  public function getSubscriptions($date) {
    $start = $date . ' 00:00:00';
    $end = $date . ' 23:59:59';
    return $this->countByDate($start, $end, 'subscribe');
  }
  
  /**
   * Return the count of UnSubscriptions of the given date
   * @param string $date
   * @return int
   */
  public function getUnsubscriptions($date) {
    $start = $date . ' 00:00:00';
    $end = $date . ' 23:59:59';
    
    return $this->countByDate($start, $end, 'unsubscribe');
  }
  
  /**
   * Return the quantity of subscription by date range and status
   * @param $start
   * @param $end
   * @param $status
   * @return int
   */
  public function countByDate($start, $end, $status) {
    $subscriptions = Subscriptions::query()
        ->whereHas('status', function($query) use ($status) {
            $query->where('status', '=', $status);
          })
        ->where('status_change', '>=', $start)
        ->where('status_change', '<=', $end)->count();
    
    return $subscriptions;
  }
  
  /**
   * Return the subscriptions actives until the date
   * @param string $date
   * @return int
   */
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
