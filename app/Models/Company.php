<?php

namespace App\Models;

use App\Utils\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property string name
 * @property string email
 * @property string phone_number
 * @property string rc_number
 * @property int user_id
 * @property-read int id
 *
 * @property User $user
 * @property Document cacDocument
 * @property Document goodsInTransitInsurance
 * @property Document fidelityInsurance
 */
class Company extends Model
{
    use HasFactory;

    const MORPH_NAME = 'company';

    const RELATIONS = ['user', 'cacDocument', 'goodsInTransitInsurance', 'fidelityInsurance'];

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the guarantor's id card.
     */
    public function cacDocument(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable')->where('document_type', DocumentType::CAC_DOCUMENT['key']);
    }

    public function goodsInTransitInsurance(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable')->where('document_type', DocumentType::GOODS_IN_TRANSIT_INSURANCE['key']);
    }

    public function fidelityInsurance(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable')->where('document_type', DocumentType::FIDELITY_INSURANCE['key']);
    }
}
