@extends('layouts.admin')

@section('title', 'Confirm Meter Reading')
@section('content-header', 'Confirm Meter Reading Preview')

@section('content')
    <div class="container">
        <div class="card shadow-sm p-4">
            <h4 class="mb-3">
                Electric Meter Reading Details (Site No: {{ $siteid }})
                {{-- @if ($site)
                    (Site:  {{ $site->sitename }})
                @else
                    (Site: Not found)
                @endif --}}
            </h4>
            <img src="{{ asset('storage/' . $image) }}" alt="Meter Image" style="max-width: 50%; height: auto;">
            <hr>
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Meter Number:</strong> {{ $reading->meter_number }}</p>
                    <p><strong>Current Reading:</strong> {{ number_format($reading->kwhNo, 2) }} kWh</p>
                    <p><strong>Previous Reading:</strong> {{ number_format($reading->kwhNo - $usage, 2) }} kWh</p>
                    <p><strong>Usage:</strong> {{ number_format($usage, 2) }} kWh over {{ $days }} days</p>

                </div>
                <div class="col-md-6">
                    @if (!$customer)
                        <div class="mb-3">
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
                        <p><strong>Email:</strong> <span id="customer_email">N/A</span></p>
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

            <h5>Total Bill</h5>
            <p class="fs-4">
                <strong>{{ config('settings.currency_symbol', '$') }}{{ number_format($total, 2) }}</strong>
                <span class="text-muted">(Rate: {{ $rate }} per kWh)</span>
            </p>

            <form action="{{ route('meters.sendBill') }}" method="POST">
                @csrf

                <input type="hidden" name="meter_number" value="{{ $meter_number }}">
                <input type="hidden" name="image" value="{{ $image }}">
                <input type="hidden" name="kwhNo" value="{{ $reading->kwhNo }}">
                <input type="hidden" name="bill" value="{{ $reading->bill }}">
                <input type="hidden" name="image" value="{{ $reading->image }}">
                <input type="hidden" name="siteno" value="{{ $siteid }}">
                <input type="hidden" name="customer_id" id="hidden_customer_id" value="{{ $customer?->id }}">
                <input type="hidden" name="usage" value="{{ $usage }}">
                <input type="hidden" name="rate" value="{{ $rate }}">
                <input type="hidden" name="days" value="{{ $days }}">
                <input type="hidden" name="start_date" value="{{ $start_date }}">
                <input type="hidden" name="end_date" value="{{ $end_date }}">

                <div class="d-flex gap-3">
                    <a href="{{ route('meters.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Save and Send Bill</button>
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

@endpush
