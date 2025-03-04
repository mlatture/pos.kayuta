@extends('layouts.admin')

@section('title', 'Reservation List')
@section('content-header', 'Reservation List')

@section('content')


    @include('reservations.components.header')

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;

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

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table thead th {
            background-color: #343A40;
            color: #EFC368;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        .form-select {
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .pagination-links {
            margin-top: 1rem;
        }

        .pagination-links a {
            background: #fff;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination-links a:hover {
            background: #007bff;
            color: white;
        }

        @media (max-width: 768px) {
            .collapse {
                margin-bottom: 1rem;

            }
        }

        .header {
            background-color: #343a40;
            padding: 1rem;
        }

        .header a {
            color: #EFC368;
            margin-right: 20px;
        }

        .header a:hover {
            color: #fff;

        }

        .btn-success {
            background-color: #28a745;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-success:hover {
            background-color: #218838;

        }

        .gantt .grid-header {
            fill: #2E7D32 !important;
            stroke: #e0e0e0;
            stroke-width: 1.4;
        }

        .gantt .grid-header text {
            fill: #ffffff !important;
            font-size: 14px;
            font-weight: bold;
        }

        .gantt .date text {
            fill: #ffffff !important;
            font-size: 12px;
            font-weight: bold;
        }

        .gantt .grid-row {
            fill: #4A4A4A !important;
        }

        .gantt .grid-row:nth-child(even) {
            fill: #5a5a5a !important;
        }

        .gantt .bar-progress {
            fill: #2E7D32 !important;
            stroke: #ffffff;

            stroke-width: 1;
            rx: 4;
            ry: 4;
        }

        .gantt .bar-label {
            fill: #ffffff !important;
            font-size: 14px;
            font-weight: bold;
            text-anchor: middle;
            dominant-baseline: central;
        }



        .gantt .unavailable-bar {
            fill: #D32F2F !important;
            opacity: 0.7;
            stroke: #B71C1C;
            stroke-width: 1;
            stroke-dasharray: 5;
        }

        .gantt .date {
            position: sticky;
            top: 0;
            background: #37474F !important;
            z-index: 2;
        }

        .gantt-container {
            display: flex;
            flex-direction: row;
            gap: 15px;
        }

        .gantt-table {
            border-collapse: collapse;
            width: 350px;
            font-size: 14px;
        }

        .gantt-table th,
        .gantt-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .gantt-table th {
            background-color: #455a64;
            color: white;
        }



        .gantt .bar-progress {
            fill: #4CAF50 !important;
            opacity: 0.85;
        }


        .gantt .bar:hover {
            filter: brightness(1.2);
        }

        .gantt-tooltip {
            position: absolute;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 8px;
            border-radius: 4px;
            font-size: 12px;
            display: none;
            pointer-events: none;
            z-index: 1000;
        }

        #ganttTableAvailable,
        #ganttReservationsAvailable {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
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
    </style>


    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark">
                        <h5 class="card-title mb-0" style="color: #EFC368 !important">Confirmed Customers</h5>
                        <div
                            class="d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-end px-md-3 px-2">
                            <div class="d-flex align-items-center gap-3">
                                <a href="javacript:void(0)" class="text-white" aria-expanded="false" id="check-available"
                                    style="color: #EFC368 !important; text-decoration: none !important"><i
                                        class="fa-solid fa-border-all"></i> Available</a>

                                {{-- <a href="javacript:void(0)" class="text-white" data-bs-toggle="collapse"
                                    data-bs-target="#collapsePlanner" aria-expanded="false" aria-controls="collapsePlanner"
                                    style="color: #EFC368 !important; text-decoration: none !important"><i
                                        class="fa-solid fa-border-all"></i> Planner</a> --}}
                                <a href="javacript:void(0)" class="text-white" data-bs-toggle="collapse"
                                    data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample"
                                    style="color: #EFC368 !important; text-decoration: none !important"><i
                                        class="fa-solid fa-users"></i> Add Customer</a>
                                <a href="javascript:void(0)" class="text-white" id="openDatePicker" role="button"
                                    style="color: #EFC368 !important; text-decoration: none !important">
                                    <i class="fa-solid fa-calendar-week"></i> Quick Add
                                </a>

                            </div>
                            <!-- <div>
                                                                                                                                                            <a href="#" class="text-white text-decoration-none">
                                                                                                                                                                <img src="{{ asset('images/help-ico.svg') }}" alt="" class="me-2" />
                                                                                                                                                                Help
                                                                                                                                                            </a>
                                                                                                                                                        </div> -->
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="gantt-container" style="max-height: 600px;">
                            <div id="ganttTable"></div>
                            <div id="ganttReservations"></div>




                        </div>

                        <div class="container">
                            <!-- Date Filters -->
                            <div class="filter-container row mb-3" hidden>
                                <div class="col-md-4">
                                    <label for="checkin">Check-in:</label>
                                    <input type="date" id="checkin" class="form-control"
                                        value="{{ now()->format('Y-m-d') }}" onchange="calculateNights()">
                                </div>
                                <div class="col-md-4">
                                    <label for="checkout">Check-out:</label>
                                    <input type="date" id="checkout" class="form-control"
                                        value="{{ now()->addDay()->format('Y-m-d') }}" onchange="calculateNights()">
                                </div>
                                <div class="col-md-4">
                                    <span id="nights" class="form-control mt-4">Nights: 1</span>
                                </div>
                                <button id="quoteButton" hidden onclick="quoteSites()" class="btn btn-primary mt-3">Get
                                    Quote</button>

                            </div>

                            <div id="ganttTableAvailable" class="row"></div>

                            <!-- Quote Modal -->
                            <div class="modal fade" id="quoteModal" tabindex="-1" aria-labelledby="quoteModalLabel"
                                aria-hidden="true">
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
                                            <button type="button" id="createReservation" class="btn btn-primary">Create
                                                Reservation</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>





                    </div>
                </div>
            </div>


        </div>
    </div>
    @extends('reservations.modals.modals')
    @extends('reservations.modals.reservations-modal')
@endsection

@push('js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script src="{{ asset('js/reservations/retrievedata.js') }}"></script>
    <script src="{{ asset('js/reservations/reservationmodal.js') }}"></script>

    <script>
        const webdavinci_api = "{{ config('app.webdavinci_api') }}";
        const webdavinci_api_key = "{{ config('app.webdavinci_api_key') }}";

        let today = new Date().toISOString().split('T')[0];
        let tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow = tomorrow.toISOString().split('T')[0];

        document.getElementById("checkin").value = today;
        document.getElementById("checkout").value = tomorrow;

        let selectedSites = [];
        let nightsCounts = 1;

        function checkAvailable(data) {
            $('#ganttTableAvailable').attr('hidden', false);
            console.log('Check available data', data);

            let data_current_sites = data.allCurrentSites || [];

            if (!Array.isArray(data_current_sites) || data_current_sites.length === 0) {
                console.error('data_current_sites is empty or not an array.');
                return;
            }

            // Show filter container
            $('.filter-container').attr('hidden', false);

            let cardHtml = '';
            let uniqueIds = new Set(); // ✅ Track unique site IDs

            data_current_sites.forEach((site) => {
                let siteId = `${site.id}`;

                if (uniqueIds.has(siteId)) return;
                uniqueIds.add(siteId); // ✅ Add ID to the Set

                let imageUrl = site.image ||
                    "https://storage.googleapis.com/proudcity/mebanenc/uploads/2021/03/placeholder-image-300x225.png";

                cardHtml += `
                    <div class="col-md-3">
                        <div class="card site-card" data-id="${siteId}" data-price="${site.price || 100}">
                            <img src="${imageUrl}" class="card-img-top" alt="Site Image">
                            <div class="card-body text-center">
                                <h5 class="card-title">${site.siteid || "Unknown"}</h5>
                                <p class="card-text">${site.siteclass || "Unknown"}</p>
                            </div>
                        </div>
                    </div>
                `;
            });

            document.getElementById("ganttTableAvailable").innerHTML = `
                <div class="row">${cardHtml}</div>
            `;

            document.querySelectorAll('.site-card').forEach(card => {
                card.addEventListener('click', function() {
                    toggleSite(this);
                });
            });
        }

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
                nightsCounts = timeDiff / (1000 * 3600 * 24); // ✅ Update global variable

                $("#nights").text(`Nights: ${nightsCounts}`);
                console.log(`Global Nights Count Updated: ${nightsCounts}`); // ✅ Debugging
            }
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
                                window.location.href = "{{ route('reservations.payment.index', ['confirmationNumber' => ':confirmationNumber']) }}".replace(':confirmationNumber', response_reservation.confirmationNumber);
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



        function fetchReservations() {
            $.ajax({
                url: "reservepeople",
                type: "GET",
                success: function(data) {
                    $('#check-available').on('click', function() {
                        checkAvailable(data);

                        // Ensure one set is visible while the other is hidden
                        $('#ganttTable, #ganttReservations').toggle();
                        $('#ganttTableAvailable, #ganttReservationsAvailable').toggle();
                    });




                    let data_reservations = data.allReservations || [];
                    let data_payments = data.payments || [];
                    let data_customers = data.customers || [];
                    if (!Array.isArray(data_reservations) || data_reservations.length === 0) {
                        console.error("No reservations found.");
                        return;
                    }


                    let minDate = null;
                    let maxDate = null;
                    let tasks = [];
                    let tableRows = [];

                    data_reservations.forEach((item, index) => {
                        if (!item.cid || !item.cod) {
                            console.warn("Skipping reservation due to missing CID or COD:", item);
                            return;
                        }

                        let startDate = new Date(item.cid);
                        let endDate = new Date(item.cod);

                        if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                            console.warn("Skipping reservation due to invalid dates:", {
                                cid: item.cid,
                                cod: item.cod
                            });
                            return;
                        }

                        if (endDate <= startDate) {
                            console.warn(
                                "End date is before start date. Swapping dates for reservation:",
                                item.id);
                            [startDate, endDate] = [endDate, startDate];
                        }

                        if (!minDate || startDate < minDate) minDate = startDate;
                        if (!maxDate || endDate > maxDate) maxDate = endDate;

                        tasks.push({
                            id: `Reservation-${item.id || "Unknown"}`,
                            name: `${item.fname} ${item.lname} - ${item.siteclass || "Unknown"} - ${item.siteid || "Unknown"}`,
                            start: startDate.toISOString().split("T")[0],
                            end: endDate.toISOString().split("T")[0],
                            progress: Math.floor(Math.random() * 100),
                            dependencies: "",
                            custom_class: index % 2 === 0 ? "gantt-bar-even" : "gantt-bar-odd"
                        });

                        // Find payment for the current reservation
                        let paymentRecord = data_payments.find(payment => payment.cartid === item
                            .cartid);
                        let paymentBalance = paymentRecord ? parseFloat(paymentRecord.payment) || 0 : 0;

                        let balances = parseFloat(item.total) - paymentBalance;
                        let balance;
                        let balanceClass;

                        if (!isNaN(balances) && balances <= 0) {
                            balance = "Paid";
                            balanceClass = "bg-success text-white";
                        } else if (!isNaN(balances)) {
                            balance = '$' + balances.toFixed(2);
                            balanceClass = "bg-danger text-white";
                        } else {
                            balance = "Invalid Amount";
                            balanceClass = "bg-warning text-dark";
                        }

                        let customerRecord = data_customers.find(customer => String(customer.id) ===
                            String(item.customernumber)) || {
                            email: "N/A",
                            phone: "N/A"
                        };
                        if (!Array.isArray(data_customers) || data_customers.length === 0) {
                            console.error("data_customers is empty or not an array.");
                        }

                        let arrivalDate = item.cid ? new Date(item.cid).toLocaleDateString("en-US", {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        }) : "N/A";
                        let departureDate = item.cod ? new Date(item.cod).toLocaleDateString("en-US", {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        }) : "N/A";


                        let checked_in_date;
                        let checked_out_date;
                        if (item.checkedin === null) {
                            checked_in_date =
                                `<input type="checkbox" name="cartid" value="${item.cartid}" class="checked_in_date">`;
                        } else {
                            checked_in_date = item.checkedin;
                        }

                        if (item.checkedout === null) {
                            checked_out_date =
                                `<input type="checkbox" name="cartid" value="${item.cartid}" class="checked_out_date">`;
                        } else {
                            checked_out_date = item.checkedout;
                        }

                        tableRows.push(`
                            <tr class="collapse-btn" onclick="toggleCollapse('${item.cartid}')" style="cursor: pointer;">
                                <td>${item.siteid || "Unknown"}</td>
                                <td>${item.fname} ${item.lname}</td>
                                <td>${startDate.toISOString().split("T")[0]} - ${endDate.toISOString().split("T")[0]}</td>

                            </tr>
                            <tr id="collapseReservationDetails${item.cartid}" class="collapse">
                                <td colspan="3">
                                    <div class="card card-body">
                                        <h5 class="mb-3">Reservation Details</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Customer:</strong> ${item.fname} ${item.lname}</p>
                                                <p><strong>Arrival:</strong> ${arrivalDate || "N/A"}</p>
                                                <p><strong>Departure:</strong> ${departureDate || "N/A"}</p>
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
                                                <p><strong>Total:</strong> $${parseFloat(item.total).toFixed(2) || "N/A"}</p>
                                                <p><strong>Balance:</strong> 
                                                    <span class="badge ${balanceClass} rounded-pill">${balance}</span>
                                                </p>
                                            </div>
                                        </div>
                        
                                        <hr>
                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Checked In:</strong> 
                                                    ${item.checkedin ? item.checkedin : `<input type="checkbox" name="cartid" value="${item.cartid}" class="checked_in_date" onclick="handleCheckIn(this, '${item.cartid}'); event.stopPropagation();">`}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Checked Out:</strong> 
                                                    ${item.checkedout ? item.checkedout : `<input type="checkbox" name="cartid" value="${item.cartid}" class="checked_out_date" onclick="handleCheckOut(this, '${item.cartid}'); event.stopPropagation();">`}
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
                                                            <i class="fa-solid fa-calendar"></i> Reschedule /
                                                            <i class="fa-solid fa-location-arrow"></i> Relocate
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
                                </td>
                            </tr>
                        `);


                    });

                    if (tasks.length === 0) {
                        console.error("No valid tasks for Gantt chart.");
                        return;
                    }

                    if (!minDate || !maxDate) {
                        console.error("Could not determine valid date range for Gantt chart.");
                        return;
                    }

                    let minDateString = minDate.toISOString().split("T")[0];
                    let maxDateString = maxDate.toISOString().split("T")[0];

                    console.log("Gantt Start Date:", minDateString);
                    console.log("Gantt End Date:", maxDateString);

                    document.getElementById("ganttTable").innerHTML = `
                        <table class="gantt-table">
                            <thead>
                                <tr>
                                    <th>Site ID</th>
                                    <th>Customer</th>
                                    <th>Dates</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows.join("")}
                            </tbody>
                        </table>
                    `;

                    var gantt = new Gantt("#ganttReservations", tasks, {
                        view_mode: "Day",
                        language: "en",
                        container_height: "auto",
                        column_width: 50,
                        bar_height: 35,
                        bar_corner_radius: 8,
                        arrow_curve: 5,
                        upper_header_height: 45,
                        lower_header_height: 30,
                        padding: 15,
                        lines: "both",
                        snap_at: "1d",
                        infinite_padding: false,
                        move_dependencies: false,
                        readonly: true,
                        today_button: true,
                        scroll_to: minDateString,
                        view_mode_select: true,
                        popup: function(task) {
                            return `
                                <div class="gantt-tooltip">
                                    <strong>${task.name}</strong><br>
                                    <small>Start: ${task.start}</small><br>
                                    <small>End: ${task.end}</small><br>
                                    <small>Progress: ${task.progress}%</small>
                                </div>`;
                        }
                    });

                    setTimeout(() => {
                        let daysBetween = (maxDate - minDate) / (1000 * 60 * 60 * 24);
                        let newWidth = Math.max(1200, daysBetween * 50) + "px";
                        document.querySelector("#ganttReservations svg").style.minWidth = newWidth;
                    }, 500);

                    console.log("Gantt Chart Rendered Successfully:", gantt);
                },
                error: function(error) {
                    console.log("Error:", error);
                }
            });
        }

        fetchReservations();    
    </script>
@endpush
