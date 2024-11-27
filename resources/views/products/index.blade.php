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
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        <th>Barcode</th>
                                        <th>Item Cost</th>
                                        <th>Item Price</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Turn on to allow this product as an add-on when a guest is making a booking.</th>
                                        <th>Suggested Add On</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $k => $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ Str::limit($product->name, 20) }}</td>
                                            <td>
                                                <img class="product-img img-thumbnail"
                                                    src="{{ $product->image && Storage::disk('public')->exists('products/' . $product->image) ? Storage::url('products/' . $product->image) : Storage::url('product-thumbnail.jpg') }}"
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
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input suggested-addon-toggle"
                                                    data-id="{{ $product->id }}"
                                                    {{ $product->suggested_addon ? 'checked' : '' }}>


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

        $(document).on('change', '.suggested-addon-toggle', function() {
            const isChecked = $(this).is(':checked');
            const productId = $(this).data('id');

            $.ajax({
                url: "{{ route('products.toggle-suggested-addon') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: productId,
                    suggested_addon: isChecked ? 1 : 0
                },
                success: function(response) {
                    const $badge = $(`.suggested-addon-badge[data-id="${productId}"]`);

                    if (isChecked) {
                        $badge.removeClass('badge-secondary').addClass('badge-success').text('Yes');
                    } else {
                        $badge.removeClass('badge-success').addClass('badge-secondary').text('No');
                    }


                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            });
        });
    </script>
@endsection
