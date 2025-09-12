@extends('layouts.admin')

@section('title', 'Confirm Meter Reading')
@section('content-header', 'Confirm Meter Reading Preview')
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
            <h4 class="mb-3">
                Electric Meter Reading Details (Site No: {{ $reading->siteid ?? 'N/A' }})
                @if (!$site?->meter_number)
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
                    {{-- <form action="{{ route('meters.scan') }}" method="POST" id="retry-form">
                        @csrf
                        <input type="hidden" name="existing_image" value="{{ $reading->image }}">
                        <button type="submit" class="btn btn-warning w-100">🔁 That doesn't seem right, try again</button>
                    </form> --}}

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

            <form id="confirm-form">
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
                            <div class="invalid-feedback d-block" id="err_meter_number" style="display:none;"></div>

                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Current Reading (kWh)</strong></label>
                            <input id="meter_reading" name="kwhNo" type="number" step="0.001" class="form-control"
                                value="{{ number_format($reading->kwhNo, 3, '.', '') }}" autocomplete="off">
                            <div class="invalid-feedback d-block" id="err_kwhNo" style="display:none;"></div>

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



                        <div class="mb-3">
                            @if (!$site?->meter_number)
                                <div class="mb-3">
                                    <label for="siteid" class="form-label"><strong>Assign to Site</strong></label>
                                    <input type="text" name="assign_siteid" id="siteid" class="form-control"
                                        placeholder="Enter Site ID">
                                    <small class="text-muted">Type to search. Site must already exist.</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Computed/readonly facts --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Previous Reading:</strong> {{ number_format($reading->previousKwh, 2) }} kWh</p>
                        <p><strong>Usage:</strong> {{ number_format($reading->usage, 2) }} kWh over {{ $reading->days }}
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

                @if (!$site?->meter_number)
                    <div class="alert alert-warning">
                        Are you sure you want to assign <strong>{{ $reading->meter_number }}</strong> to Site:
                        <strong><span id="confirmSite"></span></strong>?
                    </div>
                @endif

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
                            exceeds the {{ $reading->days }}-day threshold of
                            {{ config('settings.currency_symbol', '$') }}{{ number_format($reading->threshold, 2) }}.
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="override_send" name="override_send"
                                value="1">
                            <label class="form-check-label" for="override_send">Send anyway?</label>
                        </div>
                    </div>
                @endif


                <input type="hidden" id="ai_success" name="ai_success" value="true">
                <input type="hidden" id="ai_fixed" name="ai_fixed" value="false">

                <input type="hidden" name="reading_id" value="{{ $reading->id ?? '' }}">

                <input type="hidden" name="reservation_id" value="{{ $reservation_id }}">
                @if ($customer)
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                @endif

                <input type="hidden" name="new_meter_number" value="{{ $reading->new_meter_number ? 1 : 0 }}">
                <input type="hidden" name="image" value="{{ $reading->image }}">
                <input type="hidden" name="prevkwhNo" value="{{ $reading->previousKwh }}">
                <input type="hidden" name="siteid" value="{{ $reading->siteid }}">
                <input type="hidden" name="usage" value="{{ $reading->usage }}">
                <input type="hidden" name="rate" value="{{ $reading->rate }}">
                <input type="hidden" name="days" value="{{ $reading->days }}">
                <input type="hidden" name="start_date" value="{{ $start_date }}">
                <input type="hidden" name="end_date" value="{{ $end_date }}">

                <input type="hidden" name="action" id="form-action" value="save">

                <div class="d-flex gap-3">
                    <a href="{{ route('meters.index') }}" class="btn btn-outline-secondary">Cancel</a>

                    <button type="submit" class="btn btn-primary" id="btnSave">
                        Save
                    </button>


                    <button type="submit" id="btnSendBill" class="btn btn-success" @disabled(!$customer || $reading->total <= 0 || $reading->new_meter_number)>
                        Save and Send Bill
                    </button>



                </div>


            </form>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    {{-- Save Data --}}
    <script>
        $('#btnSave').click(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('meters.saveReading') }}",
                method: "POST",
                data: $('#confirm-form').serialize() + '&action=save',
                dataType: 'json',
                success: function(res) {
                    if (res.ok) {
                        alert(res.message || 'Meter reading saved.');
                        const to = res.redirect_url || "{{ route('meters.index') }}";
                        window.location.href = to;
                    } else {
                        alert(res.message || 'Unable to save meter reading.');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const json = xhr.responseJSON || {};
                        alert(json.message || 'Please correct the highlighted fields.');
                    } else if (xhr.status === 400 || xhr.status === 409) {
                        const json = xhr.responseJSON || {};
                        alert(json.message || 'Validation failed.');
                    } else {
                        console.error(xhr.responseText);
                        alert('Unexpected error while saving the meter reading.');
                    }
                }
            });
        });
    </script>


    {{-- Send Bill Ajax --}}
    <script>
        function clearErrors() {
            ['meter_number', 'kwhNo'].forEach(name => {
                const el = document.querySelector(`[name="${name}"]`);
                el?.classList.remove('is-invalid');
            });
            ['err_meter_number', 'err_kwhNo'].forEach(id => {
                const e = document.getElementById(id);
                if (e) {
                    e.style.display = 'none';
                    e.textContent = '';
                }
            });

        }

        function showFieldError(name, msg) {
            const input = document.querySelector(`[name="${name}"]`);
            const errEl = document.getElementById(`err_${name}`);
            input?.classList.add('is-invalid');
            if (errEl) {
                errEl.textContent = msg;
                errEl.style.display = 'block';
            }
        }

        function disableSend(disabled = true) {
            const btn = document.getElementById('btnSendBill');
            if (btn) {
                btn.disabled = disabled;
                btn.textContent = disabled ? 'Sending…' : 'Save and Send Bill';
            }
        }

        $(document).ready(function() {
            $('#btnSendBill').click(function(e) {
                e.preventDefault();
                clearErrors();
                const thresholdExceeded =
                    {{ isset($reading->threshold) && $reading->total > $reading->threshold ? 'true' : 'false' }};
                const totalIsZeroOrNegative = {{ $reading->total <= 0 ? 'true' : 'false' }};
                const isNewMeter = {{ $reading->new_meter_number ? 'true' : 'false' }};
                const overrideBox = document.getElementById('override_send');

                if (isNewMeter) return alert('New meter detected. You can Save, but cannot Send.');
                if (totalIsZeroOrNegative) return alert(
                    'Total is zero or negative. You can Save, but cannot Send.');
                if (thresholdExceeded && !overrideBox?.checked) {
                    return alert('Bill exceeds the threshold. Tick "Send anyway?" to proceed.');
                }

                const form = document.getElementById('confirm-form');
                const fd = new FormData(form);
                fd.set('action', 'send');
                fd.set('ai_success', document.getElementById('ai_success')?.value || 'true');
                fd.set('ai_fixed', document.getElementById('ai_fixed')?.value || 'false');

                const assignSite = document.getElementById('siteid')?.value || '';
                if (assignSite) fd.set('siteid', assignSite);

                disableSend(true);
                showLoading();

                $.ajax({
                    url: "{{ route('meters.sendBill') }}",
                    method: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res) {
                        disableSend(false);
                        document.getElementById('loading-overlay').style.display = 'none';

                        if (res.ok) {
                            alert(res.message || 'Bill saved and sent.');
                            const to = res.redirect_url || "{{ route('meters.index') }}";
                            window.location.href = to;
                        } else {
                            if (res.errors && typeof res.errors === 'object') {
                                Object.entries(res.errors).forEach(([field, msgs]) => {
                                    showFieldError(field, Array.isArray(msgs) ? msgs[
                                        0] : String(msgs));
                                });
                            }
                            alert(res.message || 'Unable to send bill.');
                        }
                    },
                    error: function(xhr) {
                        disableSend(false);
                        document.getElementById('loading-overlay').style.display = 'none';

                        if (xhr.status === 422) {
                            const json = xhr.responseJSON || {};
                            const errs = (json.errors || {});
                            Object.entries(errs).forEach(([field, msgs]) => {
                                showFieldError(field, Array.isArray(msgs) ? msgs[0] :
                                    String(msgs));
                            });
                            alert(json.message || 'Please correct the highlighted fields.');
                        } else if (xhr.status === 400 || xhr.status === 409) {
                            const json = xhr.responseJSON || {};
                            alert(json.message || 'Validation failed.');
                        } else {
                            console.error(xhr.responseText);
                            alert('Unexpected error while sending the bill.');
                        }
                    }
                });
            })
        })
    </script>

    <script>
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



        document.getElementById('confirm-form').addEventListener('submit', function(e) {
            const action = document.getElementById('form-action')?.value || 'save';

            if (action === 'save') return;

            const thresholdExceeded =
                {{ isset($reading->threshold) && $reading->total > $reading->threshold ? 'true' : 'false' }};
            const totalIsZeroOrNegative = {{ $reading->total <= 0 ? 'true' : 'false' }};
            const overrideBox = document.getElementById('override_send');

            if (totalIsZeroOrNegative) {
                e.preventDefault();
                alert('Total is zero or negative. You can Save, but cannot Send.');
                return;
            }
            if (thresholdExceeded && !overrideBox?.checked) {
                e.preventDefault();
                alert('Bill exceeds the threshold. Tick "Send anyway?" to proceed.');
            }
        });

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
            document.getElementById('ai_success').value = 'false';
            document.getElementById('ai_fixed').value = 'true';
            document.getElementById('training_checkbox_container')?.classList.remove('d-none');
        }
        ['meter_number', 'meter_reading'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('input', markAiFailed);
            el.addEventListener('change', markAiFailed);
        });
    </script>
@endpush
