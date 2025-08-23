@extends('layouts.admin')

@section('title', 'Customer Details')
@section('content-header', 'Customer Details')

@section('content')
    <div class="container">
        <div class="card shadow">
            <div class="card-header d-flex align-items-center bg-primary text-white">
                <h3 class="mb-0">{{ $customer->f_name }} {{ $customer->l_name }}</h3>

                <div class="ms-auto">
                    <a href="{{ route('admin.customers.account', $customer->id) }}" class="btn btn-light ms-auto "
                        style="color:black !important">
                        <i class="fas fa-user"></i> View Account
                    </a>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-light ms-auto "
                        style="color:black !important">
                        <i class="fas fa-edit"></i> Edit Customer
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Email:</strong> {{ $customer->email }}</p>
                        <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Address:</strong> {{ $customer->street_address }}</p>
                        <p><strong>Joined:</strong> {{ \Carbon\Carbon::parse($customer->created_at)->format('F j, Y') }}</p>
                    </div>
                </div>

                <hr>

                <h4 class="mt-4">Reservations</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>Actions</th>
                                <th>ID</th>
                                <th>Site</th>
                                <th>Staying</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer->reservations->sortByDesc('date')->groupBy('cartid') as $cartid => $group)
                                @php $firstReservation = $group->first(); @endphp
                                <tr class="table-primary">
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#reservationModal{{ $firstReservation->id }}">
                                            <i class="fas fa-info-circle"></i> View
                                        </button>
                                        <a href="{{ route('reservations.edit', $firstReservation->cartid) }}"
                                            class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    </td>
                                    <td>Booking #{{ $firstReservation->cartid }}</td>
                                    <td>{{ $groupedReservations[$cartid] ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($firstReservation->cid)->format('F j, Y') }} To
                                        {{ \Carbon\Carbon::parse($firstReservation->cod)->format('F j, Y') }}</td>
                                </tr>

                                <div class="modal fade" id="reservationModal{{ $firstReservation->id }}" tabindex="-1"
                                    aria-labelledby="reservationModalLabel{{ $firstReservation->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-secondary text-white">
                                                <h5 class="modal-title"
                                                    id="reservationModalLabel{{ $firstReservation->id }}">
                                                    Reservation Group #{{ $cartid }} Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    @foreach ($group as $reservation)
                                                        <div class="col-12 mb-3 border-bottom pb-2">
                                                            <strong>Site:</strong> {{ $reservation->siteid }}<br>
                                                            <strong>Class:</strong> {{ $reservation->siteclass }}<br>
                                                            <strong>Check-in:</strong>
                                                            {{ \Carbon\Carbon::parse($reservation->cid)->format('F j, Y') }}<br>
                                                            <strong>Check-out:</strong>
                                                            {{ \Carbon\Carbon::parse($reservation->cod)->format('F j, Y') }}<br>
                                                            <strong>Status:</strong> {{ $reservation->status }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <h4 class="mt-4">Cart Reservation</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>Actions</th>
                                <th>ID</th>
                                <th>Site</th>
                                <th>Staying</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedCartReservations as $cartid => $sites)
                                <tr>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#cartReservationModal{{ $cartid }}">
                                            <i class="fas fa-info-circle"></i> View
                                        </button>
                                        <a href="{{ route('reservations.payment.index', $cartid) }}"
                                            class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    </td>
                                    <td>Booking #{{ $cartid }}</td>
                                    <td>{{ $sites }}</td>
                                    <td>
                                        @foreach ($customer->cart_reservations->where('cartid', $cartid)->take(1) as $reservation)
                                            {{ \Carbon\Carbon::parse($reservation->cid)->format('F j, Y') }} To
                                            {{ \Carbon\Carbon::parse($reservation->cod)->format('F j, Y') }}
                                        @endforeach
                                    </td>
                                </tr>

                                <!-- Modal for Cart Reservation Details -->
                                <div class="modal fade" id="cartReservationModal{{ $cartid }}" tabindex="-1"
                                    aria-labelledby="cartReservationModalLabel{{ $cartid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-secondary text-white">
                                                <h5 class="modal-title" id="cartReservationModalLabel{{ $cartid }}">
                                                    Cart Reservation #{{ $cartid }} Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row row-cols-1 row-cols-md-2">
                                                    <div class="col"><strong>Cart ID:</strong> {{ $cartid }}
                                                    </div>
                                                    <div class="col"><strong>Site(s):</strong> {{ $sites }}
                                                    </div>
                                                    <div class="col"><strong>Check-in:</strong>
                                                        @foreach ($customer->cart_reservations->where('cartid', $cartid)->take(1) as $reservation)
                                                            {{ \Carbon\Carbon::parse($reservation->cid)->format('F j, Y') }}
                                                        @endforeach
                                                    </div>
                                                    <div class="col"><strong>Check-out:</strong>
                                                        @foreach ($customer->cart_reservations->where('cartid', $cartid)->take(1) as $reservation)
                                                            {{ \Carbon\Carbon::parse($reservation->cod)->format('F j, Y') }}
                                                        @endforeach
                                                    </div>
                                                    <!-- Add more details for the modal as needed -->
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                <hr>

                <h4 class="mt-4">Receipts</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Cart ID</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer->receipts->sortByDesc('date') as $receipt)
                                <tr>
                                    <td>{{ $receipt->id }}</td>
                                    <td>
                                        {{ $receipt->cartid }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($receipt->createdate)->format('F j, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr>

                <h4 class="mt-4">Cards on File</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Masked Card Number</th>
                                <th>Card Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer->cardsOnFile as $card)
                                <tr>
                                    <td>{{ $card->id }}</td>
                                    <td>{{ $card->xmaskedcardnumber }}</td>
                                    <td>{{ $card->method }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr>

                <a href="{{ route('customers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                    Back
                    to List</a>
            </div>
        </div>
    </div>


@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                responsive: true,
                dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
                buttons: [
                    'colvis',
                    'copy',
                    {
                        extend: 'csv',
                    },
                    {
                        extend: 'excel',
                    },
                    {
                        extend: 'pdf',
                    },

                    'print'
                ],
                language: {
                    search: 'Search: ',
                    lengthMenu: 'Show _MENU_ entries',
                },
                pageLength: 10
            });
        })
    </script>
@endsection
