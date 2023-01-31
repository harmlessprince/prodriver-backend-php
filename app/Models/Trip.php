<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    const MORPH_NAME = 'trip';

    const STATUS_PENDING = 'pending';
    const STATUS_IN_TRANSIT = 'in-transit';
    const STATUS_ACCIDENT = 'awaiting-offloading';
    const STATUS_AWAITING_OFFLOADING = 'awaiting-offloading';
    const STATUS_DIVERTED = 'diverted';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELED = 'canceled';
    const INACTIVE_TRIP_STATUSES = [self::STATUS_CANCELED, self::STATUS_DELIVERED];

    const RELATIONS = [
        'tripStatus',
        'waybillStatus',
        'approvedBy',
        'matchedBy',
        'declinedBy',
        'accountManager',
        'driver',
        'truck',
        'order',
        'cargoOwner',
        'transporter',
        'waybillPicture'
    ];

    public function tripStatus(): BelongsTo
    {
        return $this->belongsTo(TripStatus::class, 'trip_status_id');
    }


    public function waybillStatus(): BelongsTo
    {
        return $this->belongsTo(WaybillStatus::class, 'way_bill_status_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function matchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matched_by');
    }

    public function declinedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'declined_by');
    }

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function cargoOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cargo_owner_id');
    }

    public function transporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transporter_id');
    }

    public function waybillPicture(): BelongsTo
    {
        return $this->belongsTo(File::class, 'way_bill_status_id');
    }
}
