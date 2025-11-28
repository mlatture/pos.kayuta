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

        .site-details-row td {
            background-color: #f9fafb !important;
            border-top: 0;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .viewSiteDetails:hover {
            background-color: #f1f5f9;
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
                        <label class="form-label">Dates <span class="badge rounded-pill bg-secondary d-none"
                                id="nightsBadge">0 nights</span>
                        </label>
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
                        <input type="number" class="form-control" name="rig_length" min="0" max="100"
                            placeholder="e.g. 32" inputmode="numeric" pattern="[0-9]*">
                        <div class="form-text">Total Rig Length, tip-to-tip. Filters to sites that fit. Please Enter After
                            Typing</div>
                    </div>

                    {{-- Site class --}}
                    <div class="col-2 col-mb-2">
                        <label class="form-label">Site Class</label>
                        <select class="form-select" name="siteclass">
                            @foreach ($siteClasses as $classes)
                                <option value="{{ $classes->siteclass }}"
                                    {{ $classes->siteclass == 'RV Sites' ? 'selected' : '' }}>
                                    {{ $classes->siteclass }}
                                </option>
                            @endforeach

                        </select>
                        <div class="form-text">Select Type of site to filter</div>
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
                        <div class="form-text">Utilities Needed</div>
                    </div>

                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-primary position-relative" id="btnViewMap">
                            View Map
                        </button>
                        <div class="form-text">Please select a date first before opening the map.</div>
                    </div>


                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="include_reserved" name="include_reserved">
                            <div class="form-text">Include Reserved</div>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="form-check position-relative">
                            <input class="form-check-input" type="checkbox" id="include_seasonal" name="include_seasonal"
                                >
                            <div class="form-text">
                                Include Seasonal
                             
                            </div>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="form-check position-relative">
                            <input class="form-check-input" type="checkbox" id="include_offline" name="include_offline"
                                >
                            <div class="form-text">
                                Include Offline

                            </div>
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
                    </div>

                    <p class="small text-muted mx-3 mt-2 mb-0">
                        Tip: Click any site row to view more details and amenities.
                    </p>

                    <div class="card-body p-0" style="height: 50vh; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="resultsTable"
                            style="width: 100%; border-collapse: separate; border-spacing: 0;">
                            <thead class="table-light" id="resultsHead"
                                style="position: sticky; top: 0; z-index: 10; background: #f8f9fa;">

                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Search to see availability…
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>

            {{-- Cart --}}
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Cart</strong>
                        <span class="badge bg-secondary" id="cartCount">0</span>
                    </div>

                    <div class="card-body" id="cartBody">
                        <p class="text-muted mb-0">No items yet.</p>
                    </div>

                    {{-- Inline Customer Section --}}
                    <div class="border-top p-3 bg-light" id="customerSection">
                        <h6 class="mb-2">Select / Create Customer</h6>

                        {{-- Selected Customer Display --}}
                        <div id="selectedCustomer" class="d-none mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong id="selName"></strong><br>
                                    <small class="text-muted" id="selEmail"></small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" id="btnChangeCustomer">Change</button>
                            </div>
                        </div>

                        {{-- Search + Create Section --}}
                        <div id="customerForm">
                            <input type="text" class="form-control mb-2" id="customerSearch"
                                placeholder="Search name, email, or phone…">
                            <div id="customerSearchResults" class="list-group mt-2"></div>

                            <div class="list-group mb-3" id="customerResults"></div>

                            <div class="border-top pt-2">
                                <h6 class="small text-muted mb-2">Create new customer</h6>
                                <div class="row g-2">
                                    <div class="col-12 col-md-4"><input type="text" class="form-control"
                                            id="newName" placeholder="Full name"></div>
                                    <div class="col-12 col-md-4"><input type="email" class="form-control"
                                            id="newEmail" placeholder="Email"></div>
                                    <div class="col-12 col-md-4"><input type="text" class="form-control"
                                            id="newPhone" placeholder="Phone"></div>
                                    <div class="col-12 col-md-4"><input type="text" class="form-control"
                                            id="newStreetAdd" placeholder="Street Address"></div>
                                    <div class="col-12 col-md-4"><input type="text" class="form-control"
                                            id="newCity" placeholder="City"></div>
                                    <div class="col-12 col-md-4"><input type="text" class="form-control"
                                            id="newState" placeholder="State"></div>
                                    <div class="col-12 col-md-4"><input type="text" class="form-control"
                                            id="newZip" placeholder="Zip"></div>
                                </div>
                                <button class="btn btn-sm btn-primary mt-2 w-100" id="btnCreateCustomer">Save &
                                    Select</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <div class="position-relative d-inline-block w-100">
                            <button class="btn btn-success w-100 mt-2" id="btnCheckout" disabled>
                                Proceed to Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    {{-- Modals --}}
    @include('reservations.modals.checkout')


@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const checkin = document.getElementById('checkinHidden').value;
        const checkout = document.getElementById('checkoutHidden').value;
        const $btnViewMap = $('#btnViewMap');


        if (!document.querySelector('#dateRange')._flatpickr) {
            flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "m/d/Y",
                allowInput: true,
                defaultDate: checkin && checkout ? [checkin, checkout] : null,
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates && selectedDates.length === 2) {
                        const d1 = selectedDates[0];
                        const d2 = selectedDates[1];


                        document.getElementById('checkinHidden').value = instance.formatDate(d1, instance.config
                            .dateFormat);
                        document.getElementById('checkoutHidden').value = instance.formatDate(d2, instance
                            .config.dateFormat);


                        // document.getElementById('dateRange').value =
                        //     `${instance.formatDate(d1, instance.config.dateFormat)} — ${instance.formatDate(d2, instance.config.dateFormat)}`;



                        if (window.__availabilityTrigger) window.__availabilityTrigger();
                    }
                }
            });
        }


        function updateViewMapButton() {
            const checkin = $('#checkinHidden').val();
            const checkout = $('#checkoutHidden').val();

            // $btnViewMap.prop('disabled', !(checkin && checkout));
        }

        // Initial check
        updateViewMapButton();
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function initTooltips() {
            $('[data-bs-toggle="tooltip"]').tooltip('dispose');

            // Initialize all tooltips again
            $('[data-bs-toggle="tooltip"]').tooltip({
                trigger: 'hover',
                placement: 'top',
                html: true
            });
        }


        (function() {
           

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

            const $checkoutModal = new bootstrap.Modal('#checkoutModal');

            const fmt = n => new Intl.NumberFormat(undefined, {
                style: 'currency',
                currency: 'USD'
            }).format(n || 0);


            // Click handler
            $btnViewMap.on('click', function(e) {
                const checkin = $('#checkinHidden').val();
                const checkout = $('#checkoutHidden').val();
                const siteclass = $form.find('[name="siteclass"]').val();
                const riglength = $form.find('[name="rig_length"]').val();
                const hookup = $form.find('[name="hookup"]').val();

                if (!checkin || !checkout) {
                    e.preventDefault();
                    alert('Please select a start and end date first.');
                    return false;
                }

                const url =
                    `${routes.viewMap}?start_date=${encodeURIComponent(checkin)}&end_date=${encodeURIComponent(checkout)}&siteclass=${encodeURIComponent(siteclass)}&riglength=${encodeURIComponent(riglength)}&hookup=${encodeURIComponent(hookup)}`;
                window.location.href = url;
            });


            $('#btnRefresh').on('click', () => location.reload());


            let _inFlightAvailability = null;

            function runAvailabilitySearch() {

                let ci = $form.find('[name="start_date"]').val();
                let co = $form.find('[name="end_date"]').val();

                const fp = document.querySelector('#dateRange')._flatpickr;
                if ((!ci || !co) && fp && fp.selectedDates.length === 2) {
                    const [d1, d2] = fp.selectedDates;

                    ci = fp.formatDate(d1, fp.config.dateFormat);
                    co = fp.formatDate(d2, fp.config.dateFormat);

                    $form.find('[name="start_date"]').val(ci);
                    $form.find('[name="end_date"]').val(co);
                }

                const formData = $form.serializeArray();

                formData.push({
                    name: 'include_reserved',
                    value: $('#include_reserved').prop('checked') ? 1 : 0
                });
                formData.push({
                    name: 'include_offline',
                    value: $('#include_offline').prop('checked') ? 1 : 0
                });
                formData.push({
                    name: 'include_seasonal',
                    value: $('#include_seasonal').prop('checked') ? 1 : 0
                });


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

                // setLoading(true);
                $tbody.html(`<tr><td colspan="7" class="text-center py-4">Searching…</td></tr>`);


                _inFlightAvailability = $.get(routes.availability, $.param(formData))
                    .done(res => {
                        const $badge = $('#nightsBadge');
                        const data = res?.data.response || {};
                        const results = data.results || {};
                        const slFAmount = res?.site_lock_fee || " ";



                        let nights = 1;
                        const ci = $('[name=start_date]').val();
                        const co = $('[name=end_date]').val();


                        if (ci && co) {
                            const d1 = new Date(ci);
                            const d2 = new Date(co);
                            nights = Math.max(1, Math.floor((d2 - d1) / 86400000));
                        }
                        $badge.text(`${nights} ${nights === 1 ? 'night' : 'nights'}`).removeClass('d-none');

                        let headHtml = '';
                        let rows = '';
                        const siteClass = $('[name="siteclass"]').val();



                        if (data.view === 'units' && Array.isArray(results?.units)) {
                            headHtml = `
                            <tr >
                                <th class="sticky-header">Site ID</th>
                                <th class="sticky-header">Name</th>
                                <th class="sticky-header">Class</th>
                                <th class="sticky-header col-hookup ${siteClass !== 'RV Sites' ? 'd-none' : ' '}">Hookup</th>
                                <th class="sticky-header col-maxlength text-center ${siteClass !== 'RV Sites' ? 'd-none' : ' '}">Min Length</th>
                                <th class="sticky-header col-maxlength text-center ${siteClass !== 'RV Sites' ? 'd-none' : ' '}">Max Length</th>
                                <th class="sticky-header">Status</th>
                                <th class="sticky-header">Price Quote</th>
                                <th class="sticky-header">Actions</th>
                            </tr>
                        `;




                            rows = results.units
                                .map(unit => {
                                    const isAvailable = unit?.status?.available;
                                    const statusBadge = isAvailable ?
                                        '<span class="badge bg-success">Available</span>' :
                                        unit?.status?.reserved ?
                                        '<span class="badge bg-warning text-dark">Reserved</span>' :
                                        unit?.status?.in_cart ?
                                        '<span class="badge bg-info text-dark">In Cart</span>' :
                                        '<span class="badge bg-secondary">Unavailable</span>';

                                    let priceHtml = '';

                                    if ((isAvailable || unit?.status?.offline) && unit?.price_quote) {
                                        priceHtml = `
                                            <div class="small">
                                                <div><strong>Total:</strong> $${Number(unit.price_quote.total ?? 0).toFixed(2)}</div>
                                                <div><strong>Avg/Night:</strong> $${Number(unit.price_quote.avg_nightly ?? 0).toFixed(2)}</div>

                                            </div>
                                        `;

                                    } else {
                                        priceHtml = '<span class="text-muted small">—</span>';

                                    }


                                    const addToCartHtml = isAvailable ? `
                                        <div class="mt-2">
                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                <div class="text-center">
                                                    <label class="small text-muted d-block mb-1">Adults</label>
                                                    <input type="number" class="form-control form-control-sm occupants-input adults"
                                                        value="2" min="0" style="width:70px;">
                                                </div>
                                                <div class="text-center">
                                                    <label class="small text-muted d-block mb-1">Children</label>
                                                    <input type="number" class="form-control form-control-sm occupants-input children"
                                                        value="0" min="0" style="width:80px;">
                                                </div>

                                                
                                            </div>

                                            <div class="form-check mb-2">
                                                <input class="form-check-input siteLockFee" type="checkbox" checked id="siteLockFee_${unit.site_id}">
                                                <label class="form-check-label small text-muted" for="siteLockFee_${unit.site_id}">
                                                    Site Lock Fee: $${slFAmount}
                                                </label>
                                            </div>

                                            <button class="btn btn-sm btn-primary addToCartBtn"
                                                data-site-id="${unit.site_id}"
                                                data-total="${unit.price_quote?.total ?? 0}"
                                                data-price-quote-id="${unit.price_quote?.price_quote_id ?? ''}"
                                                data-start="${ci}"
                                                data-end="${co}"
                                                data-site-lock-fee="0" >
                                                <i class="bi bi-cart-plus"></i> Add to Cart
                                            </button>
                                        </div>
                                    ` : '';


                                    return `
                                    <tr class="viewSiteDetails" data-site-id="${unit?.site_id}" data-start="${ci}" data-end="${co}"
                                    style="cursor: pointer;"
                                    >
                                        <td><strong>${unit?.site_id ?? ''}</strong></td>
                                        <td>${unit?.name ?? ''}</td>
                                        <td>${unit?.class ? unit.class.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : ''}</td>
                                        <td class="col-hookup ${siteClass !== 'RV Sites' ? 'd-none' : ' '}">${unit?.hookup ?? ''}</td>
                                        <td class="col-maxlength text-center ${siteClass !== 'RV Sites' ? 'd-none' : ' '}">${unit?.minlength ?? ''}</td>
                                        <td class="col-maxlength text-center ${siteClass !== 'RV Sites' ? 'd-none' : ' '}">${unit?.maxlength ?? ''}</td>
                                        <td>${statusBadge}</td>
                                        <td class="text-end">
                                            ${priceHtml}

                                        </td>
                                        <td data-role="add-wrap">
                                            ${addToCartHtml}
                                        </td>
                                    </tr>`;
                                }).join('');


                        }

                        $('#resultsHead').html(headHtml);


                        if (!rows) {
                            $tbody.html(
                                `<tr><td colspan="7" class="text-center py-4 text-muted">No availability found.</td></tr>`
                            );
                        } else {
                            $tbody.html(rows);


                            $tbody.off('click', '.viewSiteDetails').on('click', '.viewSiteDetails', function(e) {
                                if ($(e.target).closest('button, input, .form-check-input').length) return;

                                const siteId = $(this).data('site-id');
                                const start = $(this).data('start');
                                const end = $(this).data('end');
                                const $row = $(this);

                                const $existing = $(`#siteDetails_${siteId}`);
                                if ($existing.length) {
                                    $existing.find('.collapse-content').slideToggle(200);
                                    return;
                                }

                                $tbody.find('.site-details-row').remove();

                                const colspan = $row.find('td').length;
                                const $detailsRow = $(`
                                    <tr class="site-details-row" id="siteDetails_${siteId}">
                                        <td colspan="${colspan}" class="bg-light">
                                            <div class="collapse-content small text-muted p-4 text-center">
                                                <i class="bi bi-hourglass-split"></i> Loading site details...
                                            </div>
                                        </td>
                                    </tr>
                                `);

                                $row.after($detailsRow);

                                $.get(routes.viewSiteDetails, {
                                        site_id: siteId,
                                        uscid: start,
                                        uscod: end
                                    })
                                    .done(res => {
                                        const site = res.response?.site || {};
                                        const constraints = res.response?.constraints || {};
                                        const policies = res.response?.policies || {};
                                        const pricing = res.response?.pricing || {};
                                        const status = res.response?.status || {};
                                        const amenities = site.amenities || [];

                                        const availabilityBadge = status.available ?
                                            '<span class="badge bg-success">Available</span>' :
                                            status.in_cart ?
                                            '<span class="badge bg-info text-dark">In Cart</span>' :
                                            status.reserved ?
                                            '<span class="badge bg-warning text-dark">Reserved</span>' :
                                            '<span class="badge bg-secondary">Unavailable</span>';

                                        const amenitiesHtml = amenities.length ?
                                            `<div class="d-flex flex-wrap gap-2 mt-2">
                                                    ${amenities
                                                        .map(a => `<span class="badge rounded-pill bg-success mb-1">
                                                                                                                                                                                                                                                                                                                                            <i class="bi bi-check-circle-fill me-1"></i>${a.replace(/_/g, ' ')}
                                                                                                                                                                                                                                                                                                                                        </span>`)
                                                        .join('')}
                                            </div>` :
                                            `<div class="text-muted small">No listed amenities.</div>`;

                                        const lockHtml = policies.site_lock?.enabled ?
                                            `<div class="alert alert-warning small mt-3 mb-0">
                                                    <i class="bi bi-lock-fill me-1"></i>
                                                    <strong>Site Lock Fee:</strong> $${policies.site_lock.fee}<br>
                                                    ${policies.site_lock.message.replace(/\n/g, '<br>')}
                                            </div>` :
                                            '';

                                        const pricingHtml = `
                                            <div class="mt-3">
                                                <h6 class="fw-bold mb-2">Pricing Summary</h6>
                                                <div class="row small">
                                                    <div class="col-md-4"><strong>Stay:</strong> ${pricing.range?.length_of_stay || 1} night(s)</div>
                                                    <div class="col-md-4"><strong>Avg/Night:</strong> $${Number(pricing.average_nightly ?? 0).toFixed(2)}</div>
                                                    <div class="col-md-4"><strong>Total:</strong> $${Number(pricing.total ?? 0).toFixed(2)}</div>
                                                </div>
                                                <div class="text-muted mt-2 fst-italic">${pricing.notes || ''}</div>
                                            </div>
                                        `;

                                        const content = `
                                            <div class="collapse-content">
                                               

                                                <p class="small text-muted mb-3">${site.attributes || ''}</p>

                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <div><strong>Site ID:</strong> ${site.site_id}</div>
                                                        <div><strong>Class:</strong> ${site.class || 'N/A'}</div>
                                                        <div><strong>Hookup:</strong> ${site.hookup || 'N/A'}</div>
                                                        <div><strong>Rig Length:</strong> ${constraints.rig_length?.min ?? 0}–${constraints.rig_length?.max ?? 0} ft</div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <strong>Amenities:</strong>
                                                        ${amenitiesHtml}
                                                    </div>
                                                </div>

                                                ${lockHtml}
                                                ${pricingHtml}

                                                <div class="text-muted small fst-italic mt-3">
                                                    <i class="bi bi-info-circle me-1"></i>Note: Site availability and policies may vary by season.
                                                </div>
                                            </div>
                                        `;

                                        $detailsRow.find('td').html(content);
                                        $detailsRow.find('.collapse-content').hide().slideDown(250);
                                        initTooltips();
                                    })
                                    .fail(() => {
                                        $detailsRow.find('td').html(
                                            `<div class="text-danger py-3">Failed to load details for site ${siteId}.</div>`
                                        );
                                    });
                            });


                        }


                        initTooltips();


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
                        // setLoading(false);
                        _inFlightAvailability = null;
                    });
            }





            $(window).on('load', async function() {
                console.log('📦 Document ready triggered');

                const storedCart = JSON.parse(localStorage.getItem('cartInfo') || '{}');

                if (storedCart.cart_id && storedCart.cart_token && storedCart.expires_at) {
                    const expiresAt = new Date(storedCart.expires_at);
                    const now = new Date();

                    if (now < expiresAt) {
                        console.log('🕒 Restoring existing cart from localStorage:', storedCart);
                        await updateCartSidebar(storedCart.cart_id, storedCart.cart_token);
                    } else {
                        console.log('🗑️ Cart expired, clearing localStorage');
                        localStorage.removeItem('cartInfo');
                    }
                }
            });



            async function createOrRestoreCart() {
                let cartId, cartToken;

                // Check existing cart first
                const stored = JSON.parse(localStorage.getItem('cartInfo') || '{}');
                const now = new Date();

                if (stored.cart_id && stored.cart_token && new Date(stored.expires_at) > now) {
                    console.log('♻️ Using existing cart:', stored);
                    cartId = stored.cart_id;
                    cartToken = stored.cart_token;
                } else {
                    console.log('🆕 Creating new cart...');
                    const cartRes = await $.ajax({
                        url: routes.cartAdd,
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({})
                    });

                    const data = cartRes.data;

                    cartId = data.cart_id;
                    cartToken = data.cart_token;
                    // Compute expiration datetime
                    const expiresAt = new Date();
                    expiresAt.setSeconds(expiresAt.getSeconds() + (cartRes.meta?.ttl_seconds || 1800));

                    // Save to localStorage
                    localStorage.setItem('cartInfo', JSON.stringify({
                        cart_id: cartId,
                        cart_token: cartToken,
                        expires_at: expiresAt.toISOString(),
                    }));
                }

                return {
                    cartId,
                    cartToken
                };
            }

            $(document).on('click', '.addToCartBtn', async function() {
                const btn = $(this);
                const container = btn.closest('div');
                const adults = parseInt(container.find('.adults').val()) || 0;
                const children = parseInt(container.find('.children').val()) || 0;
                const siteLockFee = container.find('.siteLockFee').is(':checked') ? 'on' : 'off';

                if (adults + children === 0) {
                    alert('Please enter at least one occupant.');
                    return;
                }

                btn.prop('disabled', true).html(
                    '<i class="fa-solid fa-spinner fa-spin-pulse"></i> Adding...');

                try {
                    //  Create or restore shared cart
                    const {
                        cartId,
                        cartToken
                    } = await createOrRestoreCart();

                    const payload = {
                        cart_id: parseInt(cartId),
                        token: cartToken,
                        site_id: btn.data('site-id'),
                        start_date: btn.data('start'),
                        end_date: btn.data('end'),
                        occupants: {
                            adults,
                            children
                        },
                        add_ons: [],
                        price_quote_id: btn.data('price-quote-id') || null,
                        site_lock_fee: siteLockFee
                    };

                    // Add item
                    const itemRes = await $.ajax({
                        url: routes.cartItems,
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(payload)
                    });

                    if (itemRes) {
                        await updateCartSidebar(cartId, cartToken);
                        btn.html('<i class="fa-solid fa-check" style="color: #63E6BE;"></i> Added');
                    } else {
                        btn.html('Error');
                    }
                } catch (err) {
                    console.error('Error adding to cart', err);
                    btn.html('Retry');
                } finally {
                    btn.prop('disabled', false);
                }
            });





            async function updateCartSidebar(cartId, cartToken) {
                try {
                    // Empty cart fallback
                    if (!cartId || !cartToken) {
                        btnCheckout.prop('disabled', true);
                        body.html('<p class="text-muted mb-0">No items yet.</p>');
                        count.text(0);
                        return;
                    }

                    const res = await $.ajax({
                        url: routes.getCart,
                        method: 'GET',
                        data: {
                            cart_token: cartToken,
                            cart_id: cartId
                        }
                    });


                    const cart = res.data?.cart;
                    const body = $('#cartBody');
                    const count = $('#cartCount');
                    const btnCheckout = $('#btnCheckout');

                    console.log('Cart Check', cart);

                    // Build cart items dynamically
                    let itemsHtml = '';
                    let totalSubtotal = 0;
                    let totalLockFee = 0;
                    let totalGrand = 0;

                    cart.items.forEach(item => {
                        const site = item.site || {};
                        const subtotal = item.price_snapshot?.subtotal || 0;
                        const sitelockFee = item.price_snapshot?.sitelock_fee || 0;
                        const total = item.price_snapshot?.total || 0;

                        totalSubtotal += subtotal;
                        totalLockFee += sitelockFee;
                        totalGrand += total;

                        itemsHtml += `
                            <div class="cart-item mb-2 p-2 border rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>${site.name || item.site_id}</strong><br>
                                        <small>${site.hookup || ''}</small><br>
                                        <small>${item.start_date} → ${item.end_date} (${item.nights} nights)</small><br>
                                        <small>${item.occupants?.adults || 0} adults, ${item.occupants?.children || 0} children</small>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-link text-danger p-0 remove-item-btn"
                                            data-cart-id="${cartId}"
                                            data-cart-token="${cartToken}"
                                            data-cart-item-id="${item.id}"

                                            title="Remove">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <div>Base: $${subtotal.toFixed(2)}</div>
                                        ${sitelockFee > 0 ? `<div>Site Lock: $${sitelockFee.toFixed(2)}</div>` : ''}
                                        <strong>Total: $${total.toFixed(2)}</strong>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    // Add a summary section at the end
                    itemsHtml += `
                        <hr>
                        <div class="cart-summary text-end">
                            <div><strong>Subtotal:</strong> $${totalSubtotal.toFixed(2)}</div>
                            ${totalLockFee > 0 ? `<div><strong>Site Lock Fees:</strong> $${totalLockFee.toFixed(2)}</div>` : ''}
                            <div class="fs-5"><strong>Grand Total:</strong> $${totalGrand.toFixed(2)}</div>
                            <input type="hidden" value="${totalGrand}" id="grandTotal">
                        </div>
                    `;

                    // Render to sidebar
                    body.html(itemsHtml);
                    count.text(cart.items.length);
                    btnCheckout.prop('disabled', false);

                } catch (err) {
                    console.error('❌ Error fetching cart:', err);
                    $('#cartBody').html('<p class="text-danger mb-0">Error loading cart.</p>');
                }
            }

            $(document).on('click', '.remove-item-btn', async function() {
                const btn = $(this);
                const cartId = btn.data('cart-id');
                const token = btn.data('cart-token');
                const cartItemId = btn.data('cart-item-id');

                const originalHtml = btn.html();
                btn.prop('disabled', true)
                    .html(
                        '<span class="opacity-75"><i class="fa-solid fa-spinner fa-spin-pulse"></i></span>')
                    .fadeTo(200, 0.6, 'linear');


                try {
                    const deleteCartItem = await $.ajax({
                        url: routes.cartItemRemove,
                        method: 'DELETE',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            cart_id: cartId,
                            token: token,
                            cart_item_id: cartItemId
                        }),
                    });

                    console.log('🗑️ Delete response:', deleteCartItem);

                    // Success condition (depends on API return)
                    if (deleteCartItem.code === "ITEM_REMOVED") {
                        await updateCartSidebar(cartId, token);


                        toastr.success('Item removed from cart.');
                    } else {
                        toastr.error('Failed to remove item from cart. Please try again!');
                    }
                } catch (err) {
                    console.error('Error removing cart item:', err);
                    toastr.error('Something went wrong while removing the item.');
                } finally {
                    btn.prop('disabled', false).html(originalHtml).fadeTo(200, 1);
                }
            });

            $btnCheckout.on('click', async function() {
                const btn = $(this);


                if (!window.selectCustomerForCart || !window.selectCustomerForCart.details) {
                    toastr.warning('Please select customer');
                    return;
                }

                const customerInfo = window.selectCustomerForCart.details;
                console.log('check', customerInfo);


                btn.prop('disabled', true).html(
                    '<i class="fa-solid fa-spinner fa-spin-pulse"></i>');
                const storedCart = JSON.parse(localStorage.getItem('cartInfo') || '{}');



                try {
                    // Optional: refresh latest data from API
                    const res = await $.ajax({
                        url: routes.getCart,
                        method: 'GET',
                        data: {
                            cart_id: storedCart.cart_id,
                            cart_token: storedCart.cart_token
                        }
                    });

                    const cart = res.data?.cart;
                    if (!cart || !cart.items?.length) {
                        $('#cartItemsList').html('<p class="text-muted mb-0">No items in cart.</p>');
                        $('#checkoutModal').modal('show');
                        return;
                    }

                    // Calculate totals
                    let totalSubtotal = 0;
                    let totalLockFee = 0;
                    let totalDiscounts = 0;
                    let totalTax = 0;
                    let totalGrand = 0;

                    cart.items.forEach(item => {
                        const snapshot = item.price_snapshot || {};
                        totalSubtotal += snapshot.subtotal || 0;
                        totalLockFee += snapshot.sitelock_fee || 0;
                        totalDiscounts += snapshot.discounts || 0;
                        totalTax += snapshot.tax || 0;
                        totalGrand += snapshot.total || 0;
                    });

                    // Update totals in modal
                    $('#tSubtotal').text(fmt(totalSubtotal));
                    $('#tDiscounts').text('-' + fmt(totalDiscounts));
                    $('#tTax').text(fmt(totalTax));
                    $('#tSiteLock').text(fmt(totalLockFee));
                    $('#tTotal').text(fmt(totalGrand));



                    // Build items display
                    const itemsHtml = cart.items.map(item => {
                        const site = item.site || {};
                        const snapshot = item.price_snapshot || {};
                        const subtotal = snapshot.subtotal || 0;
                        const sitelockFee = snapshot.sitelock_fee || 0;

                        return `
                            <div class="border rounded p-2 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <strong>${site.name || item.site_id}</strong><br>
                                        <small>${site.hookup || ''}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-${sitelockFee > 0 ? 'success' : 'secondary'}">
                                            ${sitelockFee > 0
                                                ? '<i class="fa-solid fa-lock"></i> Locked'
                                                : '<i class="fa-solid fa-lock-open"></i> Unlocked'}
                                        </span>
                                    </div>
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Dates:</strong> ${item.start_date} → ${item.end_date} (${item.nights} nights)
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Occupants:</strong> ${item.occupants?.adults ?? 0} Adults, ${item.occupants?.children ?? 0} Children
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Nights:</strong> ${snapshot.nights ?? 0}
                                </div>
                                <div class="text-end">
                                    <strong>Subtotal:</strong> $${subtotal.toFixed(2)}<br>
                                    ${sitelockFee > 0 ? `<small>Site Lock Fee: $${sitelockFee.toFixed(2)}</small><br>` : ''}
                                    <strong>Total:</strong> $${(snapshot.total || 0).toFixed(2)}
                                </div>
                            </div>
                        `;
                    }).join('');

                    $('#cartItemsList').html(itemsHtml);

                    // Show expiration warning if available
                    if (cart.expires_at) {
                        const expDate = new Date(cart.expires_at);
                        const formatted = expDate.toLocaleString();
                        $('#cartItemsList').prepend(
                            `<div class="alert alert-warning py-1 small mb-3">
                                Cart expires at <strong>${formatted}</strong>
                            </div>`
                        );
                    }

                    // Prefill Customer info
                    $('#custFname').val(customerInfo.f_name || '');
                    $('#custLname').val(customerInfo.l_name || '');
                    $('#custEmail').val(customerInfo.email || '');
                    $('#custPhone').val(customerInfo.phone || '');
                    $('#custStreet').val(customerInfo.street_address || '');
                    $('#custCity').val(customerInfo.city || '');
                    $('#custState').val(customerInfo.state || '');
                    $('#custZip').val(customerInfo.zip || '');
                    $('#custId').val(customerInfo.id || '');


                    // Finally, open modal
                    $('#checkoutModal').modal('show');

                } catch (err) {
                    console.error('❌ Error opening checkout modal:', err);
                    toastr.error('Something went wrong while loading your cart.');
                } finally {
                    btn.prop('disabled', false);
                }
            });





            window.__availabilityTrigger = debounce(() => {
                const ci = $('[name="start_date"]').val();
                const co = $('[name="end_date"]').val();
                const selectedClass = $('[name="siteclass"]').val();

                console.log('Availability trigger fired. CI:', ci, 'CO:', co, 'Class:', selectedClass);

                if (!ci || !co) return;

                if (selectedClass !== 'RV Sites') {
                    $('[name="rig_length"]').val('');
                }

                runAvailabilitySearch();
            }, 500); // Slightly longer delay helps prevent rapid re-triggers

            // Only bind change events, not input
            $('[name="start_date"], [name="end_date"], [name="rig_length"], [name="siteclass"], [name="hookup"], [name="include_seasonal"], [name="include_offline"], [name="include_reserved"]')
                .on('change', window.__availabilityTrigger);


            if ($('[name="siteclass"]').val() !== 'RV Sites') hideRVColumns(true);




            // Handle form submission
            $form.off('submit.avail').on('submit.avail', function(e) {
                e.preventDefault();
                runAvailabilitySearch();
            });


            $form.find('[name="rig_length"]').on('input', function() {
                const val = $(this).val().trim();
                const $siteClass = $('[name="siteclass"]');

                if (val.length > 0 && $siteClass.val() !== 'RV Sites') {
                    $siteClass.val('RV Sites');
                    $siteClass.trigger('change');
                }
            });




            $form.find('[name="site_id"]').off('change.avail').on('change.avail', debounce(() =>
                runAvailabilitySearch(), 250));


            setTimeout(() => {
                const ci = $form.find('[name="start_date"]').val();
                const co = $form.find('[name="end_date"]').val();
                const rl = $form.find('[name="rig_length"]').val();
                const sid = $form.find('[name="site_id"]').val();
                if ((ci && co) || rl || sid) runAvailabilitySearch();
            }, 200);

            let cartToken = null;



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

            $('#btnCheckCancel').on('click', function() {
                const checkoutBtn = $('#btnCheckout');

                checkoutBtn.text('Proceed To Checkout');
            });

            $('#btnPlaceOrder').on('click', function() {
                const method = $('#paymentInputs').data('method') || 'credit_card';
                const stored = JSON.parse(localStorage.getItem('cartInfo') || '{}');

                const customer = {
                    fname: $('#custFname').val().trim(),
                    lname: $('#custLname').val().trim(),
                    email: $('#custEmail').val().trim(),
                    phone: $('#custPhone').val().trim(),
                    street_address: $('#custStreet').val().trim(),
                    city: $('#custCity').val().trim(),
                    state: $('#custState').val().trim(),
                    zip: $('#custZip').val().trim(),
                    custId: $('#custId').val().trim()
                };

                for (const [key, val] of Object.entries(customer)) {
                    if (!val) {
                        toastr.error(`Please fill out ${key.replace('_', ' ')}.`);
                        return;
                    }
                }



                const total = $('#grandTotal').val();



                const payload = {
                    _token: $('input[name=_token]').val(),
                    ...customer,
                    payment_method: method,
                    gift_card_code: $('#giftCardCode').val(),
                    ach: {
                        name: $('#achName').val(),
                        routing: $('#achRouting').val(),
                        account: $('#achAccount').val()
                    },
                    cc: {
                        xCardNum: $('#ccNumber').val(),
                        xExp: $('#ccExp').val(),
                        cvv: $('#ccCvv').val()
                    },
                    api_cart: {
                        cart_id: stored.cart_id,
                        cart_token: stored.cart_token
                    },
                    // Customer info for guest place order

                    // Cart totals snapshot
                    xAmount: total,

                    applicable_coupon: $('#couponCode').val().trim() || null,

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
                            // showCancelButton: true,
                            confirmButtonText: 'Okay',
                            // cancelButtonText: 'Stay here',
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
                                location.reload();
                            } else if (result.dismiss === Swal.DismissReason.timer) {
                                location.reload();
                            }
                            localStorage.removeItem('cartInfo');
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

            // Customser Search/Create
            $(function() {
                let selectedCustomer = null;

                $('#customerSearch').on('input', function() {
                    const query = $(this).val().trim();
                    const $results = $('#customerSearchResults');

                    if (query.length < 2) {
                        $results.empty();
                        return;
                    }

                    $.get(routes.custSearch, {
                        q: query
                    }, function(data) {
                        $results.empty();

                        const customers = data.hits || [];

                        if (customers.length === 0) {
                            $results.append(
                                $('<div>')
                                .addClass('list-group-item text-muted small')
                                .text('No matching customers found.')
                            );
                            return;
                        }

                        customers.map(function(c) {
                            const item = $('<button>')
                                .addClass('list-group-item list-group-item-action')
                                .text(
                                    `${c.f_name} ${c.l_name} — ${c.street_address || c.address_1 || c.address_3 || 'No address'}`
                                )
                                .on('click', function() {
                                    window.selectCustomerForCart = {
                                        details: c,
                                    };

                                    selectCustomer(c);
                                });
                            $results.append(item);
                        })




                    });
                });



                function selectCustomer(c) {
                    selectedCustomer = c;
                    $('#selName').text(`${c.f_name} ${c.l_name}`);
                    $('#selEmail').text(c.street_address || c.address_1 || c.address_3 || '');
                    $('#selectedCustomer').removeClass('d-none');
                    $('#customerForm').addClass('d-none');
                    $('#btnCheckout').prop('disabled', false);
                    $('#customerResults').empty();

                }


                $('#btnChangeCustomer').on('click', function() {
                    selectedCustomer = null;
                    $('#selectedCustomer').addClass('d-none');
                    $('#customerForm').removeClass('d-none');
                    $('#btnCheckout').prop('disabled', true);
                });

                $('#btnCreateCustomer').on('click', function() {
                    const fields = [{
                            id: '#newName',
                            label: 'name'
                        },
                        {
                            id: '#newEmail',
                            label: 'email'
                        },
                        {
                            id: '#newPhone',
                            label: 'phone number'
                        },
                        {
                            id: '#newStreetAdd',
                            label: 'street address'
                        },
                        {
                            id: '#newCity',
                            label: 'city'
                        },
                        {
                            id: '#newState',
                            label: 'state'
                        },
                        {
                            id: '#newZip',
                            label: 'zip code'
                        },
                    ];

                    let data = {};
                    for (const field of fields) {
                        const value = $(field.id).val().trim();
                        if (!value) {
                            toastr.warning(`Please enter a ${field.label}.`);
                            return;
                        }
                        data[field.id.replace('#new', '').toLowerCase()] = value;
                    }

                    $.ajax({
                        url: routes.custCreate,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: data,
                        success: function(newCust) {
                            console.log(newCust);

                            if (newCust && newCust.ok && newCust.data && newCust.data.id) {

                                selectCustomer(newCust.data);

                                window.selectCustomerForCart = {
                                    details: newCust.data
                                };

                            } else {
                                alert('Error creating customer.');
                            }

                        },
                        error: function() {
                            alert('Server error while creating customer.');
                        }
                    });
                });
            });

        })();
    </script>
@endpush
