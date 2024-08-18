@extends('layouts.admin')

@section('title', 'Edit Organization')
@section('content-header', 'Edit Organization')

@section('content')
    <div class="card">
        <div class="card-body">

            <form action="{{ route('organizations.update',$organization->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        id="name" placeholder="Edit your Name" value="{{ old('name',$organization->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address_1">Address 1</label>
                    <input type="text" name="address_1" class="form-control @error('address_1') is-invalid @enderror"
                        id="address_1" placeholder="Address 1" value="{{ old('address_1',$organization->address_1) }}" required>
                    @error('address_1')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address_2">Address 2</label>
                    <input type="text" name="address_2" class="form-control @error('address_2') is-invalid @enderror"
                        id="address_2" placeholder="address_2" value="{{ old('address_2',$organization->address_2) }}">
                    @error('address_2')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="City">City</label>
                    <input type="text" name="city" class="form-control @error('City') is-invalid @enderror"
                        id="City" placeholder="City" value="{{ old('city',$organization->city) }}">
                    @error('City')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="State">State</label>
                    <input type="text" name="state" class="form-control @error('State') is-invalid @enderror"
                        id="State" placeholder="State" value="{{ old('state',$organization->state) }}">
                    @error('State')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="zip">zip</label>
                    <input type="text" name="zip" class="form-control @error('zip') is-invalid @enderror"
                        id="zip" placeholder="zip" value="{{ old('zip',$organization->zip) }}">
                    @error('zip')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="Country">Country</label>
                    <input type="text" name="country" class="form-control @error('Country') is-invalid @enderror"
                        id="Country" placeholder="Country" value="{{ old('country',$organization->country) }}">
                    @error('Country')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="Status">Status</label>
                    <select name="status" id="Status" class="form-control">
                        <option value="">Select Status</option>
                        <option {{ (old('status',$organization->status) == "Active") ? "selected" : "" }} value="Active">Active</option>
                        <option {{ (old('status',$organization->status) == "Inactive") ? "selected" : "" }} value="Inactive">Inactive</option>
                    </select>
                    @error('Status')
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
