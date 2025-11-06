<?php

namespace App\Http\Controllers\Admin\ContentHub;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentHub\UpdateSettingsRequest;
use App\Models\ContentHub\ContentHubSetting;
use App\Support\SystemLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class SettingsController extends Controller
{
    public function show()
    {
        // $this->authorize('contenthub.view');
        $settings = ContentHubSetting::firstOrCreate(['id'=>1]);
        
        return view('admin.content_hub.settings', compact('settings'));
    }

    public function update(Request $request)
{
    // 1) Basic validation (controller-level)
    $v = Validator::make($request->all(), [
        'is_enabled'                     => ['nullable','boolean'],
        'ai_service_provider'            => ['required','string','max:50'],
        'ai_api_credentials'             => ['nullable'], // we'll JSON-parse below if string
        'default_publish_delay_minutes'  => ['required','integer','min:0','max:1440'],
        'auto_publish_after_approvals'   => ['required','integer','min:0','max:5'],
        'face_blur_enabled'              => ['nullable','boolean'],
        'profanity_filter_enabled'       => ['nullable','boolean'],
        'guest_uploads_enabled'          => ['nullable','boolean'],
        'max_media_per_batch'            => ['required','integer','min:1','max:200'],
        'settings_json'                  => ['nullable'], // we'll JSON-parse below if string
    ]);

    if ($v->fails()) {
        return back()->withErrors($v)->withInput();
    }

    // 2) Pull validated-ish data
    $data = $v->validated();

    // 3) Normalize booleans (checkboxes may not be sent if unchecked; we sent hidden 0s in Blade already,
    // but this keeps it robust in case that changes)
    $boolKeys = ['is_enabled','face_blur_enabled','profanity_filter_enabled','guest_uploads_enabled'];
    foreach ($boolKeys as $key) {
        $data[$key] = isset($data[$key]) ? (bool)$data[$key] : false;
    }

    // 4) Parse/validate JSON inputs if provided as strings
    // ai_api_credentials may be array OR JSON string
    if (array_key_exists('ai_api_credentials', $data) && !is_null($data['ai_api_credentials']) && $data['ai_api_credentials'] !== '') {
        if (is_string($data['ai_api_credentials'])) {
            try {
                $data['ai_api_credentials'] = json_decode($data['ai_api_credentials'], true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                return back()
                    ->withErrors(['ai_api_credentials' => 'Invalid JSON for AI API Credentials.'])
                    ->withInput();
            }
        }
    } else {
        // if empty, don't touch existing creds
        unset($data['ai_api_credentials']);
    }

    // settings_json may be array OR JSON string
    $incomingSettingsJson = null;
    if (array_key_exists('settings_json', $data) && !is_null($data['settings_json']) && $data['settings_json'] !== '') {
        if (is_string($data['settings_json'])) {
            try {
                $incomingSettingsJson = json_decode($data['settings_json'], true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                return back()
                    ->withErrors(['settings_json' => 'Invalid JSON for Advanced Settings.'])
                    ->withInput();
            }
        } elseif (is_array($data['settings_json'])) {
            $incomingSettingsJson = $data['settings_json'];
        }
        unset($data['settings_json']); // we’ll merge below
    }

    // 5) Load settings row
    $settings = ContentHubSetting::firstOrCreate(['id' => 1]);

    // 6) Merge settings_json (do not overwrite entirely unless you want to)
    if (!is_null($incomingSettingsJson)) {
        $existing = $settings->settings_json ?? [];
        // shallow merge; if you want deep merge, replace with array_replace_recursive
        $merged = array_replace_recursive($existing, $incomingSettingsJson);
        $settings->settings_json = $merged;
    }

    // 7) Apply ai_api_credentials if provided (model mutator encrypts)
    if (isset($data['ai_api_credentials'])) {
        $settings->ai_api_credentials = $data['ai_api_credentials'];
        unset($data['ai_api_credentials']);
    }

    // 8) Fill the rest and save
    $settings->fill($data)->save();

    // 9) Log
    // SystemLogger::log('settings_update', [
    //     'changes'      => $data + ['settings_json_merged' => isset($merged)],
    //     'has_ai_creds' => (bool)($request->input('ai_api_credentials'))
    // ], optional($request->user())->id);

    return back()->with('status', 'Content Hub settings updated.');
}

    public function toggle(Request $request)
    {
        // $this->authorize('contenthub.manage');
// dd('sdas');

        $settings = ContentHubSetting::firstOrCreate(['id'=>1]);
        $settings->is_enabled = ! $settings->is_enabled;
        $settings->save();

        // SystemLogger::log('content_setup', ['is_enabled'=>$settings->is_enabled], $request->user()->id);

        return back()->with('status','Content Hub '.($settings->is_enabled?'enabled':'disabled'));
    }
}
