<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripWaybillPicture extends Model
{
    use HasFactory;

    protected $with = ['picture', 'uploadedBy:id,first_name,last_name',  'reviewedBy:id,first_name,last_name', 'waybillStatus'];

    protected $guarded = [];

    public function picture(): BelongsTo
    {
        return $this->belongsTo(File::class, 'picture_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function waybillStatus()
    {
        return $this->belongsTo(WaybillStatus::class, 'way_bill_status_id');
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}
