@extends('layouts.admin')

@section('title', 'Product Management')
@section('content-header', 'Product Management')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_products.value'))
        <a href="{{ route('products.create') }}" class="btn btn-success mb-2 me-2">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    @endHasPermission

    @hasPermission(config('constants.role_modules.create_products.value'))
        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="d-inline-block">
            @csrf
            <div class="input-group">
                <input type="file" name="excel_file" class="form-control form-control-sm" required>
                <button type="submit" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-file-import"></i> Import Excel
                </button>
            </div>
        </form>
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
                                        <th>Image</th>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Tax Type</th>
                                        <th>Name</th>
                                        <th>Quick Pick</th>
                                        <th>Show in Category</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                        <th>Barcode</th>
                                        <th>Account</th>
                                        <th>Markup</th>
                                        <th>Profit</th>
                                        <th>Price</th>
                                        <th>Cost</th>
                                        <th>Quantity</th>
                                        <th>Type</th>
                                        <th>Discount Type</th>
                                        <th>Discount</th>
                                        <th>Organization</th>
                                        <th>Vendor</th>
                                    
                                        <th>Suggested Addon</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>
                                                <a href="{{ route('products.edit', $product) }}"
                                                    class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                                <button class="btn btn-danger btn-sm btn-delete"
                                                    data-url="{{ route('products.destroy', $product) }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <img src="{{ $product->image ? asset('storage/products/' . $product->image) : asset('images/product-thumbnail.jpg') }}"
                                                    width="50" class="img-thumbnail" alt="{{ $product->name }}">
                                            </td>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                            <td>{{ $product->taxType->name ?? 'N/A' }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>
                                                <span
                                                    class="badge quick-pick-toggle bg-{{ $product->quick_pick ? 'success' : 'secondary' }}"
                                                    data-id="{{ $product->id }}" style="cursor:pointer">
                                                    {{ $product->quick_pick ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge show-category-toggle bg-{{ $product->show_in_category ? 'success' : 'secondary' }}"
                                                    data-id="{{ $product->id }}" style="cursor:pointer">
                                                    {{ $product->show_in_category ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge status-toggle bg-{{ $product->status ? 'success' : 'danger' }}"
                                                    data-id={{ $product->id }} style="cursor:pointer">
                                                    {{ $product->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            
                                         
                                            <td>{{ Str::limit($product->description, 50) }}</td>
                                            <td>{{ $product->barcode }}</td>
                                            <td>{{ $product->account ?? 'N/A' }}</td>
                                            <td>{{ $product->markup }}</td>
                                            <td>{{ $product->profit }}</td>
                                            <td>{{ config('settings.currency_symbol') . number_format($product->price, 2) }}
                                            </td>
                                            <td>{{ config('settings.currency_symbol') . number_format($product->cost, 2) }}
                                            </td>
                                            <td>{{ $product->quantity }}</td>
                                            <td>{{ $product->type ?? 'N/A' }}</td>
                                            <td>{{ $product->discount_type }}</td>
                                            <td>{{ $product->discount }}</td>
                                            <td>{{ $product->organization_id ?? 'N/A' }}</td>
                                            <td>{{ $product->vendor->name ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $product->suggested_addon ? 'success' : 'secondary' }}">
                                                    {{ $product->suggested_addon ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                           
                                            <td>{{ $product->created_at ? $product->created_at->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td>{{ $product->updated_at ? $product->updated_at->format('Y-m-d') : 'N/A' }}
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
        $(document).on('click', '.status-toggle', function() {
            const badge = $(this);
            const productId = badge.data('id');

            $.ajax({
                url: `/admin/products/${productId}/toggle-status`,
                method: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const newStatus = response.status ? 'Active' : 'Inactive';
                        const newClass = response.status ? 'success' : 'danger';

                        badge.removeClass('bg-success bg-danger').addClass(`bg-${newClass}`).text(
                            newStatus);
                    }
                },
                error: function(xhr) {
                    alert('Failed to update status.');
                }
            })
        })
        $(document).ready(function() {


            $('.table').DataTable({
                responsive: true,
                stateSave: true,

                dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
                buttons: [
                    'colvis', 'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    search: 'Search: ',
                    lengthMenu: 'Show _MENU_ entries',
                },
                pageLength: 10,
                columnDefs: [{
                    targets: Array.from({
                        length: 9
                    }, (_, i) => i).filter(i => i > 5),
                    visible: false
                }]
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

        $(document).on('click', '.quick-pick-toggle', function() {
            const badge = $(this);
            const id = badge.data('id');

            $.ajax({
                url: `/admin/products/${id}/toggle-quick-pick`,
                method: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    const status = response.quick_pick;
                    const label = status ? 'Yes' : 'No';
                    const color = status ? 'success' : 'secondary';
                    badge.removeClass('bg-success bg-secondary').addClass(`bg-${color}`).text(label);
                },
                error: function() {
                    alert('Failed to update Quick Pick.');
                }
            });
        });

        $(document).on('click', '.show-category-toggle', function() {
            const badge = $(this);
            const id = badge.data('id');

            $.ajax({
                url: `/admin/products/${id}/toggle-show-category`,
                method: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    const status = response.show_in_category;
                    const label = status ? 'Yes' : 'No';
                    const color = status ? 'success' : 'secondary';
                    badge.removeClass('bg-success bg-secondary').addClass(`bg-${color}`).text(label);
                },
                error: function() {
                    alert('Failed to update Show in Category.');
                }
            });
        });
    </script>
@endsection
