<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @package App
 * @property int $id
 * @property string $name
 * @property string $type Type of file e.g video, audio, document
 * @property string $mimetype File mimetype e.g application/pdf
 * @property string $provider Storage service provider
 * @property string $path File path within fs
 * @property string $url
 * @property string $owner_type Type of model that owns file
 * @property int $owner_id ID of model that owns file
 * @property int $creator_id ID of user that created file
 * @property string $meta_data File metadata
 *
 * @property User $creator
 * @property Model $owner
 */
class File extends Model
{
    use HasFactory, SoftDeletes;

    public const OWNER_TYPE_USER = User::MORPH_NAME;
    public const OWNER_TYPE_GUARANTOR = Guarantor::MORPH_NAME;
    public const OWNER_TYPE_COMPANY = Company::MORPH_NAME;

    public const OWNER_TYPE_DOCUMENT = Document::MORPH_NAME;
    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';
    public const TYPE_AUDIO = 'audio';
    public const TYPE_DOCUMENT = 'document';
    public const USER_OWNER_TYPE = 'user';
    public const PROJECT_OWNER_TYPE = 'project';
    public const  MIMETYPES = [
        'image/jpeg' => self::TYPE_IMAGE,
        'image/png' => self::TYPE_IMAGE,
        'image/gif' => self::TYPE_IMAGE,
        'image/heif' => self::TYPE_IMAGE,
        'image/heif-sequence' => self::TYPE_IMAGE,
        'image/heic' => self::TYPE_IMAGE,
        'image/heic-sequence' => self::TYPE_IMAGE,
        'image/avif' => self::TYPE_IMAGE,
        'image/avif-sequence' => self::TYPE_IMAGE,
    ];

    protected $casts = array(
        'metadata' => 'array',
    );
    protected $guarded = [];
    protected $visible = [
        'id', 'name', 'type', 'mimetype', 'url',
    ];
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

}
