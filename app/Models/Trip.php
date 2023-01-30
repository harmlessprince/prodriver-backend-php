<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory, SoftDeletes;
    const MORPH_NAME = 'trip';

    const STATUS_PENDING = 'pending';
    const STATUS_IN_TRANSIT = 'in-transit';
    const STATUS_ACCIDENT =  'awaiting-offloading';
    const STATUS_AWAITING_OFFLOADING = 'awaiting-offloading';
    const STATUS_DIVERTED = 'diverted';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELED = 'canceled';
    const INACTIVE_TRIP_STATUSES = [self::STATUS_CANCELED, self::STATUS_DELIVERED];

    public function tripStatus()
    {
        return $this->belongsTo(TripStatus::class, 'trip_status_id');
    }

    
    public function waybillStatus()
    {
        return $this->belongsTo(WaybillStatus::class, 'way_bill_status_id');
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
        return $this->belongsTo(User::class, 'declined_by');
    }

    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function cargoOwner()
    {
        return $this->belongsTo(User::class, 'cargo_owner_id');
    }

    public function transporter()
    {
        return $this->belongsTo(User::class, 'transporter_id');
    }

    public function waybillPicture()
    {
        return $this->belongsTo(File::class, 'way_bill_status_id');
    }
}
