@extends('layouts.admin')

@section('content')
    <div class="container-fluid mt-4">
        <h4 class="mb-4 text-primary">📄 Seasonal Payment Statements</h4>

        @if (session('error'))
            <div class="alert alert-warning">{{ session('error') }}</div>
        @endif

        @if ($statements->isEmpty())
            <div class="alert alert-info">
                No statements available for this customer.
            </div>
        @else
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-secondary">
                                <div class="alert alert-info">
                                    <strong>Customer Name: </strong>{{ $statements->first()->customer_name }} 
                                    <br>
                                    <strong>Customer Email:</strong> {{ $statements->first()->customer_email }}
                                </div>
                                <tr>
                                    <th>#</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    {{-- <th>Method</th>
                                    <th>Reference</th>
                                    <th>Notes</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($statements as $index => $statement)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($statement->payment_date)->format('M d, Y') }}</td>
                                        <td class="text-end">${{ number_format($statement->amount, 2) }}</td>
                                        <td>
                                            @if ($statement->status === 'Completed')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif ($statement->status === 'Pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif ($statement->status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($statement->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Total Row --}}
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold text-primary">
                                        ${{ number_format($statements->sum('amount'), 2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                ⬅️ Back to Renewals
            </a>
        </div>
    </div>
@endsection
