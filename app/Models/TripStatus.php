<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripStatus extends Model
{
    use HasFactory;
    protected $guarded = [];
    const STATUS_PENDING = 'pending';
    const STATUS_IN_TRANSIT = 'in-transit';
    const STATUS_ACCIDENT =  'awaiting-offloading';
    const STATUS_AWAITING_OFFLOADING = 'awaiting-offloading';
    const STATUS_DIVERTED = 'diverted';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELED = 'canceled';
    const INACTIVE_TRIP_STATUSES = [self::STATUS_CANCELED, self::STATUS_DELIVERED];
}
