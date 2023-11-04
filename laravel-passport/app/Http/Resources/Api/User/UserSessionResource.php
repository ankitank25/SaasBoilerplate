<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

class UserSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = parent::toArray($request);

        $user = $request->user();
        $currentAccessToken = $user->token();

        $accessToken = (object) $resource;

        $agent = new Agent();
        $agent->setUserAgent($accessToken->device_info);

        $location = 'Unknown';
        /* if ($accessToken->ip && ($position = Location::get($accessToken->ip))) {
            $location = implode(', ', array_filter([$position->countryName, $position->cityName]));
        } */

        $resource = [
            'id' => $accessToken->id,
            'ip' => $accessToken->ip ?: 'Unknown',
            'location' => $location,
            'device' => $agent->device() ?: 'Unknown',
            'platform' => $agent->platform() ?: 'Unknown',
            'browser' => $agent->browser() ?: 'Unknown',
            'current' => $currentAccessToken->id === $accessToken->id || false,
            'created_at' => $accessToken->created_at,
        ];

        return $resource;
    }
}
