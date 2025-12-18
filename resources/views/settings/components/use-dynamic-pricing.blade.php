<div class="tab-pane fade" id="pricing" role="tabpanel" aria-labelledby="pricing-rates-tab">
    <form method="POST" action="{{ route('admin.platform-fee-settings.update')  }}" enctype="multipart/form-data">
        @csrf

        {{-- Dynamic Pricing Toggle (Existing) --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-solid fa-chart-line"></i> Dynamic Pricing
            </div>
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="togglePricing" name="toggle_pricing"
                    {{ ($settings['dynamic_pricing'] ?? '0') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="togglePricing">Get Prices From Api</label>
                </div>
                <p class="text-muted small">If Dynamic Pricing is off, it will not use the API on site detail pages.</p>
            </div>
        </div>

        {{-- Platform Cost Recovery Section (New) --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-solid fa- calculator"></i> Platform Cost Recovery
            </div>
            <div class="card-body">
                <p class="mb-3">Automatically adjust rates to include WebDaVinci platform costs or additional fees in your pricing.</p>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="platform_fee_fixed" class="form-label">Fixed Amount Adjustment</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control"
                                   id="platform_fee_fixed" name="platform_fee_fixed"
                                   value="{{ old('platform_fee_fixed', $settings['platform_fee_fixed'] ?? '') }}"
                                   placeholder="0.00">
                        </div>
                        <div class="form-text">Add fixed dollar amount to each night's rate</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="platform_fee_percent" class="form-label">Percentage Adjustment</label>
                        <div class="input-group">
                            <input type="number" step="0.001" min="0" class="form-control"
                                   id="platform_fee_percent" name="platform_fee_percent"
                                   value="{{ old('platform_fee_percent', $settings['platform_fee_percent'] ?? '') }}"
                                   placeholder="0.0">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">Increase base rate by percentage</div>
                        <div id="percent-warning" class="text-danger small mt-1" style="display: none;"></div>
                    </div>
                </div>

                <div class="alert alert-info py-2">
                    <small><strong>Recommended:</strong> ${{ $settings['platform_fee_fixed'] }} + {{ $settings['platform_fee_percent'] }}% covers platform costs while improving profit margins. Set both to $0 to handle costs separately.</small>
                </div>

                <hr>

                {{-- Preview Calculator --}}
                <div class="bg-light p-3 rounded">
                    <h6>Preview Calculator</h6>
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label class="small">Base Rate:</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" id="preview_base_rate" class="form-control" value="50.00">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="ps-2">
                                <div class="small">+ Fixed Fee: <span id="calc-fixed">$0.00</span></div>
                                <div class="small">+ Percentage Fee: <span id="calc-percent">$0.00</span></div>
                                <div class="fw-bold text-success mt-1">
                                    = Adjusted Rate: <span id="calc-total">$50.00</span> /night
                                </div>
                                <div class="text-muted x-small">Guest Sees: Single nightly rate with no separate fees</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary float-end">Save Settings</button>
    </form>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            function updatePreview() {
                const baseRate = parseFloat($('#preview_base_rate').val()) || 0;
                const fixedFee = parseFloat($('#platform_fee_fixed').val()) || 0;
                const percentFee = parseFloat($('#platform_fee_percent').val()) || 0;

                if (percentFee < 0 || percentFee > 100) {
                    $('#percent-warning').text('Please enter a percentage between 0 and 100.').show();
                } else {
                    $('#percent-warning').hide();
                }

                const percentAmount = (baseRate * percentFee) / 100;
                const total = baseRate + fixedFee + percentAmount;

                $('#calc-fixed').text(`$${fixedFee.toFixed(2)}`);
                $('#calc-percent').text(`$${percentAmount.toFixed(2)}`);
                $('#calc-total').text(`$${total.toFixed(2)}`);
            }

            $('#preview_base_rate, #platform_fee_fixed, #platform_fee_percent').on('input', updatePreview);

            // Initial calculation
            updatePreview();
        })
    </script>
@endpush