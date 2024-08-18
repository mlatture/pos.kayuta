@extends('layouts.admin')

@section('title', 'Create Organization')
@section('content-header', 'Create Organization')

@section('content')
    <div class="card">
        <div class="card-body">

            <form action="{{ route('organizations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        id="name" placeholder="Enter Name" required value="{{ old('name') }}">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address_1">Address 1</label>
                    <input type="text" name="address_1" class="form-control @error('address_1') is-invalid @enderror"
                        id="address_1" placeholder="Address 1" required value="{{ old('address_1') }}">
                    @error('address_1')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="address_2">Address 2</label>
                    <input type="text" name="address_2" class="form-control @error('address_2') is-invalid @enderror"
                        id="address_2" placeholder="Address 2" value="{{ old('address_2') }}">
                    @error('address_2')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                        id="city" placeholder="City" required value="{{ old('city') }}">
                    @error('city')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                        id="state" placeholder="State" required value="{{ old('state') }}">
                    @error('state')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="zip">Zip</label>
                    <input type="text" name="zip" class="form-control @error('zip') is-invalid @enderror"
                        id="zip" placeholder="Zip" required value="{{ old('zip') }}">
                    @error('zip')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
                        id="country" placeholder="Country" required value="{{ old('country','USA') }}">
                    @error('country')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Select Status</option>
                        <option {{ (old('status') == "Active") ? "selected" : "" }} value="Active">Active</option>
                        <option {{ (old('status') == "Inactive") ? "selected" : "" }} value="Inactive">Inactive</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <button class="btn btn-success btn-block btn-lg" type="submit">Create Organization</button>
            </form>
        </div>
    </div>
@endsection

