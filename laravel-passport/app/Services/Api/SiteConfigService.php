<?php

namespace App\Services\Api;

use App\Http\Resources\Api\Setting\SettingCollection;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteConfigService
{
    public function collection(Request $request)
    {
        if (Cache::has('site_config')) {
            return Cache::get('site_config');
        } else {
            return Cache::rememberForever('site_config', function () use ($request) {
                $collection = new SettingCollection(Setting::get());

                return $collection->toArray($request);
            });
        }
    }

    public function save(array $config, Request $request)
    {
        foreach ($config as $key => $value) {
            Setting::updateOrCreate(
                ['name' => $key],
                ['value' => $value]
            );
        }

        $collection = new SettingCollection(Setting::get());
        $collection = $collection->toArray($request);

        return Cache::put('site_config', $collection);
    }
}
