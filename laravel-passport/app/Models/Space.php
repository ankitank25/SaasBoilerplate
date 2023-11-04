<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    use HasFactory, HasUuids;

    const SPACE_STATUS_ACTIVE = 1;

    const SPACE_STATUS_INACTIVE = 2;

    protected $fillable = [
        'uuid',
        'owner',
        'name',
        'status',
    ];

    protected $hidden = ['pivot'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    public function users() 
    {
        return $this->belongsToMany(User::class, 'space_user')->withPivot('role_id');
    }

    public function roles()
    {
        return $this->hasMany(SpaceRole::class);
    }

    public function invitations()
    {
        return $this->hasMany(SpaceInvite::class, 'space_id')->with('role');
    }
}
