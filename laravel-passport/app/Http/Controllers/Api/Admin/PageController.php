<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Page\CreateRequest;
use App\Http\Resources\Api\Page\PageCollection;
use App\Http\Resources\Api\Page\PageResource;
use App\Http\Resources\ApiResponseResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PageController extends Controller
{
    public function list(Request $request)
    {
        try {
            return new ApiResponseResource([
                'success' => true,
                'data' => new PageCollection(Page::paginate(20)),
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
            $page = Page::create($validated);

            return new ApiResponseResource([
                'success' => true,
                'message' => 'Page created successfully',
                'data' => new PageResource($page),
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
            $page = Page::firstWhere('id', $request->uuid);
            $page->update($validated);

            return new ApiResponseResource([
                'success' => true,
                'message' => 'Page updated successfully',
                'data' => new PageResource($page),
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
                throw new ApiException('No page id(s) provided.', 422);
            }

            Page::destroy($request->ids);

            return new ApiResponseResource([
                'success' => true,
                'message' => 'Selected page(s) deleted successfully.',
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
