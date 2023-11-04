<?php

namespace App\Listeners\Api\User;

use App\Models\Space;
use App\Models\SpaceInvite;
use Illuminate\Support\Str;
use App\Events\Space\Created;
use Illuminate\Auth\Events\Registered;

class OnRegister
{
    /**
     * Handle the user registration event.
     */
    public function handle(Registered $event): void
    {
        if($user = $event->user) {
            $space = Space::create([
                'name' => 'My Space',
                'owner' => $user->id,
                'status' => Space::SPACE_STATUS_ACTIVE
            ]);

            event(new Created($user, $space));

            SpaceInvite::where('email', $user->email)
            ->where('user_id', null)
            ->update(['user_id' => $user->id]);
        }
    }
}
