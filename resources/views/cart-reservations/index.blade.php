@extends('layouts.admin')

@section('title', 'Reservation In Cart')
@section('content-header', 'Reservation In Cart')

@section('content')





    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col">
                <div class="card shadow-sm">

                    <div class="card-body">

                        <div style="">
                            <table class="table table-striped table-hover align-middle mb-0" id="reservationTable">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>ID</th>
                                        <th>Checked In Date</th>
                                        <th>Checked Out Date</th>
                                        <th>Customer Name</th>
                                        <th>Hookups</th>
                                        <th>Site ID</th>
                                        <th>Base</th>
                                        <th>Nights</th>
                                        <th>Site Class</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservations as $key => $reservation)
                                        <tr>
                                            <td>
                                                {{-- <a href="{{ route('sites.view', $reservation->id) }}" class="btn btn-info"><i
                                                        class="fas fa-eye"></i></a> --}}
                                                <a href="{{ route('reservations.payment.index', $reservation->cartid) }}"
                                                    class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                                <a class="btn btn-danger btn-delete"
                                                    data-url="{{ route('cart-reservation.destroy', $reservation->id) }}"><i
                                                        class="fas fa-trash"></i></a>
                                            </td>
                                            <td> {{ $key + 1 }} </td>
                                            <td>{{ \Carbon\Carbon::parse($reservation->cid)->format('M d, Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($reservation->cod)->format('M d, Y') }}</td>
                                            <td>{{ $reservation->customer->first_name ?? 'N/A' }}
                                                {{ $reservation->customer->last_name ?? '' }}</td>
                                            <td>{{ $reservation->hookups }}</td>
                                            <td>{{ $reservation->siteid }}</td>
                                            <td>${{ number_format($reservation->base, 2) }}</td>
                                            <td>{{ $reservation->nights }}</td>
                                            <td>{{ $reservation->siteclass }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>


                        <div id="paginationLinks" class="pagination-links text-center"></div>


                    </div>
                </div>
            </div>

         
        </div>
    </div>

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
                pageLength: 10,
                stateSave: true,
                stateSaveCallback: function(settings, data) {
                    localStorage.setItem('DataTableState', JSON.stringify(data));
                },
                stateLoadCallback: function(settings) {
                    return JSON.parse(localStorage.getItem('DataTableState'));
                }
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
                    text: "Do you really want to delete this cart reservation?",
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
