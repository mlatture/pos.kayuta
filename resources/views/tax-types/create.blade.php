@extends('layouts.admin')

@section('title', 'Create Tax Type')
@section('content-header', 'Create Tax Type')

@section('content')

    <div class="card">
        <div class="card-body">
            <!-- Log on to codeastro.com for more projects -->
            <form action="{{ route('tax-types.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                        id="title" placeholder="Enter Tax Title" value="{{ old('title') }}" required minlength="3" maxlength="30">
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- <div class="form-group">
                    <label for="tax_type">Tax Type</label>
                    <select name="tax_type" class="form-control @error('tax_type') is-invalid @enderror" id="tax_type">
                        <option value="">Select Tax Type</option>
                        <option value="fixed_amount" {{ old('tax_type') === 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage" {{ old('tax_type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
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
                        id="tax" placeholder="Enter Tax" value="{{ old('tax') }}" min="1" step="0.01">
                    @error('tax')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button class="btn btn-success btn-block btn-lg" type="submit">Submit</button>
            </form><!-- Log on to codeastro.com for more projects -->
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
