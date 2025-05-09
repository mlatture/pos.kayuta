<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSettings;
class BusinessSettingController extends Controller
{
    //
    
    public function index()
    {
        $settingKeys = [
            'company_name',
            'company_phone',
            'company_email',
            'company_address',
            'map_url',
            'default_location',
            'timezone',
            'country',
            'company_copyright_text',
            'decimal_point_settings',
            'colors',

            'company_web_logo',
            'company_mobile_logo',
            'company_footer_logo',
            'loader_gif',
            'company_fav_icon'

        ];

        $rawSettings = BusinessSettings::whereIn('type', $settingKeys)->pluck('value', 'type');

        $settings = $rawSettings->toArray();
        if (isset($settings['default_location']) && isset($settings['colors'])) {
            $location = json_decode($settings['default_location'], true);
            $colors = json_decode($settings['colors'], true);
            $settings['latitude'] = $location['lat'] ?? '';
            $settings['longitude'] = $location['lng'] ?? '';

            $settings['primaryColor'] = $colors['primary'] ?? '';
            $settings['secondaryColor'] = $colors['secondary'] ?? '';

        }
        return view('settings.index', compact('settings'));
    }
}
