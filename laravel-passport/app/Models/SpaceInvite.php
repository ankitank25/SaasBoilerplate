<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaceInvite extends Model
{
    use HasFactory, HasUuids;

    const INVITE_STATUS_PENDING = 0;
    const INVITE_STATUS_ACCEPTED = 1;
    const INVITE_STATUS_REJECTED = 2;

    protected $fillable = [
        'uuid',
        'space_id',
        'role_id',
        'user_id',
        'email',
        'status'
    ];

    public function role()
    {
        return $this->belongsTo(SpaceRole::class);
    }
}
