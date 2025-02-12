@extends('layouts.admin')

@section('title', 'Site Management')
@section('content-header', 'Site Management')
@section('content-actions')
{{--    @hasPermission(config('constants.role_modules.create_sites_management.value'))--}}
{{--    <a href="{{ route('sites.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Site</a>--}}
{{--    @endHasPermission--}}
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
                                        <th> SL </th>
                                        <th> Site ID </th>
                                        <th> Site Name </th>
                                        <th> Site Class </th>
                                        <th> Available </th>
                                        <th> Max Length </th>
                                        <th> Min Length </th>
                                        <th> Right Type </th>
                                        <th> Class </th>
                                        <th> Attributes </th>
                                        <th> Amenities </th>
                                        {{-- <th> Ratetier </th> --}}
                                        {{-- <th> Action </th> --}}
                                        {{-- <th>Actions</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sites as $k => $site)
                                        <tr>
                                            <td>{{ ++$k }}</td>
                                            <td>
                                                {{ $site->siteid ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {!! Str::limit($site->sitename, 20) !!}
                                            </td>

                                            <td>
                                                {!! Str::limit($site->siteclass, 20) !!}
                                            </td>

                                            <td>
                                                {{ $site->available ? 'Available' : 'Not Available' }}
                                            </td>

                                            <td>
                                                {{ $site->maxlength ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $site->minlength ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ Str::limit(is_array($site->rigtypes) ? implode(',', $site->rigtypes) : 'No Rigtypes', 20) }}
                                            </td>

                                            <td>
                                                {!! Str::limit($site->class, 20) !!}
                                            </td>

                                            <td>
                                                {!! Str::limit($site->attributes, 20) !!}
                                            </td>

                                            <td>
                                                {{ Str::limit(is_array($site->amenities) ? implode(',', $site->amenities) : 'No Amenities', 20) }}
                                            </td>

                                            {{-- <td>
                            {{ $site->ratetier ?? 'N/A' }}
                        </td> --}}

                                            {{-- <td>
                                                <a href="{{ route('sites.edit', $site) }}" class="btn btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                                <button class="btn btn-danger btn-delete"
                                                    data-url="{{ route('sites.destroy', $site) }}"><i
                                                        class="fas fa-trash"></i></button>
                                            </td> --}}

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
        })
    </script>
@endsection
