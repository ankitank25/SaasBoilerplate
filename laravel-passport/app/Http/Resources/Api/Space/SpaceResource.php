<?php

namespace App\Http\Resources\Api\Space;

use App\Models\SpaceInvite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Space\SpaceUserCollection;

class SpaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = parent::toArray($request);

        if($this->users) {
            $resource['users'] = new SpaceUserCollection($this->users);
        }

        return $resource;
    }
}
