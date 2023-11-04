<?php

namespace App\Http\Resources\Api\Space;

use App\Models\SpaceRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpaceUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = parent::toArray($request);
        
        $resource['id'] = $resource['uuid'];

        unset($resource['uuid']);

        if(isset($this->pivot['role_id']) && ($roleId = $this->pivot['role_id'])) {
            $role = SpaceRole::where("id", $roleId)->first();
            if($role) {
                $resource['role_id'] = $roleId;
                $resource['role'] = $role->name;
                $resource['abilities'] = $role->abilities;
            }
        }

        unset(
            $resource['email_verified_at'], 
            $resource['two_factor_type'], 
            $resource['terms_agreed'], 
            $resource['status'], 
            $resource['pivot'], 
            $resource['created_at'], 
            $resource['updated_at']
        );
        
        return $resource;
    }
}
