@extends('layouts.admin')

@section('title', 'Tax Type Management')
@section('content-header', 'Tax Type Management')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_tax_types.value'))
    <a href="{{ route('tax-types.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Tax Type</a>
    @endHasPermission
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
                                    <tr><!-- Log on to codeastro.com for more projects -->
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Tax Type</th>
                                        <th>Tax</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($taxTypes as $k => $tax)
                                        <tr>
                                            <td>{{ $tax->id }}</td>
                                            <td>{{ $tax->title }}</td>
                                            <td>{{ $tax->tax_type }}</td>
                                            <td>{{ $tax->tax_type == 'fixed_amount' ? config('settings.currency_symbol') . $tax->tax : $tax->tax . '%' }}
                                            </td>
                                            <td>{{ $tax->created_at }}</td>
                                            <td>{{ $tax->updated_at }}</td>
                                            <td>
                                                @hasPermission(config('constants.role_modules.edit_tax_types.value'))
                                                <a href="{{ route('tax-types.edit', $tax) }}" class="btn btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                                @endHasPermission
                                                @hasPermission(config('constants.role_modules.delete_tax_types.value'))
                                                <button class="btn btn-danger btn-delete"
                                                        data-url="{{ route('tax-types.destroy', $tax) }}"><i
                                                        class="fas fa-trash"></i></button>
                                                @endHasPermission
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
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
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
                    text: "Do you really want to delete this tax type?",
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
        })
    </script>
@endsection
