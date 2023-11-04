<?php

namespace App\Listeners\Api\Space;

use App\Models\SpaceRole;
use App\Events\Space\Created;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnCreate
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Created $event): void
    {
        if(($user = $event->user) && ($space = $event->space)) {
            $adminRole = SpaceRole::create([
                "space_id" => $space->id, 
                "name" => "Administrator", 
                "abilities" => json_encode(SpaceRole::getPredefineRoleResources("admin")),
            ]);
            $user->spaces()->attach($space, ['role_id' => $adminRole->id]);
            $user->current_space = $space->uuid;
            $user->save();
            
            SpaceRole::create([
                "space_id" => $space->id, 
                "name" => "Manager", 
                "abilities" => json_encode(SpaceRole::getPredefineRoleResources("manager")),
            ]);
        }
    }
}
