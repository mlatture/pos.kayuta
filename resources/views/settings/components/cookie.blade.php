<div class="tab-pane fade" id="cookie" role="tabpanel" aria-labelledby="cookie-tab">
    <form method="POST" action="{{ route('admin.cookie-settings.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="card mb-4" style="max-width: 20vw">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fa-solid fa-cookie"></i> Cookie Settings:
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="cookieToggle"
                        name="enable_cookies" {{ $settings['cookie_status'] == 1 ? 'checked' : '' }}>
                    <label class="form-check-label"
                        for="cookieToggle">{{ $settings['cookie_status'] == 1 ? 'Disable' : 'Enable' }} </label>
                </div>
            </div>

            <div class="card-body">
                <div class="mb-3">
                    <label for="cookie_message" class="form-label">Cookie Consent Message</label>
                    <textarea name="cookie_message" id="cookie_message" rows="5" class="form-control"
                        placeholder="Enter your cookie policy or message...">{{ $settings['cookie_text'] ?? '' }}</textarea>
                </div>
            </div>
        </div>



        <button type="submit" class="btn btn-primary float-end">Save Settings</button>
    </form>
</div>
