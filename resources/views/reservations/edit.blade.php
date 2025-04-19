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

            .refund-stamp {
                position: absolute;
                top: 40%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-15deg);
                color: #d9534f;
                border: 4px solid #d9534f;
                padding: 10px 25px;
                font-size: 28px;
                font-weight: bold;
                font-family: 'Courier New', Courier, monospace;
                text-transform: uppercase;
                opacity: 0.85;
                letter-spacing: 3px;
                background-color: transparent;
                z-index: 10;
                mix-blend-mode: multiply;
                pointer-events: none;
            }

            /* Optional: ensure parent is relative */
            #invoice-table {
                position: relative;
            }
            .gantt .bar {
                pointer-events: none !important;
                cursor: not-allowed !important;

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

                <button type="button" class="btn btn border-info btn-sm float-end text-white" id="lookup-history">
                    <i class="fa-solid fa-book"></i> History
                </button>
                <button type="button" class="btn btn border-danger btn-sm float-end text-white"
                    id="cancel-reservation-edit">
                    <i class="fa-solid fa-ban"></i> Cancel
                </button>
{{-- 
                <button type="button" class="btn btn border-warning btn-sm float-end text-white" id="add-to-cart">
                    <i class="fa-solid fa-store"></i> Add To Cart
                </button> --}}

                {{-- <button type="button" class="btn btn border-success btn-sm float-end text-white" id="proceed-payment">
                    <i class="fa-solid fa-money-bill-transfer"></i> Payment
                </button> --}}


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
            <div class="row ">
                <div class="col">
                    <div class="border rounded shadow-sm p-3">
                        <h4 class="mb-3">Customer Info</h4>

                        <div id="customer-info-container">
                            <div class="customer-info-entry border rounded p-3 mb-3">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <label for="first_name" class="form-label fw-semibold">First Name *</label>
                                        <input type="text" value="{{ $user->f_name ?? 'N/A' }}" name="first_name[]"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label for="last_name" class="form-label fw-semibold">Last Name *</label>
                                        <input type="text" name="last_name[]" value="{{ $user->l_name ?? 'N/A' }}"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label for="email" class="form-label fw-semibold">Email *</label>
                                        <input type="email" name="email[]" value="{{ $user->email ?? 'N/A' }}"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label for="discovery_method" class="form-label fw-semibold">Discovery Method
                                            *</label>
                                        <input type="email" name="discovery_method"
                                            value="{{ $discovery_method ?? 'N/A' }}" class="form-control" readonly>

                                    </div>

                                    <!-- Optional Fields -->
                                    <div class="col-4">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" name="phone[]" value="{{ $user->phone ?? 'N/A' }}"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label for="work_phone" class="form-label">Work Phone</label>
                                        <input type="text" name="work_phone[]"
                                            value="{{ $user->work_phone ?? 'N/A' }}" class="form-control" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label for="home_phone" class="form-label">Home Phone</label>
                                        <input type="text" name="home_phone[]" class="form-control"
                                            value="{{ $user->home_phone ?? 'N/A' }}" readonly>
                                    </div>

                                    <div class="col-4">
                                        <label for="driving_license" class="form-label">Driving License</label>
                                        <input type="text" name="driving_license[]" class="form-control"
                                            value="{{ $user->driving_license ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                        <input type="text" name="date_of_birth[]" class="form-control"
                                            value="{{ $user->date_of_birth ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label for="anniversary" class="form-label">Anniversary</label>
                                        <input type="text" name="anniversary[]" class="form-control"
                                            value="{{ $user->anniversary ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label for="age" class="form-label">Age</label>
                                        <input type="text" name="age[]" class="form-control"
                                            value="{{ $user->age ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" name="address[]" class="form-control"
                                            value="{{ implode(
                                                ', ',
                                                array_filter([
                                                    $user->country ?? '',
                                                    $user->city ?? '',
                                                    $user->address_2 ?? '',
                                                    $user->address_3 ?? '',
                                                    $user->street_address ?? '',
                                                ]),
                                            ) }}"
                                            readonly>

                                    </div>



                                    <input type="hidden" id="customer_id" name="customer_id"
                                        value="{{ $user->id }}" data-customer="{{ $user->id }}" hidden>
                                </div>

                            </div>
                        </div>


                    </div>
                </div>

            </div>
        </div>


        <div class="container-invoice mt-3" id="invoice-table">

            <header class="invoice-header">
                <h4>Invoice</h4>

            </header>



            @php
                $firstReservation = $reservations->first();
            @endphp

            @if (
                $firstReservation &&
                    $firstReservation->payment &&
                    $firstReservation->payment->transaction_type &&
                    $firstReservation->payment->cancellation_fee !== null &&
                    $firstReservation->payment->payment !== null)
                <div class="refund-stamp">REFUNDED</div>
            @endif


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

                        @php
                            $subtotal = 0;
                            foreach ($reservations as $reservation) {
                                $subtotal += $reservation->base;
                            }

                            $cancellationFee = $reservation->payment->payment * 0.15;
                            $totalAfterFee = $reservation->payment->payment - $cancellationFee;
                        @endphp

                        <tr class="total-row">
                            <td colspan="3"></td>
                            <td class="text-end">Subtotal</td>
                            <td id="subtotal">${{ number_format($subtotal, 2) }}</td>
                        </tr>

                        <tr class="total-row">
                            <td colspan="1"></td>
                            <td>Tax</td>
                            <td></td>
                            <td>Non Taxable</td>
                            <td>$0</td>
                        </tr>

                        <tr class="total-row">
                            <td colspan="3"></td>
                            <td></td>
                            <td></td>

                        </tr>
                        <tr class="total-row">
                            <td colspan="3"></td>
                            <td></td>
                            <td></td>

                        </tr>

                        <tr class="total-row">
                            <td colspan="3"></td>
                            <td class="text-end">Pay</td>
                            <td>{{ number_format($reservation->payment->payment, 2) }} </td>
                        </tr>

                        @if (
                            $firstReservation &&
                                $firstReservation->payment &&
                                $firstReservation->payment->transaction_type &&
                                $firstReservation->payment->cancellation_fee !== null &&
                                $firstReservation->payment->payment !== null)
                            <tr class="total-row">
                                <td colspan="3"></td>
                                <td class="text-end text-danger">Cancellation Fee (15%)</td>
                                <td class="text-danger">- ${{ number_format($cancellationFee, 2) }}</td>
                            </tr>

                            <tr class="total-row">
                                <td colspan="3"></td>
                                <td class="text-end fw-bold">Total After Fee</td>
                                <td class="fw-bold">${{ number_format($totalAfterFee, 2) }}</td>
                            </tr>

                            <tr class="total-row">
                                <td>
                                    Transaction Type
                                </td>
                                <td>
                                    REFUND
                                </td>
                                <td colspan="3"></td>
                            </tr>
                            <tr class="total-row">
                                <td>
                                    Payment Type
                                </td>
                                <td>
                                    {{ $reservation->payment->method }}
                                </td>
                                <td colspan="3"></td>

                            </tr>
                        @endif




                    </tbody>
                </table>


            </div>

        </div>

        @include('reservations.modals.history')
        @include('reservations.modals.cancel-reservation')
    </div>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>


    <script>
        $('#cancel-reservation-edit').on('click', function() {
            $('#cancellationModal').modal('show');

        });
        $('#lookup-history').on('click', function() {
            $('#historyModal').modal('show');
            let customerId = $('#customer_id').data('customer');
            let url = `/admin/reservations/history/${customerId}`;

            if (!$.fn.DataTable.isDataTable('#reservation-history-table')) {
                $('#reservation-history-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: url,
                    columns: [{
                            data: 'cid',
                            name: 'cid'
                        },
                        {
                            data: 'cod',
                            name: 'cod'
                        },
                        {
                            data: 'siteid',
                            name: 'siteid'
                        },
                        {
                            data: 'cartid',
                            name: 'cartid'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'rigtype',
                            name: 'rigtype'
                        },
                        {
                            data: 'riglength',
                            name: 'riglength'
                        }
                    ],
                    responsive: true,
                    dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
                    buttons: ['colvis', 'copy', 'csv', 'excel', 'pdf', 'print'],
                    language: {
                        search: 'Search: ',
                        lengthMenu: 'Show _MENU_ entries'
                    },
                    pageLength: 10
                });
            }

        });




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
                draggable: false,
                drag_mode: "none"
            });



        });
    </script>
@endsection
