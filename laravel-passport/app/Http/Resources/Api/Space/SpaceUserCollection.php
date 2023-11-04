<?php

namespace App\Http\Resources\Api\Space;

use App\Models\SpaceUser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SpaceUserCollection extends ResourceCollection
{
    public $collects = SpaceUserResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
