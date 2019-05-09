<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscriptions';
    
    public function user() {
        return $this->belongsTo('App\Models\Users', 'user_id', 'id');
    }
    
    public function service() {
        return $this->belongsTo('App\Models\Services', 'service_id', 'id');
    }
    
    public function status() {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id');
    }
    
    public function previousStatus() {
        return $this->belongsTo('App\Models\Status', 'previous_status', 'id');
    }

}
