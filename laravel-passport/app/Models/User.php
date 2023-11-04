<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens {
        createToken as passportCreateToken;
    }
    use HasFactory, Notifiable;

    const USER_ROLE_ADMIN = 'admin';

    const USER_ROLE_SUPPORT = 'support';

    const USER_ROLE_CUSTOMER = 'customer';

    const USER_STATUS_ACTIVE = 1;

    const USER_STATUS_INACTIVE = 2;

    const USER_STATUS_LOCKED = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'password',
        'terms_agreed',
        'two_factor_type',
        'role',
        'current_space',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function createToken($name, array $scopes = [])
    {
        $passportToken = $this->passportCreateToken($name, $scopes);

        $token = $passportToken->token;

        if ($this->two_factor_type == 'email') {
            $token->two_factor_code = random_int(10000000, 99999999);
            $token->two_factor_type = 'email';
            $token->two_factor_expires_at = now()->addMinutes(10);
            $token->two_factor_verified_at = null;
        }

        return $passportToken;
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function meta(): HasMany
    {
        return $this->hasMany(UserMeta::class, 'user_id', 'id');
    }

    public function spaces(): BelongsToMany
    {
        return $this->belongsToMany(Space::class, 'space_user', 'user_id', 'space_id');
        //return $this->hasManyThrough(Space::class, SpaceUser::class, "user_id", "id", "id", "space_id");
    }

    public function currentSpace(): HasOne
    {
        return $this->hasOne(Space::class, 'id', 'current_space');
    }

    public function currentRole(): HasOneThrough
    {
        return $this->hasOneThrough(SpaceRole::class, SpaceUser::class, "space_id", "id", "current_space", "role_id");
    }

    /* public function spaceRoles()
    {
        return $this->belongsToMany(Space::class);
    } */

    public function spaceInvites(): HasMany
    {
        return $this->hasMany(SpaceInvite::class, 'user_id');
    }
}
