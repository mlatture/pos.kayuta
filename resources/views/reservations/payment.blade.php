@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @push('css')
        <style>
            .invoice-header {
                text-align: center;
                margin-bottom: 10px;
            }

            .invoice-header h4 {
                font-size: 24px;
                font-weight: bold;
                margin: 0;
            }

            .container-invoice {
                background-color: #77898d;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 10px;
            }

            .container-gantt {
                margin-top: 10px;
                background-color: #77898d;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                height: auto;
            }

            .table-invoice {
                width: 100%;
                border-collapse: collapse;
            }

            .table-invoice th,
            .table-invoice td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

            .table-invoice th {
                background-color: #f4f4f4;
                font-weight: bold;
            }

            .table-invoice tbody tr:hover {
                background-color: #f1f1f1;
            }

            .total-row {
                background-color: #f4f4f4;
                font-weight: bold;
            }

            .total-row td {
                border: none;
            }

            .total-row .text-end {
                text-align: right;
            }

            .fw-bold {
                font-weight: bold;
            }

            .invoice-footer {
                text-align: right;
                margin-top: 20px;
            }

            .gantt .bar-progress {
                fill: #4CAF50 !important;
            }

            .gantt .grid-background {
                padding: 10px !important;
            }

            .gantt .grid-header {
                fill: #77898d !important;
                stroke: #e0e0e0;
                stroke-width: 1.4;
            }
        </style>
    @endpush
    @php
        use Illuminate\Support\Facades\Request;
    @endphp
    <nav class="main-header navbar navbar-expand  d-flex justify-content-between sticky-top "
        style="background-color: #77898d; width: 30%;   border-bottom-left-radius: 5px; ">


        <ul class="navbar-nav ms-auto  d-flex flex-row">

            <div class="buttons-pymnt d-flex gap-2 float-end mt-2 ">

                {{-- <button type="button" class="btn btn border-info btn-sm float-end text-white" id="lookup-history">
                    <i class="fa-solid fa-book"></i> History
                </button> --}}
                <button type="button" class="btn btn border-danger btn-sm float-end text-white" id="cancel-reservation">
                    <i class="fa-solid fa-ban"></i> Cancel
                </button>

                <button type="button" class="btn btn border-warning btn-sm float-end text-white" id="add-to-cart">
                    <i class="fa-solid fa-store"></i> Add To Cart
                </button>

                <button type="button" class="btn btn border-success btn-sm float-end text-white" id="proceed-payment">
                    <i class="fa-solid fa-money-bill-transfer"></i> Payment
                </button>


            </div>
        </ul>
    </nav>
    <div class="overflow-auto ">


        <div class="container-invoice" id="reservation-form">
            <h4>Reservation</h4>

            <div class="row g-3">
                <div class="col-md-3">
                    <label for="Source" class="form-label">Source</label>
                    <select name="Source" id="Source" class="form-select">
                        <option value="Phone">Phone</option>
                        <option value="Office">Office</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="Source" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="Prepaid">Prepaid</option>
                        <option value="Not Confirmed">Not Confirmed</option>
                        <option value="Confirmed">Confirmed</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="createdon" class="form-label">Created On</label>
                    <input type="text" name="createdon" id="createdon" class="form-control"
                        value="{{ $reservations->first()->created_at ?? '' }}" readonly>
                </div>

                <div class="col-md-3">
                    <label for="confirmation" class="form-label">Confirmation #</label>
                    <input type="text" name="confirmation" id="confirmation" class="form-control"
                        value="{{ $reservations->first()->cartid ?? '' }}" readonly>
                </div>

                <div class="col-md-3">
                    <label for="createdby" class="form-label">Created By</label>
                    <input type="text" name="createdby" id="createdby" class="form-control"
                        value="{{ ucfirst(auth()->user()->name) }}" readonly>
                </div>
            </div>

        </div>


        <div class="container-invoice mt-3" id="gantt-container">
            <h4>Sites</h4>
            <!-- Gantt Chart Section -->
            <svg id="gantt" class="m-2"> </svg>
        </div>
        <div class="container-invoice mt-3" id="guests-form">
            <div class="row g-3">
                <!-- Guests and Vehicles Section -->
                <div class="col-md-4">
                    <div class="border rounded shadow-sm p-3">
                        <h4 class="mb-3">Guests and Vehicles</h4>
                        <div class="row g-2">
                            <div class="col-12">
                                <label for="guests" class="form-label fw-semibold">Number of Guests</label>
                                <input type="number" name="guests" id="guests" class="form-control"
                                    value="{{ $reservations->first()->number_of_guests ?? '' }}" min="0">
                            </div>
                            <div class="col-12">
                                <label for="vehicles" class="form-label fw-semibold">Number of Vehicles</label>
                                <input type="number" name="vehicles" id="vehicles" class="form-control"
                                    value="{{ $reservations->first()->vehicles ?? '' }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                @if (!Request::is('admin/reservations/invoice/*'))
                    <!-- Rig Section -->
                    <div class="col-md-4" id="rig-form">
                        <div class="border rounded shadow-sm p-3">
                            <h4 class="mb-3">Rig</h4>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label for="rigtype" class="form-label fw-semibold">Rig Type</label>
                                    <select name="rigtype" id="rigtype" class="form-select">
                                        @foreach ($rigTypes as $rigType)
                                            <option value="{{ $rigType->id }}">{{ $rigType->rigtype }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="length" class="form-label fw-semibold">Length</label>
                                    <input type="number" name="length" id="length" class="form-control"
                                        value="{{ $reservations->first()->length ?? '' }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-md-4">
                    <div class="border rounded shadow-sm p-3">
                        <label for="siteLock" class="form-label fw-semibold">Site Lock Fee ($20)</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="siteLock"
                                name="siteLock" {{ $reservations->first()->siteLock ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="container-invoice mt-3" id="customer-form">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="border rounded shadow-sm p-3">
                        <h4 class="mb-3">Customer Info</h4>

                        <div id="customer-info-container">
                            <div class="customer-info-entry border rounded p-3 mb-3">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <label for="first_name" class="form-label fw-semibold">First Name *</label>
                                        <input type="text" name="first_name[]" class="form-control" required>
                                    </div>
                                    <div class="col-4">
                                        <label for="last_name" class="form-label fw-semibold">Last Name *</label>
                                        <input type="text" name="last_name[]" class="form-control" required>
                                    </div>
                                    <div class="col-4">
                                        <label for="email" class="form-label fw-semibold">Email *</label>
                                        <input type="email" name="email[]" class="form-control" required>
                                    </div>
                                    <div class="col-4">
                                        <label for="discovery_method" class="form-label fw-semibold">Discovery Method
                                            *</label>
                                        <select name="discovery_method[]" class="form-select" required>
                                            <option value="">Select Method</option>
                                            <option value="Online">Online</option>
                                            <option value="Referral">Referral</option>
                                            <option value="Advertisement">Advertisement</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <!-- Optional Fields -->
                                    <div class="col-4">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" name="phone[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="work_phone" class="form-label">Work Phone</label>
                                        <input type="text" name="work_phone[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="home_phone" class="form-label">Home Phone</label>
                                        <input type="text" name="home_phone[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="customer_number" class="form-label">Customer Number</label>
                                        <input type="text" name="customer_number[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="driving_license" class="form-label">Driving License</label>
                                        <input type="text" name="driving_license[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                        <input type="date" name="date_of_birth[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="anniversary" class="form-label">Anniversary</label>
                                        <input type="date" name="anniversary[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="age" class="form-label">Age</label>
                                        <input type="number" name="age[]" class="form-control" min="0">
                                    </div>
                                    <div class="col-4">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" name="address[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="address_2" class="form-label">Address 2</label>
                                        <input type="text" name="address_2[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="address_3" class="form-label">Address 3</label>
                                        <input type="text" name="address_3[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" name="city[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" name="state[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="zip" class="form-label">Zip Code</label>
                                        <input type="text" name="zip[]" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" name="country[]" class="form-control">
                                    </div>

                                    <input type="text" name="user_id[]" hidden>
                                </div>

                                <button type="button" class="btn btn-danger btn-sm mt-2 remove-customer">Remove</button>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary btn-sm mt-3" id="add-customer-info">+ Add More
                            Customer Info</button>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="border rounded shadow-sm p-3">
                        <label for="customerSearch" class="form-label fw-semibold">Customer Lookup</label>
                        <input type="text" id="customerSearch" class="form-control"
                            placeholder="Search by name or email...">

                        <!-- Customer Results Table (Hidden by Default) -->
                        <div class="table-responsive mt-2">
                            <table class="table table-bordered table-hover" id="customerResults" style="display: none;">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </div>
        </div>






        <div class="container-invoice mt-3" id="invoice-table" hidden>

            <header class="invoice-header">
                <h4>Invoice</h4>

            </header>

            <div class="table-responsive">
                <table class="table table-bordered table-invoice">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Site</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $reservation)
                            <tr class="">
                                <td>
                                    @if ($reservation)
                                        {{ date('D, M d', strtotime($reservation->cid)) }} -
                                        {{ date('D, M d', strtotime($reservation->cod)) }}
                                    @else
                                        <span class="text-danger">Reservation not found</span>
                                    @endif
                                </td>
                                <td id="site_id" data-siteid='@json($reservations->pluck('siteid')->toArray())'>{{ $reservation->siteid }}
                                </td>
                                <td>{{ $reservation->siteclass }}</td>

                                @if (Request::is('admin/reservations/invoice/*'))
                                    <td>{{ $cart->description }}</td>
                                @else
                                    <td>{{ $reservation->description }}</td>
                                @endif
                                <td>${{ number_format($reservation->base, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="3"></td>
                            <td class="text-end">Subtotal</td>
                            @php
                                $subtotal = 0;
                                foreach ($reservations as $reservation) {
                                    $subtotal += $reservation->base;
                                }
                            @endphp
                            <td id="subtotal">${{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="1"></td>
                            <td>Tax</td>
                            <td></td>
                            <td>Non Taxable</td>
                            <td>$0</td>
                        </tr>
                        {{-- <tr class="total-row">
                            <td colspan="1"></td>
                            <td>Tax</td>
                            <td></td>
                            <td>Total Tax</td>
                            <td>${{ number_format($reservation->totaltax, 2) }}</td>
                        </tr> --}}
                        <tr class="total-row">
                            <td colspan="3"></td>
                            <td class="text-end">Total Payments</td>
                            <td>${{ number_format($subtotal, 2) }}</td>
                        </tr>

                        @php
                        $balance = $reservation->total - ($reservation->payment->payment ?? 0);
                    @endphp
                                            <tr class="total-row">
                            <td colspan="4"></td>
                            <td class="text-end">Balance </td>
                            <td>${{ number_format($balance, 2) }}</td>
                        </tr>

                    </tbody>
                </table>


            </div>

            <form id="paymentchoices" method="POST" id="payment-form">
                <header class="invoice-header mt-4">
                    <h4>Payments</h4>
                </header>
                <div class="container-invoice">
                    <div class="form-row mb-3">
                        <div class="col">
                            <div class="form-group">
                                <label for="transactionType">Transaction Type</label>
                                <select name="transactionType" id="transactionType" class="form-control">
                                    <option value="Full" selected>Full Payment</option>
                                    <option value="Partial">Partial Payment</option>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <label for="paymentType">Payment Type</label>
                                <select name="paymentType" id="paymentType" class="form-control">
                                    <option value="" selected disabled>Select Payment Type</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Check">Check</option>
                                    <option value="Manual">Credit Card - Manual</option>
                                    <option value="Terminal">Credit Card</option>
                                    <option value="Gift Card">Gift Card</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="col">
                            <div class="form-group">
                                <label for="xAmount">Total Amount</label>
                                <input class="form-control" type="text" name="xAmount" id="xAmount"
                                    value="{{ number_format(Request::is('admin/reservations/invoice/*') ? $balance : $subtotal, 2) }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input class="form-control" type="text" name="description" id="description">
                            </div>
                        </div>
                    </div>

                    <div class="form-row mb-3" id="creditcard-manual" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" maxlength="16" name="xCardNum" id="xCardNum" required
                                    class="form-control" placeholder="Card Number">
                            </div>
                        </div>
                        <div class="col-md-6" id="xExpGroup">
                            <div class="form-group">
                                <input type="text" name="xExp" id="xExp" required class="form-control"
                                    placeholder="Expiration" maxlength="5">
                            </div>
                        </div>
                    </div>

                    <div class="form-row mb-3" id="checkDetails" hidden>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="xCash">Account Number</label>

                                <input type="text" name="xAccount" id="xAccount" required class="form-control"
                                    placeholder="Account Number">
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="xCash">Routing Number</label>

                                <input type="text" name="xRouting" id="xRouting" required class="form-control"
                                    placeholder="Routing Number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="xCash">Full Name</label>

                                <input type="text" name="xName" id="xName" required class="form-control"
                                    placeholder="Name">
                            </div>
                        </div>
                    </div>

                    <div class="form-row mb-3" id="gift-card" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="xBarcode" id="xBarcode" required class="form-control"
                                    placeholder="Barcode">
                            </div>
                            <div id="gift-card-message"></div>

                        </div>
                    </div>

                    <div class="form-row mb-3" id="cash" style="display: none;">
                        <div class="col">
                            <div class="form-group">
                                <label for="xCash">Amount Tendered</label>
                                <input type="number" name="xCash" id="xCash" required class="form-control"
                                    placeholder="Cash">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="xChange">Change Due</label>
                                <input type="text" id="xChange" readonly class="form-control"
                                    placeholder="Change Due">
                            </div>
                        </div>
                    </div>

                    <div class="form-row mb-3" id="creditcard-terminal" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h2>Start Terminal Transaction</h2>
                            </div>
                        </div>
                    </div>

                    <div id="loader" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Waiting for card insertion...</p>
                    </div>

                    <input type="hidden" name="cartid" id="cartid" value="{{ $reservation->cartid }}">
                    <input type="hidden" name="id" value="{{ $reservation->id }}">

                    <div class="form-row d-flex justify-content-end mr-1 gap-2">
                        {{-- @if (Request::is('admin/reservations/payment/*'))
                            <div class="btn btn-danger" id="addToCart">
                                <i class="fa-solid fa-cart-shopping"></i> Add To Cart
                            </div>
                        @endif --}}
                        <div class="btn btn-success"
                            id="{{ Request::is('admin/reservations/invoice/*') ? 'payBalance' : 'payBtn' }}">
                            <i class="fa-solid fa-money-bill-transfer"></i> Pay
                        </div>
                    </div>

                </div>

            </form>
        </div>

        {{-- @include('reservations.modals.history'); --}}


    </div>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>


    <script>
        // $('#lookup-history').on('click', function() {
        //     $('#historyModal').modal('show');
        //     let customerId = $(this).data('customer');
        //     let url = `/reservations/history/${customerId}`;

        //     if (!$.fn.DataTable.isDataTable('#reservation-history-table')) {
        //         $('#reservation-history-table').DataTable({
        //             processing: true,
        //             serverSide: true,
        //             ajax: url,
        //             columns: [{
        //                     data: 'cid',
        //                     name: 'cid'
        //                 },
        //                 {
        //                     data: 'cod',
        //                     name: 'cod'
        //                 },
        //                 {
        //                     data: 'siteid',
        //                     name: 'siteid'
        //                 },
        //                 {
        //                     data: 'cartid',
        //                     name: 'cartid'
        //                 },
        //                 {
        //                     data: 'status',
        //                     name: 'status'
        //                 } {
        //                     data: 'rigtype',
        //                     name: 'rigtype'
        //                 } {
        //                     data: 'riglength',
        //                     name: 'riglength'
        //                 }
        //             ]
        //         });
        //     }

        // });

        var checkGiftCart = "{{ route('check.gift-card') }}";
        var deleteAddToCart = "{{ route('reservations.delete.add-to-cart') }}";



        $(document).ready(function() {
            var tasks = [
                @foreach ($reservations as $reservation)
                    {
                        id: "Reservation-{{ $reservation->id }}",
                        name: "{{ $reservation->siteclass }} - {{ $reservation->siteid }}",
                        start: "{{ date('Y-m-d', strtotime($reservation->cid)) }}",
                        end: "{{ date('Y-m-d', strtotime($reservation->cod)) }}",
                        progress: 100,
                        custom_class: "gantt-bar-fixed"
                    }
                    @if (!$loop->last)
                        ,
                    @endif
                @endforeach
            ]

            var gantt = new Gantt("#gantt", tasks, {
                view_mode: "Day",
                language: "en",
                draggable: false
            });


            // Add more customer info

            $('#add-customer-info').on('click', function(e) {
                e.preventDefault();

                let container = $('#customer-info-container');
                let newEntry = container.children('.customer-info-entry').first().clone();

                newEntry.find('input').val('');
                newEntry.find('select').prop('selectedIndex', 0);

                container.append(newEntry);
            });

            $(document).on('click', '.remove-customer', function(e) {
                e.preventDefault();

                if ($('.customer-info-entry').length > 1) {
                    $(this).closest('.customer-info-entry').remove();
                }
            });


        });

        let selectedContainer = null;

        // Detect when a customer-info-entry is focused
        $(document).on('focusin', '.customer-info-entry input', function() {
            selectedContainer = $(this).closest('.customer-info-entry');
        });

        // Customer search event
        $('#customerSearch').on('keyup', function() {
            let query = $(this).val().trim();
            let resultsTable = $('#customerResults tbody');

            if (query.length === 0) {
                $('#customerResults').hide();
                resultsTable.empty();
                return;
            }

            $.ajax({
                url: '{{ route('reservations.lookup-customer') }}',
                method: 'GET',
                data: {
                    search: query
                },
                success: function(response) {
                    resultsTable.empty();

                    if (response.length > 0) {
                        $('#customerResults').show();
                        response.forEach(customer => {
                            resultsTable.append(`
                        <tr class="customer-row" data-customer='${JSON.stringify(customer)}'>
                            <td>${customer.f_name} ${customer.l_name}</td>
                            <td>${customer.email}</td>
                        </tr>
                    `);
                        });
                    } else {
                        $('#customerResults').hide();
                    }
                }
            });
        });

        $(document).on('click', '.customer-row', function() {
            let customerData = $(this).data('customer');

            if (selectedContainer) {
                selectedContainer.find('input[name="first_name[]"]').val(customerData.f_name);
                selectedContainer.find('input[name="last_name[]"]').val(customerData.l_name);
                selectedContainer.find('input[name="email[]"]').val(customerData.email);
                selectedContainer.find('input[name="phone[]"]').val(customerData.phone);
                selectedContainer.find('input[name="home_phone[]"]').val(customerData.home_phone);
                selectedContainer.find('input[name="work_phone[]"]').val(customerData.work_phone);
                selectedContainer.find('input[name="customer_number[]"]').val(customerData.id);
                selectedContainer.find('input[name="driving_license[]"]').val(customerData.driving_license);
                selectedContainer.find('input[name="date_of_birth[]"]').val(customerData.date_of_birth);
                selectedContainer.find('input[name="anniversary[]"]').val(customerData.anniversary);
                selectedContainer.find('input[name="age[]"]').val(customerData.age);
                selectedContainer.find('input[name="address[]"]').val(customerData.street_address);
                selectedContainer.find('input[name="address_2[]"]').val(customerData.address_2);
                selectedContainer.find('input[name="address_3[]"]').val(customerData.address_3);
                selectedContainer.find('input[name="city[]"]').val(customerData.city);
                selectedContainer.find('input[name="state[]"]').val(customerData.state);
                selectedContainer.find('input[name="zip[]"]').val(customerData.zip);
                selectedContainer.find('input[name="country[]"]').val(customerData.country);
                selectedContainer.find('input[name="user_id[]"]').val(customerData.id);
            }

            $('#customerResults').hide();
            $('#customerSearch').val('');
        });

        $('#proceed-payment').on('click', function(e) {
            e.preventDefault();

            let customerData = [];

            $('.customer-info-entry').each(function() {
                customerData.push({
                    first_name: $(this).find('input[name="first_name[]"]').val(),
                    last_name: $(this).find('input[name="last_name[]"]').val(),
                    email: $(this).find('input[name="email[]"]').val(),
                    discovery_method: $(this).find('select[name="discovery_method[]"]').val(),
                    phone: $(this).find('input[name="phone[]"]').val(),
                    work_phone: $(this).find('input[name="work_phone[]"]').val(),
                    home_phone: $(this).find('input[name="home_phone[]"]').val(),
                    customer_number: $(this).find('input[name="customer_number[]"]').val(),
                    driving_license: $(this).find('input[name="driving_license[]"]').val(),
                    date_of_birth: $(this).find('input[name="date_of_birth[]"]').val(),
                    anniversary: $(this).find('input[name="anniversary[]"]').val(),
                    age: $(this).find('input[name="age[]"]').val(),
                    address: $(this).find('input[name="address[]"]').val(),
                    address_2: $(this).find('input[name="address_2[]"]').val(),
                    address_3: $(this).find('input[name="address_3[]"]').val(),
                    city: $(this).find('input[name="city[]"]').val(),
                    state: $(this).find('input[name="state[]"]').val(),
                    zip: $(this).find('input[name="zip[]"]').val(),
                    country: $(this).find('input[name="country[]"]').val(),
                    user_id: $(this).find('input[name="user_id[]"]').val()
                })
            });

            let formData = {
                status: $('#status').val(),
                source: $('#Source').val(),
                created_on: $('#createdon').val(),
                confirmation: $('#confirmation').val(),
                created_by: $('#createdby').val(),
                adults: $('#adults').val(),
                vehicles: $('#vehicles').val(),
                number_of_guests: $('#guests').val(),
                subtotal: $('#subtotal').val(),
                rigtype: $('#rigtype').val(),
                length: $('#length').val(),
                site_lock: $('#siteLock').prop('checked') ? 20 : 0,
                customers: customerData
            }

            $.ajax({
                url: "{{ route('reservations.create-new-reservation') }}",
                type: 'POST',
                data: JSON.stringify(formData),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    if (response.success) {
                        let existingData = JSON.parse(localStorage.getItem('reservationData')) || [];

                        let newReservation = {
                            confirmation: $('#confirmation').val(),
                            reservation_success: true
                        };

                        if (!Array.isArray(existingData)) {
                            existingData = [];
                        }
                        existingData.push(newReservation);

                        localStorage.setItem('reservationData', JSON.stringify(existingData));

                        $('#invoice-table').attr('hidden', false);
                        $('#reservation-form').attr('hidden', true);
                        $('#gantt-container').attr('hidden', true);
                        $('#guests-form').attr('hidden', true);
                        $('#customer-form').attr('hidden', true);
                        $('#proceed-payment').attr('hidden', true);
                    } else {
                        toastr.error('Failed to create reservation');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred while creating the reservation');
                }
            })
        });


        $('#cancel-reservation').on('click', function(e) {
            e.preventDefault();

            let cnNo = $('#confirmation').val();
            let siteIds = $('#site_id').data('siteid');

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to cancel this reservation?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('cancel.reservation') }}",
                        type: 'POST',
                        data: {
                            confirmation: cnNo,
                            siteid: siteIds,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    showConfirmButton: true
                                }).then(() => {
                                    if (response.redirect) {
                                        window.location.href = response
                                            .redirect;
                                    } else {
                                        window.history
                                            .back();
                                        setTimeout(() => {
                                            location
                                                .reload();
                                        }, 500);
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while canceling the reservation'
                            });
                        }
                    });
                }
            });
        });
        $(document).ready(function() {
            let storedData = JSON.parse(localStorage.getItem('reservationData')) || [];

            let inputConfirmation = $('#confirmation').val();
            let matchedReservation = storedData.find(reservation => reservation.confirmation === inputConfirmation);

            if (matchedReservation && matchedReservation.reservation_success === true) {
                $('#invoice-table').attr('hidden', false);
                $('#payment-form').attr('hidden', false);
                $('#reservation-form').attr('hidden', true);
                $('#gantt-container').attr('hidden', true);
                $('#guests-form').attr('hidden', true);
                $('#customer-form').attr('hidden', true);
                $('#proceed-payment').attr('hidden', true);

            } else {
                console.log("No matching confirmation number found.");
            }
        });

        $('#add-to-cart').on('click', function(e) {
            e.preventDefault();

            window.location.href = "{{ route('reservations.reservation-in-cart') }}";
        })
    </script>
@endsection
