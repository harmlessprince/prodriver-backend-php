<?php

namespace App\Models;

use App\Filters\OrderBuilder;
use App\Traits\SearchableTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int id
 * @property int tonnage_id
 * @property int truck_type_id
 * @property int cargo_owner_id
 * @property int created_by
 * @property int approved_by
 * @property int matched_by
 * @property int declined_by
 * @property int cancelled_by
 * @property int accepted_by
 * @property float amount_willing_to_pay
 * @property boolean display_amount_willing_to_pay
 * @property string description
 * @property string pickup_address
 * @property string destination_address
 * @property Carbon date_needed
 * @property string financial_status
 * @property string status
 *
 * @property Collection<TruckType> $truckTypes
 * @property User $user
 * @property User createdBy
 * @property User approvedBy
 * @property User matchedBy
 * @property User declinedBy
 * @property User cancelledBy
 * @property User AcceptedBy
 * @property Tonnage tonnage
 * @property User
 */
class Order extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;
    protected $guarded = [];
    protected $attributes = [
        'financial_status' => self::PENDING,
        'status' => self::PENDING,
    ];
    protected  $casts = [
        'display_amount_willing_to_pay' => 'boolean',
    ];
    const PAID = 'paid';
    const PENDING = 'pending';
    const CANCELLED = 'cancelled';
    const DECLINED = 'declined';
    const ACCEPTED = 'accepted';
    const COMPLETED = 'completed';
    const APPROVED = 'approved';
    const MATCHED = 'matched';
    const  ORDER_STATUSES = [self::CANCELLED, self::PENDING, self::ACCEPTED, self::COMPLETED, self::DECLINED];
    const FINANCIAL_STATUSES = [self::PAID, self::PENDING];
    public array $searchable = [
        'cargoOwner.first_name', 'cargoOwner.last_name', 'cargoOwner.middle_name',
        'pickup_address', 'destination_address'
    ];
    const MORPH_NAME = 'order';

    const RELATIONS = ['truckTypes', 'tonnage', 'pictures','cargoOwner:id,first_name,last_name,middle_name,phone_number,email'];

    public function cargoOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cargo_owner_id');
    }
    public  function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public  function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
    public  function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public  function matchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matched_by');
    }
    public  function declinedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'declined_by');
    }
    public  function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }
    public function truckTypes(): BelongsToMany
    {
        return $this->belongsToMany(TruckType::class, 'order_trucks', 'order_id', 'truck_type_id');
    }
    public function tonnage(): BelongsTo
    {
        return $this->belongsTo(Tonnage::class, 'tonnage_id');
    }
    public function acceptedOrMatchedRequest(): HasMany
    {
        return $this->hasMany(AcceptedOrder::class, 'order_id');
    }
    public function pictures(): MorphMany
    {
        return $this->morphMany(File::class, 'owner');
    }
 
    public function acceptedRequests()
    {
       return $this->hasMany(AcceptedOrder::class, 'order_id');
    }
    public function newEloquentBuilder($query): OrderBuilder
    {
        return new OrderBuilder($query);
    }
}
