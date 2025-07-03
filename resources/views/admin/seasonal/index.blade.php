@extends('layouts.admin')

@section('title', 'Seasonal Settings')

@section('content')


    <div class="card shadow border-0 bg-white rounded-4 overflow-hidden">
        <div class="card-header bg-gradient text-dark d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #00b09b, #96c93d);">
            <h4 class="mb-0">
                <i class="bi bi-gear-fill me-2 "></i> Seasonal Guest Renewal Settings
            </h4>
        </div>

        <div class="card-body py-4 px-4">
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
                </div>

                <div class="col-md-6">
                    <div class="border rounded-3 p-4 shadow-sm">
                        <h5 class="mb-3 text-primary">💲 Define Seasonal Rates</h5>
                        <form method="POST" action="{{ route('settings.storeRate') }}">
                            @csrf
                            <div class="form-floating mb-3">
                                <input name="rate_name" class="form-control" id="rateName" placeholder="Rate Name"
                                    required>
                                <label for="rateName">Rate Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input name="rate_price" type="number" step="0.01" class="form-control" id="ratePrice"
                                    placeholder="Rate Price" required>
                                <label for="ratePrice">Rate Price</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input name="deposit_amount" type="number" step="0.01" class="form-control"
                                    id="depositAmount" placeholder="Deposit" required>
                                <label for="depositAmount">Deposit Amount</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input name="early_pay_discount" type="number" step="0.01" class="form-control"
                                    id="earlyDiscount" placeholder="Early Pay Discount">
                                <label for="earlyDiscount">Early Pay Discount ($)</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input name="full_payment_discount" type="number" step="0.01" class="form-control"
                                    id="fullDiscount" placeholder="Full Payment Discount">
                                <label for="fullDiscount">Full Payment Discount ($)</label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Plan Starts</label>
                                <input name="payment_plan_starts" type="date" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Final Payment Due</label>
                                <input name="final_payment_due" type="date" class="form-control">
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
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="applies_to_all" value="1"
                                    id="appliesToAll">
                                <label class="form-check-label" for="appliesToAll">
                                    Applies to All (For liability waivers)
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="active" value="1"
                                    id="rateActive" checked>
                                <label class="form-check-label" for="rateActive">
                                    Active?
                                </label>
                            </div>
                            <button class="btn btn-primary w-100">Save Seasonal Rate</button>
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
                text: "Do you really want to delete this template?",
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
