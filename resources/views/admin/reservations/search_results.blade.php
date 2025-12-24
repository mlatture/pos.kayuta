@extends('layouts.admin')

@section('title', 'Search Results')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Search Results</h1>
        <a href="{{ route('reservations.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Reservations
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Found {{ $bookings->count() }} results for "{{ $query }}"</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="searchResultsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Cart ID</th>
                            <th>Customer</th>
                            <th>Dates</th>
                            <th>Sites</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $cartId => $items)
                            @php
                                $main = $items->first();
                                $sites = $items->pluck('siteid')->implode(', ');
                                $user = $main->user;
                                $customerName = ($user->f_name ?? $main->fname) . ' ' . ($user->l_name ?? $main->lname);
                                $status = $main->status;
                                $bgClass = match($status) {
                                    'Paid', 'Confirmed' => 'success',
                                    'Pending' => 'warning',
                                    'Cancelled' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="{{ route('admin.reservations.show', $cartId) }}">#{{ $cartId }}</a>
                                </td>
                                <td>
                                    {{ $customerName }}<br>
                                    <small class="text-muted">{{ $user->email ?? $main->email ?? '' }}</small>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($main->cid)->format('M d, Y') }} - 
                                    {{ \Carbon\Carbon::parse($main->cod)->format('M d, Y') }}
                                    <small class="text-muted">({{ $main->nights }} nights)</small>
                                </td>
                                <td>{{ $sites }}</td>
                                <td>
                                    <span class="badge bg-{{ $bgClass }}">{{ $status }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.reservations.show', $cartId) }}" class="btn btn-primary btn-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
