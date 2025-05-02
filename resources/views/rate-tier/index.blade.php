@extends('layouts.admin')

@section('title', 'Rate Tier Management')
@section('content-header', 'Rate Tier Management')
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
                                @include('sites.nav-sites')

                            </div>
                        </div>
                        <div class="table-responsive m-t-40 p-0">
                            <table class="display nowrap table table-hover table-striped border p-0" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>#</th>
                                        <th>Tier</th>
                                        <th>Minimum Stay</th>
                                        <th>Use Flat Rate</th>
                                        <th>Flat Rate</th>
                                        <th>Weekly Rate</th>
                                        <th>Monthly Rate</th>
                                        <th>Seasonal Rate</th>
                                        <th>Use Dynamic</th>
                                        <th>Dyn. Increase</th>
                                        <th>Dyn. Increase %</th>
                                        <th>Dyn. Decrease</th>
                                        <th>Dyn. Decrease %</th>
                                        <th>Last-Min Increase</th>
                                        <th>Last-Min Days</th>
                                        <th>Early Booking Increase</th>
                                        <th>Early Booking Days</th>
                                        <th>Sun</th>
                                        <th>Mon</th>
                                        <th>Tue</th>
                                        <th>Wed</th>
                                        <th>Thu</th>
                                        <th>Fri</th>
                                        <th>Sat</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Last Modified</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($rate_tiers as $k => $tiers)
                                        <tr>
                                            <td>
                                                <a href="{{ route('rate-tier.add-image', $tiers->id) }}"
                                                    class="btn btn-outline-primary"><i class="fa-regular fa-images"></i></a>
                                                <a href="{{ route('rate-tier.edit', $tiers->id) }}"
                                                    class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                                <a class="btn btn-danger btn-delete"
                                                    data-url="{{ route('rate-tier.destroy', $tiers) }}"><i
                                                        class="fas fa-trash"></i></a>
                                            </td>
                                            <td>{{ ++$k }}</td>
                                            <td>
                                                {{ $tiers->tier ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $tiers->minimumstay ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $tiers->useflatrate ? 'Yes' : 'No' }}
                                            </td>

                                            <td>
                                                {{ $tiers->flatrate ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $tiers->weeklyrate ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $tiers->monthlyrate ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $tiers->seasonalrate ?? 'N/A' }}
                                            </td>
                                            <td>{{ $tiers->usedynamic ? 'Yes' : 'No' }}</td>
                                            <td>{{ $tiers->dynamicincrease ?? 'N/A' }}</td>
                                            <td>{{ $tiers->dynamicincreasepercent ?? 'N/A' }}</td>
                                            <td>{{ $tiers->dynamicdecrease ?? 'N/A' }}</td>
                                            <td>{{ $tiers->dynamicdecreasepercent ?? 'N/A' }}</td>
                                            <td>{{ $tiers->lastminuteincrease ?? 'N/A' }}</td>
                                            <td>{{ $tiers->lastminutedays ?? 'N/A' }}</td>
                                            <td>{{ $tiers->earlybookingincrease ?? 'N/A' }}</td>
                                            <td>{{ $tiers->earlybookingdays ?? 'N/A' }}</td>
                                            <td>{{ $tiers->sundayrate ?? 'N/A' }}</td>
                                            <td>{{ $tiers->mondayrate ?? 'N/A' }}</td>
                                            <td>{{ $tiers->tuesdayrate ?? 'N/A' }}</td>
                                            <td>{{ $tiers->wednesdayrate ?? 'N/A' }}</td>
                                            <td>{{ $tiers->thursdayrate ?? 'N/A' }}</td>
                                            <td>{{ $tiers->fridayrate ?? 'N/A' }}</td>
                                            <td>{{ $tiers->saturdayrate ?? 'N/A' }}</td>
                                            <td>{{ $tiers->check_in ?? 'N/A' }}</td>
                                            <td>{{ $tiers->check_out ?? 'N/A' }}</td>
                                            <td>{{ $tiers->lastmodified ?? 'N/A' }}</td>
                                            


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
                stateSave: true,
                dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
                buttons: [
                    'colvis',
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                columnDefs: [
                    {
                        targets: [12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
                        visible: false
                    },
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
