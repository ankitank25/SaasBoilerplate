<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Models\Space;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Http\Requests\Api\SpaceRole\CreateRequest;
use App\Http\Requests\Api\SpaceRole\UpdateRequest;
use App\Http\Resources\Api\Space\SpaceRoleCollection;
use App\Http\Resources\Api\Space\SpaceRoleResource;
use App\Models\SpaceRole;

class SpaceRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Space $space)
    {
        try {
            return new ApiResponseResource([
                'success' => true,
                'data' => new SpaceRoleCollection($space->roles()->get())
            ], 200);
        }
        catch(Throwable $th) {
            Log::error("API Response Error: ". $th->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.')
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request, Space $space)
    {
        try {
            $validated = $request->validated();

            $role = $space->roles()->create($validated);

            return new ApiResponseResource([
                'success' => true,
                'message' => __('Role has been created successfully.'),
                'data' => ["space" => new SpaceRoleResource($role)]
            ], 200);
        }
        catch(Throwable $th) {
            Log::error("API Response Error: ". $th->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.')
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($space, SpaceRole $role)
    {
        try {
            return new ApiResponseResource([
                'success' => true,
                'data' => new SpaceRoleResource($role)
            ], 200);
        }
        catch(ApiException $e) {
            Log::error("API Exception: ". $e->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        }
        catch(Throwable $th) {
            Log::error("API Response Error: ". $th->getMessage());
            Log::error($th);
            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.')
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $space, SpaceRole $role)
    {
        try {
            $validated = $request->validated();

            $role->update($validated);

            return new ApiResponseResource([
                'success' => true,
                'message' => __('Role has been updated successfully.'),
                'data' => new SpaceRoleResource($role)
            ], 200);
        }
        catch(ApiException $e) {
            Log::error("API Exception: ". $e->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        }
        catch(Throwable $th) {
            Log::error("API Response Error: ". $th->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.')
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($space, SpaceRole $role)
    {
        try {
            $role->delete();
            return new ApiResponseResource([
                'success' => true,
                'message' => __('Role has been removed successfully.')
            ], 200);
        }
        catch(ApiException $e) {
            Log::error("API Exception: ". $e->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        }
        catch(Throwable $th) {
            Log::error("API Response Error: ". $th->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.')
            ], 500);
        }
    }

    public function resources()
    {
        try {
            $accessResources = config("access.resources");
            return new ApiResponseResource([
                'success' => true,
                'data' => $accessResources
            ], 200);
        }
        catch(Throwable $th) {
            Log::error("API Response Error: ". $th->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.')
            ], 500);
        }
    }
}
