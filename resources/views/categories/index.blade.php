@extends('layouts.admin')

@section('title', 'Category Management')
@section('content-header', 'Category Management')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_categories.value'))
        <a href="{{ route('categories.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Category</a>
    @endHasPermission
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
@endsection
@section('content')
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card product-list">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive m-t-40 p-0">
                            <table class="display nowrap table table-hover table-striped border p-0" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr><!-- Log on to codeastro.com for more projects -->
                                        <th>Actions</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Show in POS</th>
                                        <th>QuickBooks Account Name</th>
                                        <th>Account Type</th>
                                        <th>Notes</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $k => $category)
                                        <tr>
                                            <td>
                                                @hasPermission(config('constants.role_modules.edit_categories.value'))
                                                    <a href="{{ route('categories.edit', $category) }}"
                                                        class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                                @endHasPermission
                                                @hasPermission(config('constants.role_modules.delete_categories.value'))
                                                    <button class="btn btn-danger btn-delete"
                                                        data-url="{{ route('categories.destroy', $category) }}"><i
                                                            class="fas fa-trash"></i></button>
                                                @endHasPermission
                                            </td>
                                            <td>{{ $category->id }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>
                                                <span
                                                    class="right badge badge-{{ $category->status ? 'success' : 'danger' }}">{{ $category->status ? 'Active' : 'Inactive' }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge show-in-pos-toggle bg-{{ $category->show_in_pos ? 'success' : 'secondary' }}"
                                                    data-id="{{ $category->id }}" style="cursor:pointer">
                                                    {{ $category->show_in_pos ? 'Yes' : 'No' }}
                                                </span>
                                            </td>

                                            <td>{{ $category->quick_books_account_name }}</td>
                                            <td>{{ $category->account_type }}</td>
                                            <td>{{ $category->notes }}</td>
                                            <td>{{ $category->created_at }}</td>
                                            <td>{{ $category->updated_at }}</td>
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
                    }, (_, i) => i).filter(i => i > 3),
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
                    text: "Do you really want to delete this category?",
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


        $(document).on('click', '.show-in-pos-toggle', function() {
            const badge = $(this);
            const id = badge.data('id');

            $.ajax({
                url: `/admin/categories/${id}/toggle-show-in-pos`,
                method: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    const status = response.show_in_pos;
                    const label = status ? 'Yes' : 'No';
                    const color = status ? 'success' : 'secondary';
                    badge.removeClass('bg-success bg-secondary').addClass(`bg-${color}`).text(label);
                },
                error: function() {
                    alert('Failed to update Show in POS.');
                }
            });
        });
    </script>
@endsection
