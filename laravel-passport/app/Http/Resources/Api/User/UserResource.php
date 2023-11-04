<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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

        $resource['meta'] = $this->meta->pluck('value', 'name');

        if ($this->two_factor_type != null) {
            //$resource['twofa_verified_at'] = ($this->currentAccessToken()) ? $this->currentAccessToken()->twofa_verified_at : null;
        }

        return $resource;
    }
}
