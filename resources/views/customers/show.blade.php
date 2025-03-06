@extends('layouts.admin')

@section('title', 'Customer Details')
@section('content-header', 'Customer Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>{{ $customer->f_name }} {{ $customer->l_name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> {{ $customer->email }}</p>
            <p><strong>Phone:</strong> {{ $customer->phone }}</p>
            <p><strong>Address:</strong> {{ $customer->street_address }}</p>
            <p><strong>Joined:</strong> {{ \Carbon\Carbon::parse($customer->created_at)->format('F j, Y') }}</p>

            <hr>

            <h4>Reservations</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Site</th>
                        <th>Date</th>
                        {{-- <th>Actions</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer->reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->id }}</td>
                        <td>{{ $reservation->siteid }}</td>
                        <td>{{ \Carbon\Carbon::parse($reservation->date)->format('F j, Y') }}</td>
                        {{-- <td>
                            <a href="{{ route('reservations.show', $reservation->id) }}" class="btn btn-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('reservations.edit', $reservation->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                        </td> --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <h4>Receipts</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        {{-- <th>Total</th> --}}
                        <th>Date</th>
                        {{-- <th>Actions</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer->receipts as $receipt)
                    <tr>
                        <td>{{ $receipt->id }}</td>
                        {{-- <td>${{ number_format($receipt->total, 2) }}</td> --}}
                        <td>{{ \Carbon\Carbon::parse($receipt->date)->format('F j, Y') }}</td>
                        {{-- <td>
                            <a href="{{ route('receipts.show', $receipt->id) }}" class="btn btn-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('receipts.edit', $receipt->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                        </td> --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection
