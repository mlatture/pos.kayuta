<div class="tab-pane fade" id="cart" role="tabpanel" aria-labelledby="cart-tab">
    <form method="POST" action="{{ route('admin.cart-settings.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-solid fa-puzzle-piece"></i> Addons Listing
            </div>
            <div class="card-body">
                <p><strong>Shopping Cart timeout in minutes</strong> (you can update the cart timer on the cart page of
                    the frontend)</p>


                @php
                    $selectedTime = $settings['cart_hold_time'] ?? 15;
                @endphp

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Select Cart Timer</label>
                        <select name="cart_hold_time" class="form-select">
                            <option value="15" {{ $selectedTime == 15 ? 'selected' : '' }}>15 Minutes</option>
                            <option value="30" {{ $selectedTime == 30 ? 'selected' : '' }}>30 Minutes</option>
                            <option value="60" {{ $selectedTime == 60 ? 'selected' : '' }}>60 Minutes</option>
                            <option value="120" {{ $selectedTime == 120 ? 'selected' : '' }}>120 Minutes</option>
                        </select>
                    </div>
                </div>

                <p><strong>Toggle the options below to show or hide specific listing</strong></p>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="golfToggle" name="golf_listing"
                    {{ $settings2['golf_listing_show'] == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="golfToggle">Enable Golf Cart Suggestion</label>

                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="gridViewToggle" name="boat_listing"
                    {{ $settings2['boat_listing_show'] == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="gridViewToggle">Enable Boat Slip Suggestion</label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="gridViewToggle" name="pool_listing"
                    {{ $settings2['pool_listing_show'] == '1' ? 'checked' : '' }}
                    <label class="form-check-label" for="gridViewToggle">Enable Pool Cabana Suggestion</label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="gridViewToggle" name="product_listing"
                    {{ $settings2['product_listing_show'] == '1' ? 'checked' : '' }}
                    <label class="form-check-label" for="gridViewToggle">Enable Suggested Product</label>
                </div>




            </div>
        </div>



        <button type="submit" class="btn btn-primary float-end">Save Settings</button>
    </form>
</div>
