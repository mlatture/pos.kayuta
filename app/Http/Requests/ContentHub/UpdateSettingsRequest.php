<?php

namespace App\Http\Requests\ContentHub;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_enabled' => ['required','boolean'],
            'ai_service_provider' => ['required','string','max:50'],
            'ai_api_credentials' => ['nullable','array'], // {"api_key":"...","model":"..."}
            'default_publish_delay_minutes' => ['required','integer','min:0','max:1440'],
            'auto_publish_after_approvals' => ['required','integer','min:0','max:5'],
            'face_blur_enabled' => ['required','boolean'],
            'profanity_filter_enabled' => ['required','boolean'],
            'guest_uploads_enabled' => ['required','boolean'],
            'max_media_per_batch' => ['required','integer','min:1','max:200'],
            'settings_json' => ['nullable','array'],
        ];
    }
}
