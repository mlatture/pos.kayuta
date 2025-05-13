@extends('layouts.admin')

@section('title', 'Business Settings')
@section('content-header', 'Business Settings')
<style>
    .preview-image {
        display: block;
        margin: 0 auto;
        max-width: 100px;
        border: none !important;
    }

    .hold-transition {
        overflow: hidden !important;
    }
</style>
@section('content')
    <div class="card">
        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-dark" id="general-tab" data-bs-toggle="tab" href="#general" role="tab">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" id="cookie-tab" data-bs-toggle="tab" href="#cookie" role="tab">Cookie Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" id="search-tab" data-bs-toggle="tab" href="#search" role="tab">Search Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" id="cart-tab" data-bs-toggle="tab" href="#cart" role="tab">Cart Settings</a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link text-dark" id="email-tab" data-bs-toggle="tab" href="#email" role="tab">Email Templates</a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link text-dark" id="pricing-tab" data-bs-toggle="tab" href="#pricing" role="tab">Use Dynamic
                    Pricing</a>
            </li>
        </ul>


        <div class="card-body tab-content" style="max-height: 75vh; overflow-y: auto;">
            @include('settings.components.general')
            @include('settings.components.cookie')
            @include('settings.components.search')
            @include('settings.components.cart')
            {{-- @include('settings.components.email-templates') --}}
            @include('settings.components.use-dynamic-pricing')

        </div>
    </div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash;
        if (hash) {
            const tabTrigger = document.querySelector(`a[href="${hash}"]`);
            if (tabTrigger) {
                new bootstrap.Tab(tabTrigger).show();
            }
        }

        const tabLinks = document.querySelectorAll('#settingsTabs a');
        tabLinks.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (e) {
                history.replaceState(null, null, e.target.getAttribute('href'));
            });
        });
    });
</script>

@endpush
