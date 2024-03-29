<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Filters\OrderBuilder;
use App\Filters\UserBuilder;
use App\Traits\SearchableTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


/**
 * @property-read int id
 * @property string first_name
 * @property string middle_name
 * @property string last_name
 * @property string email
 * @property string phone_number
 * @property string gender
 * @property string user_type
 * @property Carbon date_of_birth
 * @property string marital_status
 * @property string home_address
 * @property string work_address
 * @property int country_id
 * @property int state_id
 * @property int profile_image_id
 * @property Carbon phone_number_verified_at
 * @property Carbon email_verified_at
 * @property string password
 *
 * @property UserSpouse $userSpouse
 * @property UserNextOfkin $nextOfKin
 * @property Company $company
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SearchableTrait;

    const MORPH_NAME = 'user';
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';
    const USER_TYPE_CARGO_OWNER = 'cargo-owner';
    const USER_TYPE_TRANSPORTER = 'transporter';
    const USER_TYPE_ADMIN = 'admin';
    const USER_TYPE_ACCOUNT_MANAGER = 'account-manager';

    const REGULAR_USER_TYPES = [self::USER_TYPE_TRANSPORTER, self::USER_TYPE_CARGO_OWNER];
    const ALL_USER_TYPES = [self::USER_TYPE_ADMIN, self::USER_TYPE_TRANSPORTER, self::USER_TYPE_CARGO_OWNER, self::USER_TYPE_ACCOUNT_MANAGER];

    const CARGO_OWNER_PROFILE = ['spouse', 'nextOfKin', 'company'];
    const TRANSPORTER_PROFILE = ['spouse', 'nextOfKin', 'bankAccount', 'guarantors', 'company'];

    public array $searchable = ['first_name', 'last_name', 'middle_name', 'email', 'phone_number', 'company.name', 'company.phone_number'];

    public const GENDERS = [
        self::GENDER_MALE, self::GENDER_FEMALE,
    ];
    public const MAXIMUM_NO_OF_GUARANTORS = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['profileImage'];

    public function profileImage(): BelongsTo
    {
        return $this->belongsTo(File::class, 'profile_image_id');
    }

    public function spouse(): HasOne
    {
        return $this->hasOne(UserSpouse::class);
    }

    public function nextOfKin(): HasOne
    {
        return $this->hasOne(UserNextOfKin::class);
    }

    public function bankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class);
    }

    public function guarantors(): HasMany
    {
        return $this->hasMany(Guarantor::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }
    public function trucks(): HasMany
    {
        return $this->hasMany(Truck::class, 'transporter_id');
    }

    public function transporterTrips(): HasMany
    {
        return $this->hasMany(Trip::class, 'transporter_id');
    }

    public function cargoOwnerTrips(): HasMany
    {
        return $this->hasMany(Trip::class, 'cargo_owner_id')->where('user_type', self::USER_TYPE_CARGO_OWNER);
    }

    public function myRelations($user_type): array
    {
        if ($user_type == self::USER_TYPE_TRANSPORTER) {
            return self::TRANSPORTER_PROFILE;
        }
        if ($user_type == self::USER_TYPE_CARGO_OWNER) {
            return self::CARGO_OWNER_PROFILE;
        }
        if ($user_type == self::USER_TYPE_ADMIN) {
            return [...self::CARGO_OWNER_PROFILE, ...self::TRANSPORTER_PROFILE];
        }
        return [];
    }
    public  function isAdmin(): bool
    {
        return $this->user_type == User::USER_TYPE_ADMIN;
    }
    public  function isTransporter(): bool
    {
        return $this->user_type == User::USER_TYPE_TRANSPORTER;
    }

    public  function isCargoOwner(): bool
    {
        return $this->user_type == User::USER_TYPE_CARGO_OWNER;
    }
    public  function isAccountManager(): bool
    {
        return $this->user_type == User::USER_TYPE_ACCOUNT_MANAGER;
    }

    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }
}
