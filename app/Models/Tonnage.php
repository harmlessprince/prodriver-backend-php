<?php

namespace App\Models;

use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tonnage extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    public array $searchable = [
        'name',
    ];
    protected $guarded = [];

 
}
