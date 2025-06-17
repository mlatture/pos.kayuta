@extends('layouts.admin')

@section('title', 'Confirm Meter Reading')
@section('content-header', 'Confirm Meter Reading Preview')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h4 class="mb-3">Electric Meter Reading Details</h4>

        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>Meter Number:</strong> {{ $reading->meter_number }}</p>
                <p><strong>Current Reading:</strong> {{ number_format($reading->kwhNo, 2) }} kWh</p>
                <p><strong>Previous Reading:</strong> {{ number_format($reading->kwhNo - $usage, 2) }} kWh</p>
                <p><strong>Usage:</strong> {{ number_format($usage, 2) }} kWh over {{ $days }} days</p>
            </div>
            <div class="col-md-6">
                <p><strong>Customer Name:</strong> {{ $customer_name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $customer?->email ?? 'N/A' }}</p>
                <p><strong>Site:</strong> {{ $site?->siteno ?? 'N/A' }}</p>
                <p><strong>Billing Period:</strong> {{ $start_date }} to {{ $end_date }}</p>
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
            <input type="hidden" name="kwhNo" value="{{ $reading->kwhNo }}">
            <input type="hidden" name="bill" value="{{ $reading->bill }}">
            <input type="hidden" name="image" value="{{ $reading->image }}">
            <input type="hidden" name="siteno" value="{{ $site?->siteno }}">
            <input type="hidden" name="customer_id" value="{{ $customer_id?->id }}">
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
