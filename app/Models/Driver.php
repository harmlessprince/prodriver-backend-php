<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int id
 * @property int user_id
 * @property int company_id
 * @property  string first_name
 * @property string last_name
 * @property string phone_number
 * @property string license_number
 * @property int picture_id
 * @property int license_picture_id
 * @property Carbon $created_at
 * @property Carbon updated_at
 *
 * @property File $picture
 * @property File $licensePicture
 * @property User $user
 * @property Company $company
 */
class Driver extends Model
{
    use HasFactory;

    protected $guarded = [];
    const MORPH_NAME = 'driver';

    public function picture(): BelongsTo
    {
        return $this->belongsTo(File::class, 'picture_id');
    }

    public function licensePicture(): BelongsTo
    {
        return $this->belongsTo(File::class, 'license_picture_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function truck()
    {
        return $this->hasOne(Truck::class, 'driver_id');
    }
}
