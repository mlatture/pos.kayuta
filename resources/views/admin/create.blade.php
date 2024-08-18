@extends('layouts.admin')

@section('title', 'Create Admin')
@section('content-header', 'Create Admin')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Customizing Select2 */
        .select2-container {
            width: 100%;
            /* Adjust width as needed */
        }

        /* Customizing Dropdown Position */
        .select2-container--open .select2-dropdown--below {
            top: 100%;
            /* Adjust position as needed */
        }

        /* Customizing Dropdown Width */
        .select2-dropdown {
            width: 100%;
            /* Adjust width as needed */
        }

        /* Customizing Placeholder Text */
        .select2-selection__placeholder {
            color: #999;
            /* Adjust color as needed */
        }

        .admin-select .select2-selection__arrow {
            height: 42px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default .select2-selection--single {
            height: 42px !important;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admins.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="name">Admin Name</label>
                    <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror" id="name"
                        placeholder="Enter admin name"  required value="{{ old('name') }}">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>



                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                        id="phone" placeholder="Enter your phone number" required value="{{ old('phone') }}">
                    @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        id="email" placeholder="Enter your email" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" placeholder="Enter your password" value="{{ old('password') }}" required>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="organization_id">Select Organization</label>
                    <select id="organization_id" class="js-example-basic-single admin-select w-100 " name="organization_id" required>
                        <option value="">Select Organization</option>
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="organization">Select Admin Role</label>
                    <select class="form-control" name="admin_role_id" id="admin_role_id" required>
                        <option value="">Select Admin Role</option>
                        @foreach($adminRoles as $adminRole)
                            <option value="{{ $adminRole->id }}">{{ $adminRole->name }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="form-group">
                    <label for="image">Upload Image</label>
                    <input type="file" id="image" class="form-control" name="image" accept="image/*" >
                    @error('image')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <button class="btn btn-success btn-block btn-lg" type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#organization_id').select2();
            $('#admin_role_id').select2();
            $('#status').select2();
        });
    </script>
@endsection
