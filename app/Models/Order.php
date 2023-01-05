<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    const PAID = 'paid';
    const PENDING = 'pending';
    const CANCELLED = 'cancelled';
    const ACCEPTED = 'accepted';
    const COMPLETED = 'completed';
    const  ORDER_STATUSES = [self::CANCELLED, self::PENDING, self::ACCEPTED, self::COMPLETED];
    const FINANCIAL_STATUSES = [self::PAID, self::PENDING];
}
