<?php

namespace App\Http\Controllers\Admin\ContentHub;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentHub\UpdateSettingsRequest;
use App\Models\ContentHub\ContentHubSetting;
use App\Support\SystemLogger;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show()
    {
        $this->authorize('contenthub.view');
        $settings = ContentHubSetting::firstOrCreate(['id'=>1]);
        return view('admin.content_hub.settings', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request)
    {
        $settings = ContentHubSetting::firstOrCreate(['id'=>1]);
        $data = $request->validated();

        if (isset($data['ai_api_credentials'])) {
            $settings->ai_api_credentials = $data['ai_api_credentials']; // encrypted by mutator
            unset($data['ai_api_credentials']);
        }

        $settings->fill($data)->save();

        SystemLogger::log('settings_update', [
            'changes' => $data,
            'has_ai_creds' => (bool)$request->ai_api_credentials
        ], $request->user()->id);

        return back()->with('status','Content Hub settings updated.');
    }

    public function toggle(Request $request)
    {
        $this->authorize('contenthub.manage');

        $settings = ContentHubSetting::firstOrCreate(['id'=>1]);
        $settings->is_enabled = ! $settings->is_enabled;
        $settings->save();

        SystemLogger::log('content_setup', ['is_enabled'=>$settings->is_enabled], $request->user()->id);

        return back()->with('status','Content Hub '.($settings->is_enabled?'enabled':'disabled'));
    }
}
