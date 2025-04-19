@extends('layouts.admin')

@section('title', 'Product Management')
@section('content-header', 'Product Management')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_products.value'))
        <a href="{{ route('products.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Product</a>
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
                                    <tr>
                                        <th>Actions</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        <th>Barcode</th>
                                        <th>Item Cost</th>
                                        <th>Item Price</th>
                                        <th>Quantity</th>
                                        <th>Status</th>

                                        <th>Suggested Add On</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $k => $product)
                                        <tr>
                                            <td>
                                                @hasPermission(config('constants.role_modules.edit_products.value'))
                                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i
                                                            class="fas fa-edit"></i></a>
                                                @endHasPermission
                                                @hasPermission(config('constants.role_modules.delete_products.value'))
                                                    <button class="btn btn-danger btn-delete"
                                                        data-url="{{ route('products.destroy', $product) }}"><i
                                                            class="fas fa-trash"></i></button>
                                                @endHasPermission
                                            </td>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ Str::limit($product->name, 20) }}</td>
                                            <td>
                                                <img class="product-img img-thumbnail"
                                                src="{{ file_exists(public_path('assets/products/' . $product->image)) ? asset('assets/products/' . $product->image) : asset('images/product-thumbnail.jpg') }}"

                                                width="60px" height="60px" alt="{{ $product->name }}">
                                            </td>

                                            <td>{{ $product->barcode }}</td>
                                            <td>{{ config('settings.currency_symbol') }}{{ $product->cost }}</td>
                                            <td>{{ config('settings.currency_symbol') }}{{ $product->price }}</td>
                                            <td>{{ $product->quantity }}</td>
                                            <td>
                                                <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                                    {{ $product->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>

                                            <td>

                                                <span
                                                    class="suggested-addon-badge badge badge-{{ $product->suggested_addon ? 'success' : 'secondary' }}"
                                                    data-id="{{ $product->id }}">
                                                    {{ $product->suggested_addon ? 'Yes' : 'No' }}
                                                </span>
                                            </td>


                                            <td>{{ $product->created_at }}</td>
                                            <td>{{ $product->updated_at }}</td>
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
                    text: "Do you really want to delete this product?",
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
