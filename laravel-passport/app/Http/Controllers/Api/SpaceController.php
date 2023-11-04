<?php

namespace App\Http\Controllers\Api;

use App\Events\Space\Created;
use Throwable;
use App\Models\Space;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Space\SpaceResource;
use App\Http\Resources\Api\Space\SpaceCollection;
use App\Http\Resources\ApiResponseResource;
use App\Http\Requests\Api\Space\CreateRequest;
use App\Http\Requests\Api\Space\UpdateRequest;
use App\Models\SpaceRole;

class SpaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            return new ApiResponseResource([
                'success' => true,
                'data' => new SpaceCollection($request->user()->spaces()->with(["users", "invitations"])->get())
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
    public function store(CreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['owner'] = $request->user()->id;

            $space = Space::create($validated);

            event(new Created($request->user(), $space));

            return new ApiResponseResource([
                'success' => true,
                'message' => __('Space has been created successfully.'),
                'data' => new SpaceResource($space)
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
    public function show(Space $space)
    {
        try {
            return new ApiResponseResource([
                'success' => true,
                'data' => new SpaceResource($space)
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
    public function update(UpdateRequest $request, Space $space)
    {
        try {
            $validated = $request->validated();

            $space->update($validated);

            return new ApiResponseResource([
                'success' => true,
                'message' => __('Space has been updated successfully.'),
                'data' => new SpaceResource($space)
            ], 200);

            throw new ApiException("Space not found.", 401);
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
    public function destroy(Request $request, Space $space)
    {
        try {
            $space->delete();
            return new ApiResponseResource([
                'success' => true,
                'message' => __('Space has been removed successfully.')
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
}
