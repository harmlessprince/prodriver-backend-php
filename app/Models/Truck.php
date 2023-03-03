<?php

namespace App\Models;

use App\Filters\TruckBuilder;
use App\Traits\SearchableTrait;
use App\Utils\DocumentType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int id
 * @property int transporter_id
 * @property int company_id
 * @property int truck_type_id
 * @property int registration_number
 * @property int tonnage_id
 * @property string chassis_number
 * @property string maker
 * @property string model
 * @property int driver_id
 * @property int on_trip
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Collection<Document> pictures
 * @property Document proofOfOwnership
 * @property Document roadWorthiness
 * @property Document license
 * @property Document insurance
 */

class Truck extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;


    protected $guarded = [];
    const MORPH_NAME = 'truck';
    const DOCUMENT_RELATIONS   = ['picture', 'proofOfOwnership', 'roadWorthiness', 'license', 'insurance'];
    const NON_DOCUMENT_RELATIONS = ['driver', 'tonnage:id,name,value', 'truckType:id,name', 'truckOwner:id,first_name,last_name,middle_name,user_type,phone_number'];
    const RELATIONS = [...self::DOCUMENT_RELATIONS, ...self::NON_DOCUMENT_RELATIONS];
    public array $searchable = [
        'truckOwner.first_name', 'truckOwner.last_name', 'truckOwner.middle_name', 'truckOwner.email', 'truckOwner.phone_number',
        'driver.first_name', 'driver.last_name', 'driver.phone_number', 'plate_number', 'chassis_number', 'model',
        'maker'
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    public function tonnage(): BelongsTo
    {
        return $this->belongsTo(Tonnage::class, 'tonnage_id');
    }

    public function truckType(): BelongsTo
    {
        return $this->belongsTo(TruckType::class, 'truck_type_id');
    }

    public function truckOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transporter_id');
    }
    public function picture(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable')->where('document_type', DocumentType::TRUCK_PICTURE['key']);
    }
    public function proofOfOwnership(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable')->where('document_type', DocumentType::TRUCK_PROOF_OF_OWNERSHIP['key']);
    }
    public function roadWorthiness(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable')->where('document_type', DocumentType::TRUCK_ROAD_WORTHINESS['key']);
    }
    public function license(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable')->where('document_type', DocumentType::TRUCK_LICENSE['key']);
    }
    public function insurance(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable')->where('document_type', DocumentType::TRUCK_INSURANCE['key']);
    }

    public function newEloquentBuilder($query): TruckBuilder
    {
        return new TruckBuilder($query);
    }
}
