<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripStatus extends Model
{
    use HasFactory;
    protected $guarded = [];
    const STATUS_PENDING = 'pending';
    const STATUS_IN_TRANSIT = 'intransit';
    const STATUS_ACCIDENT =  'accidented';
    const STATUS_AWAITING_OFFLOADING = 'awaiting-offloading';
    const STATUS_DIVERTED = 'diverted';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'cancelled';
    const STATUS_LOADING = 'loading';
    const STATUS_IN_PREMISE = 'in-premise';
    const STATUS_GATED_OUT = 'gated_out';
    const INACTIVE_TRIP_STATUSES = [self::STATUS_CANCELED, self::STATUS_DELIVERED];
    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_IN_TRANSIT,
        self::STATUS_ACCIDENT,
        self::STATUS_AWAITING_OFFLOADING,
        self::STATUS_DIVERTED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELED,
        self::STATUS_COMPLETED,
        self::STATUS_LOADING,
        self::STATUS_IN_PREMISE,
        self::STATUS_GATED_OUT
    ];


    public function trips()
    {
        return $this->hasMany(Trip::class, 'trip_status_id');
    }
}
