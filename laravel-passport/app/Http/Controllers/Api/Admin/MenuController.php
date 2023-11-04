<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Menu\CreateRequest;
use App\Http\Resources\Api\Menu\MenuCollection;
use App\Http\Resources\Api\Menu\MenuResource;
use App\Http\Resources\ApiResponseResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MenuController extends Controller
{
    public function list(Request $request)
    {
        try {
            return new ApiResponseResource([
                'success' => true,
                'data' => new MenuCollection(Menu::paginate(20)),
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }

    public function create(CreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $menu = Menu::create($validated);

            return new ApiResponseResource([
                'success' => true,
                'message' => 'Menu created successfully',
                'data' => new MenuResource($menu),
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }

    public function update(CreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $menu = Menu::firstWhere('id', $request->uuid);
            $menu->update($validated);

            return new ApiResponseResource([
                'success' => true,
                'message' => 'Menu updated successfully',
                'data' => new MenuResource($menu),
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            if (! $request->ids || ! is_array($request->ids)) {
                throw new ApiException('No menu id(s) provided.', 422);
            }

            Menu::destroy($request->ids);

            return new ApiResponseResource([
                'success' => true,
                'message' => 'Selected menu(s) deleted successfully.',
            ], 200);
        } catch (ApiException $e) {
            Log::error('API Exception: '.$e->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }
}
