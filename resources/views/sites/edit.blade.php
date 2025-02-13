@extends('layouts.admin')

@section('title', 'Edit Site')
@section('content-header', 'Edit Site')

@section('content')
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <div class="container">
                <h2 class="mb-4">Edit Site</h2>
                <form action="{{ route('sites.update', $site) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="siteid">Siteid (Site ID)</label>
                        <input type="text" name="siteid" class="form-control @error('siteid') is-invalid @enderror"
                            id="siteid" value="{{ old('siteid', $site->siteid) }}">
                        @error('siteid')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sitename">Site Name</label>
                        <input type="text" name="sitename" class="form-control @error('sitename') is-invalid @enderror"
                            id="sitename" value="{{ old('sitename', $site->sitename) }}">
                        @error('sitename')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Add 360° Image</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="image" id="image">
                            <label class="custom-file-label" for="image">Choose Image</label>
                        </div>
                        @error('image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>



                    <div class="form-group">
                        <label for="virtual_link">Virtual Link</label>

                        <input type="text" name="virtual_link"
                            class="form-control @error('virtual_link') is-invalid @enderror" id="virtual_link"
                            value="{{ old('virtual_link', $site->virtual_link) }}">
                        @error('virtual_link')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        @php
                            $selectedHookups = is_array($site->hookup)
                                ? $site->hookup
                                : (is_string($site->hookup)
                                    ? json_decode($site->hookup, true) ?? explode(',', $site->hookup)
                                    : []);
                        @endphp
                        <label for="hookup">Site Hookup</label>
                        <select name="hookup[]" multiple class="form-control @error('hookup') is-invalid @enderror"
                            id="hookup">
                            @foreach ($siteHookup as $hookup)
                                <option value="{{ $hookup->id }}"
                                    {{ in_array($hookup->sitehookup, $selectedHookups) ? 'selected' : '' }}>
                                    {{ $hookup->sitehookup }}
                                </option>
                            @endforeach
                        </select>

                        @error('hookup')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>



                    <div class="form-group form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="availableonline" id="availableonline"
                            value="1" {{ old('availableonline', $site->availableonline) ? 'checked' : '' }}>
                        <label class="form-check-label" for="availableonline">Available Online</label>
                    </div>

                    <div class="form-group form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="available" id="available" value="1"
                            {{ old('available', $site->available) ? 'checked' : '' }}>
                        <label class="form-check-label" for="available">Available</label>
                    </div>

                    <div class="form-group form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="seasonal" id="seasonal" value="1"
                            {{ old('seasonal', $site->seasonal) ? 'checked' : '' }}>
                        <label class="form-check-label" for="seasonal">Seasonal</label>
                    </div>

                    <div class="form-group">
                        <label for="maxlength">Max Length</label>
                        <input type="text" name="maxlength" class="form-control @error('maxlength') is-invalid @enderror"
                            id="maxlength" value="{{ old('maxlength', $site->maxlength) }}">
                        @error('maxlength')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="minlength">Min Length</label>
                        <input type="text" name="minlength" class="form-control @error('minlength') is-invalid @enderror"
                            id="minlength" value="{{ old('minlength', $site->minlength) }}">
                        @error('minlength')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        @php
                            $selectedRigTypes = is_array($site->rigtypes)
                                ? $site->rigtypes
                                : (is_string($site->rigtypes)
                                    ? json_decode($site->rigtypes, true) ?? explode(',', $site->rigtypes)
                                    : []);
                        @endphp
                        <label for="rigtypes">Rig Types</label>
                        <select name="rigtypes[]" multiple class="form-control @error('rigtypes') is-invalid @enderror"
                            id="rigtypes">
                            @foreach ($rigTypes as $rigtype)
                                <option value="{{ $rigtype->id }}"
                                    {{ in_array($rigtype->rigtype, $selectedRigTypes) ? 'selected' : '' }}>
                                    {{ $rigtype->rigtype }}
                                </option>
                            @endforeach
                        </select>

                        @error('rigtypes')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="siteclass">Site Class</label>
                        @php
                            $selectedSiteClass = is_array($site->siteclass)
                                ? $site->siteclass
                                : (is_string($site->siteclass)
                                    ? explode(',', $site->siteclass)
                                    : []);

                            $selectedSiteClass = old('siteclass', $selectedSiteClass);

                            $selectedSiteClass = array_map(
                                fn($item) => str_replace(' ', '_', trim($item)),
                                $selectedSiteClass,
                            );
                        @endphp

                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($siteClass as $class)
                                @php
                                    $classValue = str_replace(' ', '_', trim($class->siteclass));
                                @endphp
                                <div class="form-check">
                                    <input type="checkbox" name="siteclass[]" value="{{ $classValue }}"
                                        class="form-check-input" id="siteclass_{{ $class->id }}"
                                        {{ in_array($classValue, $selectedSiteClass) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="siteclass_{{ $class->id }}">
                                        {{ str_replace('_', ' ', $class->siteclass) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @error('siteclass')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="coordinates">Coordinates</label>
                        <input type="text" name="coordinates"
                            class="form-control @error('coordinates') is-invalid @enderror" id="coordinates"
                            value="{{ old('coordinates', $site->coordinates) }}">
                        @error('coordinates')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="attributes">Attributes</label>
                        <input type="text" name="attributes"
                            class="form-control @error('attributes') is-invalid @enderror" id="attributes"
                            value="{{ old('attributes', $site->attributes) }}">
                        @error('attributes')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>



                      
                    <div class="form-group">
                        <label for="amenities">Amenities</label>
                        @php
                            $selectedAmenities = is_array($site->amenities) 
                                ? $site->amenities 
                                : (is_string($site->amenities) ? json_decode($site->amenities, true) ?? explode(',', $site->amenities) : []);
                    
                            $selectedAmenities = old('amenities', $selectedAmenities);
                    
                            $selectedAmenities = array_map(fn($item) => str_replace(' ', '_', trim($item)), $selectedAmenities);
                        @endphp
                    
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($amenities as $amenity)
                                @php
                                    $amenityValue = str_replace(' ', '_', trim($amenity->title));
                    
                                @endphp
                    
                                <div class="form-check">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenityValue }}" 
                                        class="form-check-input" id="amenity_{{ $amenity->id }}"
                                        {{ in_array($amenityValue, $selectedAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="amenity_{{ $amenity->id }}">
                                        {{ str_replace('_', ' ', $amenity->title) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    
                        @error('amenities')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    


                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description">{{ old('description', $site->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label for="rate_tier">Rate Tier</label>
                        <input type="text" name="rate_tier"
                            class="form-control @error('rate_tier') is-invalid @enderror" id="rate_tier"
                            value="{{ old('rate_tier', $site->ratetier) }}">
                        @error('rate_tier')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tax">Tax</label>
                        <input type="text" name="tax"
                            class="form-control @error('tax') is-invalid @enderror" id="tax"
                            value="{{ old('tax', $site->tax) }}">
                        @error('tax')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="minimumstay">Minimum Stay</label>
                        <input type="text" name="minimumstay"
                            class="form-control @error('minimumstay') is-invalid @enderror" id="minimumstay"
                            value="{{ old('minimumstay', $site->minimumstay) }}">
                        @error('minimumstay')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sitesection">Site Section</label>
                        <input type="text" name="sitesection"
                            class="form-control @error('sitesection') is-invalid @enderror" id="sitesection"
                            value="{{ old('sitesection', $site->sitesection) }}">
                        @error('sitesection')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>



                  
                    



                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-secondary" type="reset">Reset</button>
                        <button class="btn btn-success" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
