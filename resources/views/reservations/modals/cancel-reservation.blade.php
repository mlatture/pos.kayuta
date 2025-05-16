<div class="modal fade" id="cancellationModal" tabindex="-1" aria-labelledby="cancellationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancellationModalLabel">Cancel Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="cancelLoader" class="text-center my-3" style="display: none;">
                <div class="spinner-border text-danger" role="status">
                    <span class="visually-hidden">Processing...</span>
                </div>
                <p class="mt-2 text-danger fw-semibold">Processing cancellation...</p>
            </div>

            <div class="modal-body">
                <p>
                    Are you sure you want to cancel this reservation? Note: This will cancel all selected sites.
                    <span class="text-bold">This action cannot be undone. (Note: To remove a single site from this
                        reservation, click the "Remove Site" button instead.)</span>
                </p>


                <!-- Checkboxes for selecting multiple sites -->
                <div class="mb-3">
                    <label for="site_select" class="form-label">Select Sites for Refund</label>
                    <div id="site_select">
                        @foreach ($reservations as $reservation)
                            @if ($reservation->refunds->isEmpty())
                                <div class="form-check">
                                    <input class="form-check-input site-checkbox" type="checkbox"
                                        value="{{ $reservation->siteid }}" id="site_{{ $reservation->siteid }}"
                                        name="siteid[]" data-subtotal="{{ $reservation->base }}">
                                    <label class="form-check-label" for="site_{{ $reservation->siteid }}">
                                        {{ str_replace('_', ' ', $reservation->siteid) }}
                                        ({{ str_replace('_', ' ', $reservation->siteclass) }} ${{ $reservation->base }})
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                @if (!empty($cancellation['require_cancellation_fee']) && $cancellation['require_cancellation_fee'])
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="applyCancellationFee" checked>
                        <label class="form-check-label" for="applyCancellationFee">
                            Apply {{ $cancellation['cancellation_fee'] }}% Cancellation Fee
                        </label>
                    </div>
                @endif
                <div class="mb-3">
                    <label for="site_select" class="form-label">Refund total $<span id="refund-total">____</span>
                        (Calculate from checboxes)</label>
                    <div id="site_select">

                        <div>
                            <div class="form-check">
                                <input class="form-check-input refund-method-radio" type="radio" name="refund_method"
                                    id="credit-card" value="credit-card">
                                <label class="form-check-label" for="credit-card">
                                    Credit Card
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input refund-method-radio" type="radio" name="refund_method"
                                    id="gift-card" value="gift-card">
                                <label class="form-check-label" for="gift-card">
                                    Gift Card
                                </label>
                                <div id="giftCardCodeContainer" style="display:none;" class="mt-2">
                                    {{-- <label for="giftCardCode" class="form-label">Gift Card Code</label> --}}
                                    <input type="text" id="giftCardCode" class="form-control w-50"
                                        placeholder="Enter gift card code">
                                </div>

                            </div>
                            <div class="form-check">
                                <input class="form-check-input refund-method-radio" type="radio" name="refund_method"
                                    id="account-credit" value="account-credit">
                                <label class="form-check-label" for="account-credit">
                                    Account Credit
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input refund-method-radio" type="radio" name="refund_method"
                                    id="cash-check" value="cash">
                                <label class="form-check-label" for="cash">
                                    Cash
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input refund-method-radio" type="radio" name="refund_method"
                                    id="check" value="check">
                                <label class="form-check-label" for="check">
                                    Check
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- Reason for cancellation -->
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Cancellation Reason</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4"
                            placeholder="Enter reason for cancellation..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-primary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-outline-danger" id="yes-cancellation">Yes</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        const cancellationFeePercent = {{ $cancellation['cancellation_fee'] ?? 0 }};

        $(document).ready(function() {
            function updateRefundTotal() {
                let total = 0;
                let applyFee = $('#applyCancellationFee').is(':checked');
                let feeMultiplier = (100 - cancellationFeePercent) / 100;

                $('.site-checkbox:checked').each(function() {
                    let base = parseFloat($(this).data('subtotal'));
                    total += applyFee ? base * feeMultiplier : base;
                });

                $('#refund-total').text(total.toFixed(2));
            }


            $('.refund-method-radio').on('change', function() {
                if ($('#gift-card').is(':checked')) {
                    $('#giftCardCodeContainer').show();
                } else {
                    $('#giftCardCodeContainer').hide();
                }
            });



            $(document).on('change', '.site-checkbox, .refund-method-radio, #applyCancellationFee',
                updateRefundTotal);
            updateRefundTotal();

            $('#yes-cancellation').on('click', function() {
                const reason = $('#cancellation_reason').val();
                const cartid = $('#confirmation').val();
                const refundMethod = $('.refund-method-radio:checked').val();

                let selectedSites = [];
                $('.site-checkbox:checked').each(function() {
                    selectedSites.push({
                        siteid: $(this).val(),
                        base: parseFloat($(this).data('subtotal'))
                    });
                });

                if (selectedSites.length === 0) {
                    alert("Please select at least one site for refund.");
                    return;
                }

                if (!refundMethod) {
                    alert("Please select a refund method.");
                    return;
                }

                const postData = {
                    cartid: cartid,
                    reason: reason,
                    refund_method: refundMethod,
                    sites: selectedSites,
                    apply_fee: $('#applyCancellationFee').is(':checked') ? '1' : '0',
                    gift_card_code: $('#giftCardCode').val() || null,
                    _token: '{{ csrf_token() }}'
                };

                $('#cancelLoader').show();
                $('#yes-cancellation').prop('disabled', true).text('Processing...');



                $.ajax({
                    url: '/admin/reservations/refund',
                    method: 'PATCH',
                    data: postData,
                    success: function(response) {
                        $('#cancelLoader').hide();
                        $('#yes-cancellation').prop('disabled', false).text('Yes');
                        alert('Reservation cancelled successfully!');
                        $('#cancellationModal').modal('hide');
                        location.reload();

                    },
                    error: function(xhr) {
                        alert('Something went wrong: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
