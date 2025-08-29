@extends('layouts.admin')

@section('title', 'Confirm Meter Reading')
@section('content-header', 'Confirm Meter Reading Preview')

@section('content')
    <div class="container">
        <div class="card shadow-sm p-4">
            <h4 class="mb-3">
                Electric Meter Reading Details (Site No: {{ $reading->siteid ?? 'N/A' }})
                @if ($reading->new_meter_number)
                    <span class="text-success ms-2">(New Meter Registered)</span>
                @endif
            </h4>

            <div class="row mb-3 align-items-start">
                <div class="col-md-6">
                    <img src="{{ asset('storage/' . $reading->image) }}" alt="Meter Image"
                        style="max-width: 50%; height: auto;">
                </div>

                <div class="col-md-6 d-flex flex-column gap-2">
                    {{-- Retry same image (sends user back to scan flow) --}}
                    <form action="{{ route('meters.scan') }}" method="POST" id="retry-form">
                        @csrf
                        <input type="hidden" name="existing_image" value="{{ $reading->image }}">
                        <button type="submit" class="btn btn-warning w-100">🔁 That doesn't seem right, try again</button>
                    </form>

                    {{-- Take another picture --}}
                    <form action="{{ route('meters.scan') }}" method="POST" enctype="multipart/form-data"
                        id="take-photo-form">
                        @csrf
                        <input type="file" name="photo" id="take-photo-input" accept="image/*" capture="environment"
                            style="display:none;" required>
                    </form>
                    <button type="button" class="btn btn-secondary w-100" id="take-photo-button">📷 Take another
                        picture</button>
                </div>
            </div>

            {{-- Loading overlay --}}
            <div id="loading-overlay"
                style="display:none !important; position:fixed; top:0; left:0; width:100%; height:100%;
                background:rgba(255,255,255,0.8); z-index:99999; align-items:center; justify-content:center;">
                <div class="text-center">
                    <i class="fa-solid fa-hourglass-end fa-spin"></i> Please wait, scanning meter...
                </div>
            </div>

            <hr>

            <form action="{{ route('meters.sendBill') }}" method="POST" id="confirm-form">
                @csrf

                <input type="hidden" id="ai_success" name="ai_success" value="true">
                <input type="hidden" name="reading_id" value="{{ $reading->id ?? '' }}">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Meter Number</strong></label>
                            <input id="meter_number" name="meter_number" class="form-control"
                                value="{{ $reading->meter_number }}" inputmode="numeric" pattern="[0-9]*"
                                autocomplete="off">
                            <small class="text-muted">Digits only (format: the “Meter: [number]” sticker).</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Current Reading (kWh)</strong></label>
                            <input id="meter_reading" name="kwhNo" type="number" step="0.001" class="form-control"
                                value="{{ number_format($reading->kwhNo, 3, '.', '') }}" autocomplete="off">
                        </div>

                        <div class="form-check mb-3 d-none" id="training_checkbox_container">
                            <input class="form-check-input" type="checkbox" value="1" id="training_checkbox"
                                name="training_opt_in">
                            <label class="form-check-label" for="training_checkbox">
                                Use this photo to help retrain the AI
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        @if (!$customer)
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

                {{-- Computed/readonly facts --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Previous Reading:</strong> {{ number_format($reading->previousKwh, 2) }} kWh</p>
                        <p><strong>Usage:</strong> {{ number_format($reading->usage, 2) }} kWh over {{ $days }}
                            days</p>
                    </div>
                    <div class="col-md-6">
                        @if ($customer)
                            <h5>Total Bill</h5>
                            <p class="fs-5 mb-0">
                                <strong>{{ config('settings.currency_symbol', '$') }}{{ number_format($reading->total, 2) }}</strong>
                                <span class="text-muted">(Rate: {{ $reading->rate }} per kWh)</span>
                            </p>
                        @endif
                    </div>
                </div>

                <hr>

                {{-- Validation banners per spec --}}
                @if ($reading->new_meter_number)
                    <div class="alert alert-warning">
                        New meter detected. Guest billing is disabled for this reading.
                    </div>
                @endif

                @if ($reading->total <= 0)
                    <div class="alert alert-secondary">
                        Bill is zero or negative. Sending is disabled.
                    </div>
                @endif

                @if (isset($reading->threshold) && $reading->total > $reading->threshold)
                    <div class="alert alert-warning">
                        This seems like it could be wrong.
                        <div class="small text-muted mb-2">
                            {{ config('settings.currency_symbol', '$') }}{{ number_format($reading->total, 2) }}
                            exceeds the {{ $days }}-day threshold of
                            {{ config('settings.currency_symbol', '$') }}{{ number_format($reading->threshold, 2) }}.
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="override_send" name="override_send"
                                value="1">
                            <label class="form-check-label" for="override_send">Send anyway?</label>
                        </div>
                    </div>
                @endif

                {{-- Hidden fields to keep your existing downstream handling intact --}}
                <input type="hidden" name="new_meter_number" value="{{ $reading->new_meter_number ? 1 : 0 }}">
                <input type="hidden" name="image" value="{{ $reading->image }}">
                <input type="hidden" name="prevkwhNo" value="{{ $reading->previousKwh }}">
                <input type="hidden" name="siteid" value="{{ $reading->siteid }}">
                <input type="hidden" name="reservation_id" value="{{ $reservation_id }}">
                <input type="hidden" name="usage" value="{{ $reading->usage }}">
                <input type="hidden" name="rate" value="{{ $reading->rate }}">
                <input type="hidden" name="days" value="{{ $days }}">
                <input type="hidden" name="start_date" value="{{ $start_date }}">
                <input type="hidden" name="end_date" value="{{ $end_date }}">

                <div class="d-flex gap-3">
                    <a href="{{ route('meters.index') }}" class="btn btn-outline-secondary">Cancel</a>

                    @php
                        $disableSend = $reading->new_meter_number || $reading->total <= 0;
                    @endphp
                    <button type="submit" class="btn btn-success" {{ $disableSend ? 'disabled' : '' }}>
                        {{ $reading->new_meter_number || !$customer ? 'Save' : 'Save and Send Bill' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function showLoading() {
            document.getElementById('loading-overlay').style.display = 'flex';
        }

        document.getElementById('take-photo-button').addEventListener('click', function() {
            document.getElementById('take-photo-input').click();
        });

        document.getElementById('take-photo-input').addEventListener('change', function() {
            if (this.files.length > 0) {
                showLoading();
                document.getElementById('take-photo-form').submit();
            }
        });

        document.querySelectorAll('form[action="{{ route('meters.scan') }}"]').forEach(form => {
            form.addEventListener('submit', showLoading);
        });

        function markAiFailed() {
            const aiHidden = document.getElementById('ai_success');
            const trainer = document.getElementById('training_checkbox_container');
            if (aiHidden) aiHidden.value = 'false';
            if (trainer) trainer.classList.remove('d-none');
        }

        ['meter_number', 'meter_reading'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('input', markAiFailed);
            el.addEventListener('change', markAiFailed);
        });

        document.getElementById('confirm-form').addEventListener('submit', function(e) {
            const overrideBox = document.getElementById('override_send');
            const thresholdExceeded =
                {{ isset($reading->threshold) && $reading->total > $reading->threshold ? 'true' : 'false' }};
            const blocked = {{ $reading->new_meter_number || $reading->total <= 0 ? 'true' : 'false' }};
            if (blocked) {
                e.preventDefault();
                return;
            }
            if (thresholdExceeded && !overrideBox?.checked) {
                e.preventDefault();
                alert('Bill exceeds the threshold. Tick "Send anyway?" to proceed.');
            }
        });
    </script>
@endpush
