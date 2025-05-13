<div class="tab-pane fade" id="pricing" role="tabpanel" aria-labelledby="pricing-tab">
    <form method="POST" action="{{ route('admin.dynamic-pricing-settings.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Company Information --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-solid fa-building"></i> Company Information
            </div>
            <div class="card-body">
                <p><strong>If Dynamic Pricing is off, it will not use api of dynamic pricing on site detail page</strong></p>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="togglePricing" name="toggle_pricing"
                    {{ $settings['dynamic_pricing'] == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="togglePricing">Get Prices From Api</label>

                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary float-end">Save Settings</button>
    </form>
</div>