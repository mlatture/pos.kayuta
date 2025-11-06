{{-- resources/views/admin/content_hub/settings.blade.php --}}
@extends('layouts.admin')

@section('title', 'AI Content Hub Settings')

@section('content')
    <div class="card shadow border-0 bg-white rounded-4 overflow-hidden">
        <div class="card-header bg-gradient text-dark d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #00b09b, #96c93d);">
            <h4 class="mb-0 d-flex align-items-center">
                <i class="bi bi-robot me-2"></i> AI Content Hub — Settings
            </h4>

            {{-- Enable/Disable Toggle --}}
            <form method="POST" action="{{ route('admin.content-hub.toggle') }}">
                @csrf
                <button class="btn btn-{{ $settings->is_enabled ? 'danger' : 'success' }}">
                    <i class="bi {{ $settings->is_enabled ? 'bi-toggle-off' : 'bi-toggle-on' }}"></i>
                    {{ $settings->is_enabled ? 'Disable' : 'Enable' }}
                </button>
            </form>
        </div>

        <div class="card-body px-4 py-3" style="max-height: 80vh; overflow-y: auto;">
            {{-- Nav Tabs --}}
            @php($active = request('tab','overview'))
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link text-dark {{ $active === 'overview' ? 'active' : '' }}"
           href="{{ route('admin.content-hub.settings', ['tab'=>'overview']) }}">
            <i class="bi bi-info-circle me-1"></i> Overview
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark {{ $active === 'ai' ? 'active' : '' }}"
           href="{{ route('admin.content-hub.settings', ['tab'=>'ai']) }}">
            <i class="bi bi-cpu me-1"></i> AI Provider & Credentials
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark {{ $active === 'safety' ? 'active' : '' }}"
           href="{{ route('admin.content-hub.settings', ['tab'=>'safety']) }}">
            <i class="bi bi-shield-check me-1"></i> Safety Filters
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark {{ $active === 'advanced' ? 'active' : '' }}"
           href="{{ route('admin.content-hub.settings', ['tab'=>'advanced']) }}">
            <i class="bi bi-sliders me-1"></i> Advanced
        </a>
    </li>
</ul>

            {{-- Flash --}}
            @if(session('status'))
                <div class="alert alert-success mt-3 mb-0">
                    <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
                </div>
            @endif

            {{-- Content --}}
            <div class="tab-content mt-3">
                {{-- OVERVIEW --}}
                <div class="tab-pane fade {{ $active === 'overview' ? 'show active' : '' }}" id="overview" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="p-3 border rounded-3 h-100">
                                <h5 class="mb-3">
                                    <i class="bi bi-clipboard-check me-2"></i> Setup Checklist
                                </h5>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        Feature Status:
                                        @if($settings->is_enabled)
                                            <span class="badge text-bg-success">Enabled</span>
                                        @else
                                            <span class="badge text-bg-secondary">Disabled</span>
                                        @endif
                                    </li>
                                    <li class="mb-2">AI Provider: <strong>{{ strtoupper($settings->ai_service_provider ?? 'CLAUDE') }}</strong></li>
                                    <li class="mb-2">Default Tone Profiles: <span class="badge text-bg-success">Seeded</span></li>
                                    <li class="mb-2">Default Hashtags: <span class="badge text-bg-success">Seeded</span></li>
                                    <li class="mb-2">Publishing Delay: <strong>{{ $settings->default_publish_delay_minutes ?? 5 }} min</strong></li>
                                    <li class="mb-2">Max Media / Batch: <strong>{{ $settings->max_media_per_batch ?? 20 }}</strong></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="p-3 border rounded-3 h-100">
                                <h5 class="mb-3">
                                    <i class="bi bi-journal-text me-2"></i> Notes
                                </h5>
                                <p class="text-muted mb-2">• Single-tenant deployment: this config applies to <em>this</em> park only.</p>
                                <p class="text-muted mb-2">• Credentials are stored encrypted at rest.</p>
                                <p class="text-muted mb-0">• Toggle above safely enables/disables Content Hub.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- AI PROVIDER & CREDS --}}
                <div class="tab-pane fade {{ $active === 'ai' ? 'show active' : '' }}" id="ai" role="tabpanel">
                    <form method="POST" action="{{ route('admin.content-hub.settings.update') }}" class="mt-1">
                        @csrf
                        <input type="hidden" name="is_enabled" value="{{ (int)$settings->is_enabled }}"/>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">AI Service Provider</label>
                                <select name="ai_service_provider" class="form-control @error('ai_service_provider') is-invalid @enderror">
                                    <option value="claude" @selected(($settings->ai_service_provider ?? 'claude')==='claude')>Claude</option>
                                    <option value="openai" @selected(($settings->ai_service_provider ?? '')==='openai')>OpenAI</option>
                                </select>
                                @error('ai_service_provider') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">AI API Credentials (JSON)</label>
                                <textarea name="ai_api_credentials" rows="4"
                                          class="form-control @error('ai_api_credentials') is-invalid @enderror"
                                          placeholder='{"api_key":"***","model":"gpt-4o/claude-3.5","extra":{}}'></textarea>
                                <small class="text-muted">Stored encrypted. Leave blank to keep existing.</small>
                                @error('ai_api_credentials') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Default Publish Delay (minutes)</label>
                                <input type="number" name="default_publish_delay_minutes"
                                       value="{{ old('default_publish_delay_minutes', $settings->default_publish_delay_minutes ?? 5) }}"
                                       class="form-control @error('default_publish_delay_minutes') is-invalid @enderror" min="0" max="1440">
                                @error('default_publish_delay_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Auto Publish After Approvals (0 = never)</label>
                                <input type="number" name="auto_publish_after_approvals"
                                       value="{{ old('auto_publish_after_approvals', $settings->auto_publish_after_approvals ?? 0) }}"
                                       class="form-control @error('auto_publish_after_approvals') is-invalid @enderror" min="0" max="5">
                                @error('auto_publish_after_approvals') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Max Media per Batch</label>
                                <input type="number" name="max_media_per_batch"
                                       value="{{ old('max_media_per_batch', $settings->max_media_per_batch ?? 20) }}"
                                       class="form-control @error('max_media_per_batch') is-invalid @enderror" min="1" max="200">
                                @error('max_media_per_batch') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Save Settings
                            </button>
                            <a class="btn btn-outline-secondary"
                               href="{{ route('admin.content-hub.settings', ['tab'=>'overview']) }}">
                                <i class="bi bi-arrow-left-short me-1"></i> Back to Overview
                            </a>
                        </div>
                    </form>
                </div>

                {{-- SAFETY FILTERS --}}
                <div class="tab-pane fade {{ $active === 'safety' ? 'show active' : '' }}" id="safety" role="tabpanel">
                    <form method="POST" action="{{ route('admin.content-hub.settings.update') }}" class="mt-1">
                        @csrf
                        <input type="hidden" name="is_enabled" value="{{ (int)$settings->is_enabled }}"/>

                        {{-- hidden fallbacks ensure unchecked checkboxes send 0 --}}
                        <input type="hidden" name="face_blur_enabled" value="0">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="face_blur_enabled" value="1"
                                   id="face_blur_enabled" @checked($settings->face_blur_enabled)>
                            <label class="form-check-label" for="face_blur_enabled">Face Blur Enabled</label>
                        </div>

                        <input type="hidden" name="profanity_filter_enabled" value="0">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="profanity_filter_enabled" value="1"
                                   id="profanity_filter_enabled" @checked($settings->profanity_filter_enabled)>
                            <label class="form-check-label" for="profanity_filter_enabled">Profanity Filter Enabled</label>
                        </div>

                        <input type="hidden" name="guest_uploads_enabled" value="0">
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="guest_uploads_enabled" value="1"
                                   id="guest_uploads_enabled" @checked($settings->guest_uploads_enabled)>
                            <label class="form-check-label" for="guest_uploads_enabled">Guest Uploads Enabled</label>
                        </div>

                        <button class="btn btn-primary">
                            <i class="bi bi-shield-check me-1"></i> Save Filters
                        </button>
                    </form>
                </div>

                {{-- ADVANCED --}}
                <div class="tab-pane fade {{ $active === 'advanced' ? 'show active' : '' }}" id="advanced" role="tabpanel">
                    <form method="POST" action="{{ route('admin.content-hub.settings.update') }}" class="mt-1">
                        @csrf
                        <input type="hidden" name="is_enabled" value="{{ (int)$settings->is_enabled }}"/>

                        <div class="mb-3">
                            <label class="form-label">Advanced Settings (JSON)</label>
                            <textarea class="form-control @error('settings_json') is-invalid @enderror" rows="6" name="settings_json"
                                      placeholder='{"hashtags":{"evergreen":["#camping","#outdoors"]}}'>{{ old('settings_json') }}</textarea>
                            <small class="text-muted">
                                This merges into <code>settings_json</code>. Leave blank to keep current.
                            </small>
                            @error('settings_json') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="alert alert-light border">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>
                                    Current <code>settings_json</code> snapshot:
                                    <pre class="mb-0 small" style="white-space: pre-wrap;">{{ json_encode($settings->settings_json ?? [], JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary">
                            <i class="bi bi-sliders me-1"></i> Save Advanced
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    // Optional: prettify JSON before submit (don’t block submit if invalid; server-side validation will catch)
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        const creds = form.querySelector('textarea[name="ai_api_credentials"]');
        const adv   = form.querySelector('textarea[name="settings_json"]');

        try { if (creds && creds.value.trim()) creds.value = JSON.stringify(JSON.parse(creds.value)); } catch {}
        try { if (adv && adv.value.trim())   adv.value   = JSON.stringify(JSON.parse(adv.value));   } catch {}
    }, true);
</script>
@endpush
