@extends('layouts.admin')

@section('title', 'Reservation Management — Check Availability')

@push('css')
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }

        .badge-offline {
            background-color: #6c757d;
        }

        .badge-online {
            background-color: #198754;
        }

        .badge-fits {
            background-color: #0d6efd;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 mb-0">Reservation Management <span class="text-muted">/ Check Availability</span></h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" id="btnRefresh">Refresh</button>
                <a class="btn btn-outline-primary btn-sm"  href="{{ route('reservations.index') }}">Back</a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form id="availabilityForm" class="row g-3 align-items-end" method="POST"
                    action="{{ route('admin.reservation_mgmt.availability', ['admin' => auth()->user()->id]) }}">
                    @csrf
                    <div class="col-12 col-md-3">
                        <label class="form-label">Check-in</label>
                        <input type="date" class="form-control" name="checkin" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Check-out</label>
                        <input type="date" class="form-control" name="checkout" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label">Rig length (ft)</label>
                        <input type="number" class="form-control" name="rig_length" min="0" max="100"
                            placeholder="e.g. 32">
                        <div class="form-text">Validates vs site spec</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label">Site type</label>
                        <select class="form-select" name="site_id">
                            <option value="">Any</option>
                            @foreach ($siteTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->sitename }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2 form-check ps-4">
                        <input class="form-check-input" type="checkbox" value="1" id="include_offline"
                            name="include_offline" checked>
                        <label class="form-check-label" for="include_offline">Include offline sites</label>
                    </div>
                    <div class="col-12 col-md-2 ms-auto text-end">
                        <button type="submit" class="btn btn-primary w-100" id="btnSearch">
                            <span class="spinner-border spinner-border-sm d-none" id="searchSpinner" role="status"
                                aria-hidden="true"></span>
                            Check Availability
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info d-flex align-items-center gap-3 py-2 mb-3 d-none" id="selectCustomerHint">
            <div><strong>Heads up:</strong> select a customer to enable “Add to Cart”.</div>
            <button class="btn btn-sm btn-primary ms-auto" id="hintSelectCustomer">Select Customer / Create
                Customer</button>
        </div>


        <div class="row">
            {{-- Results --}}
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>
                            Available Sites


                        </strong>
                        <span class="badge rounded-pill bg-secondary d-none" id="nightsBadge">0 nights</span>


                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="resultsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Site</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th class="text-end">Nightly</th>

                                        <th class="text-end">Taxes</th>
                                        <th class="text-end">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Search to see availability…
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cart --}}
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Cart</strong>
                        <span class="badge bg-secondary" id="cartCount">0</span>
                        <span class="badge bg-secondary d-none" id="forUser"></span>
                    </div>
                    <div class="card-body" id="cartBody">
                        <p class="text-muted mb-0">No items yet.</p>
                    </div>
                    <div class="card-footer bg-white">
                        <button class="btn btn-outline-primary w-100" id="btnCustomer">Select/Create Customer</button>
                        <button class="btn btn-success w-100 mt-2" id="btnCheckout" disabled>Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Modal --}}
    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Search customer</label>
                    <input type="text" class="form-control" id="customerSearch" placeholder="Name, email, or phone…">
                    <div class="list-group mt-2" id="customerResults"></div>

                    <hr>
                    <h6>Create new customer</h6>
                    <div class="row g-2">
                        <div class="col-md-4"><input type="text" class="form-control" id="newName"
                                placeholder="Full name"></div>
                        <div class="col-md-4"><input type="email" class="form-control" id="newEmail"
                                placeholder="Email"></div>
                        <div class="col-md-4"><input type="text" class="form-control" id="newPhone"
                                placeholder="Phone"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="btnCreateCustomer">Save & Select</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Checkout Modal --}}
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Checkout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Coupon code</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="couponCode" placeholder="Enter code">
                                <button class="btn btn-outline-secondary" id="btnApplyCoupon">Apply</button>
                            </div>
                            <div class="form-text">Same validation rules as book site.</div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3" id="totalsBox">
                                <div class="d-flex justify-content-between"><span>Subtotal</span><strong
                                        id="tSubtotal">—</strong></div>
                                <div class="d-flex justify-content-between"><span>Discounts</span><strong
                                        id="tDiscounts">—</strong></div>
                                <div class="d-flex justify-content-between"><span>Tax</span><strong
                                        id="tTax">—</strong></div>
                                <hr>
                                <div class="d-flex justify-content-between fs-5"><span>Total</span><strong
                                        id="tTotal">—</strong></div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2 d-md-flex">
                        <button class="btn btn-outline-dark" data-method="cash">Cash</button>
                        <button class="btn btn-outline-dark" data-method="ach">ACH</button>
                        <button class="btn btn-outline-dark" data-method="gift_card">Gift Card</button>
                        <button class="btn btn-primary" data-method="credit_card">Credit Card</button>
                    </div>

                    <div class="mt-3" id="paymentInputs"><!-- dynamically injected --></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" id="btnPlaceOrder">Place Order</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        (function() {
            const routes = {
                availability: @json(route('admin.reservation_mgmt.availability', ['admin' => auth()->user()->id])),
                cartAdd: @json(route('admin.reservation_mgmt.cart.add', ['admin' => auth()->user()->id])),
                cartGet: @json(route('admin.reservation_mgmt.cart', ['admin' => auth()->user()->id])),
                custSearch: @json(route('admin.reservation_mgmt.customer.search', ['admin' => auth()->user()->id])),
                custCreate: @json(route('admin.reservation_mgmt.customer.create', ['admin' => auth()->user()->id])),
                couponApply: @json(route('admin.reservation_mgmt.coupon.apply', ['admin' => auth()->user()->id])),
                checkout: @json(route('admin.reservation_mgmt.checkout', ['admin' => auth()->user()->id])),
                giftcardLookup: @json(route('admin.reservation_mgmt.giftcard.lookup', ['admin' => auth()->user()->id])),

            };

            // Simple debounce
            const debounce = (fn, d = 250) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), d);
                };
            };

            const $form = $('#availabilityForm');
            const $btnSearch = $('#btnSearch');
            const $spinner = $('#searchSpinner');
            const $tbody = $('#resultsTable tbody');
            const $cartBody = $('#cartBody');
            const $cartCount = $('#cartCount');
            const $btnCheckout = $('#btnCheckout');

            const $customerModal = new bootstrap.Modal('#customerModal');
            const $checkoutModal = new bootstrap.Modal('#checkoutModal');

            const fmt = n => new Intl.NumberFormat(undefined, {
                style: 'currency',
                currency: 'USD'
            }).format(n || 0);
            const setLoading = (b) => {
                $btnSearch.prop('disabled', b);
                $spinner.toggleClass('d-none', !b);
            };

            const cart = {
                items: [],
                customer_id: null,
                totals: {
                    subtotal: 0,
                    tax: 0,
                    discounts: 0,
                    total: 0
                }
            };

            function initTooltips() {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                    const t = bootstrap.Tooltip.getInstance(el);
                    if (t) t.dispose();
                    new bootstrap.Tooltip(el);
                });
            }

            function enableAddButtonsAfterCustomerSelected() {
                $('#resultsTable [data-role="add-wrap"]').each(function() {
                    $(this).replaceWith(
                        '<button class="btn btn-sm btn-outline-primary btnAdd">Add to Cart</button>');
                });
            }

            function recalcTotals() {
                let subtotal = 0,
                    tax = 0,
                    discounts = 0;
                cart.items.forEach(it => {
                    subtotal += (it.price_breakdown?.subtotal || 0);
                    tax += (it.price_breakdown?.tax || 0);
                    discounts += (it.price_breakdown?.discounts || 0);
                });
                cart.totals = {
                    subtotal,
                    tax,
                    discounts,
                    total: subtotal - discounts + tax
                };
            }

            function renderCart() {
                if (!cart.items.length) {
                    $cartBody.html('<p class="text-muted mb-0">No items yet.</p>');
                    $btnCheckout.prop('disabled', true);
                    $cartCount.text(0);
                    return;
                }
                let html = '';
                cart.items.forEach(it => {
                    html += `
                    <div class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong>${it.site_name}</strong><span>${it.checkin} → ${it.checkout}</span>
                        </div>
                        <div class="small text-muted">${it.available_online ? '' : ' • Offline-only'}</div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>Total</span><strong>${fmt(it.price_breakdown?.total)}</strong>
                        </div>
                    </div>`;
                });
                recalcTotals();
                html += `<div class="mt-2">
                    <div class="d-flex justify-content-between"><span>Subtotal</span><strong>${fmt(cart.totals.subtotal)}</strong></div>
                    <div class="d-flex justify-content-between"><span>Discounts</span><strong>-${fmt(cart.totals.discounts)}</strong></div>
                    <div class="d-flex justify-content-between"><span>Tax</span><strong>${fmt(cart.totals.tax)}</strong></div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5"><span>Total</span><strong>${fmt(cart.totals.total)}</strong></div>
                </div>`;

                $cartBody.html(html);
                $btnCheckout.prop('disabled', !cart.customer_id);
                $cartCount.text(cart.items.length);
            }

            $('#btnRefresh').on('click', () => location.reload());

            // Open Customer modal
            $('#btnCustomer, #btnOpenCustomer').on('click', () => $customerModal.show());

            // Availability search
            $form.on('submit', function(e) {
                e.preventDefault();
                setLoading(true);
                $tbody.html(`<tr><td colspan="7" class="text-center py-4">Searching…</td></tr>`);
                $.post(routes.availability, $form.serialize())
                    .done(res => {
                        const $badge = $('#nightsBadge');

                        let nights = null;
                        const raw = res && Array.isArray(res.items) && res.items[0]?.pricing?.nights;

                        if (raw !== undefined && raw !== null && raw !== '') {
                            const n = Number(raw);
                            if (Number.isFinite(n)) nights = n;
                        }

                        if (nights === null) {
                            const ci = $('[name=checkin]').val();
                            const co = $('[name=checkout]').val();
                            if (ci && co) {
                                const d1 = new Date(ci + 'T00:00:00');
                                const d2 = new Date(co + 'T00:00:00');
                                const diff = Math.floor((d2 - d1) / 86400000);
                                nights = Math.max(1, diff);
                            }
                        }

                        if (Number.isFinite(nights)) {
                            $badge.text(`${nights} ${nights === 1 ? 'night' : 'nights'}`).removeClass(
                                'd-none');
                        } else {
                            $badge.addClass('d-none').text('');
                        }

                        if (!res.ok || !res.items?.length) {
                            $tbody.html(
                                `<tr><td colspan="7" class="text-center py-4 text-muted">No availability found.</td></tr>`
                            );
                            return;
                        }

                        const rows = res.items.map(item => {
                            const status = item.available_online ?
                                '<span class="badge badge-online">Online</span>' :
                                '<span class="badge badge-offline">Offline</span>';
                            const fits = item.fits ? ' <span class="badge badge-fits">Fits</span>' :
                                '';

                            const addBtn = cart.customer_id ?
                                `<button class="btn btn-sm btn-outline-primary btnAdd">Add to Cart</button>` :
                                `<span class="d-inline-block" data-role="add-wrap" data-bs-toggle="tooltip" data-bs-title="Select a customer first">
                                      <button class="btn btn-sm btn-outline-secondary" disabled aria-disabled="true">Add to Cart</button>
                                   </span>`;

                            return `
                            <tr data-id="${item.id}" data-json='${JSON.stringify(item)}'>
                                <td><strong>${item.name}</strong></td>
                                <td>${item.type_display ?? ''}</td>
                                <td>${status}${fits}</td>
                                <td class="text-end">${fmt(item.pricing?.nightly)}</td>
                                <td class="text-end">${fmt(item.pricing?.tax)}</td>
                                <td class="text-end">${fmt(item.pricing?.total)}</td>
                                <td class="text-end">${addBtn}</td>
                            </tr>`;
                        }).join('');

                        $tbody.html(rows);

                        // Init tooltips for disabled Add buttons
                        initTooltips();

                        // Show the hint if no customer selected yet
                        if (!cart.customer_id) {
                            $('#selectCustomerHint').removeClass('d-none');
                        }
                    })
                    .fail(xhr => {
                        $tbody.html(
                            `<tr><td colspan="7" class="text-danger text-center py-4">${xhr.responseJSON?.message || 'Search failed.'}</td></tr>`
                        );
                    })
                    .always(() => setLoading(false));
            });

            let cartToken = null;

            $('#resultsTable').on('click', '.btnAdd', function() {
                // Safety guard: require selected customer
                if (!cart.customer_id) {
                    $('#selectCustomerHint').removeClass('d-none');
                    $('#customerModal').modal('show');
                    return;
                }

                const $tr = $(this).closest('tr');
                const item = JSON.parse($tr.attr('data-json'));

                const checkin = $form.find('[name="checkin"]').val();
                const checkout = $form.find('[name="checkout"]').val();

                const payload = {
                    _token: $('input[name=_token]').val(),
                    site_id: item.id,
                    checkin,
                    checkout,
                    customer_id: cart.customer_id || null,
                    cart_token: cartToken,
                    price_breakdown: {
                        nightly: item.pricing?.nightly || 0,
                        nights: item.pricing?.nights || 1,
                        subtotal: item.pricing?.subtotal || 0,
                        tax: item.pricing?.tax || 0,
                        discounts: 0,
                        total: item.pricing?.total || 0
                    }
                };

                $.post(routes.cartAdd, payload)
                    .done(res => {
                        if (res.cart_token && !cartToken) cartToken = res.cart_token;
                        cart.items.push({
                            customer_id: cart.customer_id || null,
                            site_id: item.id,
                            site_name: item.name,
                            site_type: item.type ?? '',
                            available_online: !!item.available_online,
                            checkin,
                            checkout,
                            price_breakdown: payload.price_breakdown,
                        });
                        renderCart();

                    })
                    .fail(xhr => alert(xhr.responseJSON?.message || 'Unable to add to cart.'));
            });

            (function() {
                const $input = $('#customerSearch');
                const $results = $('#customerResults');

                let searchTimer = null;
                let inFlightReq = null;

                function renderResults(res) {
                    const html = (res?.hits || []).map(h => `
                    <button type="button" class="list-group-item list-group-item-action selCustomer" data-name="${h.f_name} ${h.l_name}" data-id="${h.id}">
                        ${h.f_name}  ${h.l_name}${h.email ? ' • ' + h.email : ''}${h.phone ? ' • ' + h.phone : ''}
                    </button>
                    `).join('');
                    $results.html(html || '<div class="text-muted small px-2 py-1">No matches</div>');
                }

                function doSearch(q) {
                    // cancel previous request if still running
                    if (inFlightReq && inFlightReq.readyState !== 4) inFlightReq.abort();

                    inFlightReq = $.ajax({
                            url: routes.custSearch,
                            method: 'POST',
                            data: {
                                _token: $('input[name=_token]').val(),
                                q
                            },
                        })
                        .done(res => {
                            // ensure we’re still showing results for the same query
                            if ($input.val().trim() === q) renderResults(res);
                        })
                        .fail((xhr, status) => {
                            if (status !== 'abort') {
                                console.error('Customer search failed:', xhr?.responseJSON || status);
                                $results.html('<div class="text-danger small px-2 py-1">Search failed</div>');
                            }
                        });
                }

                function schedule() {
                    clearTimeout(searchTimer);
                    const q = $input.val().trim();
                    if (q.length < 2) {
                        $results.empty();
                        return;
                    }
                    searchTimer = setTimeout(() => doSearch(q), 300);
                }

                // bind once, namespaced to avoid duplicates
                $input.off('input.customer').on('input.customer', schedule);

                // optional: tidy up on modal hide/show
                $('#customerModal')
                    .off('shown.bs.modal.customer hidden.bs.modal.customer')
                    .on('shown.bs.modal.customer', () => $input.trigger('focus'))
                    .on('hidden.bs.modal.customer', () => {
                        clearTimeout(searchTimer);
                        if (inFlightReq && inFlightReq.readyState !== 4) inFlightReq.abort();
                        $results.empty();
                        $input.val('');
                    });
            })();

            function applySelectedCustomer(id, name) {
                cart.customer_id = id;
                cart.customer_name = (name || '').trim() || '(selected)';

                const $nameCust = $('#forUser');
                if (cart.customer_id) {
                    $nameCust.removeClass('d-none').text(`For: ${cart.customer_name}`);
                    // Enable Add-to-Cart buttons that were disabled
                    if (typeof enableAddButtonsAfterCustomerSelected === 'function') {
                        enableAddButtonsAfterCustomerSelected();
                    }
                    $('#selectCustomerHint').addClass('d-none');
                    $('#btnCheckout').prop('disabled', cart.items.length === 0);
                } else {
                    $nameCust.addClass('d-none').text('');
                    $('#btnCheckout').prop('disabled', true);
                }
                renderCart();
                $('#customerModal').modal('hide');
            }


            $('#customerResults').on('click', '.selCustomer', function() {
                cart.customer_id = $(this).data('id');
                cart.customer_name = $(this).data('name');
                $nameCust = $('#forUser');
                console.log('Selected customer ID:', cart.customer_id);
                if (cart.customer_id) {
                    $nameCust.removeClass('d-none').text(`For: ${cart.customer_name || '(selected)'}`);
                    console.log('Cart customer ID:', [cart.customer_id, cart.customer_name]);
                    enableAddButtonsAfterCustomerSelected();
                    $('#selectCustomerHint').addClass('d-none');
                } else {
                    $nameCust.addClass('d-none').text('');
                }
                renderCart();
                $customerModal.hide();
            });

            $('#btnCreateCustomer').on('click', function() {
                const fullName = ($('#newName').val() || '').trim();
                $.post(routes.custCreate, {
                        _token: $('input[name=_token]').val(),
                        name: fullName,
                        email: $('#newEmail').val(),
                        phone: $('#newPhone').val(),
                    })
                    .done(res => {
                        applySelectedCustomer(res.id, fullName);
                        $('#newName, #newEmail, #newPhone').val('');
                    })
                    .fail(xhr => alert(xhr.responseJSON?.message || 'Unable to create customer'));
            });


            // Quick path from hint banner to modal
            $('#hintSelectCustomer').on('click', () => $('#customerModal').modal('show'));

            // Checkout
            $('#btnCheckout').on('click', function() {
                $('#tSubtotal').text(fmt(cart.totals.subtotal));
                $('#tDiscounts').text('-' + fmt(cart.totals.discounts));
                $('#tTax').text(fmt(cart.totals.tax));
                $('#tTotal').text(fmt(cart.totals.total));
                $checkoutModal.show();
            });

            $('#btnApplyCoupon').on('click', function() {
                const code = $('#couponCode').val().trim();
                if (!code) return;
                $.post(routes.couponApply, {
                        _token: $('input[name=_token]').val(),
                        code
                    })
                    .done(res => {
                        if (res.totals) {
                            $('#tSubtotal').text(fmt(res.totals.subtotal || 0));
                            $('#tDiscounts').text('-' + fmt(res.totals.discounts || 0));
                            $('#tTax').text(fmt(res.totals.tax || 0));
                            $('#tTotal').text(fmt(res.totals.total || 0));
                        }
                    })
                    .fail(xhr => alert(xhr.responseJSON?.message || 'Coupon invalid'));
            });

            // Lookup gift card and validate balance
            $('#checkoutModal').on('click', '#btnLookupGiftcard', function() {
                const code = ($('#giftCardCode').val() || '').trim();
                if (!code) {
                    alert('Please enter a gift card code.');
                    return;
                }
                const $btn = $(this);
                $btn.prop('disabled', true);
                $('#giftCardInfo').text('Looking up...');
                $.post(routes.giftcardLookup, {
                        _token: $('input[name=_token]').val(),
                        code
                    })
                    .done(res => {
                        const balance = Number(res.balance) || 0;
                        $('#giftCardInfo').text(`Balance: ${fmt(balance)}`);
                    })
                    .fail(xhr => {
                        $('#giftCardInfo').text('');
                        alert(xhr.responseJSON?.message || 'Unable to validate gift card.');
                    })
                    .always(() => $btn.prop('disabled', false));
            });







            // Payment input switching
            $('#checkoutModal').on('click', 'button[data-method]', function() {
                const method = $(this).data('method');
                let html = '';
                if (method === 'gift_card') {
                    html = `
                    <label class="form-label mt-3">Gift card code / scan</label>
                    <div class="input-group">
                        <input class="form-control" id="giftCardCode" placeholder="Scan or type code">
                        <button class="btn btn-outline-secondary" id="btnLookupGiftcard">Lookup</button>
                    </div>
                    <div class="mt-2 small" id="giftCardInfo"></div>`;

                } else if (method === 'ach') {
                    html = `
                    <div class="row g-2 mt-3">
                    <div class="col-md-6"><input class="form-control" id="achName" placeholder="Account holder"></div>
                    <div class="col-md-6"><input class="form-control" id="achRouting" placeholder="Routing #"></div>
                    <div class="col-md-6"><input class="form-control" id="achAccount" placeholder="Account #"></div>
                    </div>`;
                } else if (method === 'credit_card') {
                    html = `
                    <div class="row g-2 mt-3">
                    <div class="col-md-8"><input class="form-control" id="ccNumber" placeholder="Card number (via SOLA/iFields)"></div>
                    <div class="col-md-2"><input class="form-control" id="ccExp" placeholder="MM/YY"></div>
                    <div class="col-md-2"><input class="form-control" id="ccCvv" placeholder="CVV"></div>
                    </div>`;
                } else {
                    html = '<div class="mt-3 text-muted">Cash selected.</div>';
                }
                $('#paymentInputs').html(html).data('method', method);
            });

            // Place order
            $('#btnPlaceOrder').on('click', function() {
                const method = $('#paymentInputs').data('method') || 'credit_card';
                const payload = {
                    _token: $('input[name=_token]').val(),
                    customer_id: cart.customer_id,
                    payment_method: method,
                    gift_card_code: $('#giftCardCode').val(),
                    ach: {
                        name: $('#achName').val(),
                        routing: $('#achRouting').val(),
                        account: $('#achAccount').val()
                    },
                    cc: {
                        number: $('#ccNumber').val(),
                        exp: $('#ccExp').val(),
                        cvv: $('#ccCvv').val()
                    },
                };

                if (method === 'gift_card') {
                    const code = (payload.gift_card_code || '').trim();
                    if (!code) {
                        alert('Please enter a gift card code.');
                        return;
                    }
                    $.post(routes.giftcardLookup, {
                            _token: $('input[name=_token]').val(),
                            code
                        })
                        .done(res => {
                            const balance = Number(res.balance) || 0;
                            const total = Number(cart.totals.total) || 0;
                            if (balance < total) {
                                alert(
                                    `Gift card balance (${fmt(balance)}) is less than the total (${fmt(total)}). Choose another method or split the payment.`
                                );
                                return;
                            }
                            // ok to proceed
                            doCheckout(payload);
                        })
                        .fail(xhr => {
                            alert(xhr.responseJSON?.message || 'Unable to validate gift card.');
                        });
                    return;
                }


                doCheckout(payload);

            });

            function doCheckout(payload) {
                $.post(routes.checkout, payload)
                    .done(res => {
                        alert(res.message || 'Success');
                        location.reload();
                    })
                    .fail(xhr => alert(xhr.responseJSON?.message || 'Checkout failed'));
            }

        })();
    </script>
@endpush
