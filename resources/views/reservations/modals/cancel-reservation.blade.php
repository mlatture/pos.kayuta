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
                                <input class="form-check-input" type="checkbox" value="{{ $reservation->siteid }}"
                                    id="site_{{ $reservation->siteid }}" name="siteid[]">
                                <label class="form-check-label" for="site_{{ $reservation->siteid }}">
                                    {{ $reservation->siteid }} ({{ $reservation->siteclass }})
                                </label>
                            </div>
                        @endforeach
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
