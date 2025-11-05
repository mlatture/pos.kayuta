@extends('layouts.admin')

@section('title', 'Channel Attribution Report')

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center">
            <img width="20" src="{{ asset('assets/back-end/img/earning_report.png') }}" alt="" class="mr-2">
            Channel Attribution Report
        </h2>
    </div>
    <!-- End Page Title -->

    <!-- End Inline Menu -->

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-end">
            <div>
                <a href="{{ route('admin.api_channels.reports.channel.export', request()->query()) }}"
                   class="btn btn-sm btn-outline-success"
                   data-toggle="tooltip" title="Download CSV of current filters">
                    <i class="tio-download-to mr-1"></i> Export CSV
                </a>
            </div>
        </div>

        <div class="card-body">
            <form method="get" class="mb-3">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="organization_id" class="mb-1">Property / Org ID</label>
                        <input type="number"
                               class="form-control"
                               id="organization_id"
                               name="organization_id"
                               placeholder="e.g., 2001"
                               value="{{ $filters['organization_id'] ?? '' }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="from" class="mb-1">From</label>
                        <input type="date"
                               class="form-control"
                               id="from"
                               name="from"
                               value="{{ $filters['from'] ?? '' }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="to" class="mb-1">To</label>
                        <input type="date"
                               class="form-control"
                               id="to"
                               name="to"
                               value="{{ $filters['to'] ?? '' }}">
                    </div>

                    <div class="form-group col-md-3 d-flex align-items-end">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="tio-filter-list mr-1"></i> Apply
                            </button>
                            <a href="{{ route('admin.api_channels.reports.channel.index') }}"
                               class="btn btn-outline-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            @php
                $sumBookings = 0;
                $sumGross = 0.0;
                $sumFees = 0.0;
                $sumNet = 0.0;
                foreach ($rows as $r) {
                    $sumBookings += (int) $r->bookings;
                    $sumGross += (float) $r->gross_base;
                    $sumFees += (float) $r->channel_fees;
                    $sumNet += (float) $r->net_after_fees;
                }
            @endphp

            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-3 bg-light h-100">
                        <div class="text-muted small">Bookings (page)</div>
                        <div class="h4 mb-0">{{ number_format($sumBookings) }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-3 bg-light h-100">
                        <div class="text-muted small">Gross Base (page)</div>
                        <div class="h4 mb-0">{{ number_format($sumGross, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-3 bg-light h-100">
                        <div class="text-muted small">Fees (page)</div>
                        <div class="h4 mb-0">{{ number_format($sumFees, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-3 bg-light h-100">
                        <div class="text-muted small">Net (page)</div>
                        <div class="h4 mb-0">{{ number_format($sumNet, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Channel</th>
                            <th class="text-right">Bookings</th>
                            <th class="text-right">Gross Base</th>
                            <th class="text-right">Fees</th>
                            <th class="text-right">Net</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ $r->channel_name }}</span>
                                    <small class="text-monospace text-muted">#{{ $r->channel_id }}</small>
                                </div>
                            </td>
                            <td class="text-right">{{ number_format($r->bookings) }}</td>
                            <td class="text-right">{{ number_format($r->gross_base, 2) }}</td>
                            <td class="text-right">{{ number_format($r->channel_fees, 2) }}</td>
                            <td class="text-right font-weight-semibold">
                                {{ number_format($r->net_after_fees, 2) }}
                            </td>
                            <td>
                                <span class="badge badge-pill badge-info">{{ $r->currency }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No data found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    @if($rows->count())
                    <tfoot>
                        <tr class="font-weight-semibold">
                            <td class="text-right">Page totals:</td>
                            <td class="text-right">{{ number_format($sumBookings) }}</td>
                            <td class="text-right">{{ number_format($sumGross, 2) }}</td>
                            <td class="text-right">{{ number_format($sumFees, 2) }}</td>
                            <td class="text-right">{{ number_format($sumNet, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <div class="mt-3">
                {{ $rows->appends(request()->query())->links() }}
            </div>

            <p class="small text-muted mt-4 mb-0">
                Privacy: UTMs are stored strictly for attribution; they are not used for retargeting. Update your privacy policy accordingly.
            </p>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
