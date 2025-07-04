@extends('layouts.admin')

@section('title', 'Seasonal Settings')

@section('content')


    <div class="card shadow border-0 bg-white rounded-4 overflow-hidden">
        <div class="card-header bg-gradient text-dark d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #00b09b, #96c93d);">

            <h4 class="mb-0 d-flex align-items-center">
                <i class="bi bi-gear-fill me-2"></i> Seasonal Guest Renewal Settings
            </h4>


        </div>

        {{-- Seasonal Add Ons Modal --}}
        @include('admin.seasonal.modal.add-ons')

        <div class="card-body px-4 py-3" style="max-height: 80vh; overflow-y: auto;">
            <div class="row g-5 p-4">
                <div class="border rounded-3 p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">📋 Seasonal Guest Renewals</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-danger" id="clearAndReloadBtn">
                                <i class="bi bi-arrow-clockwise"></i> Clear and Reload
                            </button>
                            <button class="btn btn-success" id="sendEmailBtn" data-url="{{ route('seasonal.sendEmails') }}">
                                <i class="bi bi-envelope"></i> Send Emails
                            </button>

                        </div>
                    </div>

                    <div class="alert alert-warning small">
                        <strong>Warning:</strong> Clicking <em>Clear and Reload</em> will reset the renewal process.
                        This should only be used for a new season.
                    </div>

                    @include('admin.seasonal.component.renewal-table')


                </div>

            </div>
            <div class="row g-5">
                <div class="col-md-6">
                    <div class="border rounded-3 p-4 shadow-sm">
                        <h5 class="mb-3 text-primary">📄 Upload Document Templates</h5>
                        <form method="POST" action="{{ route('settings.storeTemplate') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-floating mb-3">
                                <input name="name" class="form-control" id="templateName" placeholder="Template Name"
                                    required>
                                <label for="templateName">Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input name="description" class="form-control" id="templateDescription"
                                    placeholder="Description">
                                <label for="templateDescription">Description</label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upload File (DOC, DOCX, PDF)</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                            <button class="btn btn-success w-100">Upload Template</button>
                        </form>

                        <hr class="my-4">
                        <h6>📂 Existing Templates</h6>
                        <ul class="list-group">
                            @forelse ($documentTemplates as $template)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>{{ $template->name }}</strong>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-sm btn-outline-secondary"
                                            href="{{ asset('storage/' . $template->file) }}" target="_blank">Download</a>
                                        <button class="btn btn-sm btn-outline-danger btn-delete"
                                            data-url="{{ route('template.destroy', $template->id) }}">Delete</button>
                                    </div>
                                </li>

                            @empty
                                <li class="list-group-item">No templates uploaded yet.</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="border rounded-3 mt-3 p-4 show-sm">
                        <button type="button" class="btn btn-sm btn-outline-primary d-flex justify-content-end float-right"
                            data-bs-toggle="modal" data-bs-target="#addOnsModal">Add Ons</button>

                        <h6 class="mt-3">🏕️ Existing Seasonal Add Ons</h6>
                        <ul class="list-group">
                            @forelse ($seasonalAddOns as $addon)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $addon->seasonal_add_on_name }}</strong> -
                                        ${{ $addon->seasonal_add_on_price }}

                                    </div>
                                    <div class="d-flex gap-2">

                                        <button class="btn btn-sm btn-outline-danger btn-delete"
                                            data-url="{{ route('seasonal.addon.destroy', $addon->id) }}">Delete</button>
                                    </div>
                                </li>

                            @empty
                                <li class="list-group-item">No addon added yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-3 p-4 shadow-sm">
                        <h5 class="mb-3 text-primary">💲 Define Seasonal Rates</h5>
                        <form method="POST" action="{{ route('settings.storeRate') }}">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input name="rate_name" class="form-control" id="rateName" placeholder="Rate Name"
                                            required>
                                        <label for="rateName">Rate Name</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input name="rate_price" type="number" step="0.01" class="form-control"
                                            id="ratePrice" placeholder="Rate Price" required>
                                        <label for="ratePrice">Rate Price</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input name="deposit_amount" type="number" step="0.01" class="form-control"
                                            id="depositAmount" placeholder="Deposit" required>
                                        <label for="depositAmount">Deposit Amount</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input name="early_pay_discount" type="number" step="0.01"
                                            class="form-control" id="earlyDiscount" placeholder="Early Pay Discount">
                                        <label for="earlyDiscount">Early Pay Discount ($)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input name="full_payment_discount" type="number" step="0.01" class="form-control"
                                    id="fullDiscount" placeholder="Full Payment Discount">
                                <label for="fullDiscount">Full Payment Discount ($)</label>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Plan Starts</label>
                                        <input name="payment_plan_starts" type="date" class="form-control">
                                    </div>

                                </div>

                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Final Payment Due</label>
                                        <input name="final_payment_due" type="date" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Template</label>
                                <select name="template_id" class="form-select">
                                    <option value="">-- Select Template --</option>
                                    @foreach ($documentTemplates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="applies_to_all"
                                            value="1" id="appliesToAll">
                                        <label class="form-check-label" for="appliesToAll">
                                            Applies to All (For liability waivers)
                                        </label>
                                    </div>

                                </div>
                                <div class="col">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="active" value="1"
                                            id="rateActive" checked>
                                        <label class="form-check-label" for="rateActive">
                                            Active?
                                        </label>
                                    </div>
                                </div>
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
                                </li>
                            @empty
                                <li class="list-group-item">No seasonal rates defined yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>


            </div>


        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).on('click', '#sendEmailBtn', function() {
            const $btn = $(this);
            const url = $btn.data('url');

            $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Sending...');

            $.post(url, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                $.toast({
                    heading: 'Success',
                    text: res.message,
                    icon: 'success',
                    position: 'bottom-left',
                    hideAfter: 3000,
                });

                $btn.prop('disabled', false).html('<i class="bi bi-envelope"></i> Send Emails');
            }).fail(function(err) {
                alert(err.responseJSON?.message || 'Something went wrong.');
                $btn.prop('disabled', false).html('<i class="bi bi-envelope"></i> Send Emails');
            });
        });

        $(document).on('click', '.btn-delete', function() {
            $this = $(this);

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
