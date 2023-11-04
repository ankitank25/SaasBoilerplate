<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponseResource extends JsonResource
{
    private $statusCode;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $statusCode = 200)
    {
        $this->resource = $resource;
        $this->statusCode = $statusCode;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = (object) $this->resource;

        $data = isset($resource->data)? $resource->data : '';

        if(isset($resource->data) && isset($resource->data->resource) && $resource->data->resource instanceof LengthAwarePaginator) {
            $data = $resource->data->resource;
        }

        return [
            'success' => $resource->success,
            'message' => (isset($resource->message)) ? $resource->message : '',
            'errorCode' => (isset($resource->error_code)) ? $resource->error_code : '',
            'data' => $data,
            //'data' => (isset($resource->data->resource)) ? $resource->data->resource : ((isset($resource->data)) ? $resource->data : ''),
        ];
    }

    /**
     * Send response with status code.
     */
    public function toResponse($request)
    {
        return (new ResourceResponse($this))->toResponse($request)->setStatusCode($this->statusCode);
    }
}
