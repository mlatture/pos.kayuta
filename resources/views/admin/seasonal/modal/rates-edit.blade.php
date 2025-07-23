<div class="modal fade" id="editRateModal" tabindex="-1" aria-labelledby="editRateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editRateForm" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRateModalLabel">Add-On Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="rate_id" value="{{ old('rate_id') }}">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input name="rate_name" value="{{ old('rate_name') }}"
                                    class="form-control @error('rate_name') is-invalid @enderror" id="rateName"
                                    placeholder="Rate Name" required>
                                <label for="rateName">Rate Name</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input name="rate_price" value="{{ old('rate_price') }}" type="number" step="0.01"
                                    class="form-control @error('rate_price') is-invalid @enderror" id="ratePrice"
                                    placeholder="Rate Price" required>
                                <label for="ratePrice">Rate Price</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input name="deposit_amount" value="{{ old('deposit_amount') }}" type="number"
                                    step="0.01" class="form-control @error('deposit_amount') is-invalid @enderror"
                                    id="depositAmount" placeholder="Deposit">
                                <label for="depositAmount">Deposit Amount</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input name="early_pay_discount" value="{{ old('early_pay_discount') }}" type="number"
                                    step="0.01"
                                    class="form-control @error('early_pay_discount') is-invalid @enderror"
                                    id="earlyDiscount" placeholder="Early Pay Discount">
                                <label for="earlyDiscount">Early Pay Discount ($)</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input name="full_payment_discount" value="{{ old('full_payment_discount') }}" type="number"
                            step="0.01" class="form-control @error('full_payment_discount') is-invalid @enderror"
                            id="fullDiscount" placeholder="Full Payment Discount">
                        <label for="fullDiscount">Full Payment Discount ($)</label>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Payment Plan Starts</label>
                                <input name="payment_plan_starts" type="date"
                                    value="{{ old('payment_plan_starts') }}"
                                    class="form-control @error('payment_plan_starts') is-invalid @enderror">


                            </div>

                        </div>

                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Final Payment Due</label>

                                <input name="final_payment_due" type="date" value="{{ old('final_payment_due') }}"
                                    class="form-control @error('final_payment_due') is-invalid @enderror">

                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Template</label>
                            <select name="template_id" class="form-select" required>
                                <option value="">-- Select Template --</option>
                                @foreach ($documentTemplates as $template)
                                    <option value="{{ $template->id }}"
                                        {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="active" value="1" checked
                                    id="rateActive" {{ old('active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="rateActive">
                                    Active?
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update Rate</button>
                </div>
            </div>
        </form>
    </div>
</div>


@push('js')
    <script>
        const updateRateUrlTemplate = "{{ route('settings.update.rate', ['rate' => '__ID__']) }}";
        // Seasonal Add on Create
        $(document).ready(function() {
            $('#editRateForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = form.serializeArray();
                const rateId = form.find('input[name="rate_id"]').val();

                if (!rateId) {
                    alert("Rate ID missing");
                    return;
                }

                const updateUrl = updateRateUrlTemplate.replace('__ID__', rateId);

                console.log('Updating rate via PUT to:', updateUrl);

                $.ajax({
                    url: updateUrl,
                    type: "POST",
                    data: form.serialize(),
                    success: function(res) {
                        if (res.success) {
                            $('#editRateModal').modal('hide');
                            $.toast({
                                heading: 'Success',
                                text: res.message || 'Rate updated successfully!',
                                icon: 'success',
                                position: 'bottom-left',
                                hideAfter: 3000
                            });
                        }
                    },
                    error: function(err) {
                        console.error(err);

                        form.find('.is-invalid').removeClass('is-invalid');
                        form.find('.invalid-feedback').remove();

                        if (err.status === 422 && err.responseJSON.errors) {
                            const errors = err.responseJSON.errors;

                            for (const field in errors) {
                                const input = form.find(`[name="${field}"]`);
                                input.addClass('is-invalid');

                                input.after(
                                    `<div class="invalid-feedback">${errors[field][0]}</div>`
                                );
                            }

                            // $.toast({
                            //     heading: 'Error',
                            //     text: err.responseJSON?.message ||
                            //         'There was a problem updating the rate.',
                            //     icon: 'error',
                            //     position: 'top-right',
                            //     hideAfter: 5000
                            // });
                        } else {
                            $.toast({
                                heading: 'Error',
                                text: err.responseJSON?.message ||
                                    'There was a problem updating the rate.',
                                icon: 'error',
                                position: 'top-right',
                                hideAfter: 5000
                            });
                        }

                    }
                });
            });
        });
    </script>
@endpush
