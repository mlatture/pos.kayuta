@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Inventory Change Log</h2>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Staff</th>
                <th>Old Qty</th>
                <th>New Qty</th>
                <th>Reason</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->product->name }}</td>
                    <td>{{ $log->staff->name }}</td>
                    <td>{{ $log->old_quantity }}</td>
                    <td>{{ $log->new_quantity }}</td>
                    <td>{{ $log->reason }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
