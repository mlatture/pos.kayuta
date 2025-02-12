@extends('layouts.admin')

@section('title', 'Whitelist Management')
@section('content-header', 'Whitelist Management')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive m-t-40 p-3">
                            <table class="table table-hover table-striped border">
                                <thead>
                                <tr>
                                    <th>Actions</th>
                                    <th>ID</th>
                                    <th>Table Name</th>
                                    <th>Can Read?</th>
                                    <th>Can Update?</th>
                                    <th>Can Delete?</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($whitelists as $key => $whitelist)
                                    <tr>
                                        <td>
                                            @if (auth()->user()->hasPermission("read_{$whitelist->table_name}"))
                                                <a title="View {{ $whitelist->table_name }} data"
                                                   href="{{ route('admin.dynamic-module-records', $whitelist->table_name) }}"
                                                   class="btn btn-primary"><i
                                                        class="fas fa-eye"></i></a>
                                            @endif
                                            @if (auth()->user()->hasPermission("update_{$whitelist->table_name}"))
                                                <a title="Edit {{ $whitelist->table_name }} table"
                                                   href="{{ route('admin.edit-table', $whitelist->table_name) }}"
                                                   class="btn btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                            @endif
                                            @if (auth()->user()->hasPermission("delete_{$whitelist->table_name}"))
                                                <a title="Delete {{ $whitelist->table_name }} table" href="#"
                                                   class="btn btn-danger delete-table"
                                                   data-url="{{ route('admin.delete-table', $whitelist->table_name) }}">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $whitelist->id }}</td>
                                        <td>{{ $whitelist->table_name }}</td>
                                        @php
                                            $allowRead = auth()->user()->hasPermission("read_{$whitelist->table_name}");
                                            $allowUpdate = auth()->user()->hasPermission("update_{$whitelist->table_name}");
                                            $allowDelete = auth()->user()->hasPermission("delete_{$whitelist->table_name}");
                                        @endphp
                                        <td><span class="badge {{ $allowRead ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $allowRead ? 'Yes' : 'No' }}
                                                </span>
                                        </td>
                                        <td><span class="badge {{ $allowUpdate ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $allowUpdate ? 'Yes' : 'No' }}
                                                </span>
                                        </td>
                                        <td><span class="badge {{ $allowRead ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $allowDelete ? 'Yes' : 'No' }}
                                                </span>
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
                responsive: true,
                dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
                buttons: [
                    'colvis',
                    'copy',
                    {
                        extend: 'csv',
                    },
                    {
                        extend: 'excel',
                    },
                    {
                        extend: 'pdf',
                    },

                    'print'
                ],
                language: {
                    search: 'Search: ',
                    lengthMenu: 'Show _MENU_ entries',
                },
                pageLength: 10
            });

            $(document).on('click', '.delete-table', function() {
                $this = $(this);
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to delete this whitelist?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.post($this.data('url'), {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        }, function(res) {
                            $this.closest('tr').fadeOut(500, function() {
                                $(this).remove();
                            })
                        })
                    }
                })
            })
        });
    </script>
@endsection
