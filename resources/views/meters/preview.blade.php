@extends('layouts.admin')

@section('title', 'Confirm Meter Reading')
@section('content-header', 'Confirm Meter Reading Preview')

@section('content')
    <div class="container">
        <div class="card shadow-sm p-4">
            <h4 class="mb-3">
                Electric Meter Reading Details (Site No: {{ $reading->siteid }})



                @if ($reading->new_meter_number || !empty($request->new_meter_number))
                    <span class="text-success ms-2">(New Meter Registered)</span>
                @endif
            </h4>

            <div class="row mb-3 align-items-start">
                <div class="col-md-6">
                    <img src="{{ asset('storage/' . $reading->image) }}" alt="Meter Image"
                        style="max-width: 50%; height: auto;">
                </div>

                <div class="col-md-6 d-flex flex-column gap-2">
                    <form action="{{ route('meters.scan') }}" method="POST" id="retry-form">
                        @csrf
                        <input type="hidden" name="existing_image" value="{{ $reading->image }}">
                        <button type="submit" class="btn btn-warning w-100">
                            🔁 That doesn't seem right, try again
                        </button>
                    </form>

                    <form action="{{ route('meters.scan') }}" method="POST" enctype="multipart/form-data"
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

                    <i class="fa-solid fa-hourglass-end fa-spin"></i> Please wait, scanning meter...
                </div>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Meter Number:</strong> {{ $reading->meter_number }}</p>
                    <p><strong>Current Reading:</strong> {{ number_format($reading->kwhNo, 2) }} kWh</p>
                    <p><strong>Previous Reading:</strong> {{ number_format($reading->previousKwh, 2) }} kWh</p>
                    <p><strong>Usage:</strong> {{ number_format($reading->usage, 2) }} kWh over {{ $days }} days</p>

                </div>
                <div class="col-md-6">
                    @if (!$customer)
                        {{-- <div class="mb-3">
                            <label for="customer_select" class="form-label"><strong>Select Customer:</strong></label>
                            <select name="customer_id" id="customer_select" class="form-control" required>
                                <option value="">-- Select Customer --</option>
                                @foreach (App\Models\User::orderBy('f_name')->get() as $user)
                                    <option value="{{ $user->id }}" data-email="{{ $user->email }}"
                                        data-name="{{ $user->f_name . ' ' . $user->l_name }}">
                                        {{ $user->f_name . ' ' . $user->l_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p><strong>Email:</strong> <span id="customer_email">N/A</span></p> --}}
                        <p><strong>No customer was staying</strong></p>
                    @else
                        <p><strong>Customer Name:</strong> {{ $customer_name }}</p>
                        <p><strong>Email:</strong> {{ $customer?->email }}</p>
                    @endif

                    <p>
                        <strong>Billing Period:</strong>
                        {{ \Carbon\Carbon::parse($start_date)->format('F j, Y') }}
                        to
                        {{ \Carbon\Carbon::parse($end_date)->format('F j, Y') }}
                    </p>
                </div>
            </div>


            <hr>

            @if ($customer)
                <h5>Total Bill</h5>
                <p class="fs-4">
                    <strong>{{ config('settings.currency_symbol', '$') }}{{ number_format($reading->total, 2) }}</strong>
                    <span class="text-muted">(Rate: {{ $reading->rate }} per kWh)</span>
                </p>
            @endif

            <form action="{{ route('meters.sendBill') }}" method="POST">
                @csrf

                <input type="hidden" name="new_meter_number" value="{{ $reading->new_meter_number }}">
                <input type="hidden" name="meter_number" value="{{ $reading->meter_number }}">
                <input type="hidden" name="image" value="{{ $reading->image }}">
                <input type="hidden" name="kwhNo" value="{{ $reading->kwhNo }}">
                <input type="hidden" name="prevkwhNo" value="{{ $reading->previousKwh }}">
                <input type="hidden" name="total" value="{{ $reading->total }}">
                <input type="hidden" name="siteid" value="{{ $reading->siteid }}">
                <input type="hidden" name="reservation_id" value="{{ $reservation_id }}">

                <input type="hidden" name="customer_id" id="hidden_customer_id" value="{{ $customer?->id }}">
                <input type="hidden" name="usage" value="{{ $reading->usage }}">
                <input type="hidden" name="rate" value="{{ $reading->rate }}">
                <input type="hidden" name="days" value="{{ $days }}">
                <input type="hidden" name="start_date" value="{{ $start_date }}">
                <input type="hidden" name="end_date" value="{{ $end_date }}">

                <div class="d-flex gap-3">
                    <a href="{{ route('meters.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">
                        {{ $reading->new_meter_number || !$customer ? 'Save' : 'Save and Send Bill' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('customer_select');
            const emailSpan = document.getElementById('customer_email');
            const hiddenCustomerId = document.getElementById('hidden_customer_id');

            if (select) {
                select.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const email = selected.getAttribute('data-email') || 'N/A';
                    const id = selected.value;

                    emailSpan.textContent = email;
                    hiddenCustomerId.value = id;
                });
            }
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
        document.querySelectorAll('form[action="{{ route('meters.scan') }}"]').forEach(form => {
            form.addEventListener('submit', showLoading);
        });
    </script>
@endpush
