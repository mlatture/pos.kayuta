@extends('layouts.admin')

@section('title', 'Admins Management')
@section('content-header', 'Admins Management')
@section('content-actions')
    <a href="{{ route('admins.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Admin</a>
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
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Admin Role</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($admins as $admin)
                              
                                        <tr>
                                            <td>{{ $admin->id }}</td>
                                            <td><img src="{{ asset('images/logo.png') }}" class="org-img img-fluid" alt=""></td>
                                            <td><span class="name">{{ $admin->name }}</span></td>
                                            <td><span class="phone">{{ $admin->phone }}</span></td>
                                            <td><span class="address_2">{{ $admin->email }}</span></td>
                                            <td><span class="admin-role">{{ $admin->role }}</span></td>

                                            <td>
                                                <span @class(["right","badge","badge-success" => $admin->status, 'badge-danger' => !$admin->status])>{{ $admin->status ? "Active" : "Inactive" }}</span>
                                            </td>
                                            <td>
                                                <span class="created_at">{{ $admin->created_at->format('m/d/Y') }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admins.edit',$admin->id) }}" class="btn btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                                <form action="{{ route('admins.destroy',$admin->id) }}"
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
