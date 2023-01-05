<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSpouse extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'user_spouses';
}
