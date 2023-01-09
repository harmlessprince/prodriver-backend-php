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
    const ACCEPTED = 'accepted';
    const COMPLETED = 'completed';
    const  ORDER_STATUSES = [self::CANCELLED, self::PENDING, self::ACCEPTED, self::COMPLETED];
    const FINANCIAL_STATUSES = [self::PAID, self::PENDING];

    const RELATIONS = ['truckTypes', 'tonnage'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public  function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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
    public function truckTypes(): BelongsToMany
    {
        return $this->belongsToMany(TruckType::class, 'order_trucks', 'order_id', 'truck_type_id');
    }
    public function tonnage(): BelongsTo
    {
        return $this->belongsTo(Tonnage::class, 'tonnage_id');
    }

}
