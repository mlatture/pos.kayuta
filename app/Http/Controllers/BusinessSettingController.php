<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSettings;
use App\Models\Setting;
class BusinessSettingController extends Controller
{
    //

    public function index()
    {
        $settingKeys = ['maintenance_mode', 'company_name', 'company_phone', 'company_email', 'company_address', 'map_url', 'default_location', 'timezone', 'country', 'company_copyright_text', 'decimal_point_settings', 'colors', 'company_web_logo', 'company_mobile_logo', 'company_footer_logo', 'loader_gif', 'company_fav_icon', 'cookie_setting', 'cart_hold_time', 'dynamic_pricing', 'FB_PIXEL_ID', 'FB_ACCESS_TOKEN'];

        $rawSettings = BusinessSettings::whereIn('type', $settingKeys)->pluck('value', 'type');

        $settings = $rawSettings->toArray();
        
        $location = json_decode($settings['default_location'] ?? '{}', true);
        $colors = json_decode($settings['colors'] ?? '{}', true);
        $cookie = json_decode($settings['cookie_setting'] ?? '{}', true);

        $settings['latitude'] = $location['lat'] ?? '';
        $settings['longitude'] = $location['lng'] ?? '';
        $settings['primaryColor'] = $colors['primary'] ?? '';
        $settings['secondaryColor'] = $colors['secondary'] ?? '';
        $settings['cookie_status'] = $cookie['status'] ?? 0;
        $settings['cookie_text'] = $cookie['cookie_text'] ?? '';

        $settingsKeys2 = ['is_grid_view', 'golf_listing_show', 'boat_listing_show', 'pool_listing_show', 'product_listing_show'];

        $rawSettings = Setting::whereIn('key', $settingsKeys2)->get();

        $settings2 = [];

        foreach ($rawSettings as $row) {
            $key = $row->key;

            $settings2[$key] = $row->$key ?? null;
        }

        return view('settings.index', compact('settings', 'settings2'));
    }

    public function generalUpdate(Request $request)
    {
        $fields = ['company_name', 'company_phone', 'company_email', 'company_address', 'map_url', 'latitude', 'longitude', 'timezone', 'country', 'company_copyright_text', 'decimal_point_settings', 'primaryColor', 'secondaryColor', 'maintenance_mode', 'FB_ACCESS_TOKEN', 'FB_PIXEL_ID'];

        foreach ($fields as $key) {
            BusinessSettings::set($key, $request->input($key));
        }

        $imageFields = ['company_web_logo', 'company_footer_logo', 'company_fav_icon', 'loader_gif', 'company_mobile_logo'];

        foreach ($imageFields as $key) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $fileName = time() . '_' . $file->getClientOriginalName();
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

    public function dynamicPricingUpdate(Request $request)
    {
        BusinessSettings::set('dynamic_pricing', $request->has('togglePricing') ? 1 : 0);

        return redirect()->back()->with('success', 'Dynamic pricing settings updated.');
    }

    public function searchUpdate(Request $request)
    {
        Setting::gridViewUpdate('is_grid_view', $request->has('grid_view') ? '1' : '0');

        return redirect()->back()->with('success', 'Search Settings updated.');
    }

    public function cartUpdate(Request $request)
    {
        $cartSetting = BusinessSettings::where('type', 'cart_hold_time')->first();
        if ($cartSetting) {
            $cartSetting->value = $request->input('cart_hold_time');
            $cartSetting->save();
        }

        $toggles = [
            'golf_listing_show' => 'golf_listing',
            'boat_listing_show' => 'boat_listing',
            'pool_listing_show' => 'pool_listing',
            'product_listing_show' => 'product_listing',
        ];

        foreach ($toggles as $dbColumn => $inputName) {
            $setting = Setting::where('key', $dbColumn)->first();
            if ($setting) {
                $setting->$dbColumn = $request->has($inputName) ? 1 : 0;
                $setting->save();
            }
        }

        return redirect()->back()->with('success', 'Cart Settings updated Successfully.');
    }
}
