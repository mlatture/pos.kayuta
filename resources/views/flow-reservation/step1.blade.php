@extends('layouts.admin')

@section('title', 'Reservation Draft – Search & Cart (Step 1)')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

        .add-on-selector {
            font-size: 0.85rem;
        }

        .total-row {
            font-weight: bold;
            font-size: 1.1rem;
        }

        .platform-fee-info {
            font-size: 0.75rem;
            color: #666;
            font-style: italic;
        }

        .occupants-input {
            width: 60px;
            display: inline-block;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-3">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0">Reservation Draft – Search & Cart (Step 1)</h1>
        </div>

        {{-- Top Section: Search Criteria --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form id="searchForm" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Dates</label>
                        <input type="text" class="form-control" id="dateRange" placeholder="Select Dates" required>
                        <input type="hidden" name="start_date" id="startDate">
                        <input type="hidden" name="end_date" id="endDate">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Site Class</label>
                        <select class="form-select" name="siteclass">
                            <option value="">Any</option>
                            @foreach ($siteClasses as $class)
                                <option value="{{ $class->siteclass }}">{{ $class->siteclass }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hookup</label>
                        <select class="form-select" name="hookup">
                            <option value="">Any</option>
                            @foreach ($siteHookups as $hookup)
                                <option value="{{ $hookup->sitehookup }}">{{ $hookup->sitehookup }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rig Length (ft)</label>
                        <input type="number" class="form-control" name="rig_length" placeholder="e.g. 30">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search Sites</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            {{-- Left 2/3: Search Results --}}
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Available Sites</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="resultsTable">
                                <thead class="table-light">
                                    <tr>
                                    <tr>
                                        <th>Site</th>
                                        <th>Class</th>
                                        <th>Hookup</th>
                                        <th>Rig Length</th>
                                        <th>Occupants / Extras</th>
                                        <th>Price Breakdown</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Enter search criteria above
                                            to see available sites.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right 1/3: Cart Panel --}}
            <div class="col-lg-4">


                <div class="card shadow-sm cart-panel">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Your Cart</h5>
                        <span class="badge bg-primary" id="cartCount">0</span>
                    </div>
                    <div class="card-body">


                        <div id="cartItems">
                            <p class="text-muted text-center py-3" id="emptyCartMsg">
                                Cart is empty.
                            </p>
                            <div id="auto-fetch-loader" class="d-none text-muted" style="font-size:20px">
                                <i class="fa fa-spinner fa-spin"></i> Updating cart…
                            </div>
                        </div>

                        <hr>

                        {{-- Discounts & Coupons --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Coupon Code</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="couponCode" placeholder="Enter code">
                                <button class="btn btn-outline-secondary" type="button" id="applyCoupon">Apply</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Instant Discount ($)</label>
                            <input type="number" class="form-control form-control-sm" id="instantDiscount" step="0.01"
                                value="0.00">
                        </div>

                        <hr>

                        {{-- Totals --}}
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="subtotalDisplay">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Discounts</span>
                            <span id="discountsDisplay">-$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Estimated Tax</span>
                            <span id="taxDisplay">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 total-row">
                            <span>Grand Total</span>
                            <span id="grandTotalDisplay">$0.00</span>
                        </div>
                        <div class="platform-fee-info mt-2">
                            * Pricing includes a platform fee per site.
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <button class="btn btn-success w-100 py-2" id="nextBtn" disabled>
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(function() {
            let cart = [];
            // Platform fee removed to match manage/reservation logic
            // let platformFee = 0; 
            let taxRate = 0.07; // 7% placeholder, should come from settings

            // Initialize Flatpickr
            const fp = flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                minDate: "today",
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        $('#startDate').val(instance.formatDate(selectedDates[0], "Y-m-d"));
                        $('#endDate').val(instance.formatDate(selectedDates[1], "Y-m-d"));
                    }
                }
            });

            // Handle Search
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                const $btn = $(this).find('button[type="submit"]');
                $btn.prop('disabled', true).text('Searching...');

                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();

                $.get("{{ route('flow-reservation.search') }}", $(this).serialize())
                    .done(function(res) {
                        // platformFee = parseFloat(res.platform_fee) || 5.00; // Removed
                        const siteLockFee = parseFloat(res.site_lock_fee) || 0;
                        const units = res.data.response.results.units;
                        const tbody = $('#resultsTable tbody');
                        tbody.empty();

                        if (units.length === 0) {
                            tbody.append(
                                '<tr><td colspan="7" class="text-center py-5">No available sites found for these criteria.</td></tr>'
                            );
                        } else {
                            units.forEach(unit => {
                                const basePrice = parseFloat(unit.price_quote.total);
                                const avgNight = parseFloat(unit.price_quote.avg_nightly || 0);
                                const total = basePrice +
                                    siteLockFee; // Initial view assumes site lock checked

                                tbody.append(`
                                    <tr data-id="${unit.site_id}">
                                        <td>
                                            <strong>${unit.name}</strong><br>
                                            <small class="text-muted">ID: ${unit.site_id}</small>
                                        </td>
                                        <td>${unit.class.replace(/_/g, ' ')}</td>
                                        <td>${unit.hookup || 'N/A'}</td>
                                        <td>${unit.maxlength || 'N/A'} ft</td>
                                        <td>
                                            <div class="d-flex gap-2 mb-2">
                                                <div>
                                                    <label class="small text-muted d-block">Adults</label>
                                                    <input type="number" class="form-control form-control-sm occupants-input adults" value="2" min="1">
                                                </div>
                                                <div>
                                                    <label class="small text-muted d-block">Children</label>
                                                    <input type="number" class="form-control form-control-sm occupants-input children" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input site-lock-toggle" type="checkbox" id="lock_${unit.site_id}" checked data-fee="${siteLockFee}">
                                                <label class="form-check-label small" for="lock_${unit.site_id}">
                                                    Site Lock Fee ($${siteLockFee.toFixed(2)})
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div>Site Lock Fee: $<span class="fee-display">${siteLockFee.toFixed(2)}</span></div>
                                                <div>Sub Total: $${basePrice.toFixed(2)}</div>
                                                <div>Avg/Night: $${avgNight.toFixed(2)}</div>
                                                <div>Extras: $<span class="extras-display">0.00</span></div>
                                                <div class="fw-bold border-top mt-1 pt-1">Total: $<span class="total-display">${total.toFixed(2)}</span></div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary add-to-cart" 
                                                data-id="${unit.site_id}" 
                                                data-name="${unit.name}" 
                                                data-base="${basePrice}"
                                                data-fee="0"
                                                data-start="${startDate}"
                                                data-end="${endDate}">
                                                Add to Cart
                                            </button>
                                        </td>
                                    </tr>
                                `);
                            });
                        }
                    })
                    .fail(function() {
                        alert('An error occurred while searching.');
                    })
                    .always(function() {
                        $btn.prop('disabled', false).text('Search Sites');
                    });
            });

            // Handle Site Lock Toggle for Price Update
            $(document).on('change', '.site-lock-toggle', function() {
                const $row = $(this).closest('tr');
                const fee = parseFloat($(this).data('fee'));
                const isChecked = $(this).is(':checked');
                const basePrice = parseFloat($row.find('.add-to-cart').data('base'));

                const currentFee = isChecked ? fee : 0;
                const total = basePrice + currentFee;

                $row.find('.fee-display').text(currentFee.toFixed(2));
                $row.find('.total-display').text(total.toFixed(2));
            });

            // Add to Cart
            $(document).on('click', '.add-to-cart', function() {
                const $row = $(this).closest('tr');
                const adults = parseInt($row.find('.adults').val()) || 0;
                const children = parseInt($row.find('.children').val()) || 0;
                const siteLockChecked = $row.find('.site-lock-toggle').is(':checked');
                const siteLockFee = siteLockChecked ? 'on' : 'off';

                const item = {
                    id: $(this).data('id'),
                    name: $(this).data('name'),
                    base: parseFloat($(this).data('base')),
                    fee: parseFloat($(this).data('fee')),
                    start_date: $(this).data('start'),
                    end_date: $(this).data('end'),
                    occupants: {
                        adults: adults,
                        children: children
                    },
                    site_lock_fee: siteLockFee
                };

                cart.push(item);
                updateCartUI();
                $(this).prop('disabled', true).text('Added');
            });

            // Remove from Cart
            $(document).on('click', '.remove-item', function() {
                const index = $(this).data('index');
                const siteId = cart[index].id;
                cart.splice(index, 1);

                // Re-enable button in results if it exists
                $(`.add-to-cart[data-id="${siteId}"]`).prop('disabled', false).text('Add to Cart');

                updateCartUI();
            });

            // Recalculate on manual discount change
            $('#instantDiscount').on('input', updateTotals);

            function updateCartUI() {
                const $container = $('#cartItems');
                $container.empty();

                if (cart.length === 0) {
                    $('#emptyCartMsg').show();
                    $('#nextBtn').prop('disabled', true);
                } else {
                    $('#emptyCartMsg').hide();
                    $('#nextBtn').prop('disabled', false);

                    cart.forEach((item, index) => {
                        $container.append(`
                    <div class="cart-item" data-index="${index}">
                        <div class="d-flex justify-content-between">
                            <strong>${item.name}</strong>
                            <a href="javascript:void(0)" class="text-danger remove-item" data-index="${index}"><i class="fas fa-trash"></i></a>
                        </div>
                        <div class="small text-muted">Base: $${(item.base).toFixed(2)}</div>
                    </div>
                `);
                    });
                }

                $('#cartCount').text(cart.length);
                updateTotals();
            }

            function updateTotals() {
                let subtotal = 0;
                cart.forEach(item => {
                    subtotal += item.base; // + item.fee (Removed)
                });

                const instantDiscount = parseFloat($('#instantDiscount').val()) || 0;
                // Mock coupon logic for UI
                const couponDiscount = 0;

                const totalDiscount = instantDiscount + couponDiscount;
                const subtotalAfterDiscount = Math.max(0, subtotal - totalDiscount);
                const tax = subtotalAfterDiscount * taxRate;
                const grandTotal = subtotalAfterDiscount + tax;

                $('#subtotalDisplay').text('$' + subtotal.toFixed(2));
                $('#discountsDisplay').text('-$' + totalDiscount.toFixed(2));
                $('#taxDisplay').text('$' + tax.toFixed(2));
                $('#grandTotalDisplay').text('$' + grandTotal.toFixed(2));

                window.currentTotals = {
                    subtotal: subtotal,
                    discount_total: totalDiscount,
                    estimated_tax: tax,
                    platform_fee_total: 0, // cart.length * platformFee (Removed)
                    grand_total: grandTotal
                };
            }

            // Next Step
            $('#nextBtn').on('click', function() {
                const $btn = $(this);
                $btn.prop('disabled', true).text('Saving Draft...');

                $.post("{{ route('flow-reservation.save-draft') }}", {
                        _token: "{{ csrf_token() }}",
                        cart_data: cart,
                        totals: window.currentTotals
                    })
                    .done(function(res) {
                        window.location.href = res.redirect_url;
                    })
                    .fail(function() {
                        alert('Failed to save draft. Please try again.');
                        $btn.prop('disabled', false).text('Next');
                    });
            });

            // Mock Apply Coupon
            $('#applyCoupon').on('click', function() {
                const code = $('#couponCode').val();
                if (code) {
                    alert(
                        'Coupon logic would be implemented here. For this draft, the coupon record will be created at the next stage.'
                    );
                }
            });


            // Automatation
            const itemsToAutomate = getUrlItems();

            if (itemsToAutomate.length > 0) {
                processAutomatedSelections(itemsToAutomate);
            }

            async function processAutomatedSelections(items) {
                for (const item of items) {
                    await autoFetchAndAdd(item.siteId, item.cid, item.cod);
                }
            }

            function autoFetchAndAdd(siteId, cid, cod) {
                const loader = $('#auto-fetch-loader');
                loader.removeClass('d-none');

                return $.get("{{ route('flow-reservation.search') }}", {
                        start_date: cid,
                        end_date: cod,
                        siteid: siteId
                    })
                    .done(function(res) {
                        // const platformFee = parseFloat(res.platform_fee) || 5.00; // Removed
                        const units = res.data.response.results.units;

                        const unit = units.find(u => u.site_id === siteId);



                        if (unit) {
                            const automatedItem = {
                                id: unit.site_id,
                                name: unit.name,
                                base: parseFloat(unit.price_quote.total),
                                fee: 0, // platformFee (Removed)
                                cid: cid, // We store the specific dates for this site
                                cod: cod,
                            };



                            // Push to global cart array
                            cart.push(automatedItem);

                            // Update Sidebar UI and Totals
                            updateCartUI();

                        }
                    })
                    .fail(function() {
                        console.error(`Failed to automate site: ${siteId}`);
                    })
                    .always(function() {
                        loader.addClass('d-none');
                    });
            }

            function getUrlItems() {
                const params = new URLSearchParams(window.location.search);
                const items = [];
                for (const [key, value] of params.entries()) {
                    const match = key.match(/items\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = match[1];
                        const field = match[2];
                        if (!items[index]) items[index] = {};
                        items[index][field] = value;
                    }
                }
                return items.filter(Boolean);
            }


        });
    </script>
@endpush
