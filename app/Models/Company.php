<?php

namespace App\Models;

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
        return $this->morphOne(Document::class, 'documentable');
    }

    public function goodsInTransitInsurance(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable');
    }

    public function fidelityInsurance(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable');
    }
}
