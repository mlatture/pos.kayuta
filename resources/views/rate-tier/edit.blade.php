@extends('layouts.admin')

@section('title', 'Edit Site')
@section('content-header', 'Edit Site')

@section('content')
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <div class="container">
                <form action="{{ route('rate-tier.update', $rateTier->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="tier">Tier </label>
                        <input type="text" name="tier" class="form-control @error('tier') is-invalid @enderror"
                            id="tier" value="{{ old('tier', $rateTier->tier) }}">
                        @error('tier')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="minimumstay">Minimum Stay</label>
                        <input type="number" name="minimumstay"
                            class="form-control @error('minimumstay') is-invalid @enderror" id="minimumstay"
                            value="{{ old('minimumstay', $rateTier->minimumstay) }}">
                        @error('minimumstay')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>


                    <div class="form-group form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="useflatrate" id="useflatrate" value="1"
                            {{ old('useflatrate', $rateTier->useflatrate) ? 'checked' : '' }}>
                        <label class="form-check-label" for="useflatrate">Use Flat Rate</label>
                    </div>


                    <div class="form-group form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="usedynamic" id="usedynamic" value="1"
                            {{ old('usedynamic', $rateTier->usedynamic) ? 'checked' : '' }}>
                        <label class="form-check-label" for="usedynamic"> Use Dynamic</label>
                    </div>

                    <div class="form-group">
                        <input type="number" name="flatrate" class="form-control @error('flatrate') is-invalid @enderror"
                            id="flatrate" value="{{ old('flatrate', $rateTier->flatrate) }}">
                        @error('flatrate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>


                    <div class="form-group">
                        <label for="weeklyrate">Weekly Rate</label>
                        <input type="number" name="weeklyrate"
                            class="form-control @error('weeklyrate') is-invalid @enderror" id="weeklyrate"
                            value="{{ old('weeklyrate', $rateTier->weeklyrate) }}">
                        @error('weeklyrate')
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
