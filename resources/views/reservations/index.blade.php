@extends('layouts.admin')

@section('title', 'Reservation List')
@section('content-header', 'Reservation List')
@section('content-actions')
    @hasPermission(config('constants.role_modules.pos_management.value'))
        <a href="{{ route('cart.index') }}" class="btn btn-success">Open POS</a>
    @endHasPermission
@endsection


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
    </style>

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
    </header>
    <div
        class="table-actions d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between pe-2 pt-md-3 pt-2">
        <div class="d-flex align-items-center action__links">
            <a href="javascript:void(0)" id="check-available" class="border text-decoration-none p-2 text-dark">
                <img src="{{ asset('images/add-ico.svg') }}" alt="" class="me-2" />
                Multiple Sites
            </a>
            {{-- <a href="#" class="border text-decoration-none p-2 text-dark">
                <img src="{{ asset('images/search-ico.svg') }}" alt="" class="me-2" />
                Search
            </a> --}}
            {{-- <a href="javascript:void(0)" onclick="openReservationModal()" class="border text-decoration-none p-2 text-dark">
                <img src="{{ asset('images/track-ico.svg') }}" alt="" class="me-2" />
                Arrivals & Departures
            </a> --}}
            {{-- <a href="javascript:void(0)" onclick="openReservationSiteModal()"
                class="border text-decoration-none p-2 text-dark">
                <img src="{{ asset('images/return-ico.svg') }}" alt="" class="me-2" />
                Relocate
            </a> --}}
            {{-- <a href="#" class="border text-decoration-none p-2 text-dark">
                <img src="{{ asset('images/drop-ico.svg') }}" alt="" class="me-2" />
                Theme
            </a> --}}
        </div>



    </div>
    <input type="hidden" id="initialSiteCount" value="20">
    <input type="hidden" id="totalSites" value="{{ $sites->count() }}">
    <div class="text-center my-2 position-sticky bottom-0 z-3" style="background: white;">
        <button id="loadMoreSitesBtn" class="btn btn-secondary d-none">
            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
            Load More Sites
        </button>
    </div>


    <div class="table-responsive" style="max-height: 60vh !important; max-width: 100%">
        <table class="table management-table table-striped">

            <thead>
                <tr class="t__head">

                    <th class="sticky-col  bg-dark text-white" rowspan="2">Site</th>
                    <th class="sticky-col bg-dark text-white" rowspan="2">Type</th>
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
            <tbody>
                @foreach ($sites as $index => $site)
                    <tr @if ($index >= 20) style="display: none" @endif>
                        <td class="sticky-col bg__sky">{{ $site->siteid }}</td>
                        <td class="sticky-col bg__sky">{{ $site->ratetier }}</td>

                        @php
                            $calendarCount = count($calendar);
                            $i = 0;
                        @endphp

                        @while ($i < $calendarCount)
                            @php
                                $currentDate = $calendar[$i];
                                $highlightToday = $currentDate == now()->format('Y-m-d') ? 'border border-warning' : '';

                                $reservationFound = null;
                                foreach ($site->reservations as $reservation) {
                                    $resStart = \Carbon\Carbon::parse($reservation->cid)->format('Y-m-d');
                                    $resEnd = \Carbon\Carbon::parse($reservation->cod)->format('Y-m-d');

                                    if ($currentDate >= $resStart && $currentDate < $resEnd) {
                                        $reservationFound = $reservation;
                                        break;
                                    }
                                }
                            @endphp

                            @if ($reservationFound)
                                @php
                                    $resStart = \Carbon\Carbon::parse($reservationFound->cid);
                                    $resEnd = \Carbon\Carbon::parse($reservationFound->cod);
                                    $reservationColSpan = $resStart->diffInDays($resEnd);
                                    $i += $reservationColSpan;
                                @endphp
                                <td colspan="{{ $reservationColSpan }}" style="cursor: pointer"
                                    class="reservation-details rounded text-center bg-success text-white {{ $highlightToday }}"
                                    data-reservation-id="{{ $reservationFound->id }}"
                                    data-start-date="{{ $reservationFound->cid }}"
                                    data-end-date="{{ $reservationFound->cod }}">
                                    {{ $reservationFound->user->f_name ?? 'Guest' }}
                                </td>
                            @else
                                @php
                                    $availableColSpan = 0;
                                    $startIndex = $i;
                                    $today = \Carbon\Carbon::today();

                                    if (\Carbon\Carbon::parse($calendar[$i])->lt($today)) {
                                        $i++;
                                        continue;
                                    }

                                    while (
                                        $i + $availableColSpan < $calendarCount &&
                                        \Carbon\Carbon::parse($calendar[$i + $availableColSpan])->gte($today) &&
                                        !$site->reservations->some(function ($r) use (
                                            $calendar,
                                            $i,
                                            $availableColSpan,
                                        ) {
                                            $checkDate = $calendar[$i + $availableColSpan];
                                            $resStart = \Carbon\Carbon::parse($r->cid)->format('Y-m-d');
                                            $resEnd = \Carbon\Carbon::parse($r->cod)->format('Y-m-d');
                                            return $checkDate >= $resStart && $checkDate < $resEnd;
                                        })
                                    ) {
                                        $availableColSpan++;
                                    }

                                    $i += $availableColSpan;
                                @endphp

                                @if ($availableColSpan > 0)
                                    <td colspan="{{ $availableColSpan }}"
                                        class="text-center bg-info text-white {{ $highlightToday }}" style="opacity: 50%">
                                        Available
                                    </td>
                                @endif
                            @endif
                        @endwhile
                    </tr>
                @endforeach
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
                <input type="date" id="checkout" class="form-control" value="{{ now()->addDay()->format('Y-m-d') }}"
                    onchange="calculateNights()">
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
    <script>
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
            let isAvailableHidden = $('#ganttTableAvailable').is(':hidden');

            if (isAvailableHidden) {
                checkAvailable({!! $sites->toJson() !!});
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

            // Verify property names first, then adjust accordingly
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

        function showFilteredCards(selectedSiteIds, allSites, showAll = false) {
            let filteredSites = showAll ?
                allSites :
                allSites.filter(site => selectedSiteIds.includes(String(site.id)));

            let cardHtml = filteredSites.map(site => `
                <div class="col-md-3">
                    <div class="card site-card" data-id="${site.id}" data-price="${site.price || 100}">
                        <img src="${site.image || "https://storage.googleapis.com/proudcity/mebanenc/uploads/2021/03/placeholder-image-300x225.png"}"
                            class="card-img-top" alt="Site Image">
                        <div class="card-body text-center">
                            <h5 class="card-title">${site.siteid}</h5>
                            <p class="card-text">${site.siteclass}</p>
                        </div>
                    </div>
                </div>
            `).join('');

            document.getElementById("ganttTableAvailable").innerHTML = `<div class="row">${cardHtml}</div>`;

            document.querySelectorAll('.site-card').forEach(card => {
                card.addEventListener('click', function() {
                    toggleSite(this);
                });
            });
        };


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
            const $tableWrapper = $('.table-responsive');
            const $loadMoreSitesBtn = $('#loadMoreSitesBtn');
            const initialCount = parseInt($('#initialSiteCount').val());
            const totalCount = parseInt($('#totalSites').val());
            let visibleCount = initialCount;

            const rows = $('.management-table tbody tr');

            // Initially show only first 20 rows
            rows.each(function(index, row) {
                if (index < visibleCount) {
                    $(row).show();
                }
            });

            // Show button when scrolled to bottom
            $tableWrapper.on('scroll', function() {
                const scrollTop = $tableWrapper.scrollTop();
                const scrollHeight = $tableWrapper[0].scrollHeight;
                const clientHeight = $tableWrapper[0].clientHeight;

                if (scrollTop + clientHeight >= scrollHeight - 5 && visibleCount < totalCount) {
                    $loadMoreSitesBtn.removeClass('d-none');
                } else {
                    $loadMoreSitesBtn.addClass('d-none');
                }
            });

            // On "Load More" click
            $loadMoreSitesBtn.on('click', function() {
                const $spinner = $(this).find('.spinner-border');
                $(this).attr('disabled', true);
                $spinner.removeClass('d-none');

                setTimeout(function() {
                    visibleCount += 20;

                    rows.each(function(index, row) {
                        if (index < visibleCount) {
                            $(row).show();
                        }
                    });

                    $spinner.addClass('d-none');
                    $loadMoreSitesBtn.removeAttr('disabled');

                    if (visibleCount >= totalCount) {
                        $loadMoreSitesBtn.hide();
                    }
                }, 500);
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

        function searchSites() {
            $('.btn--apply').attr('disabled', true);
            let siteNames = $('#sitename').val();
            let siteClasses = $('#siteclass').val();
            const currentUrl = window.location.href;
            const url = new URL(currentUrl);
            const searchParams = url.searchParams;
            searchParams.set('site_names', siteNames.length > 0 ? siteNames : '');
            searchParams.set('site_classes', siteClasses.length > 0 ? siteClasses : '');
            const getUrl = `${window.location.pathname}?${searchParams.toString()}`;
            window.location.href = getUrl;

        }

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
