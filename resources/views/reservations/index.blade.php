@extends('layouts.admin')

@section('title', 'Reservation List')
@section('content-header', 'Reservation List')
@section('content-actions')
    @hasPermission(config('constants.role_modules.pos_management.value'))
        <a href="{{ route('cart.index') }}" class="btn btn-success">Open POS</a>
    @endHasPermission
@endsection
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />


@section('content')
    <style>
        .table-responsive {
            overflow-x: auto;
            width: 100%;
            max-width: 100%;
            position: relative;
        }

        .management-table {
            border-collapse: separate;
            border-spacing: 0;
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

        .card-header {
            background-color: #343a40;
            color: white;
            border-radius: 8px 8px 0 0;
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

        .quote-btn {
            position: fixed;
            bottom: 30px;
            z-index: 1000;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 50px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        #siteFilter,
        #typeFilter {
            width: 100%;
            margin-top: 5px;
            padding: 5px 10px;
            border-radius: 0.25rem;
            background-color: #f8f9fa;
            /* Light background for better visibility */
        }

        /* Responsive design for smaller screens */
        @media (max-width: 768px) {

            #siteFilter,
            #typeFilter {
                width: 100%;
                /* Full width on smaller screens */
            }
        }

        /* Optional: Make the Select2 dropdown look consistent */
        .select2-container {
            width: 100% !important;
        }

        /* Optional: Customize the dropdown arrow */
        .select2-container .select2-selection__arrow {
            border-color: #007bff;
            /* Change arrow color if needed */
        }
    </style>
{{-- 
    <header class="reservation__head bg-dark py-2">
        <div
            class="d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between px-md-3 px-2">
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="text-white text-decoration-none">
                    <img src="{{ asset('images/grid-ico.svg') }}" alt="" class="me-2" />
                    Planner
                </a>
                <a href="#" class="text-white text-decoration-none">
                    <img src="{{ asset('images/pin-ico.svg') }}" alt="" class="me-2" />
                    Map
                </a>
                <a href="#" class="text-white text-decoration-none">
                    <img src="{{ asset('images/flash-ico.svg') }}" alt="" class="me-2" />
                    Quick Add
                </a>
            </div>
            <div>
                <a href="#" class="text-white text-decoration-none">
                    <img src="{{ asset('images/help-ico.svg') }}" alt="" class="me-2" />
                    Help
                </a>
            </div>
        </div>
    </header> --}}
    <div
        class="table-actions d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between pe-2 pt-md-3 pt-2">
        <div class="d-flex align-items-center action__links gap-2">
            <a href="javascript:void(0)" id="check-available" class="border text-decoration-none p-2 text-dark">
                <img src="{{ asset('images/add-ico.svg') }}" alt="" class="me-2" />
                Multiple Sites
            </a>

            <a href="javascript:void(0)" id="loadMoreSitesBtn" class="border text-decoration-none p-2 text-dark">
                <i class="fa-solid fa-spinner"></i>
                Load More <span class="spinner-border spinner-border-sm d-none"></span>
            </a>

            <a class="border text-decoration-none p-2 text-dark" data-bs-toggle="collapse" href="#collapseExample"
                role="button" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa-brands fa-searchengin"></i> Search
            </a>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="seasonal" id="seasonalFilter" checked>
                <label class="form-check-label" for="seasonalFilter">Show Seasonal Sites</label>
            </div>
            {{-- <div class="form-check">
                <input class="form-check-input" type="checkbox" id="availableSitesFilter">
                <label class="form-check-label" for="availableSitesFilter">Available Sites</label>
            </div> --}}

            <div class="collapse" id="collapseExample">
                <div class="card card-body">
                    <div class="input-group">
                        <input type="text" id="searchBox" class="form-control" placeholder="Search...">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            🔍
                        </button>
                    </div>
                </div>
            </div>




        </div>




    </div>
    <input type="hidden" id="initialSiteCount" value="20">
    <input type="hidden" id="totalSites" value="{{ $sites->count() }}">

    <div class="text-center my-2 position-sticky bottom-0 z-3" style="background: white;">
        <input type="hidden" id="nextPageUrl" value="{{ $sites->nextPageUrl() }}">

    </div>


    <div class="table-responsive" style="max-height: 60vh !important; max-width: 100%">
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
                                @foreach ($sites->pluck('ratetier')->unique() as $rateTier)
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
                            <hr class="m-0"> {{ date('d', strtotime($dates)) }}
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



    <div class="container">
        <!-- Date Filters -->
        <div class="filter-container row g-3 align-items-end mb-3" hidden>
            <!-- Check-in Date -->
            <div class="col-md-4">
                <label for="checkin">Check-in:</label>
                <input type="date" id="checkin" class="form-control" value="{{ now()->format('Y-m-d') }}"
                    onchange="calculateNights()">
            </div>

            <!-- Check-out Date -->
            <div class="col-md-4">
                <label for="checkout">Check-out:</label>
                <input type="date" id="checkout" class="form-control"
                    value="{{ now()->addDay()->format('Y-m-d') }}" onchange="calculateNights()">
            </div>

            <!-- Nights Display -->
            <div class="col-md-4">
                <label>Nights:</label>
                <input type="text" id="nights" value="1" class="form-control" readonly>
            </div>

            <!-- Site Availability Button -->
            <div class="col-md-4">
                <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                    data-bs-target="#filterModal">
                    Site Availability
                </button>
            </div>

            <!-- Get Quote Button (Hidden Initially) -->
            <div class="col-12 text-center">
                <button id="quoteButton" hidden onclick="quoteSites()" class="btn btn-primary quote-btn"">
                    Get Quote
                </button>
            </div>
        </div>

        <div id="ganttTableAvailable" class="row" hidden></div>

        <!-- Quote Modal -->
        <div class="modal fade" id="quoteModal" tabindex="-1" aria-labelledby="quoteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="quoteModalLabel">Quote Summary</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="quoteModalBody">
                        <!-- Table is populated dynamically -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                        <button type="button" id="createReservation" class="btn btn-primary">Create
                            Reservation</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Modal --}}

        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Select Sites</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="list-group" id="site-list" style="max-height: 400px; overflow-y: auto;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="apply-filter" data-bs-dismiss="modal">Apply
                            Filter</button>

                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- Reservation Details Modal -->
    <div class="modal fade" id="reservationDetailsModal" tabindex="-1" aria-labelledby="reservationDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="reservationDetailsModalLabel">Reservation Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reservationDetailsModalBody">
                    <!-- Dynamic Content Loaded Here -->
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>





@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
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

        $('#availableSitesFilter').on('change', function() {
            filterTable();
        })

        function filterTable() {
            let hideSeasonal = $('#seasonalFilter').is(':checked');
            let showAvailableSites = $('#availableSitesFilter').is(':checked');
            const selectedSites = $('#siteFilter').val();
            const selectedTypes = $('#typeFilter').val();

            const rows = document.querySelectorAll('#siteTableBody tr');

            rows.forEach(row => {
                const siteId = row.dataset.siteSiteid;
                const siteTier = row.dataset.siteRatetier;
                const isSeasonal = row.dataset.siteSeasonal === '1';
                // const isAvailable = row.dataset.siteAvailable === '1';
                let showRow = true;
                
                if (hideSeasonal && !isSeasonal) showRow = false;
                // if (showAvailableSites && !isAvailable) showRow = false;

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
                    console.log(item.cartid)
                    let customerRecord = item.customerRecord || {};
                    let arrivalDate = item.cid ? moment(item.cid).format('MMM DD, YYYY') : "N/A";
                    let departureDate = item.cod ? moment(item.cod).format('MMM DD, YYYY') : "N/A";
                    let balance = parseFloat(item.balance || 0).toFixed(2);
                    let balanceClass = balance > 0 ? 'bg-danger' : 'bg-success';

                    let modalBody = `
                        <div class="card card-body">
                            <h5 class="mb-3">Reservation Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Customer:</strong> ${item.fname || 'Guest'} ${item.lname || ''}</p>
                                    <p><strong>Arrival:</strong> ${arrivalDate}</p>
                                    <p><strong>Departure:</strong> ${departureDate}</p>
                                    <p><strong>Site ID:</strong> ${item.siteid || "N/A"}</p>
                                    <p><strong>Rig Type:</strong> ${item.rigtype || "N/A"}</p>
                                    <p><strong>Rig Length:</strong> ${item.riglength || "N/A"}</p>
                                    <p><strong>Class:</strong> ${item.siteclass || "N/A"}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Email:</strong> ${customerRecord.email || "N/A"}</p>
                                    <p><strong>Phone:</strong> ${customerRecord.phone || "N/A"}</p>
                                    <p><strong>Length of Stay:</strong> ${item.nights || "N/A"} nights</p>
                                    <p><strong>Confirmation Number:</strong> ${item.cartid || "N/A"}</p>
                                    <p><strong>Comments:</strong> ${item.comments || "N/A"}</p>
                                    <p><strong>Total:</strong> $${parseFloat(item.total || 0).toFixed(2)}</p>
                                    <p><strong>Balance:</strong> 
                                        <span class="badge ${balanceClass} rounded-pill">$${balance}</span>
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Checked In:</strong> 
                                        ${item.checkedin || `<input type="checkbox" class="checked_in_date" onclick="handleCheckIn(this, '${item.cartid}');">`}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Checked Out:</strong> 
                                        ${item.checkedout || `<input type="checkbox" class="checked_out_date" onclick="handleCheckOut(this, '${item.cartid}');">`}
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <p><strong>Source:</strong> ${item.source || "Walk-In"}</p>

                            <hr> 

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-2 action-card" style="cursor: pointer; height: 150px;" data-id="${item.cartid}" id="action1">
                                        <div class="card-body d-flex justify-content-center align-items-center">
                                            <h6 class="card-title text-center">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                Edit Reservations
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-2 action-card" style="cursor: pointer; height: 150px;" id="action3" data-id="${item.cartid}">
                                        <div class="card-body d-flex justify-content-center align-items-center">
                                            <h6 class="card-title text-center">
                                                <i class="fa-solid fa-hand-holding-dollar"></i> Payment
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#reservationDetailsModalBody').html(modalBody);
                    $('#reservationDetailsModal').modal('show');
                },
                error: function(error) {
                    alert('Unable to fetch reservation details.');
                    console.log(error);
                }
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
