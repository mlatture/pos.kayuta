@extends('layouts.admin')

@section('title', 'Product Vendors')
@section('content-header', 'Product Vendors')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_product_vendors.value'))
    <a href="{{ route('product-vendors.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Product Vendor</a>
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
                                    <th>Name</th>
                                    <th>Contact Name</th>
                                    <th>Email</th>
                                    <th>Work Phone</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($productVendors as $k => $productVendor)
                                    <tr>
                                        <td>{{ $productVendor->id }}</td>
                                        <td>{{ Str::limit($productVendor->name, 20) }}</td>
                                        <td>{{ $productVendor->contact_name }}</td>
                                        <td>{{ $productVendor->email }}</td>
                                        <td>{{ $productVendor->work_phone }}</td>
                                        <td>{{ $productVendor->created_at }}</td>
                                        <td>{{ $productVendor->updated_at }}</td>
                                        <td>
                                            @hasPermission(config('constants.role_modules.edit_product_vendors.value'))
                                            <a href="{{ route('product-vendors.edit', $productVendor) }}" class="btn btn-primary"><i
                                                    class="fas fa-edit"></i></a>
                                            @endHasPermission
                                            @hasPermission(config('constants.role_modules.delete_products.value'))
                                            <button class="btn btn-danger btn-delete"
                                                    data-url="{{ route('product-vendors.destroy', $productVendor) }}"><i
                                                    class="fas fa-trash"></i></button>
                                            @endHasPermission
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
                    text: "Do you really want to delete this product Vendor?",
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
