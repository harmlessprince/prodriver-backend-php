<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaybillStatus extends Model
{
    use HasFactory;
    protected $guarded = [];
    const STATUS_PENDING = 'pending';
    const STATUS_IN_TRANSIT = 'in-transit';
    const STATUS_RECEIVED =  'received';
    const STATUS_INVOICED = 'invoiced';
}
