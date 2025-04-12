@extends('layouts.admin')

@section('title', 'Customer Details')
@section('content-header', 'Customer Details')

@section('content')
    <div class="container">
        <div class="card shadow">
            <div class="card-header d-flex align-items-center bg-primary text-white">
                <h3 class="mb-0">{{ $customer->f_name }} {{ $customer->l_name }}</h3>
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-light ms-auto "
                    style="color:black !important">
                    <i class="fas fa-edit"></i> Edit Customer
                </a>
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
                            @foreach ($group as $index => $reservation)
                                    <tr @if ($index === 0) class="table-primary" @endif>
                                        <td>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#reservationModal{{ $reservation->id }}">
                                                <i class="fas fa-info-circle"></i> View
                                            </button>
                                            <a href="{{ route('reservations.edit', $reservation->cartid) }}"
                                                class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                        </td>
                                        <td>
                                            @if ($index === 0)
                                                Booking #{{ $reservation->cartid }}
                                            @else
                                                <span class="ms-3 text-muted">#{{ $reservation->id }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $reservation->siteid }}</td>
                                        <td>{{ \Carbon\Carbon::parse($reservation->cid)->format('F j, Y') }} To
                                            {{ \Carbon\Carbon::parse($reservation->cod)->format('F j, Y') }}</td>
                                    </tr>
                                    {{-- <tr>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#reservationModal{{ $reservation->id }}">
                                            <i class="fas fa-info-circle"></i> View
                                        </button>
                                        <a href="{{ route('reservations.edit', $reservation->cartid) }}"
                                            class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>

                                    </td>
                                    <td>{{ $reservation->id }}</td>
                                    <td>{{ $reservation->siteid }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->cid)->format('F j, Y') }} To
                                        {{ \Carbon\Carbon::parse($reservation->cod)->format('F j, Y') }}</td>
                                </tr> --}}

                                    <div class="modal fade" id="reservationModal{{ $reservation->id }}" tabindex="-1"
                                        aria-labelledby="reservationModalLabel{{ $reservation->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-secondary text-white">
                                                    <h5 class="modal-title"
                                                        id="reservationModalLabel{{ $reservation->id }}">
                                                        Reservation #{{ $reservation->id }} Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row row-cols-1 row-cols-md-2">
                                                        <div class="col"><strong>Cart ID:</strong>
                                                            {{ $reservation->cartid ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Site ID:</strong>
                                                            {{ $reservation->siteid ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Site Class:</strong>
                                                            {{ $reservation->siteclass ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Check-in:</strong>
                                                            {{ $reservation->cid ? \Carbon\Carbon::parse($reservation->cid)->format('F j, Y') : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Check-out:</strong>
                                                            {{ $reservation->cod ? \Carbon\Carbon::parse($reservation->cod)->format('F j, Y') : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Status:</strong>
                                                            {{ $reservation->status ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Reason:</strong>
                                                            {{ $reservation->reason ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Total:</strong>
                                                            {{ $reservation->total ? '$' . number_format($reservation->total, 2) : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Subtotal:</strong>
                                                            {{ $reservation->subtotal ? '$' . number_format($reservation->subtotal, 2) : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Tax Rate:</strong>
                                                            {{ $reservation->taxrate ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Total Tax:</strong>
                                                            {{ $reservation->totaltax ? '$' . number_format($reservation->totaltax, 2) : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Extra Charge:</strong>
                                                            {{ $reservation->extracharge ? '$' . number_format($reservation->extracharge, 2) : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Base Rate:</strong>
                                                            {{ $reservation->base ? '$' . number_format($reservation->base, 2) : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Rate Adjustment:</strong>
                                                            {{ $reservation->rateadjustment ? '$' . number_format($reservation->rateadjustment, 2) : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Discount Code:</strong>
                                                            {{ $reservation->discountcode ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Discount:</strong>
                                                            {{ $reservation->discount ? '$' . number_format($reservation->discount, 2) : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Total Charges:</strong>
                                                            {{ $reservation->totalcharges ? '$' . number_format($reservation->totalcharges, 2) : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Total Payments:</strong>
                                                            {{ $reservation->totalpayments ? '$' . number_format($reservation->totalpayments, 2) : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Balance:</strong>
                                                            {{ $reservation->balance ? '$' . number_format($reservation->balance, 2) : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Adults:</strong>
                                                            {{ $reservation->adults ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Children:</strong>
                                                            {{ $reservation->children ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Pets:</strong>
                                                            {{ $reservation->pets ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Rig Type:</strong>
                                                            {{ $reservation->rigtype ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Rig Length:</strong>
                                                            {{ $reservation->riglength ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Comments:</strong>
                                                            {{ $reservation->comments ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Confirmation #:</strong>
                                                            {{ $reservation->xconfnum ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Receipt:</strong>
                                                            {{ $reservation->receipt ?? 'N/A' }}</div>
                                                        <div class="col"><strong>Created Date:</strong>
                                                            {{ $reservation->createdate ? \Carbon\Carbon::parse($reservation->createdate)->format('F j, Y') : 'N/A' }}
                                                        </div>
                                                        <div class="col"><strong>Last Modified:</strong>
                                                            {{ $reservation->lastmodified ? \Carbon\Carbon::parse($reservation->lastmodified)->format('F j, Y g:i A') : 'N/A' }}
                                                        </div>
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

                <a href="{{ route('customers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back
                    to List</a>
            </div>
        </div>
    </div>
@endsection
