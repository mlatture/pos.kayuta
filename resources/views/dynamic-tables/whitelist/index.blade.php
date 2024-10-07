@extends('layouts.admin')

@section('title', 'Whitelist Management')
@section('content-header', 'Whitelist Management')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive m-t-40 p-3">
                            <table class="table table-hover table-striped border">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Table Name</th>
                                    <th>Read Permission</th>
                                    <th>Update Permission</th>
                                    <th>Delete Permission</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($whitelists as $key => $whitelist)
                                    <tr>
                                        <td>{{ $whitelist->id }}</td>
                                        <td>{{ $whitelist->table_name }}</td>
                                        <td>{{ $whitelist->read_permission_level === 1 ? 'Allowed' : 'No Allowed' }}</td>
                                        <td>{{ $whitelist->update_permission_level === 1 ? 'Allowed' : 'No Allowed' }}</td>
                                        <td>{{ $whitelist->delete_permission_level === 1 ? 'Allowed' : 'No Allowed' }}</td>
                                        <td>
                                            @if($whitelist->update_permission_level === 1)
                                                <a title="View {{ $whitelist->table_name }} data" href="{{ route('admin.dynamic-module-records', $whitelist->table_name) }}"
                                                   class="btn btn-primary"><i
                                                        class="fas fa-eye"></i></a>
                                                <a title="Create new {{ $whitelist->table_name }}" href="{{ route('admin.dynamic-module-create-form-data', $whitelist->table_name) }}"
                                                   class="btn btn-primary"><i
                                                        class="fas fa-plus"></i></a>
                                                <a title="Edit {{ $whitelist->table_name }} table" href="{{ route('admin.edit-table', $whitelist->table_name) }}"
                                                   class="btn btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('.table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection
