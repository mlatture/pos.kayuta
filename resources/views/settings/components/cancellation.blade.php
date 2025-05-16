<div class="tab-pane fade" id="cancellation" role="tabpanel" aria-labelledby="cancellation-tab">
    <form method="POST" action="{{ route('admin.cancellation-settings.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-solid fa-ban"></i> Cancellation Settings
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="requireCancellationFee" name="require_cancellation_fee"
                    {{ !empty($settings['require_cancellation_fee']) && $settings['require_cancellation_fee'] === true ? 'checked' : '' }}>
                    <label class="form-check-label" for="requireCancellationFee">Require Cancellation Fee</label>
                </div>
        
                <div class="mb-3">
                    <label for="cancellation_fee" class="form-label">Cancellation Fee (%)</label>
                    <input type="number" class="form-control @error('cancellation_fee') is-invalid @enderror"
                           id="cancellation_fee" name="cancellation_fee" min="1.01" step="0.01"
                           value="{{ old('cancellation_fee', $settings['cancellation_fee'] ?? '') }}"
                           placeholder="e.g., 15">
                    @error('cancellation_fee')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        

        <button type="submit" class="btn btn-primary float-end">Save Settings</button>
    </form>
</div>
