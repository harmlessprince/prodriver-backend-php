<?php

namespace App\Models;

use App\Utils\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int id
 * @property int truck_owner_id
 * @property int company_id
 * @property int truck_type_id
 * @property int registration_number
 * @property int tonnage_id
 * @property string chassis_number
 * @property string maker
 * @property string model
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
    use HasFactory, SoftDeletes;
    const MORPH_NAME = 'truck';

    public function pictures(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable')->where('document_type', DocumentType::TRUCK_PICTURE['key']);
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
}
