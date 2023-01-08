<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    const PAID = 'paid';
    const PENDING = 'pending';
    const CANCELLED = 'cancelled';
    const ACCEPTED = 'accepted';
    const COMPLETED = 'completed';
    const  ORDER_STATUSES = [self::CANCELLED, self::PENDING, self::ACCEPTED, self::COMPLETED];
    const FINANCIAL_STATUSES = [self::PAID, self::PENDING];

    public function truckTypes(): BelongsToMany
    {
        return $this->belongsToMany(TruckType::class, 'order_trucks', 'order_id', 'truck_type_id');
    }
}
