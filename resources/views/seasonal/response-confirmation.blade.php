
@extends('layouts.admin')

@section('title', 'Thank You')
@push('css')
    <style>
        .main-header {
            display: none !important;
        }
    </style>
@endpush
@section('content')
<div class="container py-5 text-center">
    <div class="alert alert-{{ $status === 'accepted' ? 'success' : 'secondary' }}">
        <h4 class="alert-heading">
            {{ $status === 'accepted' ? 'Thank you for renewing!' : 'No worries!' }}
        </h4>
        <p>
            {{ $status === 'accepted' 
                ? 'We’ve recorded your renewal. You’ll receive further instructions soon.' 
                : 'We’ve noted that you’re not renewing for the upcoming season.' }}
        </p>
    </div>
</div>
@endsection
