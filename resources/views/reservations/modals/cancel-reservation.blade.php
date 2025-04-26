<div class="modal fade" id="cancellationModal" tabindex="-1" aria-labelledby="cancellationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancellationModalLabel">Cancel Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <div class="form-check">
                                <input class="form-check-input site-checkbox" type="checkbox"
                                    value="{{ $reservation->siteid }}" id="site_{{ $reservation->siteid }}"
                                    name="siteid[]" data-subtotal="{{ $reservation->subtotal }}">
                                <label class="form-check-label" for="site_{{ $reservation->siteid }}">
                                    {{ str_replace('_', ' ', $reservation->siteid) }}
                                    ({{ str_replace('_', ' ', $reservation->siteclass) }} ${{ $reservation->subtotal }})
                                </label>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="mb-3">
                    <label for="site_select" class="form-label">Refund total $<span id="refund-total">____</span>
                        (Calculate from checboxes)</label>
                    <div id="site_select">

                        <div>
                            <div class="form-check">
                                <input class="form-check-input refund-method-radio" type="radio" name="refund_method"
                                    id="credit-card" value="credit-card">
                                <label class="form-check-label" for="credit-card">
                                    Credit Card (Minus 15% Cancellation Fee)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input refund-method-radio" type="radio" name="refund_method"
                                    id="account-credit" value="account-credit">
                                <label class="form-check-label" for="account-credit">
                                    Account Credit (Minus 15% Cancellation Fee)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input refund-method-radio" type="radio" name="refund_method"
                                    id="cash-check" value="cash-check">
                                <label class="form-check-label" for="cash-check">
                                    Cash/Credit (Minus 15% Cancellation Fee)
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
        $(document).ready(function() {
            function updateRefundTotal() {
                let total = 0;
                $('.site-checkbox:checked').each(function() {
                    total += parseFloat($(this).data('subtotal'));
                });

                let applyFee = $('.refund-method-radio:checked').length > 0;

                if (applyFee && total > 0) {
                    total = total * 0.85; // Subtract 15%
                }

                $('#refund-total').text(total.toFixed(2));
            }

            $(document).on('change', '.site-checkbox, .refund-method-radio', updateRefundTotal);

            updateRefundTotal();
        });


        $('#yes-cancellation').on('click', function() {
            let cancellationFee = {{ $cancellationFee ?? 0 }};
            let refundedAmount = {{ $totalAfterFee ?? 0 }};
            let reason = $('#cancellation_reason').val();

            let cartid = $('#confirmation').val();

            let selectedSiteIds = [];
            $('input[name="siteid[]"]:checked').each(function() {
                selectedSiteIds.push($(this).val());
            });

            if (selectedSiteIds.length === 0) {
                alert("Please select at least one site for refund.");
                return;
            }

            $.ajax({
                url: '/admin/reservations/refund',
                method: 'PATCH',
                data: {
                    cartid: cartid,
                    reason: reason,
                    cancellation_fee: cancellationFee,
                    refunded_amount: refundedAmount,
                    siteid: selectedSiteIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Reservation cancelled successfully!');
                    $('#cancellationModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Something went wrong: ' + xhr.responseText);
                }
            });
        });
    </script>
