@extends('layouts.admin')

@section('title', 'Edit Tax Type')
@section('content-header', 'Edit Tax Type')

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('tax-types.update', $taxType) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                        id="title" placeholder="title" value="{{ old('title', $taxType->title) }}" minlength="3" maxlength="30">
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- <div class="form-group">
                    <label for="tax_type">Tax Type</label>
                    <select name="tax_type" class="form-control @error('tax_type') is-invalid @enderror"
                        id="tax_type">
                        <option value="">Select Tax Type</option>
                        <option value="fixed_amount" {{ $taxType->tax_type === 'fixed_amount' ? 'selected' : '' }}>Fixed Amount
                        </option>
                        <option value="percentage" {{ $taxType->tax_type === 'percentage' ? 'selected' : '' }}>
                            Percentage
                        </option>
                    </select>
                    @error('tax_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div> --}}

                <div class="form-group">
                    <label for="tax">Tax</label>
                    <input type="number" name="tax" class="form-control @error('tax') is-invalid @enderror"
                        id="tax" placeholder="Enter Tax" value="{{ old('tax', $taxType->tax) }}" step="0.01" min="1">
                    @error('tax')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button class="btn btn-success btn-block btn-lg" type="submit">Save Changes</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
