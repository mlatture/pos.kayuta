@extends('layouts.admin')

@section('title', 'Register New Meter')
@section('content-header', 'Register New Meter to Site')
@push('css')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        .ui-autocomplete {
            z-index: 9999 !important;
            background: white;
            border: 1px solid #ccc;
        }
    </style>
@endpush
@section('content')
    <div class="container">
        <div class="card shadow-sm p-4">
            <h4 class="mb-3">New Meter Not Found in System</h4>

            <div class="mb-3">
                <img src="{{ asset('storage/' . $image) }}" alt="Meter Image" style="max-width: 50%; height: auto;">
            </div>

            <form action="{{ route('meters.register') }}" method="POST">
                @csrf

                <input type="hidden" name="meter_number" value="{{ $meter_number }}">
                <input type="hidden" name="kwhNo" value="{{ $reading }}">
                <input type="hidden" name="image" value="{{ $image }}">
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="mb-3">
                    <label for="siteid" class="form-label"><strong>Assign to Site</strong></label>
                    <input type="text" name="siteid" id="siteid" class="form-control" placeholder="Enter Site ID"
                        required>
                    <small class="text-muted">Type to search. Site must already exist.</small>
                </div>

                <div class="mb-3">
                    <label><strong>Meter Number</strong></label>
                    <p>{{ $meter_number }}</p>
                </div>

                <div class="mb-3">
                    <label><strong>kWh Reading</strong></label>
                    <p>{{ $reading }} kWh</p>
                </div>

                <div class="mb-3">
                    <label><strong>Date</strong></label>
                    <p>{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</p>
                </div>

                <div class="alert alert-warning">
                    Are you sure you want to assign <strong>{{ $meter_number }}</strong> to Site: <strong><span
                            id="confirmSite"></span></strong>?
                </div>

                <div class="d-flex gap-3">

                    <a href="{{ route('meters.index') }}" class="btn btn-outline-secondary">Cancel</a>

                    <form action="{{ route('meters.read') }}" method="POST">
                        @csrf
                        <input type="hidden" name="existing_image" value="{{ $image }}">
                        <button type="submit" class="btn btn-warning">
                            🔁 That doesn't seem right, try again
                        </button>
                    </form>

                    <a href="{{ route('meters.read') }}" class="btn btn-secondary">
                        📷 Take another picture
                    </a>

                    <button type="submit" class="btn btn-primary">Register Meter</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const siteInput = document.getElementById('siteid');
            const confirmSpan = document.getElementById('confirmSite');

            siteInput.addEventListener('input', function() {
                confirmSpan.textContent = this.value || 'N/A';
            });

            $('#siteid').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('api.sites.search') }}",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            console.log('Autocomplete data:', data);
                            response(data);
                        },
                        error: function(xhr) {
                            console.error('Autocomplete error:', xhr.responseText);
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $('#siteid').val(ui.item.value);
                    $('#confirmSite').text(ui.item.label);
                    return false;
                }
            });

        });
    </script>
@endpush
