@extends('layouts.admin')

@section('title', 'Reservation Draft – Customer (Step 2)')

@push('css')
    <style>
        .cart-panel {
            position: sticky;
            top: 20px;
        }
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .dirty-input {
            border-color: #ffc107;
            background-color: #fffdec;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Reservation Draft – Customer (Step 2)</h1>
        <div>
            <button class="btn btn-outline-secondary me-2" id="returnBtn">
                <i class="fas fa-arrow-left me-1"></i> Return to Step 1
            </button>
            <button class="btn btn-primary" id="topPayBtn">
                Pay <i class="fas fa-credit-card ms-1"></i>
            </button>
        </div>
    </div>

    {{-- Top Section: Customer Actions --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Customer Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="customerSearch" placeholder="Search by name, email, or phone...">
                    </div>
                    <div id="searchResults" class="list-group mt-1 position-absolute w-100 shadow-lg d-none" style="z-index: 1000; max-width: 600px;"></div>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-outline-primary" id="addNewCustomerBtn">
                        <i class="fas fa-user-plus me-1"></i> Add New Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Left 2/3: Customer Forms --}}
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Customer Details</h5>
                <button class="btn btn-sm btn-warning update-customer-btn" disabled>
                    Update Customer
                </button>
            </div>

            <form id="customerForm">
                <input type="hidden" name="customer_id" id="customer_id" value="{{ $draft->customer_id }}">
                
                {{-- Primary Customer --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Primary Customer <span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="primary[f_name]" value="{{ $primaryCustomer->f_name ?? '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="primary[l_name]" value="{{ $primaryCustomer->l_name ?? '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-info-emphasis small">(Required if no phone)</span></label>
                                <input type="email" class="form-control" name="primary[email]" value="{{ $primaryCustomer->email ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone <span class="text-info-emphasis small">(Required if no email)</span></label>
                                <input type="tel" class="form-control" name="primary[phone]" value="{{ $primaryCustomer->phone ?? '' }}">
                            </div>
                            <div class="col-12">
                                <hr>
                                <label class="form-label fw-bold">Address (Optional)</label>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Street Address</label>
                                <input type="text" class="form-control" name="primary[street_address]" value="{{ $primaryCustomer->street_address ?? '' }}">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="primary[city]" value="{{ $primaryCustomer->city ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="primary[state]" value="{{ $primaryCustomer->state ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Zip Code</label>
                                <input type="text" class="form-control" name="primary[zip]" value="{{ $primaryCustomer->zip ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Guest --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Guest (Secondary, Optional)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="guest_data[name]" value="{{ $draft->guest_data['name'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="guest_data[email]" value="{{ $draft->guest_data['email'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="guest_data[phone]" value="{{ $draft->guest_data['phone'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mb-5">
                    <button type="button" class="btn btn-warning update-customer-btn" disabled>
                        Update Customer
                    </button>
                </div>
            </form>
        </div>

        {{-- Right 1/3: Cart Review --}}
        <div class="col-lg-4">
            <div class="card shadow-sm cart-panel">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Cart Review</h5>
                </div>
                <div class="card-body">
                    <div id="cartItems">
                        @foreach($draft->cart_data as $index => $item)
                            <div class="cart-item" data-index="{{ $index }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $item['name'] }}</strong><br>
                                        <small class="text-muted">Base: ${{ number_format($item['base'] + $item['fee'], 2) }}</small>
                                    </div>
                                    <button class="btn btn-sm btn-link text-danger remove-item" data-index="{{ $index }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="addons-list small">
                                    @foreach($item['addons'] as $addon)
                                        <div class="text-muted">+ {{ $addon['name'] }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span id="subtotalDisplay">${{ number_format($draft->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Discounts</span>
                        <span id="discountsDisplay">-${{ number_format($draft->discount_total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Estimated Tax</span>
                        <span id="taxDisplay">${{ number_format($draft->estimated_tax, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 fw-bold h5">
                        <span>Grand Total</span>
                        <span id="grandTotalDisplay">${{ number_format($draft->grand_total, 2) }}</span>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <button class="btn btn-success w-100 py-3 fw-bold" id="payBtn">
                        Pay Now <i class="fas fa-credit-card ms-1"></i>
                    </button>
                    <div class="text-center mt-2 small text-muted">
                        Changes to dates or sites require return to Step 1.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Return Warning Modal --}}
<div class="modal fade" id="returnWarningModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Unsaved Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                You have unsaved customer changes. Continuing will discard them.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel (Stay Step 2)</button>
                <a href="{{ route('flow-reservation.step1', ['draft_id' => $draft->draft_id]) }}" class="btn btn-danger">Continue (Discard & Return)</a>
            </div>
        </div>
    </div>
</div>

@include('cart.components.summary', ['subtotal' => $draft->subtotal, 'totalDiscount' => $draft->discount_total, 'totalTax' => $draft->estimated_tax, 'order_id' => $draft->draft_id])

@endsection

@push('js')
<script>
$(function() {
    let draft = @json($draft);
    let isDirty = false;

    // Search Logic
    $('#customerSearch').on('input', function() {
        const q = $(this).val();
        if (q.length < 2) {
            $('#searchResults').addClass('d-none');
            return;
        }

        $.get("{{ route('admin.reservation_mgmt.customer.search', ['admin' => auth()->id()]) }}", { q: q })
            .done(function(res) {
                const results = $('#searchResults');
                results.empty().removeClass('d-none');
                if (res.hits.length === 0) {
                    results.append('<div class="list-group-item">No customers found.</div>');
                } else {
                    res.hits.forEach(customer => {
                        results.append(`
                            <a href="javascript:void(0)" class="list-group-item list-group-item-action select-customer" 
                                data-id="${customer.id}" 
                                data-fname="${customer.f_name}" 
                                data-lname="${customer.l_name}"
                                data-email="${customer.email || ''}"
                                data-phone="${customer.phone || ''}"
                                data-street="${customer.street_address || ''}"
                                data-city="${customer.city || ''}"
                                data-state="${customer.state || ''}"
                                data-zip="${customer.zip || ''}">
                                <strong>${customer.f_name} ${customer.l_name}</strong><br>
                                <small class="text-muted">${customer.email || ''} | ${customer.phone || ''}</small>
                            </a>
                        `);
                    });
                }
            });
    });

    $(document).on('click', '.select-customer', function() {
        const data = $(this).data();
        $('#customer_id').val(data.id);
        $('[name="primary[f_name]"]').val(data.fname);
        $('[name="primary[l_name]"]').val(data.lname);
        $('[name="primary[email]"]').val(data.email);
        $('[name="primary[phone]"]').val(data.phone);
        $('[name="primary[street_address]"]').val(data.street);
        $('[name="primary[city]"]').val(data.city);
        $('[name="primary[state]"]').val(data.state);
        $('[name="primary[zip]"]').val(data.zip);
        
        $('#searchResults').addClass('d-none');
        $('#customerSearch').val(`${data.fname} ${data.lname}`);
        
        markDirty(true);
    });

    $('#addNewCustomerBtn').on('click', function() {
        $('#customer_id').val('');
        $('#customerForm')[0].reset();
        $('#customerSearch').val('');
        markDirty(true);
    });

    // Dirty Tracking
    $('#customerForm input').on('input change', function() {
        markDirty(true);
        $(this).addClass('dirty-input');
    });

    function markDirty(dirty) {
        isDirty = dirty;
        $('.update-customer-btn').prop('disabled', !dirty);
        if (!dirty) {
            $('input').removeClass('dirty-input');
        }
    }

    // Save Logic
    $('.update-customer-btn').on('click', async function() {
        await saveCustomer();
    });

    async function saveCustomer() {
        const $btn = $('.update-customer-btn');
        $btn.prop('disabled', true).text('Saving...');

        try {
            const payload = {
                _token: "{{ csrf_token() }}",
                customer_id: $('#customer_id').val(),
                primary: {
                    f_name: $('[name="primary[f_name]"]').val(),
                    l_name: $('[name="primary[l_name]"]').val(),
                    email: $('[name="primary[email]"]').val(),
                    phone: $('[name="primary[phone]"]').val(),
                    street_address: $('[name="primary[street_address]"]').val(),
                    city: $('[name="primary[city]"]').val(),
                    state: $('[name="primary[state]"]').val(),
                    zip: $('[name="primary[zip]"]').val(),
                },
                guest_data: {
                    name: $('[name="guest_data[name]"]').val(),
                    email: $('[name="guest_data[email]"]').val(),
                    phone: $('[name="guest_data[phone]"]').val(),
                }
            };

            const res = await $.post("{{ route('flow-reservation.update-customer', $draft->draft_id) }}", payload);

            if (res.success) {
                $('#customer_id').val(res.customer_id);
                draft.customer_id = res.customer_id;
                draft.primary = payload.primary; // Update local state
                markDirty(false);
                toastr.success(res.message);
                return true;
            }
        } catch (err) {
            let errorMessage = 'An unexpected error occurred.';
            if (err.responseJSON && err.responseJSON.message) {
                errorMessage = err.responseJSON.message;
            } else if (err.status === 422) {
                errorMessage = 'Validation failed. Please ensure First Name and either Email or Phone are provided.';
            }
            toastr.error(errorMessage);
            return false;
        } finally {
            $btn.text('Update Customer').prop('disabled', !isDirty);
        }
    }

    // Return Logic
    $('#returnBtn').on('click', function() {
        if (isDirty) {
            $('#returnWarningModal').modal('show');
        } else {
            window.location.href = "{{ route('flow-reservation.step1', ['draft_id' => $draft->draft_id]) }}";
        }
    });

    // Remove Item
    $('.remove-item').on('click', function() {
        const index = $(this).data('index');
        if (!confirm('Are you sure you want to remove this item?')) return;

        $.post("{{ route('flow-reservation.remove-item', $draft->draft_id) }}", {
            _token: "{{ csrf_token() }}",
            index: index
        })
        .done(function(res) {
            if (res.success) {
                location.reload();
            }
        });
    });

    // Payment Drawer Integration
    $('#payBtn, #topPayBtn').on('click', async function() {
        const fname = $('[name="primary[f_name]"]').val();
        const email = $('[name="primary[email]"]').val();
        const phone = $('[name="primary[phone]"]').val();

        if (!fname || (!email && !phone)) {
            toastr.warning('Please select a customer or add a new one (First Name + Email/Phone required).');
            return;
        }

        if (isDirty) {
            const saved = await saveCustomer();
            if (!saved) return;
        }

        openPaymentDrawer();
    });

    function openPaymentDrawer() {
        const fmt = (amount) => `$${parseFloat(amount).toFixed(2)}`;

        // Map Draft Totals to Drawer
        $('#offcanvasSubtotal').text(fmt(draft.subtotal));
        $('#offcanvasTax').text(fmt(draft.estimated_tax));
        $('#displayTotalAmount').text(fmt(draft.grand_total));
        $('#remainingBalance').text(fmt(draft.grand_total));
        
        $('#total-amount').val(draft.grand_total.toFixed(2));
        $('#subtotal-amount').val(draft.subtotal.toFixed(2));
        $('#tax-amount').text(fmt(draft.estimated_tax));

        // Reservation Specific Fees
        $('.res-fee-row').show();
        $('#offcanvasPlatformFee').text(fmt(draft.platform_fee || 0));
        $('#offcanvasSiteLock').text(fmt(draft.sitelock_fee || 0));

        // Discounts
        if (draft.discount_total > 0) {
            $('#discount-section, #discount-section1').show();
            $('#offcanvasDiscount').text(fmt(draft.discount_total));
        } else {
            $('#discount-section, #discount-section1').hide();
        }

        // Prefill Email
        $('#email_invoice').val($('[name="primary[email]"]').val() || '');
        $('#cust_email').val($('[name="primary[email]"]').val() || '');

        // Set draft context
        $('#order_id').val(draft.draft_id);

        const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasOrder'));
        offcanvas.show();
    }

    // JS Environment for POS Drawer
    window.cartOrderStoreUrl = "{{ route('flow-reservation.finalize', $draft->draft_id) }}";
    window.cartOrderUpdateUrl = "{{ route('flow-reservation.finalize', $draft->draft_id) }}";
    window.processGiftCard = "{{ route('orders.process.gift.card') }}";
    window.updateGiftCardBalance = "{{ route('orders.process.gift.card.balance') }}";
    window.sentInvoiceEmail = "{{ route('orders.send.invoice') }}";
    window.processingCheckPayment = "{{ route('cart.processCheckPayment') }}";
    window.cardknoxApiKey = "{{ env('CARDKNOX_API_KEY') }}";
    window.insertCardsOnFiles = "{{ route('insert.cards.on.files') }}";
});
</script>
@endpush
