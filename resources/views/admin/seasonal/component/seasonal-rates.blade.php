<div class="row">
    <div class="col">
        <div class="border rounded-3 p-4 shadow-sm">
            <h5 class="mb-3 text-primary">💲 Define Seasonal Rates</h5>
            <form method="POST" action="{{ route('settings.storeRate') }}">
                @csrf
                <div class="row">
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
                                step="0.01" class="form-control @error('early_pay_discount') is-invalid @enderror"
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
                            <input name="payment_plan_starts" type="date" value="{{ old('payment_plan_starts') }}"
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
                <div class="row">
                    {{-- <div class="col">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="applies_to_all" value="1" class="form-check-input"
                                id="appliesToAll" {{ old('applies_to_all') ? 'checked' : '' }}>
                            Applies to All (For liability waivers)
                            </label>
                        </div>

                    </div> --}}

                </div>
                <button class="btn btn-success w-100">Save Seasonal Rate</button>
            </form>

            <hr class="my-4">
            <h6>📊 Existing Rates</h6>
            <ul class="list-group">
                @forelse ($seasonalRates as $rate)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $rate->rate_name }}</strong> - ${{ $rate->rate_price }}
                            @if ($rate->template)
                                <small class="text-muted">({{ $rate->template->name }})</small>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                data-bs-target="#editRateModal" data-rate='@json($rate)'
                                data-param-id="{{ $rate->id }}" data-param-type="seasonal">

                                Update
                            </button>

                            <button class="btn btn-sm btn-outline-danger btn-delete"
                                data-url="{{ route('settings.destroy.rate', $rate->id) }}">Delete</button>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">No seasonal rates defined yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    @include('admin.seasonal.modal.rates-edit')

</div>

@push('js')
    @if (session('error'))
        <script>
            toastr.error("{{ session('error') }}");
        </script>
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>
                toastr.error("{{ $error }}");
            </script>
        @endforeach
    @endif


    <script>
        $('#editRateModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const rate = button.data('rate');
            const rateId = button.data('param-id');
            const modal = $(this);


            // Set form values
            modal.find('input[name="rate_id"]').val(rateId || '');
            modal.find('input[name="rate_name"]').val(rate.rate_name || '');
            modal.find('input[name="rate_price"]').val(rate.rate_price || '');
            modal.find('input[name="deposit_amount"]').val(rate.deposit_amount || '');
            modal.find('input[name="early_pay_discount"]').val(rate.early_pay_discount || '');
            modal.find('input[name="full_payment_discount"]').val(rate.full_payment_discount || '');
            modal.find('input[name="payment_plan_starts"]').val(rate.payment_plan_starts || '');
            modal.find('input[name="final_payment_due"]').val(rate.final_payment_due || '');
            modal.find('select[name="template_id"]').val(rate.template_id || '');


            modal.find('input[name="active"]').prop('checked', !!rate.active);



        });

        // Delete Rate
        $(document).on('click', '.btn-delete', function() {
            $this = $(this);

            console.log('Delete button clicked:', $this.data('url'));

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this data?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No',
                reverseButtons: true
            }).then((result) => {
                console.log('result:', result);

                if (result.value) {
                    $.post($this.data('url'), {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        $this.closest('li').fadeOut(500, function() {
                            $(this).remove();
                        });
                        $.toast({
                            heading: 'Success',
                            text: res.message,
                            icon: 'success',
                            position: 'bottom-left',
                            hideAfter: 3000,
                            stack: 3
                        });

                    })
                }
            });
        });
    </script>

@endpush
