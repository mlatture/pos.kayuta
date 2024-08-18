@extends('layouts.admin')

@section('title', 'Reservation Report')
@section('content-header', 'Reservation Report')
{{-- @section('content-actions')
    <a href="{{route('gift-cards.create')}}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Gift Card</a>
@endsection --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
@endsection
@section('content')
    <div class="row animated fadeInUp">
        <div class="card">
            <div class="card-body">
                {{-- <h4 class="card-title mb-0">Filters</h4> --}}
                <!--                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>-->
                <form action="{!! route('reports.reservationReport') !!}" method="GET">
                    <div class="row mt-3">
                        {{-- <div class="col-md-5">
                            <div class="form-group">
                                <input type="text" class="form-control" id="tb-fname" placeholder="Search"
                                    name="search" value="{!! isset($_GET['search']) ? $_GET['search'] : '' !!}">
                            </div>
                        </div> --}}
                        <div class="col-md-7">
                            <div class='input-group mb-3'>
                                <input type='text' class="form-control daterange" id="productDate" name="date"
                                    autocomplete="off" value="{!! isset($_GET['date']) ? $_GET['date'] : '' !!}" />
                                <span class="input-group-text">
                                    <span class="ti-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <button type="submit"
                                class="search-btn btn btn-primary waves-effect waves-light text-white w-100 height-55">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive m-t-40 p-0">
                        <table table class="display nowrap table table-hover table-striped border p-0" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>Checkin</th>
                                    <th>Checkout</th>
                                    <th>Customer #/POS id</th>
                                    <th>Customer</th>
                                    <th>Source</th>
                                    <th>Cart id</th>
                                    <th>Site id</th>
                                    <th>Site Name</th>
                                    <th>Amount</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $tax = 0;
                                    $discount = 0;
                                @endphp
                                @foreach ($reservations as $key => $reservation)
                                    <tr>
                                        <td>{{ date('m/d/Y', strtotime($reservation->cid)) }}</td>
                                        <td>{{ date('m/d/Y', strtotime($reservation->cod)) }}</td>
                                        <td>{{ $reservation->user->id ?? 'N/A' }}</td>
                                        <td>{{ $reservation->user ? $reservation->user->f_name . ' ' . $reservation->user->l_name : 'N/A' }}
                                        </td>
                                        <td>Reservations ({{ $reservation->source ?? '' }})</td>
                                        <td>{{ $reservation->cartid ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $reservation->siteid ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $reservation->site ? $reservation->site->sitename : 'N/A' }}
                                        </td>
                                        <td>{{ $reservation->total ?? 0 }}</td>
                                        <td>{{ $reservation->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
            $('#productDate').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#productDate').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                    'MM/DD/YYYY'));
            });

            $('#productDate').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            $('#productDate').attr("placeholder", "Select Date");

            $('.table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                searching: false
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
                    text: "Do you really want to delete this Gift Card?",
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
