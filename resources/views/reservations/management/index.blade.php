@extends('layouts.admin')

@section('title', 'Reservation Management — Check Availability')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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
                <a class="btn btn-outline-primary btn-sm" href="{{ route('reservations.index') }}">Back</a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form id="availabilityForm" class="row g-3 align-items-end" method="POST"
                    action="{{ route('admin.reservation_mgmt.availability', ['admin' => auth()->user()->id]) }}">
                    @csrf

                    {{-- Dates (wide) --}}
                    <div class="col-12 col-md-5">
                        <label class="form-label">Dates</label>
                        <input type="text" class="form-control" id="dateRange" placeholder="MM/DD/YYYY — MM/DD/YYYY"
                            autocomplete="off" required>
                        <!-- Hidden fields kept for server compatibility -->
                        <input type="hidden" name="start_date" id="checkinHidden">
                        <input type="hidden" name="end_date" id="checkoutHidden">
                        <div class="form-text">Select check-in and check-out (MM/DD/YYYY).</div>
                    </div>

                    {{-- Rig length --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label">Rig length (ft)</label>
                        <input type="number" class="form-control" name="riglength" min="0" max="100"
                            placeholder="e.g. 32" inputmode="numeric" pattern="[0-9]*">
                        <div class="form-text">Matches site max length</div>
                    </div>

                    {{-- Site class --}}
                    <div class="col-2 col-mb-2">
                        <label class="form-label">Site Class</label>
                        <select class="form-select" name="siteclass">
                            <option value="">Any</option>
                            @foreach ($siteClasses as $classes)
                                <option value="{{ $classes->siteclass }}">{{ $classes->siteclass }}</option>
                            @endforeach

                        </select>
                        <div class="form-text">---</div>
                    </div>
                    {{-- Site hookups --}}
                    <div class="col-2 col-mb-2">
                        <label class="form-label">Site Hookups</label>
                        <select class="form-select" name="hookup">
                            <option value="">Any</option>
                            @foreach ($siteHookups as $hookup)
                                <option value="{{ $hookup->sitehookup }}">{{ $hookup->sitehookup }}</option>
                            @endforeach

                        </select>
                        <div class="form-text">---</div>
                    </div>


                    {{-- View String --}}
                    <div class="col-2 col-mb-2">
                        <label class="form-label">View </label>
                        <select class="form-select" name="view">
                            <option value="units">units</option>
                            <option value="aggregated">aggregated</option>


                        </select>
                    </div>

                    <div class="col-auto me-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="pets_ok" name="pets_ok"
                                checked>
                            <div class="form-text">With Pets</div>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="include_offline"
                                name="include_offline" checked>
                            <div class="form-text">Include Offline</div>
                        </div>
                    </div>




                    <div class="col-12">
                        <div class="d-flex gap-2">

                            <div class="text-muted small align-self-center">
                                Tip: availability checks automatically when you change any filter.
                            </div>
                        </div>
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

                    <div class="card-body p-0" style="height: 50vh; overflow-y: auto; position: relative;">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="resultsTable">
                                <thead class="table-light" id="resultsHead">
                                    <tr>
                                        <th>Site ID</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Hookup</th>
                                        <th class="text-center">Max Length</th>
                                        <th>Status</th>
                                        <th>Price Quote</th>
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

    <!-- Add Occupants Modal -->
    <div class="modal fade" id="addOccupantsModal" tabindex="-1" aria-labelledby="addOccupantsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOccupantsModalLabel">Add Occupants</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="occupantsForm">
                        <div class="mb-3">
                            <label for="adultsInput" class="form-label">Adults</label>
                            <input type="number" class="form-control" id="adultsInput" name="adults" value="0"
                                min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="childrenInput" class="form-label">Children</label>
                            <input type="number" class="form-control" id="childrenInput" name="children"
                                value="0" min="0" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnConfirmOccupants" class="btn btn-primary">Continue</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        if (!document.querySelector('#dateRange')._flatpickr) {
            flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "m/d/Y",
                allowInput: true,
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates && selectedDates.length === 2) {
                        const d1 = selectedDates[0];
                        const d2 = selectedDates[1];

                        const toIso = d =>
                            `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

                        document.getElementById('checkinHidden').value = toIso(d1);
                        document.getElementById('checkoutHidden').value = toIso(d2);

                        document.getElementById('dateRange').value =
                            `${instance.formatDate(d1, 'm/d/Y')} — ${instance.formatDate(d2, 'm/d/Y')}`;

                        if (window.__availabilityTrigger) window.__availabilityTrigger();
                    }
                }
            });
        }


        // (function() {
        //     const ci = document.getElementById('checkinHidden').value;
        //     const co = document.getElementById('checkoutHidden').value;
        //     if (ci && co) {
        //         const d1 = new Date(ci + 'T00:00:00');
        //         const d2 = new Date(co + 'T00:00:00');
        //         const fp = document.querySelector('#dateRange')._flatpickr;
        //         if (fp) fp.setDate([d1, d2], true);
        //     }
        // })();
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        (function() {
            const routes = {
                availability: @json(route('admin.reservation_mgmt.availability', ['admin' => auth()->user()->id])),
                cartAdd: @json(route('admin.reservation_mgmt.cart.add', ['admin' => auth()->user()->id])),
                cartItems: @json(route('admin.reservation_mgmt.cart.item.cartItems', ['admin' => auth()->user()->id])),
                cartGet: @json(route('admin.reservation_mgmt.cart', ['admin' => auth()->user()->id])),
                custSearch: @json(route('admin.reservation_mgmt.customer.search', ['admin' => auth()->user()->id])),
                custCreate: @json(route('admin.reservation_mgmt.customer.create', ['admin' => auth()->user()->id])),
                couponApply: @json(route('admin.reservation_mgmt.coupon.apply', ['admin' => auth()->user()->id])),
                checkout: @json(route('admin.reservation_mgmt.checkout', ['admin' => auth()->user()->id])),
                giftcardLookup: @json(route('admin.reservation_mgmt.giftcard.lookup', ['admin' => auth()->user()->id]))
            };

            const debounce = (fn, d = 300) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), d);
                };
            };

            const $form = $('#availabilityForm');
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

            $('#btnCustomer, #btnOpenCustomer').on('click', () => $customerModal.show());

            let _inFlightAvailability = null;

            function runAvailabilitySearch() {

                let ci = $form.find('[name="start_date"]').val();
                let co = $form.find('[name="end_date"]').val();

                const fp = document.querySelector('#dateRange')._flatpickr;
                if ((!ci || !co) && fp && fp.selectedDates.length === 2) {
                    const [d1, d2] = fp.selectedDates;
                    const toIso = d =>
                        `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
                    ci = toIso(d1);
                    co = toIso(d2);
                    $form.find('[name="start_date"]').val(ci);
                    $form.find('[name="end_date"]').val(co);
                }

                if (!ci || !co) {
                    $tbody.html(
                        `<tr><td colspan="7" class="text-center py-4 text-muted">
                            Enter check-in and check-out to see availability…
                        </td></tr>`
                    );
                    return;
                }



                if (_inFlightAvailability && _inFlightAvailability.readyState !== 4) {
                    try {
                        _inFlightAvailability.abort();
                    } catch (e) {}
                    _inFlightAvailability = null;
                }

                setLoading(true);
                $tbody.html(`<tr><td colspan="7" class="text-center py-4">Searching…</td></tr>`);


                _inFlightAvailability = $.get(routes.availability, $form.serialize())
                    .done(res => {
                        const $badge = $('#nightsBadge');
                        const data = res?.response || {};
                        const results = data.results || {};


                        console.log('Availability results:', data.view);
                        let nights = 1;
                        const ci = $('[name=start_date]').val();
                        const co = $('[name=end_date]').val();

                        if (ci && co) {
                            const d1 = new Date(ci + 'T00:00:00');
                            const d2 = new Date(co + 'T00:00:00');
                            nights = Math.max(1, Math.floor((d2 - d1) / 86400000));
                        }
                        $badge.text(`${nights} ${nights === 1 ? 'night' : 'nights'}`).removeClass('d-none');

                        let headHtml = '';
                        let rows = '';

                        if (data.view === 'units' && Array.isArray(results?.units)) {
                            headHtml = `
                            <tr>
                                <th>Site ID</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Hookup</th>
                                <th class="text-center">Max Length</th>
                                <th>Status</th>
                                <th>Price Quote</th>
                            </tr>
                        `;

                            const selectedClass = $('[name="siteclass"]').val();
                            const selectedHookup = $('[name="hookup"]').val();



                            rows = results.units
                                .filter(unit => {
                                    const matchClass = selectedClass === '---' || !selectedClass ?
                                        true :
                                        unit.class?.toLowerCase() === selectedClass.toLowerCase();

                                    const matchHookup = selectedHookup === '---' || !selectedHookup ?
                                        true :
                                        unit.hookup?.toLowerCase() === selectedHookup.toLowerCase();

                                    return matchClass && matchHookup;
                                })

                                .map(unit => {
                                    const isAvailable = unit?.status?.available;
                                    const statusBadge = isAvailable ?
                                        '<span class="badge bg-success">Available</span>' :
                                        unit?.status?.reserved ?
                                        '<span class="badge bg-warning text-dark">Reserved</span>' :
                                        unit?.status?.in_cart ?
                                        '<span class="badge bg-info text-dark">In Cart</span>' :
                                        '<span class="badge bg-secondary">Unavailable</span>';

                                    const priceHtml = unit?.price_quote ?
                                        `
                                        <div class="small">
                                            <div><strong>Total:</strong> $${Number(unit.price_quote.total ?? 0).toFixed(2)}</div>
                                            <div><strong>Avg/Night:</strong> $${Number(unit.price_quote.avg_nightly ?? 0).toFixed(2)}</div>
                                        </div>
                                    ` :
                                        '<span class="text-muted small">—</span>';

                                    const addToCartBtn = isAvailable ?
                                        `
                                        <button class="btn btn-sm btn-primary addToCartBtn mt-1"
                                            data-site-id="${unit.site_id}"
                                            data-total="${unit.price_quote?.total ?? 0}"
                                            data-price-quote-id="${unit.price_quote.price_quote_id ?? ''}"
                                            data-start="${ci}"
                                            data-end="${co}"
                                            >
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        </button>
                                    ` :
                                        '';

                                    return `
                                    <tr>
                                        <td><strong>${unit?.site_id ?? ''}</strong></td>
                                        <td>${unit?.name ?? ''}</td>
                                        <td>${unit?.class ? unit.class.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : ''}</td>
                                        <td>${unit?.hookup ?? ''}</td>
                                        <td class="text-center">${unit?.maxlength ?? ''}</td>
                                        <td>${statusBadge}</td>
                                        <td class="text-end">
                                            ${priceHtml}
                                            ${addToCartBtn}
                                        </td>
                                    </tr>`;
                                }).join('');
                        } else if (data.view === 'aggregated' && Array.isArray(results.buckets)) {
                            headHtml = `
                            <tr>
                                <th>Class</th>
                                <th>Hookup</th>
                                <th class="text-center">Available</th>
                                <th class="text-center">Held</th>
                                <th class="text-center">Reserved</th>
                                <th class="text-center">Total</th>
                                <th>Price Options</th>
                            </tr>
                        `;

                            rows = results.buckets.map(bucket => `
                            <tr>
                                <td>${bucket?.class ? bucket.class.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : ''}</td>
                                <td>${bucket.hookup || ''}</td>
                                <td class="text-center">${bucket.available ?? 0}</td>
                                <td class="text-center">${bucket.held ?? 0}</td>
                                <td class="text-center">${bucket.reserved ?? 0}</td>
                                <td class="text-center">${bucket.total ?? 0}</td>
                                <td class="text-end">
                                    ${
                                        Array.isArray(bucket.price_options) && bucket.price_options.length
                                            ? bucket.price_options
                                                .map(po => `
                                                                            <div class="small border-bottom pb-1 mb-1">
                                                                                <div><strong>Total:</strong> $${po.total ?? '0'}</div>
                                                                                <div><strong>Avg/Night:</strong> $${nights ?? '0'}</div>
                                                                            </div>
                                                                        `)
                                                .join('')
                                            : '<span class="text-muted small">—</span>'
                                    }
                                </td>
                            </tr>
                        `).join('');
                        }

                        $('#resultsHead').html(headHtml);


                        if (!rows) {
                            $tbody.html(
                                `<tr><td colspan="7" class="text-center py-4 text-muted">No availability found.</td></tr>`
                            );
                        } else {
                            $tbody.html(rows);
                        }


                        initTooltips();

                        // if (!cart.customer_id) {
                        //     $('#selectCustomerHint').removeClass('d-none');
                        // }
                    })
                    .fail(xhr => {
                        if (xhr && xhr.statusText === 'abort') {
                            return;
                        }
                        $tbody.html(
                            `<tr><td colspan="7" class="text-danger text-center py-4">${xhr.responseJSON?.message || 'Search failed.'}</td></tr>`
                        );
                    })
                    .always(() => {
                        setLoading(false);
                        _inFlightAvailability = null;
                    });
            }

            let selectedBtn = null; // track which button was clicked

            $(document).on('click', '.addToCartBtn', function() {
                selectedBtn = $(this); // store clicked button
                $('#addOccupantsModal').modal('show'); // show modal
            });

            $('#btnConfirmOccupants').on('click', async function() {
                if (!selectedBtn) return;

                const adults = parseInt($('#adultsInput').val());
                const children = parseInt($('#childrenInput').val());

                $('#addOccupantsModal').modal('hide');
                selectedBtn.prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...'
                    );

                try {
                    let cartId = sessionStorage.getItem('cart_id');
                    let cartToken = sessionStorage.getItem('cart_token');

                    if (!cartId || !cartToken) {
                        const cartRes = await $.ajax({
                            url: routes.cartAdd,
                            method: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({})
                        });

                        cartId = cartRes.data.cart_id;
                        cartToken = cartRes.data.cart_token;
                        sessionStorage.setItem('cart_id', cartId);
                        sessionStorage.setItem('cart_token', cartToken);
                    }

                    const payload = {
                        cart_id: parseInt(cartId),
                        token: cartToken,
                        site_id: selectedBtn.data('site-id'),
                        start_date: selectedBtn.data('start'),
                        end_date: selectedBtn.data('end'),
                        occupants: {
                            adults,
                            children
                        },
                        add_ons: [],
                        price_quote_id: selectedBtn.data('price-quote-id') || null,
                    };

                    const itemRes = await $.ajax({
                        url: routes.cartItems,
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(payload)
                    });

                    if (itemRes) {
                        updateCartSidebar(itemRes.cart);
                        selectedBtn.html('<i class="bi bi-check-lg"></i> Added');
                    } else {
                        console.error(itemRes);
                        selectedBtn.html('Error');
                    }

                } catch (err) {
                    console.error('Error adding to cart', err);
                    selectedBtn.html('Retry');
                } finally {
                    selectedBtn.prop('disabled', false);
                    selectedBtn = null;
                }
            });


            function updateCartSidebar(cartData) {
                const body = $('#cartBody');
                const count = $('#cartCount');

                if (!cartData || !cartData.items || !cartData.items.length === 0) {
                    body.html('<p class="text-muted mb-0">No items yet.</p>');
                    count.text(0);
                    $('#btnCheckout').prop('disabled', true);
                    return
                }

                const itemsHtml = cartData.items.map(item => `
                    <div class="border-bottom pb-2 mb-2">
                        <div><strong>${item.site_id}</strong></div>
                        <div class="text-muted small">
                            ${item.start_date} → ${item.end_date}
                        </div>
                        <div class="text-end small">
                            $${item.price?.subtotal ?? 0}
                        </div>
                    </div>
                `).join('');

                body.html(itemsHtml);
                count.text(cartData.items.length);
                $('#btnCheckout').prop('disabled', !cartData.customer_id);

            }

            window.__availabilityTrigger = debounce(() => {
                const ci = $('[name="start_date"]').val();
                const co = $('[name="end_date"]').val();
                if (!ci || !co) return;

                runAvailabilitySearch();
            }, 300);

            // $('#dateRange').on('change', window.__availabilityTrigger);
            $('[name="riglength"], [name="siteclass"], [name="hookup"], [name="view"], [name="pets_ok"], [name="include_offline"]')
                .on('change', window.__availabilityTrigger);


            $form.off('submit.avail').on('submit.avail', function(e) {
                e.preventDefault();
                runAvailabilitySearch();
            });

            $form.find('[name="rig_length"]').off('input.avail').on('input.avail', debounce(() =>
                runAvailabilitySearch(), 450));
            $form.find('[name="site_id"]').off('change.avail').on('change.avail', debounce(() =>
                runAvailabilitySearch(), 250));
            $form.find('[name="include_offline"]').off('change.avail').on('change.avail', debounce(() =>
                runAvailabilitySearch(), 250));

            setTimeout(() => {
                const ci = $form.find('[name="start_date"]').val();
                const co = $form.find('[name="end_date"]').val();
                const rl = $form.find('[name="rig_length"]').val();
                const sid = $form.find('[name="site_id"]').val();
                if ((ci && co) || rl || sid) runAvailabilitySearch();
            }, 200);

            let cartToken = null;

            $('#resultsTable').on('click', '.btnAdd', function() {
                if (!cart.customer_id) {
                    $('#selectCustomerHint').removeClass('d-none');
                    $('#customerModal').modal('show');
                    return;
                }

                const $tr = $(this).closest('tr');
                const item = JSON.parse($tr.attr('data-json'));

                const checkin = $form.find('[name="start_date"]').val();
                const checkout = $form.find('[name="end_date"]').val();

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

                $input.off('input.customer').on('input.customer', schedule);

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
                if (cart.customer_id) {
                    $nameCust.removeClass('d-none').text(`For: ${cart.customer_name || '(selected)'}`);
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


            $('#hintSelectCustomer').on('click', () => $('#customerModal').modal('show'));

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
                        const message = res?.message || 'Order placed successfully.';
                        let timerInterval;

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            html: `
                    <p>${message}</p>
                    <p class="small text-muted">Page will refresh in <b></b> seconds.</p>
                `,
                            timer: 4000, // auto close after 4s
                            timerProgressBar: true,
                            showCancelButton: true,
                            confirmButtonText: 'Reload now',
                            cancelButtonText: 'Stay here',
                            didOpen: () => {
                                const b = Swal.getPopup().querySelector('b');
                                let timeLeft = 4;
                                b.textContent = timeLeft;
                                timerInterval = setInterval(() => {
                                    timeLeft--;
                                    b.textContent = timeLeft;
                                }, 1000);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // user clicked Reload now
                            } else if (result.dismiss === Swal.DismissReason.timer) {
                                location.reload(); // auto reload after timer
                            }
                        });
                    })
                    .fail(xhr => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Checkout failed',
                            text: xhr.responseJSON?.message || 'Something went wrong.'
                        });
                    });
            }

        })();
    </script>
@endpush
