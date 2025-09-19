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
                                        <th>Actions</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Module Access</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $permissions = [];
                                    @endphp

                                    @foreach ($adminRoles as $adminRole)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin-roles.edit', $adminRole->id) }}"
                                                    class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                                <a class="btn btn-danger btn-delete"
                                                    data-url="{{ route('admin-roles.destroy', $adminRole->id) }}"><i
                                                        class="fas fa-trash"></i></a>


                                            </td>
                                            <td>{{ $adminRole->id }}</td>
                                            <td><span class="name">{{ $adminRole->name }}</span></td>
                                            <td class="text-wrap">
                                                @php
                                                    $moduleAccess = is_string($adminRole->module_access)
                                                        ? json_decode($adminRole->module_access, true)
                                                        : $adminRole->module_access;

                                                @endphp


                                                @if (!empty($moduleAccess) && is_array($moduleAccess))
                                                    @foreach ($moduleAccess as $access)
                                                        <span class="badge badge-info">
                                                            {{ ucwords(str_replace('_', ' ', e($access))) }}
                                                        </span>
                                                    @endforeach
                                                @elseif ($adminRole->id === 1)
                                                    <span class="text-muted">Access All</span>
                                                @else
                                                    <span class="text-muted">No Access</span>
                                                @endif
                                            </td>




                                            <td>
                                                <span
                                                    @class([
                                                        'right',
                                                        'badge',
                                                        'badge-success' => $adminRole->status,
                                                        'badge-danger' => !$adminRole->status,
                                                    ])>{{ $adminRole->status ? 'Active' : 'Inactive' }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="created_at">{{ $adminRole->created_at ? $adminRole->created_at->format('m/d/Y') : '' }}</span>

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

    @push('js')
        <script>
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


            $(document).on('click', '.btn-delete', function() {
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
                    text: "Do you really want to delete this role?",
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
        </script>
    @endpush
@endsection
