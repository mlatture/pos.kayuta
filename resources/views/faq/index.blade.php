@extends('layouts.admin')

@section('title', 'FAQ Management')
@section('content-header', 'FAQ Management')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_faqs.value'))
        <a href="{{ route('faq.create') }}" class="btn btn-success"><i class="fas fa-plus"></i>
            {{ config('constants.role_modules.create_faqs.name') }}</a>
    @endHasPermission
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
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
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Show in Details</th>
                                        <th>Description</th>

                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>

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
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-left",
            "timeOut": "4000"
        };
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                toastr.success(@json(session('success')));
            @endif

            @if (session('error'))
                toastr.error(@json(session('error')));
            @endif
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('faq.index') }}",
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'show_in_details',
                        name: 'show_in_details'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },

                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                ],
                responsive: true,
                dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
                buttons: ['colvis', 'copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    search: 'Search: ',
                    lengthMenu: 'Show _MENU_ entries'
                },
                pageLength: 10
            });

            // $(document).on('click', '.btn-delete', function() {
            //     $this = $(this);
            //     const swalWithBootstrapButtons = Swal.mixin({
            //         customClass: {
            //             confirmButton: 'btn btn-success',
            //             cancelButton: 'btn btn-danger'
            //         },
            //         buttonsStyling: false
            //     })

            //     swalWithBootstrapButtons.fire({
            //         title: 'Are you sure?',
            //         text: "Do you really want to delete this customer?",
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonText: 'Yes, delete it!',
            //         cancelButtonText: 'No',
            //         reverseButtons: true
            //     }).then((result) => {
            //         if (result.value) {
            //             $.post($this.data('url'), {
            //                 _method: 'DELETE',
            //                 _token: '{{ csrf_token() }}'
            //             }, function(res) {
            //                 $this.closest('tr').fadeOut(500, function() {
            //                     $(this).remove();
            //                 })
            //             })
            //         }
            //     })
            // })
        })
    </script>
@endsection
