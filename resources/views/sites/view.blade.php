@extends('layouts.admin')

@section('title', 'Site Details')
@section('content-header', 'Site Details')

@section('content')
<div class="card shadow-lg border-0">
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Site Id:</strong> {{ $site->siteid ?? 'N/A' }}</p>
                    <p><strong>Site Name:</strong> {{ $site->sitename ?? 'N/A' }}</p>
                    <p><strong>Site Class:</strong> {{ $site->siteclass ?? 'N/A' }}</p>
                    <p><strong>Site Hookup:</strong> {{ $site->hookup ?? 'N/A' }}</p>
                    <p><strong>Available Online:</strong> {{ $site->availableonline ? 'Yes' : 'No' }}</p>
                    <p><strong>Available:</strong> {{ $site->available ? 'Yes' : 'No' }}</p>
                    <p><strong>Seasonal:</strong> {{ $site->seasonal ? 'Yes' : 'No' }}</p>
                    <p><strong>Max Length:</strong> {{ $site->maxlength ?? 'N/A' }}</p>
                    <p><strong>Min Length:</strong> {{ $site->minlength ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Rig Types:</strong></p>
                    @php
                        $rigtypes = is_array($site->rigtypes) ? $site->rigtypes : json_decode($site->rigtypes, true);
                    @endphp
                    @if(!empty($rigtypes))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($rigtypes as $rigtype)
                                <span class="badge bg-primary p-2">{{ $rigtype }}</span>
                            @endforeach
                        </div>
                    @else
                        <p>N/A</p>
                    @endif

                    <p><strong>Class:</strong> {{ $site->class ?? 'N/A' }}</p>
                    <p><strong>Coordinates:</strong> {{ $site->coordinates ?? 'N/A' }}</p>
                    <p><strong>Attributes:</strong> {{ $site->attributes ?? 'N/A' }}</p>
                    
                    <p><strong>Amenities:</strong></p>
                    @php
                        $amenities = is_array($site->amenities) ? $site->amenities : json_decode($site->amenities, true);
                    @endphp
                    @if(!empty($amenities))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($amenities as $amenity)
                                <span class="badge bg-success p-2">{{ $amenity }}</span>
                            @endforeach
                        </div>
                    @else
                        <p>N/A</p>
                    @endif

                    <p><strong>Description:</strong> {{ $site->description ?? 'N/A' }}</p>
                    <p><strong>Rate Tier:</strong> {{ $site->ratetier ?? 'N/A' }}</p>
                    <p><strong>Tax:</strong> {{ $site->tax ?? 'N/A' }}</p>
                    <p><strong>Minimum Stay:</strong> {{ $site->minimumstay ?? 'N/A' }}</p>
                    <p><strong>Site Section:</strong> {{ $site->sitesection ?? 'N/A' }}</p>
                </div>
            </div>

            <h4 class="mt-4">Images</h4>
            <div class="row">
                @if(!empty($site->images))
                    @foreach(json_decode($site->images, true) as $image)
                        <div class="col-md-3 mb-3">
                            <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded shadow" alt="Site Image">
                        </div>
                    @endforeach
                @else
                    <p>N/A</p>
                @endif
            </div>
            
            <h4 class="mt-4">Virtual Link</h4>
            <p>
                @if($site->virtual_link)
                    <a href="{{ $site->virtual_link }}" target="_blank" class="btn btn-primary">View Virtual Tour</a>
                @else
                    N/A
                @endif
            </p>
            
            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection