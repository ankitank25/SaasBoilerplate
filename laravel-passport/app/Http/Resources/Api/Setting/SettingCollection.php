<?php

namespace App\Http\Resources\Api\Setting;

use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Resources\Api\Menu\MenuResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SettingCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result = [];
        $result['config'] = $this->collection;
        $resources = [];

        if ($headerMenuConfig = Setting::firstWhere('name', 'header_menu_id')) {
            $headerMenu = new MenuResource(Menu::firstWhere('id', $headerMenuConfig['value']));
            $resources['header_menu'] = $headerMenu->items;
        }

        if ($footerMenuConfig = Setting::firstWhere('name', 'footer_menu_id')) {
            $footerMenu = new MenuResource(Menu::firstWhere('id', $footerMenuConfig['value']));
            $resources['footer_menu'] = $footerMenu->items;
        }

        if ($socialMenuConfig = Setting::firstWhere('name', 'social_menu_id')) {
            $socialMenu = new MenuResource(Menu::firstWhere('id', $socialMenuConfig['value']));
            $resources['social_menu'] = $socialMenu->items;
        }

        $result['resources'] = $resources;

        return $result;
    }
}
