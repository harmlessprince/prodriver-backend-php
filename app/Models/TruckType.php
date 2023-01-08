<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TruckType extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function truckTypes(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_trucks', 'truck_type_id', 'order_id');
    }
}
