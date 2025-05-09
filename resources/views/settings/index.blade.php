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

</style>
@section('content')
    <div class="card">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" aria-current="page" href="#">Active</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" aria-current="page" href="#">Active</a>
            </li>
        </ul>

        <div class="card-body" id="general-card">
            <form method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Company Information --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fa-solid fa-building"></i> Company Information
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Company Name</label>
                                <input type="text" class="form-control" name="company_name"
                                    value="{{ $settings['company_name'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="company_phone"
                                    value="{{ $settings['company_phone'] ?? '' }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" class="form-control" name="company_email"
                                    value="{{ $settings['company_email'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label>Company Address</label>
                                <input type="text" class="form-control" name="company_address"
                                    value="{{ $settings['company_address'] ?? '' }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Map URL</label>
                                <input type="text" class="form-control" name="map_url"
                                    value="{{ $settings['map_url'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label>Latitude</label>
                                <input type="text" class="form-control" name="latitude"
                                    value="{{ $settings['latitude'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label>Longitude</label>
                                <input type="text" class="form-control" name="longitude"
                                    value="{{ $settings['longitude'] ?? '' }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Time Zone</label>
                                <select name="timezone" class="form-select">
                                    <option value="US/Eastern"
                                        {{ ($settings['timezone'] ?? '') == 'US/Eastern' ? 'selected' : '' }}>
                                        (GMT-05:00) Eastern Time
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Country</label>
                                <input type="text" class="form-control" name="country"
                                    value="{{ $settings['country'] ?? 'United States' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Business Information --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fa-solid fa-briefcase"></i> Business Information
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Company Copyright Text</label>
                                <input type="text" class="form-control" name="company_copyright_text"
                                    value="{{ $settings['company_copyright_text'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label>Digit After Decimal Point (Ex: 0.00)</label>
                                <input type="text" class="form-control" name="decimal_point_settings"
                                    value="{{ $settings['decimal_point_settings'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Website Color and Logos in Grid --}}
                <div class="d-grid gap-3 mb-4" style="grid-template-columns: repeat(3, 1fr);">
                    {{-- Website Color --}}
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fa-solid fa-palette"></i> Website Color
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <label class="fw-semibold">Primary Color</label>
                                    <div class="d-flex flex-column align-items-center mt-2">
                                        <input type="color" name="primaryColor"
                                            value="{{ $settings['primaryColor'] ?? '#1b7fed' }}"
                                            class="border-0 rounded shadow-sm"
                                            style="width: 80px; height: 80px; cursor: pointer;">
                                        <span class="mt-2 text-muted small">{{ $settings['primaryColor'] ?? '#1b7fed' }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="fw-semibold">Secondary Color</label>
                                    <div class="d-flex flex-column align-items-center mt-2">
                                        <input type="color" name="secondaryColor"
                                            value="{{ $settings['secondaryColor'] ?? '#000000' }}"
                                            class="border-0 rounded shadow-sm"
                                            style="width: 80px; height: 80px; cursor: pointer;">
                                        <span class="mt-2 text-muted small">{{ $settings['secondaryColor'] ?? '#000000' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                

                    {{-- Header Logo --}}
                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-image"></i> Website Header Logo
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Upload Header Logo</label>
                                <input type="file" class="form-control" name="company_web_logo">
                                @if (!empty($settings['company_web_logo']))
                                    <img src="{{ asset('storage/company/' . $settings['company_web_logo']) }}" alt="Header Logo"
                                        class="preview-image mt-2" width="100">
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Footer Logo --}}
                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-image"></i> Website Footer Logo
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Upload Footer Logo</label>
                                <input type="file" class="form-control" name="company_footer_logo">
                                @if (!empty($settings['company_footer_logo']))
                                    <img src="{{ asset('storage/company/' . $settings['company_footer_logo']) }}"
                                        alt="Footer Logo" class="preview-image mt-2" width="100">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Website Favicon, Loading Gif, App Logo  --}}
                <div class="d-grid gap-3 mb-4" style="grid-template-columns: repeat(3, 1fr);">
                    {{-- Website Favicon --}}
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fa-solid fa-image"></i> Website Favicon
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Upload Favicon</label>
                                <input type="file" class="form-control" name="company_fav_icon">
                                @if (!empty($settings['company_fav_icon']))
                                    <img src="{{ asset('storage/company/' . $settings['company_fav_icon']) }}" alt="Favicon"
                                        class="preview-image mt-2" width="100">
                                @endif
                            </div>
                        </div>
                    </div>
                

                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-image"></i> Loading Gif
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Upload Gif</label>
                                <input type="file" class="form-control" name="loader_gif">
                                @if (!empty($settings['loader_gif']))
                                    <img src="{{ asset('storage/company/' . $settings['loader_gif']) }}" alt="Loading Gif"
                                        class="preview-image mt-2" width="100">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-image"></i> App Logo
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Upload App Logo</label>
                                <input type="file" class="form-control" name="company_mobile_logo">
                                @if (!empty($settings['company_mobile_logo']))
                                    <img src="{{ asset('storage/company/' . $settings['company_mobile_logo']) }}"
                                        alt="Mobile Logo" class="preview-image mt-2" width="100">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary float-end">Save Settings</button>
            </form>
        </div>
    </div>
@endsection
