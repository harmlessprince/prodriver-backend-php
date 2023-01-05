<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
/**
 * @property string name
 * @property string email
 * @property string home_address
 * @property string work_address
 * @property string phone_number
 * @property string gender
 * @property string relationship
 * @property string occupation
 * @property int user_id
 * @property-read int id
 *
 * @property User $user
 * @property Document $idCard
 */
class Guarantor extends Model
{
    use HasFactory;

    const MORPH_NAME = 'guarantor';

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the guarantor's id card.
     */
    public function idCard(): MorphOne
    {
        return $this->morphOne(Document::class, 'documentable');
    }
}
