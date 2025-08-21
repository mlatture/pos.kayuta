<div class="tab-pane fade" id="platformFee" role="tabpanel" aria-labelledby="platformFee-tab">
    <form method="POST" action="{{ route('admin.platform-fee-settings.update') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-solid fa-building"></i> Platform Fee Settings
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="platform_fee_percent" class="form-label">Platform Fee (%)</label>
                        <input type="number" step="0.01" min="0" class="form-control"
                               id="platform_fee_percent" name="platform_fee_percent"
                               value="{{ old('platform_fee_percent', $settings['platform_fee_percent'] ?? 0) }}">
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <label for="platform_fee_fixed" class="form-label">Platform Fee (Fixed)</label>
                        <input type="number" step="0.01" min="0" class="form-control"
                               id="platform_fee_fixed" name="platform_fee_fixed"
                               value="{{ old('platform_fee_fixed', $settings['platform_fee_fixed'] ?? 0) }}">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary float-end">Save Settings</button>
    </form>
</div>