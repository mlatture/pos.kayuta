@extends('layouts.admin')

@section('title', 'Api Channels Fees')

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="mb-4 pb-2">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center">
            <img src="{{ asset('assets/back-end/img/system-setting.png') }}" alt="" class="mr-2">
            Api Channels Fees
        </h2>
    </div>
    <!-- End Page Title -->

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-end">
                    <a href="{{ route('admin.business-settings.index') }}#apiChannels" class="btn btn-sm btn-outline-secondary">
                        &larr; Back to Channels
                    </a>
                </div>

                <div class="card-body">
                    <h5 class="mb-3">
                        Channel: <strong>{{ $channel->name }}</strong>
                        <small class="text-monospace">(#{{ $channel->id }})</small>
                    </h5>

                    {{-- Alerts --}}
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 pl-3">
                                @foreach($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('admin.api_channels.fees.update', $channel->id) }}">
                        @csrf

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="type" class="font-weight-semibold">Fee Type</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="flat" {{ old('type', $fee->type ?? '') === 'flat' ? 'selected' : '' }}>Flat ($)</option>
                                    <option value="percent" {{ old('type', $fee->type ?? '') === 'percent' ? 'selected' : '' }}>Percent (%)</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="amount" class="font-weight-semibold">Amount</label>
                                <div class="input-group">
                                    <input
                                        name="amount"
                                        id="amount"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value="{{ old('amount', $fee->amount ?? 0) }}"
                                        class="form-control">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="amtUnit">
                                            {{ (old('type', $fee->type ?? '') === 'percent') ? '%' : '$' }}
                                        </span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    If type is <em>Percent</em>, this is a percentage of the base; if <em>Flat</em>, this is a fixed amount.
                                </small>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold d-block">Active</label>
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', ($fee->is_active ?? 1)) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Enabled</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            @php
                                $vf = isset($fee->valid_from) && $fee->valid_from
                                    ? \Illuminate\Support\Carbon::parse($fee->valid_from)->format('Y-m-d\TH:i')
                                    : '';
                                $vt = isset($fee->valid_to) && $fee->valid_to
                                    ? \Illuminate\Support\Carbon::parse($fee->valid_to)->format('Y-m-d\TH:i')
                                    : '';
                            @endphp

                            <div class="form-group col-md-6">
                                <label for="valid_from" class="font-weight-semibold">Valid From</label>
                                <input name="valid_from" id="valid_from" type="datetime-local"
                                       value="{{ old('valid_from', $vf) }}" class="form-control">
                                <small class="form-text text-muted">Leave empty to start immediately.</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="valid_to" class="font-weight-semibold">Valid To</label>
                                <input name="valid_to" id="valid_to" type="datetime-local"
                                       value="{{ old('valid_to', $vt) }}" class="form-control">
                                <small class="form-text text-muted">Leave empty for no end date.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold d-block">Pass to customer</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="pass_to_customer" disabled>
                                <label class="custom-control-label text-muted" for="pass_to_customer">
                                    Disabled for v1 (always false)
                                </label>
                            </div>
                        </div>

                        {{-- Preview Calculator --}}
                        <div class="border rounded p-3 bg-light">
                            <div class="font-weight-semibold mb-2">Preview (admin-only, not guest)</div>
                            <div class="form-row align-items-end">
                                <div class="form-group col-md-4">
                                    <label for="exBase" class="mb-1">Example Base</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                        <input id="exBase" type="number" step="0.01" min="0" class="form-control" placeholder="e.g., 100.00">
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <button type="button" class="btn btn-primary btn-block" id="btnCalc">
                                        Calculate
                                    </button>
                                </div>
                                <div class="form-group col-md-5">
                                    <div class="text-monospace" id="exOut"></div>
                                </div>
                            </div>
                        </div>

                        <div class="text-right mt-4">
                            <button class="btn btn-success">
                                <i class="tio-save mr-1"></i> Save
                            </button>
                        </div>
                    </form>

                    <p class="small text-muted mt-3 mb-0">
                        Guest sees: unchanged price. Fees are internal and used only for reporting/commissions.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
(function () {
    function currentType() {
        var sel = document.getElementById('type');
        return sel ? sel.value : 'flat';
    }

    function refreshAmtUnit() {
        var unit = (currentType() === 'percent') ? '%' : '$';
        document.getElementById('amtUnit').textContent = unit;
    }

    function calc() {
        var type = currentType();
        var amt  = parseFloat(document.getElementById('amount').value || 0);
        var base = parseFloat(document.getElementById('exBase').value || 0);

        var fee = (type === 'percent') ? (base * (amt / 100)) : amt;
        var total = base + fee;

        document.getElementById('exOut').textContent =
            'Base $' + base.toFixed(2) +
            ' → Fee $' + fee.toFixed(2) +
            ' → Gross $' + total.toFixed(2);
    }

    document.getElementById('type').addEventListener('change', refreshAmtUnit);
    document.getElementById('amount').addEventListener('input', function(){ /* live update if needed */ });
    document.getElementById('btnCalc').addEventListener('click', calc);

    // init
    refreshAmtUnit();
})();
</script>
@endpush
