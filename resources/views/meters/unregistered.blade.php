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

            <div class="row mb-3 align-items-start">
                <div class="col-md-6">
                    <img src="{{ asset('storage/' . $image) }}" alt="Meter Image" style="max-width: 80%; height: auto;">
                </div>

                <div class="col-md-6 d-flex flex-column gap-2">
                    <form action="{{ route('meters.read') }}" method="POST" id="retry-form">
                        @csrf
                        <input type="hidden" name="existing_image" value="{{ $image }}">
                        <button type="submit" class="btn btn-warning w-100">
                            🔁 That doesn't seem right, try again
                        </button>
                    </form>

                    <form action="{{ route('meters.read') }}" method="POST" enctype="multipart/form-data"
                        id="take-photo-form">
                        @csrf
                        <input type="file" name="photo" id="take-photo-input" accept="image/*" capture="environment"
                            style="display: none;" required>
                    </form>

                    <button type="button" class="btn btn-secondary w-100" id="take-photo-button">
                        📷 Take another picture
                    </button>
                </div>
            </div>

            <div id="loading-overlay"
                style="display: none !important; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
background: rgba(255, 255, 255, 0.8); z-index: 99999; display: flex; align-items: center; justify-content: center;">
                <div class="text-center">
                    {{-- <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div> --}}
                    <i class="fa-solid fa-hourglass-end fa-spin"></i>  Please wait, scanning meter...
                </div>
            </div>

            <form action="{{ route('meters.register') }}" method="POST">
                @csrf

                <input type="hidden" name="meter_number" value="{{ $meter_number }}">
                <input type="hidden" name="kwhNo" value="{{ $reading }}">
                <input type="hidden" name="image" value="{{ $image }}">
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="mb-3">
                    <label for="siteid" class="form-label"><strong>Assign to Site</strong></label>
                    <input type="text" name="siteid" id="siteid" class="form-control" placeholder="Enter Site ID">
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


    <script>
        function showLoading() {
            document.getElementById('loading-overlay').style.display = 'flex';
        }

        document.getElementById('take-photo-button').addEventListener('click', function() {
            document.getElementById('take-photo-input').click();
        });


        // Show loading when any form with image is submitted
        document.getElementById('take-photo-input').addEventListener('change', function() {
            if (this.files.length > 0) {
                showLoading();
                document.getElementById('take-photo-form').submit();
            }
        });

        // Handle retry button
        document.querySelectorAll('form[action="{{ route('meters.read') }}"]').forEach(form => {
            form.addEventListener('submit', showLoading);
        });
    </script>
@endpush
