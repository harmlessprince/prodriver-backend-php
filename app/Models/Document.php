<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property-read int $id
 * @property int user_id
 * @property int verified_by
 * @property int declined_by
 * @property int file_id
 * @property string document_type
 * @property string status
 * @property string reason
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property User $user
 * @property User $declinedBy
 * @property User $verifiedBy
 * @property File $file
*/
class Document extends Model
{
    use HasFactory;

    const PENDING = 'pending';
    const  DECLINED = 'declined';
    const ACCEPTED = 'accepted';
    const  MORPH_NAME = 'document';

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function declinedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'declined_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
