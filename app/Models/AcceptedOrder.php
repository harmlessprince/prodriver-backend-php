<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptedOrder extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }
    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function matchedBy()
    {
        return $this->belongsTo(User::class, 'matched_by');
    }

    public function declinedBy()
    {
        return $this->belongsTo(User::class, 'matched_by');
    }
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

   
}
