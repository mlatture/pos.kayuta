@extends('layouts.admin')

@section('title', 'Update Admin Role')
@section('content-header', 'Update Admin Role')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin-roles.update',$adminRole->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Role Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           id="name" placeholder="Admin Role Name" required value="{{ old('name',$adminRole->name) }}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Module Access</label>
                    @foreach(config('constants.role_modules') as $module)
                        @if($module['value'] != config('constants.role_modules.dashboard.value'))
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="{{ $module['value'] }}" name="module_access[]" value="{{ $module['value'] }}" {{ (in_array($module['value'],old('module_access',$adminRole->module_access))) ? "checked" : "" }} >
                                <label class="form-check-label" for="{{ $module['value'] }}">{{ $module['name'] }}</label>
                            </div>
                       @endif
                    @endforeach
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option {{ (old('status',$adminRole->status) == 1) ? "selected" : "" }} value="1">Active</option>
                        <option {{ (old('status',$adminRole->status) == 0) ? "selected" : "" }} value="0">Inactive</option>
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
            $('#adminRoleSelect').select2();
        });
    </script>
@endsection
