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
            'maintenance_mode',
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
            'company_fav_icon',

            'cookie_setting'

        ];

        $rawSettings = BusinessSettings::whereIn('type', $settingKeys)->pluck('value', 'type');

        $settings = $rawSettings->toArray();
        if (isset($settings['default_location']) && isset($settings['colors']) && isset($settings['cookie_setting'])) {
            $location = json_decode($settings['default_location'], true);
            $colors = json_decode($settings['colors'], true);
            $cookie = json_decode($settings['cookie_setting'], true);
            $settings['latitude'] = $location['lat'] ?? '';
            $settings['longitude'] = $location['lng'] ?? '';

            $settings['primaryColor'] = $colors['primary'] ?? '';
            $settings['secondaryColor'] = $colors['secondary'] ?? '';

            $settings['cookie_status'] = $cookie['status'] ?? 0;
            $settings['cookie_text'] = $cookie['cookie_text'] ?? '';

        }
        return view('settings.index', compact('settings'));
    }

    public function generalUpdate(Request $request)
    {
        $fields = [
            'company_name',
            'company_phone',
            'company_email',
            'company_address',
            'map_url',
            'latitude',
            'longitude',
            'timezone',
            'country',
            'company_copyright_text',
            'decimal_point_settings',
            'primaryColor',
            'secondaryColor',
            'maintenance_mode'
    
        ];

        foreach ($fields as $key) {
            BusinessSettings::set($key, $request->input($key));
        }

        $imageFields = [
            'company_web_logo',
            'company_footer_logo',
            'company_fav_icon',
            'loader_gif',
            'company_mobile_logo',
    
        ];

        foreach ($imageFields as $key) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $fileName = time(). '_' . $file->getClientOriginalName();
                $destination = public_path('storage/company');

                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }
                $file->move($destination, $fileName);
                BusinessSettings::set($key, $fileName);
            }
        }

        return redirect()->back()->with('success', 'General Settings Updated Successfully!');

    }

    public function cookieUpdate(Request $request)
    {
        $data = [
            'status' => $request->has('enable_cookies'),
            'cookie_text' => $request->input('cookie_message'),
        ];

        BusinessSettings::set('cookie_setting', json_encode($data));

        return redirect()->back()->with('success', 'Cookie Settings updated.');
    }
}
