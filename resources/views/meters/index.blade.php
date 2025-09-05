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
                        <form action="{{ route('meters.read') }}" method="POST" enctype="multipart/form-data"
                            id="meter-form">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Choose an image</label>
                                <input type="file" class="form-control" name="photo" accept="image/*"
                                    capture="environment" required>
                                <small class="form-text text-muted">Make sure the meter number and reading are clearly
                                    visible.</small>
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
                                    <div
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <strong>Site:</strong> {{ $site->siteid }} —
                                                {{ $site->sitename ?? 'Unnamed' }}
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
        @if ($getLatestMN->count())
            <div class="row">
                <div class="col mx-auto">
                    <div class="card border-0 shadow rounded-3">
                        <div class="card-body p-4">
                            <h5 class="mb-4 text-primary">
                                <i class="fas fa-bolt me-2"></i>
                                Latest Meter Readings (All Attributes)
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Meter #</th>
                                            <th>KWH</th>
                                            <th>Image</th>
                                            <th>Date</th>
                                            <th>Created At</th>
                                            <th>Updated At</th>
                                            <th>Style</th>
                                            <th>Manufacturer</th>
                                            <th>AI Meter #</th>
                                            <th>AI Reading</th>
                                            <th>AI Success</th>
                                            <th>AI Fixed</th>
                                            <th>AI Confidence</th>
                                            <th>AI Notes</th>
                                            <th>AI Attempts</th>
                                            <th>Prompt Version</th>
                                            <th>Model Version</th>
                                            <th>Latency (ms)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($getLatestMN as $reading)
                                            <tr>
                                                <td>{{ $reading->id }}</td>
                                                <td>{{ $reading->meter_number }}</td>
                                                <td>{{ $reading->kwhNo }}</td>
                                                <td>
                                                    @if ($reading->image)
                                                        <a href="{{ asset($reading->image) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $reading->image) }}" alt="Meter Image"
                                                                class="img-thumbnail" style="max-width: 80px;">
                                                        </a>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $reading->date }}</td>
                                                <td>{{ $reading->created_at }}</td>
                                                <td>{{ $reading->updated_at }}</td>
                                                <td>{{ $reading->meter_style }}</td>
                                                <td>{{ $reading->manufacturer }}</td>
                                                <td>{{ $reading->ai_meter_number }}</td>
                                                <td>{{ $reading->ai_meter_reading }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $reading->ai_success ? 'success' : 'danger' }}">
                                                        {{ $reading->ai_success ? 'Yes' : 'No' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $reading->ai_fixed ? 'info' : 'secondary' }}">
                                                        {{ $reading->ai_fixed ? 'Fixed' : 'No' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $reading->ai_confidence === 'high' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($reading->ai_confidence) }}
                                                    </span>
                                                </td>
                                                <td>{{ $reading->ai_notes }}</td>
                                                <td>{{ $reading->ai_attempts }}</td>
                                                <td><small>{{ $reading->prompt_version }}</small></td>
                                                <td><small>{{ $reading->model_version }}</small></td>
                                                <td>{{ $reading->ai_latency_ms }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col mx-auto text-center">
                    <div class="alert alert-info shadow-sm">
                        <i class="fas fa-info-circle me-2"></i> No meter readings found.
                    </div>
                </div>
            </div>
        @endif


    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            const meterForm = $('#meter-form');
            const loadingMsg = $('#loading-msg');
            const scanBtn = $('#scan-btn');

            meterForm.on('submit', function() {
                loadingMsg.show();
                scanBtn.prop('disabled', true).text('Processing...');
            });

            setTimeout(() => {
                $('.alert-info, .alert-success').fadeOut('slow');
            }, 4000);
        });
    </script>
@endpush
