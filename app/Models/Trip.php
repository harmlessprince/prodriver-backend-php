<?php

namespace App\Models;

use App\Filters\TripBuilder;
use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    protected $guarded = [];
    const MORPH_NAME = 'trip';

    const PAYOUT_COMPLETED = 'completed';
    const PAYOUT_PENDING = 'pending';
    const TRIP_STATUSES =  TripStatus::STATUSES;

    public array $searchable = [
        'trip_id',
        'accountManager.first_name', 'accountManager.last_name', 'accountManager.middle_name',
        'accountManager.phone_number',
        'cargoOwner.first_name', 'cargoOwner.last_name', 'cargoOwner.middle_name',  'cargoOwner.phone_number',
        'transporter.first_name', 'transporter.last_name', 'transporter.middle_name',  'transporter.phone_number',
        'order.pickup_address', 'order.destination_address', 'truck.plate_number'
    ];

    const RELATIONS = [
        'tripStatus',
        'waybillStatus',
        'approvedBy',
        'matchedBy',
        'declinedBy',
        'accountManager',
        'driver',
        'truck',
        'truck.truckType',
        'truck.driver',
        'order',
        'cargoOwner',
        'cargoOwner.company',
        'transporter',
        'transporter.company',
        'waybillPicture',
        'tripWaybillPictures'
        
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
        return $this->belongsTo(File::class, 'way_bill_picture_id');
    }

    public function tripWaybillPictures(): HasMany
    {
        return $this->hasMany(TripWaybillPicture::class);
    }

    public function newEloquentBuilder($query): TripBuilder
    {
        return new TripBuilder($query);
    }
}
