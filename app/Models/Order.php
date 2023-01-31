<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    use HasFactory, SoftDeletes;
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

    const RELATIONS = ['truckTypes', 'tonnage', ];

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
}
