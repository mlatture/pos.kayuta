@extends('layouts.admin')

@section('title', 'Seasonal Renewal')
@push('css')
    <style>
        .main-header {
            display: none !important;
        }
    </style>
@endpush
@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 mx-auto" style="max-width: 600px;">
        <div class="card-header bg-success text-white text-center">
            <h4 class="mb-0">Seasonal Renewal Invitation</h4>
        </div>

        <div class="card-body text-center">
            <h5 class="mb-3">Hello {{ $user->f_name . ' ' . $user->l_name ?? 'Guest' }} 👋</h5>
            
            <p class="lead mb-4">
                We’re excited to invite you to renew your seasonal site for the upcoming season.
            </p>

            <div class="mb-4">
                <div class="text-muted mb-2">Your Offered Rate:</div>
                <h2 class="text-primary fw-bold">${{ number_format($renewal->offered_rate, 2) }}</h2>

                @if($renewal->created_at)
                    <p class="text-muted mt-2">
                        Please respond by <strong>{{ $renewal->created_at->addDays(14)->format('F j, Y') }}</strong>
                    </p>
                @endif
            </div>

            <form action="{{ route('seasonal.renewal.respond', $user->id) }}" method="POST">
                @csrf

                <div class="d-grid gap-3">
                    <button type="submit" name="response" value="accepted" class="btn btn-success btn-lg">
                        ✅ Yes, Renew My Site
                    </button>

                    <button type="submit" name="response" value="declined" class="btn btn-outline-secondary btn-lg">
                        ❌ No, I’m Not Renewing
                    </button>
                </div>
            </form>
        </div>

        <div class="card-footer text-muted text-center">
            If you have questions, please contact our office.
        </div>
    </div>
</div>
@endsection
