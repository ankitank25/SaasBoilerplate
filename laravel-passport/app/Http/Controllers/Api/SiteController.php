<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Site\SaveRequest;
use App\Http\Resources\ApiResponseResource;
use App\Services\Api\SiteConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SiteController extends Controller
{
    private $configService;

    public function __construct(SiteConfigService $siteConfigService)
    {
        $this->configService = $siteConfigService;
    }

    public function config(Request $request)
    {
        try {
            return new ApiResponseResource([
                'success' => true,
                'data' => $this->configService->collection($request),
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }

    public function save(SaveRequest $request)
    {
        try {
            $validated = $request->validated();
            $configCollection = [];
            if (isset($validated['config']) && is_array($config = $validated['config'])) {
                $configCollection = $this->configService->save($config, $request);
            }

            return new ApiResponseResource([
                'success' => true,
                'message' => 'Configuration has been saved.',
                'data' => $configCollection,
            ], 200);
        } catch (ApiException $e) {
            Log::error('API Exception: '.$e->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }
}
