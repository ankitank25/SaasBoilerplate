<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Page\PageResource;
use App\Http\Resources\ApiResponseResource;
use App\Models\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PageController extends Controller
{
    public function get(Request $request)
    {
        try {
            if (! ($slug = $request->slug)) {
                throw new ApiException('Page slug not provided', 422);
            }

            $page = Page::where(['url_key' => $slug, 'status' => Page::STATUS_ENABLED])->firstOrFail();

            return new ApiResponseResource([
                'success' => true,
                'data' => new PageResource($page),
            ], 200);
        } catch (ModelNotFoundException $e) {
            Log::error('ModelNotFound Exception: '.$e->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => 'Requested page not found',
            ], 404);
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
