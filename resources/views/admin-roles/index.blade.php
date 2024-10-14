@extends('layouts.admin')

@section('title', 'Admins Roles Management')
@section('content-header', 'Admins Roles Management')
@section('content-actions')
    <a href="{{ route('admin-roles.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Admin
        Role</a>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
@endsection
@section('content')
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive m-t-40 p-0">
                            <table class="display nowrap table table-hover table-striped border p-0" cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Module Access</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $permissions = [];
                                @endphp

                                @foreach($adminRoles as $adminRole)
                                    <tr>
                                        <td>{{ $adminRole->id }}</td>
                                        <td><span class="name">{{ $adminRole->name }}</span></td>
                                        <td class="text-wrap">
                                            @foreach($adminRole->module_access as $access)
                                                {{--                                                <div class="row">--}}
                                                {{--                                                    <div class="col">--}}
                                                {{--                                                        @if(isset(config('constants.role_modules')[$access]))--}}
                                                {{--                                                            {{ config('constants.role_modules')[$access]['name'] }}--}}
                                                {{--                                                        @else--}}
                                                {{--                                                            {{ ucwords(str_replace('_', ' ', $access)) }}--}}
                                                {{--                                                        @endif--}}
                                                @php
                                                    $permissionName = isset(config('constants.role_modules')[$access])
                                                        ? config('constants.role_modules')[$access]['name']
                                                        : ucwords(str_replace('_', ' ', $access));
                                                @endphp
                                                <span class="badge badge-info">{{ $permissionName }}</span>
                                                {{--                                                    </div>--}}
                                                {{--                                                </div>--}}
                                            @endforeach
                                        </td>
                                        <td>
                                            <span  @class(["right","badge","badge-success" => $adminRole->status,'badge-danger' => !$adminRole->status])>{{ $adminRole->status  ? "Active" : "Inactive" }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="created_at">{{ $adminRole->created_at->format('m/d/Y') }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin-roles.edit',$adminRole->id) }}"
                                               class="btn btn-primary"><i
                                                    class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin-roles.destroy',$adminRole->id) }}"
                                                  method="post" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-delete" type="submit"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- {{ $customers->render() }} --}}
                </div>
            </div>
        </div>
    </div>
@endsection
