@extends('layouts.admin')

@section('title', 'Organizations Management')
@section('content-header', 'Organizations Management')
@section('content-actions')
    <a href="{{ route('organizations.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Organization</a>
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
                            <table class="display nowrap table table-hover table-striped border p-0" cellspacing="0" id="organizations_table"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Status </th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($organizations as $organization)
                                        <tr>
                                            <td>{{ $organization->id }}</td>
                                            <td><span class="name">{{ $organization->name }}</span></td>
                                            <td><span class="address_1">{{ $organization->full_address }}</span></td>
                                            <td>
                                                <span @class(['right','badge','badge-success' => $organization->status == 'Active','badge-danger' => $organization->status == 'Inactive'])> {{$organization->status}}</span>
                                            </td>
                                            <td>
                                                <span class="created_at">{{$organization->created_at->format('m/d/Y')}}</span>
                                            </td>
                                            <td>
                                                {{-- edit route name /organization-edit --}}
                                                <a href="{{ route('organizations.edit',$organization->id) }}" class="btn btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                                <form action="{{ route('organizations.destroy',$organization->id) }}"
                                                      method="post" class="d-inline"">
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
@push('js')
    <script>
        window.onload = function(){
            $('#organizations_table').DataTable();
        }
    </script>
@endpush
