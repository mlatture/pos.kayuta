@extends('layouts.admin')

@section('title', 'Reservation List')
@section('content-header')
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <h4 class="me-2">Reservation List</h4>
            <div class="form-check form-switch">
                <h4>
                    <input class="form-check-input" type="checkbox" name="seasonal" id="seasonalFilter"
                        {{ request('seasonal') == 1 ? 'checked' : '' }}>
                    <label class="form-check-label mt-1" for="seasonalFilter">Show Seasonal Sites</label>
                </h4>
            </div>
        </div>
    </div>
@endsection
@section('content-actions')
    <div class="d-flex align-items-center justify-content-end mb-3">
        <button class="btn btn-outline-secondary me-2" id="prev30">&larr;</button>
        <input type="date" id="startDatePicker" value="{{ $filters['startDate'] }}" class="form-control w-auto">
        <button class="btn btn-outline-secondary ms-2" id="next30">&rarr;</button>
    </div>
@endsection

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

@section('content')
    <style>
        body {
            overflow: hidden !important;
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
            max-width: 100%;
            position: relative;
            max-height: 80vh !important;
        }

        .table-actions {
            margin-bottom: 10px;
        }

        .management-table th,
        .management-table td {
            white-space: nowrap;
        }

        .sticky-col {
            position: sticky;
            left: 0;
            z-index: 2;
            background: #343a40 !important;
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sticky-col+.sticky-col {
            left: 50px;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .site-card {
            border: 2px solid #ddd;
            cursor: pointer;
            transition: 0.3s;
        }

        .site-card:hover {
            border: 2px solid #007bff;
        }

        .site-card.selected {
            border: 2px solid green;
            background-color: #f0fff0;
        }

        #siteFilter,
        #typeFilter {
            width: 100%;
            margin-top: 5px;
            padding: 5px 10px;
            border-radius: 0.25rem;
            background-color: #f8f9fa;
        }

        .select2-container {
            width: 100% !important;
        }

        body,
        html {
            height: 100%;
        }

        main.content {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .highlight-today {
            outline: 3px dashed #00ffcc;
            outline-offset: -3px;
        }
    </style>


    <div class="table-responsive">
        <table class="table management-table table-striped">
            <thead>
                <tr class="t__head">
                    <th class="sticky-col bg-dark text-white" rowspan="2">
                        <div class="d-flex flex-column justify-content-between align-items-start">
                            <span class="me-2">Site</span>
                            <select id="siteFilter" class="form-select form-select-sm w-100" multiple="multiple">
                                @foreach ($sites as $site)
                                    <option value="{{ $site->siteid }}">{{ $site->siteid }}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    <th class="sticky-col bg-dark text-white" rowspan="2">
                        <div class="d-flex flex-column justify-content-between align-items-start">
                            <span class="me-2">Type</span>
                            <select id="typeFilter" class="form-select form-select-sm w-100" multiple="multiple">
                                @foreach ($sites->pluck('ratetier')->unique()->sort() as $rateTier)
                                    <option value="{{ $rateTier }}">{{ $rateTier }}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    @php
                        $recurringMonth = '';
                        $colspan = 0;
                    @endphp
                    @foreach ($calendar as $key => $dates)
                        @if ($key < 1)
                            @php
                                $recurringMonth = date('M Y', strtotime($dates));
                            @endphp
                        @endif
                        @if ($recurringMonth == date('M Y', strtotime($dates)) && count($calendar) - 1 != $key)
                            @php
                                $colspan += 1;
                            @endphp
                        @else
                            @if (count($calendar) - 1 == $key)
                                @php
                                    $colspan += 1;
                                @endphp
                            @endif
                            <td colspan="{{ $colspan }}"
                                class="month sticky-top bg-dark text-center text-white text-uppercase">
                                {{ $recurringMonth }}
                            </td>
                            @php
                                $colspan = 1;
                                $recurringMonth = date('M Y', strtotime($dates));
                            @endphp
                        @endif
                    @endforeach
                </tr>
                <tr>
                    @foreach ($calendar as $dates)
                        <th data-date="{{ $dates }}" class="sticky-top custom--dates">
                            {{ date('D', strtotime($dates)) }}
                            <hr class="m-0">{{ date('d', strtotime($dates)) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="siteTableBody">
                @include('reservations.components._site_rows_list', [
                    'sites' => $sites,
                    'calendar' => $calendar,
                ])
            </tbody>
        </table>
    </div>

@endsection
@include('reservations.modals.details')


@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        document.getElementById('startDatePicker').addEventListener('change', function() {
            const startDate = this.value;
            const seasonal = document.getElementById('seasonalFilter').checked ? 1 : 0;
            window.location.href = `?startDate=${startDate}&seasonal=${seasonal}`;
        });

        document.getElementById('prev30').addEventListener('click', function() {
            const current = new Date(document.getElementById('startDatePicker').value);
            current.setDate(current.getDate() - 30);
            document.getElementById('startDatePicker').value = current.toISOString().split('T')[0];
            document.getElementById('startDatePicker').dispatchEvent(new Event('change'));
        });

        document.getElementById('next30').addEventListener('click', function() {
            const current = new Date(document.getElementById('startDatePicker').value);
            current.setDate(current.getDate() + 30);
            document.getElementById('startDatePicker').value = current.toISOString().split('T')[0];
            document.getElementById('startDatePicker').dispatchEvent(new Event('change'));
        });

        $(document).ready(function() {
            // Initialize Select2 for multi-select dropdowns
            $('#siteFilter').select2({
                placeholder: "",
                width: '100%',
            });

            $('#typeFilter').select2({
                placeholder: "",
                width: '100%',
            });

            // Event listeners for filters change
            $('#siteFilter, #typeFilter').on('change', function() {
                filterTable();
            });
        });

        $('#seasonalFilter').on('change', function() {
            filterTable();
        });

        $('#seasonalFilter').on('change', function() {
            const seasonal = $(this).is(':checked') ? 1 : 0;
            const startDate = $('#startDatePicker').val();
            window.location.href = `?startDate=${startDate}&seasonal=${seasonal}`;
        });


        function filterTable() {
            const hideSeasonal = !$('#seasonalFilter').is(':checked'); // Invert logic: unchecked means hide seasonal
            const selectedSites = $('#siteFilter').val() || [];
            const selectedTypes = $('#typeFilter').val() || [];

            const rows = document.querySelectorAll('#siteTableBody tr');

            rows.forEach(row => {
                const siteId = row.dataset.siteSiteid;
                const siteTier = row.dataset.siteRatetier;
                const isSeasonal = row.dataset.siteSeasonal === '1';

                let showRow = true;

                if (hideSeasonal && isSeasonal) showRow = false;

                if (selectedSites.length > 0 && !selectedSites.includes(siteId)) {
                    showRow = false;
                }

                if (selectedTypes.length > 0 && !selectedTypes.includes(siteTier)) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        }




        $(document).on('click', '.reservation-details', function() {
            let reservationId = $(this).data('reservation-id');
            fetchReservationDetails(reservationId);
        });

        function fetchReservationDetails(reservationId) {
            $.ajax({
                url: "{{ route('reservations.details') }}",
                type: 'GET',
                data: {
                    id: reservationId
                },
                success: function(item) {
                    const c = item.customerRecord || {};
                    const format = d => d ? moment(d).format('MMM DD, YYYY') : 'N/A';
                    const balance = parseFloat(item.balance || 0).toFixed(2);

                    $('#resCustomerName').text(`${item.fname || 'Guest'} ${item.lname || ''}`);
                    $('#resArrivalDate').text(format(item.cid));
                    $('#resDepartureDate').text(format(item.cod));
                    $('#resSiteId').text(item.siteid || 'N/A');
                    $('#resRigType').text(item.rigtype || 'N/A');
                    $('#resRigLength').text(item.riglength || 'N/A');
                    $('#resSiteClass').text(item.siteclass || 'N/A');

                    $('#resEmail').text(c.email || 'N/A');
                    $('#resPhone').text(c.phone || 'N/A');
                    $('#resNights').text(item.nights || 'N/A');
                    $('#resCartId').text(item.cartid || 'N/A');
                    $('#resComments').text(item.comments || 'N/A');
                    $('#resTotal').text(parseFloat(item.total || 0).toFixed(2));

                    const balanceBadge = $('#resBalanceBadge');
                    balanceBadge.text(`$${balance}`);
                    balanceBadge.removeClass('bg-danger bg-success').addClass(balance > 0 ? 'bg-danger' :
                        'bg-success');

                    $('#resCheckIn').html(item.checkedin ||
                        `<input type="checkbox" class="checked_in_date" onclick="handleCheckIn(this, '${item.cartid}')">`
                    );
                    $('#resCheckOut').html(item.checkedout ||
                        `<input type="checkbox" class="checked_out_date" onclick="handleCheckOut(this, '${item.cartid}')">`
                    );
                    $('#resSource').text(item.source || 'Walk-In');

                    $('#action1').data('id', item.cartid);
                    $('#action3').data('id', item.cartid);

                    $('#reservationDetailsContent').show();
                    $('#reservationDetailsModal').modal('show');
                },
                error: function(err) {
                    alert('Unable to fetch reservation details.');
                    console.error(err);
                }
            });
        }

        let originalCID = null;
        let originalCOD = null;

        $(document).on('click', '#btnAdjustDates', function() {
            const cid = $('#resArrivalDate').text();
            const cod = $('#resDepartureDate').text();
            if (cid && cod) {
                originalCID = moment(cid, 'MMM DD, YYYY');
                originalCOD = moment(cod, 'MMM DD, YYYY');
                updateDateRangePicker();
                $('#adjustDatesPicker').slideDown();
            }
        });

        function updateDateRangePicker() {
            $('#adjustDateRange').val(`${originalCID.format('MMM DD, YYYY')} → ${originalCOD.format('MMM DD, YYYY')}`);
        }

        function adjustDates(direction) {
            originalCID.add(direction, 'days');
            originalCOD.add(direction, 'days');
            updateDateRangePicker();
        }

        function submitAdjustedDates() {
            // Send AJAX PATCH or POST to your backend with new CID/COD
            Swal.fire('Dates adjusted!', 'You can now apply charges/refunds and email a receipt.', 'success');
        }



        function handleCheckIn(checkbox, cartId) {
            Swal.fire({
                title: 'Confirm Check-In',
                text: 'Are you sure you want to check in this guest?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, check in',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    const now = new Date().toLocaleString();
                    const checkInRoute = "{{ route('reservations.updateCheckedIn') }}";

                    fetch(checkInRoute, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                            },
                            body: JSON.stringify({
                                cartid: cartId
                            }),
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                checkbox.parentElement.innerHTML = now;
                                Swal.fire('Checked in!', `Time: ${now}`, 'success');
                            } else {
                                Swal.fire('Error', data.message || 'Could not check in.', 'error');
                                checkbox.checked = false;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Request failed. Please try again.', 'error');
                            checkbox.checked = false;
                        });
                } else {
                    checkbox.checked = false;
                }
            });
        }


        function handleCheckOut(checkbox, cartId) {
            Swal.fire({
                title: 'Confirm Check-Out',
                text: 'Are you sure you want to check out this guest?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, check out',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    const checkOutRoute = "{{ route('reservations.updateCheckedOut') }}";

                    fetch(checkOutRoute, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                            },
                            body: JSON.stringify({
                                cartid: cartId
                            }),
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                checkbox.parentElement.innerHTML = formatDateTime(data.checked_out_date);
                                Swal.fire('Checked out!', `Time: ${formatDateTime(data.checked_out_date)}`,
                                    'success');
                            } else {
                                Swal.fire('Error', data.message || 'Could not check out.', 'error');
                                checkbox.checked = false;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Request failed. Please try again.', 'error');
                            checkbox.checked = false;
                        });

                } else {
                    checkbox.checked = false;
                }
            });
        }

        function formatDateTime(datetimeStr) {
            const date = new Date(datetimeStr);
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }




        $(document).on("click", "#action1", function() {
            const id = $(this).data("id");
            url = "reservations/edit/" + id;
            window.location = url;
        });


        $('#check-available').on('click', function() {
            const isHidden = $('#ganttTableAvailable').is(':hidden');

            if (isHidden) {
                const sitesFromTable = [];

                $('#siteTableBody tr').each(function() {
                    const $row = $(this);

                    sitesFromTable.push({
                        id: $row.data('site-id'),
                        siteid: $row.data('site-siteid'),
                        siteclass: $row.data('site-siteclass'),
                        images: $row.data('site-images'),
                        price: parseFloat($row.data('site-price')),
                    });
                });

                checkAvailable(sitesFromTable);
                $('.management-table').hide();
                $('#ganttTableAvailable').show();
                $('.filter-container').removeAttr('hidden');
            } else {
                $('.management-table').show();
                $('#ganttTableAvailable').hide();
                $('.filter-container').attr('hidden', true);
            }
        });



        function checkAvailable(data) {
            console.log("Data from Laravel:", data);

            $('#site-list').empty();

            if (!Array.isArray(data) || data.length === 0) {
                $('#site-list').append('<p>No sites available</p>');
                return;
            }

            let sitesHTML = data.map(site => `
                <label class="list-group-item">
                    <input type="checkbox" class="site-checkbox" value="${site.id}">
                    ${site.siteid || site.sitename || 'Unnamed Site'}
                </label>
            `).join('');

            $('#site-list').append(sitesHTML);
            showFilteredCards([], data, true);

            $('#apply-filter').off('click').on('click', function() {
                let selectedSites = $('.site-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedSites.length === 0) {
                    showFilteredCards([], data, true);
                } else {
                    showFilteredCards(selectedSites, data, false);
                }

                $('#filterModal').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });

            $('#ganttTableAvailable').attr('hidden', false);
            $('.filter-container').attr('hidden', false);
        }

        const storageBaseUrl = "{{ asset('storage/sites') }}";
        const fallbackImage =
            "https://storage.googleapis.com/proudcity/mebanenc/uploads/2021/03/placeholder-image-300x225.png";

        function showFilteredCards(selectedSiteIds, allSites, showAll = false) {
            console.log('all sites', allSites);
            let filteredSites = showAll ?
                allSites :
                allSites.filter(site => selectedSiteIds.includes(String(site.id)));

            let cardHtml = '';
            const imageRotationData = [];

            filteredSites.forEach(site => {
                let images = [];

                if (Array.isArray(site.images)) {
                    images = site.images;
                } else if (typeof site.images === 'string') {
                    try {
                        let parsed = JSON.parse(site.images);
                        images = Array.isArray(parsed) ? parsed : [parsed];
                    } catch (e) {
                        images = [];
                    }
                }

                images = images.map(img => `${storageBaseUrl}/${img}`);
                if (!images.length) images = [fallbackImage];

                const imgId = `img-${site.id}-${Math.floor(Math.random() * 10000)}`;
                console.log('Site:', site);
                console.log('Parsed images:', images);

                cardHtml += `
                    <div class="col-md-3 mb-3">
                        <div class="card site-card" data-id="${site.id}" data-price="${site.price || 100}">
                            <img id="${imgId}" src="${images[0]}" class="card-img-top" alt="Site Image"
                                style="max-height: 200px; object-fit: cover;"
                                onerror="this.onerror=null; this.src='${fallbackImage}'">
                            <div class="card-body text-center">
                                <h5 class="card-title">${site.siteid}</h5>
                                <p class="card-text">${site.siteclass}</p>
                            </div>
                        </div>
                    </div>
                `;

                if (images.length > 1) {
                    imageRotationData.push({
                        imgId,
                        images
                    });
                }
            });

            document.getElementById("ganttTableAvailable").innerHTML = `<div class="row">${cardHtml}</div>`;

            document.querySelectorAll('.site-card').forEach(card => {
                card.addEventListener('click', function() {
                    toggleSite(this);
                });
            });

            imageRotationData.forEach(({
                imgId,
                images
            }) => {
                let index = 0;
                setInterval(() => {
                    index = (index + 1) % images.length;
                    const imgEl = document.getElementById(imgId);
                    if (imgEl) imgEl.src = images[index];
                }, 3000);
            });
        }



        function quoteSites() {
            let selectedCards = document.querySelectorAll('.site-card.selected');

            let siteIds = [...new Set(Array.from(selectedCards).map(card => card.getAttribute("data-id")))];
            let nights = $('#nights').val();
            console.log('Unique Selected Site IDs:', siteIds);
            $.ajax({
                url: '{{ route('reservations.quoteSite') }}',
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}',
                    siteIds: siteIds,
                    nights: nightsCounts
                },
                success: function(response) {
                    console.log('Quote response:', response);

                    let checkinDate = $('#checkin').val();
                    let checkoutDate = $('#checkout').val();

                    let tableBody = "";
                    let totalCost = 0;

                    response.forEach(site => {
                        let frequency = nightsCounts === 7 ? "Weekly" : (nightsCounts > 7 ?
                            "Weekly + Extra Nights" : "Nightly");
                        let description = `Booking for ${nightsCounts} ${nightsCounts === 1
                        ? 'night' : 'nights'} from ${checkinDate} to ${checkoutDate}`;

                        totalCost += site.rate;

                        tableBody += `
                        <tr>
                            <td>${checkinDate} - ${checkoutDate}</td>
                            <td>${site.siteid}</td>
                            <td id="table-rate" data-rate="${site.rate}">$${site.rate.toFixed(2)}</td>
                            <td>${frequency}</td>
                            <td>${description}</td>
                            </tr>
                            `;

                    });

                    let tableFooter = `
                        <tr>
                            <td colspan="2"><strong>Total</strong></td>
                            <td><strong id="totalCost" data-total="${totalCost}">$${totalCost.toFixed(2)}</strong></td>
                            <td colspan="2"></td>
                            </tr>
                            
                            `;

                    $('#quoteModalBody').html(`
                            <p><strong>Check-in:</strong> ${checkinDate}</p>
                            <p><strong>Check-out:</strong> ${checkoutDate}</p>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Site</th>
                                        <th>Rate</th>
                                        <th>Frequency</th>
                                        <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            ${tableBody}
                                            </tbody>
                                            <tfoot>
                                                ${tableFooter}
                                                </tfoot>
                                                </table>
                                                
                                                `);

                    $('#quoteModal').modal('show');

                    $('#createReservation').on('click', function() {




                        $.ajax({
                            url: " {{ route('reservations.create-reservation') }}",
                            type: "GET",
                            data: {
                                siteIds: siteIds,
                                nights: nightsCounts,
                                checkin: checkinDate,
                                checkout: checkoutDate,

                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response_reservation) {
                                console.log(response_reservation);

                                $.ajax({
                                    url: " {{ route('reservations.update-availability') }} ",
                                    type: "PATCH",
                                    data: {
                                        siteIds: siteIds,
                                        _token: '{{ csrf_token() }}'

                                    },
                                    success: function(response_availability) {
                                        console.log('Availability: ',
                                            response_availability);
                                    },
                                    error: function(error) {
                                        console.log(error);
                                    }

                                });

                                window.location.href =
                                    "{{ route('reservations.payment.index', ['confirmationNumber' => ':confirmationNumber']) }}"
                                    .replace(':confirmationNumber', response_reservation
                                        .confirmationNumber);
                            },
                            error: function(error) {
                                console.log(error);
                            }
                        })

                    })

                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
                button.addEventListener("click", function() {
                    let modal = document.getElementById("quoteModal");
                    let modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    setTimeout(() => {
                        document.body.classList.remove('modal-open');
                        document.querySelectorAll('.modal-backdrop').forEach(el => el
                            .remove());
                    }, 300);
                });
            });
        });

        let today = new Date().toISOString().split('T')[0];
        let tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow = tomorrow.toISOString().split('T')[0];

        document.getElementById("checkin").value = today;
        document.getElementById("checkout").value = tomorrow;

        let selectedSites = [];
        let nightsCounts = 1;


        function toggleSite(card) {
            let siteId = card.getAttribute("data-id");
            let price = parseFloat(card.getAttribute("data-price"));

            let index = selectedSites.findIndex(site => site.id === siteId);

            if (index > -1) {
                selectedSites.splice(index, 1);
                card.classList.remove("selected");
            } else {
                selectedSites.push({
                    id: siteId,
                    price: price
                });
                card.classList.add("selected");
            }

            console.log('Updated Selected Sites:', selectedSites);
            document.getElementById("quoteButton").hidden = selectedSites.length === 0;
        }

        function calculateNights() {
            let checkin = new Date(document.getElementById("checkin").value);
            let checkout = new Date(document.getElementById("checkout").value);

            if (!isNaN(checkin.getTime()) && !isNaN(checkout.getTime())) {
                let timeDiff = checkout.getTime() - checkin.getTime();
                nightsCounts = timeDiff / (1000 * 3600 * 24);

                $("#nights").val(`${nightsCounts}`);
                console.log(`Global Nights Count Updated: ${nightsCounts}`); // ✅ Debugging
            }
        }



        $(document).ready(function() {
            const $loadMoreBtn = $('#loadMoreSitesBtn');
            const $spinner = $loadMoreBtn.find('.spinner-border');
            const $siteTableBody = $('#siteTableBody');
            const $nextPageUrl = $('#nextPageUrl');
            const $searchBox = $('#searchBox');
            const $searchBtn = $('#searchBtn');


            let currentSearch = '';

            // Load more pagination
            $loadMoreBtn.on('click', function() {
                const nextUrl = $nextPageUrl.val();
                if (!nextUrl) return;

                $loadMoreBtn.attr('disabled', true);
                $spinner.removeClass('d-none');

                $.get(nextUrl, {
                    search: currentSearch,
                    seasonal: $('#seasonalFilter').is(':checked') ? 1 : 0,
                }, function(response) {
                    $siteTableBody.append(response.sites);
                    $nextPageUrl.val(response.next_page_url);

                    if (!response.next_page_url) {
                        $loadMoreBtn.hide();
                    } else {
                        $loadMoreBtn.removeAttr('disabled');
                    }
                    filterTable();
                }).always(function() {
                    $spinner.addClass('d-none');
                });
            });

            // Trigger search on button click
            $searchBtn.on('click', function() {
                currentSearch = $searchBox.val();
                const url = `{{ route('reservations.index') }}`;

                $.get(url, {
                    search: currentSearch
                }, function(response) {
                    $siteTableBody.html(response.sites);
                    $nextPageUrl.val(response.next_page_url);

                    if (!response.next_page_url) {
                        $loadMoreBtn.hide();
                    } else {
                        $loadMoreBtn.show();
                    }
                });
            });

            // Also trigger search on Enter key
            $searchBox.on('keypress', function(e) {
                if (e.which == 13) {
                    $searchBtn.click();
                }
            });
        });


        $(document).ready(function() {
            $('#sitename').select2();
            $('#siteclass').select2();
            $('.reservation-select').select2();
        });

        $('#customerDate').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#customerDate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                'MM/DD/YYYY'));
        });

        $('#customerDate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#customerDate').attr("placeholder", "Select Date");

        $('#customersTable').DataTable({
            // 'responsive': true,
            order: [
                [0, 'asc']
            ],
            "columnDefs": [{
                    "sortable": false,
                    "targets": 1
                },
                {
                    "sortable": false,
                    "targets": 8
                },
                {
                    "sortable": false,
                    "targets": 9
                }
            ]
        });

        function openReservationModal() {
            $('.res-cid').val('');
            $('.res-cod').val('');
            $('.reservationId').val('');
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $(':input').removeClass('is-invalid');
            $('.error--msg').remove();
            $(`#reservationDateModal`).modal('toggle');
        }

        function openReservationSiteModal() {
            $('.res-siteid').val('');
            $('.res-siteclass').val('');
            $('.reservationId2').val('');
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $(':input').removeClass('is-invalid');
            $('.error--msg').remove();
            $(`#reservationSiteModal`).modal('toggle');
        }

        function closeReservationModal(modalId) {
            $(`#${modalId}`).modal('hide');
        }

        function saveReservationDates(input) {
            $(input).attr('disabled', true);
            $(':input').removeClass('is-invalid');
            $('.error--msg').remove();
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $.post($('#reservationDateForm').attr('action'), $('#reservationDateForm').serialize()).done(function(res) {
                    if (res.status == "success") {
                        $('.alert-success').removeClass('d-none').text(res.message);
                        setTimeout(function() {
                            $('#reservationDateModal').modal('hide');
                            $(input).attr('disabled', false);
                            $('.alert-success').addClass('d-none').text('');
                            $('.alert-danger').addClass('d-none').text('');
                        }, 1500);
                    } else {
                        $(input).attr('disabled', false);
                        $('.alert-danger').removeClass('d-none').text(res.message)
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    $(input).attr('disabled', false);
                    if (jqXHR.status === 422) {
                        $.each(jqXHR.responseJSON.errors, function(k, v) {
                            $(`#${k}`).addClass('is-invalid').after(
                                `<span class="error--msg" role="alert"><strong class="text-danger">${v[0]}</strong></span>`
                            );
                        });
                    }
                });
        }

        function saveReservationSites(input) {
            $(input).attr('disabled', true);
            $(':input').removeClass('is-invalid');
            $('.error--msg').remove();
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $.post($('#reservationSiteForm').attr('action'), $('#reservationSiteForm').serialize()).done(function(res) {
                    if (res.status == "success") {
                        $('.alert-success').removeClass('d-none').text(res.message);
                        setTimeout(function() {
                            $('#reservationSiteModal').modal('hide');
                            $(input).attr('disabled', false);
                            $('.alert-success').addClass('d-none').text('');
                            $('.alert-danger').addClass('d-none').text('');
                        }, 1500);
                    } else {
                        $(input).attr('disabled', false);
                        $('.alert-danger').removeClass('d-none').text(res.message)
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    $(input).attr('disabled', false);
                    if (jqXHR.status === 422) {
                        $.each(jqXHR.responseJSON.errors, function(k, v) {
                            $(`#${k}`).addClass('is-invalid').after(
                                `<span class="error--msg" role="alert"><strong class="text-danger">${v[0]}</strong></span>`
                            );
                        });
                    }
                });
        }
    </script>
@endpush
