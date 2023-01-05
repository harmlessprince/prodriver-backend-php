<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * User model class
 * @package App\Models
 *
 * @property-read int $id
 * @property string $target_type
 * @property int $target_id
 * @property string $type
 * @property  string $token
 * @property boolean $used
 * @property boolean $active
 * @property string $metadata
 * @property Carbon $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property  Model $target
 */
class AppToken extends Model
{
    public const TARGET_TYPE_USER = User::MORPH_NAME;
    public const TYPE_PASSWORD_RESET = 'password-reset';
    public const TYPE_EMAIL_VERIFICATION = 'email-verification';
    public const TYPE_PHONE_NUMBER_VERIFICATION = 'phone-number-verification';
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = array(
        'used' => false,
        'active' => true,
    );
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = array(
        'expires_at' => 'datetime',
        'metadata' => 'array',
    );

    protected $fillable = ['target_type', 'target_id', 'type', 'token', 'expires_at'];

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    public function hasExpired(): bool
    {
        return $this->expires_at && $this->expires_at <= new \DateTime;
    }

    public function isValid(): bool
    {
        return $this->active && !$this->used && !$this->hasExpired();
    }
}
