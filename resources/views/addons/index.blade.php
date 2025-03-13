@extends('layouts.admin')

@section('title', 'Add Ons Management')
@section('content-header', 'Add Ons Management')
@section('content-actions')
    {{--    @hasPermission(config('constants.role_modules.create_sites_management.value')) --}}
    {{--    <a href="{{ route('sites.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Site</a> --}}
    {{--    @endHasPermission --}}
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
                        <div class="row">
                            <div class="col-12">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb bg-light p-3 rounded">
                                        <li class="breadcrumb-item active"  aria-current="page"><a  style="text-decoration: none"href="{{ route('sites.index') }}"> Sites </a></li>
                                        <li class="breadcrumb-item"><a style="text-decoration: none" href="{{ route('sites.rate_tiers') }}">Rate Tier</a></li>
                                        <li class="breadcrumb-item"> <a style="text-decoration: none" href="{{ route('addons.index') }}">Add Ons</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <div class="table-responsive m-t-40 p-0">
                            <table class="display nowrap table table-hover table-striped border p-0" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr><!-- Log on to codeastro.com for more projects -->
                                        <th>Actions</th>
                                        <th> # </th>
                                        <th> Add On Name </th>
                                        <th>  Price </th>
                                        <th> Add On Type </th>
                                        <th> Capacity </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($addons as $k => $add)
                                    <tr>
                                        <td>
                                            {{-- <a href="{{ route('sites.view', $add->id) }}" class="btn btn-info"><i class="fas fa-eye"></i></a> --}}
                                            <a href="{{ route('addons.edit', $add->id) }}" class="btn btn-primary"><i
                                                    class="fas fa-edit"></i></a>
                                            <a class="btn btn-danger btn-delete"
                                                data-url="{{ route('addons.destroy', $add) }}"><i
                                                    class="fas fa-trash"></i></a>
                                        </td>
                                        <td>{{ ++$k }}</td>
                                            <td>
                                                {{ $add->addon_name ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ number_format($add->price, 2) ?? 0.0  }}
                                            </td>

                                            <td>
                                                {{ $add->addon_type }}
                                            </td>

                                            <td>
                                                {{ $add->capacity ?? 'N/A' }}
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
                    text: "Do you really want to delete this addon?",
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
