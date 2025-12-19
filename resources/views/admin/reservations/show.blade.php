@extends('layouts.admin')

@section('title', 'Reservation Details #' . $mainReservation->cartid)

@section('css')
<style>
    @media print {
        @page { size: auto;  margin: 0mm; }
        body { margin: 10mm; background-color: white !important; }
        .d-print-none, .sidebar, .main-header, .footer, .control-sidebar { display: none !important; }
        .content-wrapper { margin-left: 0 !important; background-color: white !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .badge { border: 1px solid #000; color: #000 !important; }
        .table { width: 100% !important; }
        a[href]:after { content: none !important; } 
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Block -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Reservation #{{ $mainReservation->cartid }}</h1>
            <p class="mb-0 text-muted">
                Created on {{ $mainReservation->created_at->format('M d, Y h:i A') }} 
                by {{ $mainReservation->createdby }}
            </p>
        </div>
        <div>
            <span class="badge bg-{{ in_array($mainReservation->status, ['Paid', 'Confirmed']) ? 'success' : ($mainReservation->status === 'Pending' ? 'warning' : 'secondary') }} fs-6">
                {{ $mainReservation->status ?? 'Unknown' }}
            </span>
        </div>
    </div>

    <!-- Print Header (Visible only on print) -->
    <div class="d-none d-print-block mb-4">
        <h1>Reservation #{{ $mainReservation->cartid }}</h1>
        <p>
            Created on {{ $mainReservation->created_at->format('M d, Y h:i A') }} 
            by {{ $mainReservation->createdby }}
        </p>
    </div>

    <!-- Action Toolbar -->
    <div class="row mb-4 d-print-none">
        <div class="col-12">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body py-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <!-- Print -->
                        <a href="{{ request()->fullUrlWithQuery(['print' => 1]) }}" onclick="window.print();" class="btn btn-secondary btn-sm">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                        
                        <!-- View Customer -->
                        @if(isset($user) && $user->id)
                        <a href="{{ route('customers.show', $user->id) }}" class="btn btn-info text-white btn-sm">
                            <i class="fas fa-user me-1"></i> Customer
                        </a>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                         @php
                            $activeStatuses = ['Paid', 'Confirmed', 'Pending'];
                            $now = \Carbon\Carbon::now();
                            $today = \Carbon\Carbon::today();

                            // Check-In Logic
                            // Valid if ANY reservation is (Active AND Arrival <= Today AND Not Checked In)
                            $canCheckIn = $reservations->contains(function($r) use ($activeStatuses, $today) {
                                return in_array($r->status, $activeStatuses) && 
                                       $today->gte(\Carbon\Carbon::parse($r->cid)) && 
                                       is_null($r->checkedin);
                            });

                            // Check-Out Logic
                            // Valid if ANY reservation is ((Checked In OR Active) AND Not Checked Out)
                            $canCheckOut = $reservations->contains(function($r) use ($activeStatuses) {
                                return (!is_null($r->checkedin) || in_array($r->status, $activeStatuses)) && 
                                       is_null($r->checkedout);
                            });
                        @endphp
                        
                        <!-- Check In -->
                        <form action="{{ route('admin.reservations.checkin', $mainReservation->cartid) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" {{ !$canCheckIn ? 'disabled' : '' }} title="{{ !$canCheckIn ? 'Not eligible for Check-In' : 'Check In' }}">
                                <i class="fas fa-check-circle me-1"></i> Check In
                            </button>
                        </form>

                        <!-- Check Out -->
                        <form action="{{ route('admin.reservations.checkout', $mainReservation->cartid) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm" {{ !$canCheckOut ? 'disabled' : '' }} title="{{ !$canCheckOut ? 'Not eligible for Check-Out' : 'Check Out' }}">
                                <i class="fas fa-sign-out-alt me-1"></i> Check Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer & Reservation Info -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold">Customer Information</h5>
                    <hr>
                    <p><strong>Name:</strong> {{ $user->f_name ?? $mainReservation->fname }} {{ $user->l_name ?? $mainReservation->lname }}</p>
                    <p><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $user->address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold">Site Summary</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Site ID</th>
                                    <th>Dates</th>
                                    <th>Type</th>
                                    <th>Rig</th>
                                    <th>Guests</th>
                                    <th>Pets</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservations as $res)
                                <tr>
                                    <td>
                                        <span class="badge bg-info text-dark">{{ $res->siteid }}</span>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($res->cid)->format('M d') }} - 
                                        {{ \Carbon\Carbon::parse($res->cod)->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">({{ $res->nights }} nights)</small>
                                    </td>
                                    <td>{{ $res->siteclass }}</td>
                                    <td>{{ $res->riglength }}' {{ $res->rigtype }}</td>
                                    <td>{{ $res->adults ?? 0 }} Ad, {{ $res->children ?? 0 }} Ch</td>
                                    <td>{{ $res->pets ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financials -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Charges Breakdown</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            @foreach($reservations as $res)
                            <tr>
                                <td>Site: {{ $res->siteid }}</td>
                                <td class="text-end">${{ number_format($res->base, 2) }}</td>
                            </tr>
                            @if($res->sitelock > 0)
                            <tr>
                                <td class="ps-4 text-muted">Site Lock Fee</td>
                                <td class="text-end text-muted">${{ number_format($res->sitelock, 2) }}</td>
                            </tr>
                            @endif
                            @endforeach
                            <!-- Summary -->
                             <tr class="table-active fw-bold">
                                <td>Total</td>
                                <td class="text-end">${{ number_format($reservations->sum('total'), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payments</h6>
                </div>
                <div class="card-body">
                    @if($payments->isEmpty())
                        <p class="text-center text-muted">No payments recorded.</p>
                    @else
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                    <td>{{ $payment->method }}</td>
                                    <td class="text-success fw-bold">${{ number_format($payment->payment, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- System Logs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-secondary">System Logs (Read-Only)</h6>
        </div>
        <div class="card-body">
             @if($logs->isEmpty())
                <p class="text-muted">No logs found for this reservation.</p>
            @else
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-xs table-hover" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Type</th>
                                <th>User</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td style="white-space:nowrap;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $log->transaction_type }}</td>
                                <td>{{ $log->user_id ?? 'System' }}</td>
                                <td>
                                    <details>
                                        <summary>View Changes</summary>
                                        <pre class="mt-2 bg-light p-2 rounded">{{ json_encode(json_decode($log->after), JSON_PRETTY_PRINT) }}</pre>
                                    </details>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
