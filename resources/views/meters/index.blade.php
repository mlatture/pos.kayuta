@extends('layouts.admin')

@section('title', 'Scan Electric Meter')
@section('content-header', 'Scan Electric Meter')

@section('content')
    <div class="container-fluid">
        {{-- Flash Messages --}}
        @foreach (['success', 'error', 'info', 'warning'] as $msg)
            @if (session($msg))
                <div class="alert alert-{{ $msg }} alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i> {{ session($msg) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endforeach

        {{-- Upload Form --}}
        <div class="row mb-4">
            <div class="col mx-auto">
                <div class="card border-0 shadow rounded-3">
                    <div class="card-body p-4">
                        <h5 class="mb-3">
                            <i class="fas fa-camera me-2 text-primary"></i>
                            Upload or Take a Photo of the Electric Meter
                        </h5>
                        <form action="{{ route('meters.read') }}" method="POST" enctype="multipart/form-data" id="meter-form">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Choose an image</label>
                                <input type="file" class="form-control" name="photo" accept="image/*" capture="environment" required>
                                <small class="form-text text-muted">Make sure the meter number and reading are clearly visible.</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="scan-btn">
                                <i class="fas fa-bolt me-1"></i> Scan and Preview Bill
                            </button>
                            <div id="loading-msg" class="mt-3 text-center text-muted" style="display: none;">
                                <i class="fa-solid fa-hourglass-end fa-spin"></i> Scanning meter, please wait...
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Overdue Sites --}}
        @if ($overdueSites->count())
            <div class="row">
                <div class="col mx-auto">
                    <div class="card border-0 shadow rounded-3">
                        <div class="card-body p-4">
                            <h5 class="mb-4 text-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Meters Overdue (Not Read in 20+ Days)
                            </h5>
                            <div class="list-group">
                                @foreach ($overdueSites as $site)
                                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <strong>Site:</strong> {{ $site->siteid }} — {{ $site->sitename ?? 'Unnamed' }}
                                            </h6>
                                            <small class="text-muted">Meter #: {{ $site->meter_number ?? 'N/A' }}</small>
                                        </div>
                                        <span class="badge bg-danger rounded-pill px-3 py-2">Overdue</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col mx-auto text-center">
                    <div class="alert alert-info shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> No overdue meters found.
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            const meterForm = $('#meter-form');
            const loadingMsg = $('#loading-msg');
            const scanBtn = $('#scan-btn');

            meterForm.on('submit', function () {
                loadingMsg.show();
                scanBtn.prop('disabled', true).text('Processing...');
            });

            setTimeout(() => {
                $('.alert-info, .alert-success').fadeOut('slow');
            }, 4000);
        });
    </script>
@endpush
