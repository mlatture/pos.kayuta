@extends('layouts.admin')

@section('title', 'Create Admin Role')
@section('content-header', 'Create Admin Role')

@section('content')
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin-roles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="name">Role Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        id="name" placeholder="Admin Role Name" required value="{{ old('name') }}">
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
                                <input class="form-check-input" type="checkbox" id="{{ $module['value'] }}" name="module_access[]" value="{{ $module['value'] }}" {{ (in_array($module['value'],old('module_access',[]))) ? "checked" : "" }} >
                                <label class="form-check-label" for="{{ $module['value'] }}">{{ $module['name'] }}</label>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
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
