@extends('layouts.admin')

@section('title', 'Customer Details')
@section('content-header', 'Customer Details')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header d-flex align-items-center bg-primary text-white">
            <h3 class="mb-0">{{ $customer->f_name }} {{ $customer->l_name }}</h3>
            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-light ms-auto " style="color:black !important">
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
                            <th>ID</th>
                            <th>Site</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer->reservations->sortByDesc('date') as $reservation)
                        <tr>
                            <td>{{ $reservation->id }}</td>
                            <td>{{ $reservation->siteid }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->date)->format('F j, Y') }}</td>
                            <td>
                                <a href="{{ route('reservations.show', $reservation->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
                                <a href="{{ route('reservations.edit', $reservation->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            </td>
                        </tr>
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
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer->receipts->sortByDesc('date') as $receipt)
                        <tr>
                            <td>{{ $receipt->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($receipt->date)->format('F j, Y') }}</td>
                            <td>
                                {{-- Receipt actions commented out for now --}}
                            </td>
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
                        @foreach($customer->cardsOnFile as $card)
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

            <a href="{{ route('customers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
        </div>
    </div>
</div>
@endsection
