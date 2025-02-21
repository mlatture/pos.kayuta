@extends('layouts.admin')

@section('title', 'Reservation In Cart')
@section('content-header', 'Reservation In Cart')

@section('content')



  

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col">
                <div class="card shadow-sm">
                    {{-- <div class="card-header bg-dark">
                        <h5 class="card-title mb-0" style="color: #EFC368 !important">Confirmed Customers</h5>
                        <div
                            class="d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-end px-md-3 px-2">
                            <div class="d-flex align-items-center gap-3">

                                <a href="javacript:void(0)" class="text-white" data-bs-toggle="collapse"
                                    data-bs-target="#collapsePlanner" aria-expanded="false" aria-controls="collapsePlanner"
                                    style="color: #EFC368 !important"><i class="fa-solid fa-border-all"></i> Planner</a>
                                <a href="javacript:void(0)" class="text-white" data-bs-toggle="collapse"
                                    data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample"
                                    style="color: #EFC368 !important"><i class="fa-solid fa-users"></i> Add Customer</a>
                                <a href="javascript:void(0)" class="text-white" id="openDatePicker" role="button"
                                    style="color: #EFC368 !important">
                                    <i class="fa-solid fa-calendar-week"></i> Quick Add
                                </a>

                            </div>
                            <!-- <div>
                                                                    <a href="#" class="text-white text-decoration-none">
                                                                        <img src="{{ asset('images/help-ico.svg') }}" alt="" class="me-2" />
                                                                        Help
                                                                    </a>
                                                                </div> -->
                        </div>
                    </div> --}}
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <label for="limitSelector" class="form-label">Show:</label>
                            <select id="limitSelector" class="form-select w-auto">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>

                        <div style="height: 400px; overflow-y: auto;">
                            <table class="table table-striped table-hover align-middle mb-0" id="reservationTable">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
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
                                    @foreach($reservations as $reservation)
                                        <tr>
                                            <td>
                                                <a href="{{ route('sites.view', $reservation->id) }}" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('sites.edit', $reservation) }}" class="btn btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                                <a class="btn btn-danger btn-delete"
                                                    data-url="{{ route('sites.destroy', $reservation) }}"><i
                                                        class="fas fa-trash"></i></a>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($reservation->cid)->format('M d, Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($reservation->cod)->format('M d, Y') }}</td>
                                            <td>{{ $reservation->customer->first_name ?? 'N/A' }} {{ $reservation->customer->last_name ?? '' }}</td>
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

            {{-- <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark">
                        <h5 class="card-title mb-0" style="color: #EFC368 !important">Cart Reservations</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <label for="limitSelectorNotReserve" class="form-label">Show:</label>
                            <select id="limitSelectorNotReserve" class="form-select w-auto">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>

                        <div style="height: 400px; overflow-y: auto;">
                            <table class="table table-striped table-hover align-middle mb-0" id="notReserveTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div id="paginationLinks1" class="pagination-links text-center"></div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

@endsection
