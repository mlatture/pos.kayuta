@extends('layouts.admin')

@section('title', 'Reservation List')
@section('content-header', 'Reservation List')
@section('content-actions')
@hasPermission(config('constants.role_modules.pos_management.value'))
<a href="{{ route('cart.index') }}" class="btn btn-success">Open POS</a>
@endHasPermission
@endsection

@section('content')
<style>
    .fc{
        height: 70%;
    }
</style>

<header class="reservation__head bg-dark py-2">
    <div
        class="d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between px-md-3 px-2">
        <div class="d-flex align-items-center gap-3">
            <a href="#" class="text-white text-decoration-none">
                <img src="{{ asset('images/grid-ico.svg') }}" alt="" class="me-2" />
                Planner
            </a>
            <a href="#" class="text-white text-decoration-none">
                <img src="{{ asset('images/pin-ico.svg') }}" alt="" class="me-2" />
                Map
            </a>
            <a href="#" class="text-white text-decoration-none">
                <img src="{{ asset('images/flash-ico.svg') }}" alt="" class="me-2" />
                Quick Add
            </a>
        </div>
        <div>
            <a href="#" class="text-white text-decoration-none">
                <img src="{{ asset('images/help-ico.svg') }}" alt="" class="me-2" />
                Help
            </a>
        </div>
    </div>
</header>
{{-- <div
    class="table-actions d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between pe-2 pt-md-3 pt-2">
    <div class="d-flex align-items-center action__links">
        <a href="{{route('reservations.create')}}" target="_blank" class="border text-decoration-none p-2 text-dark">
            <img src="{{ asset('images/add-ico.svg') }}" alt="" class="me-2" />
            Available
        </a>
        <a href="#" class="border text-decoration-none p-2 text-dark">
            <img src="{{ asset('images/search-ico.svg') }}" alt="" class="me-2" />
            Search
        </a>
        <a href="javascript:void(0)" onclick="openReservationModal()" class="border text-decoration-none p-2 text-dark">
            <img src="{{ asset('images/track-ico.svg') }}" alt="" class="me-2" />
            Arrivals & Departures
        </a>
        <a href="javascript:void(0)" onclick="openReservationSiteModal()"
            class="border text-decoration-none p-2 text-dark">
            <img src="{{ asset('images/return-ico.svg') }}" alt="" class="me-2" />
            Relocate
        </a>
        <a href="#" class="border text-decoration-none p-2 text-dark">
            <img src="{{ asset('images/drop-ico.svg') }}" alt="" class="me-2" />
            Theme
        </a>
    </div>

    <div class="d-flex gap-2 align-items-center border p-1 rounded">
        <form action="{{ route('reservations.index') }}" method="get">
            <div class='input-group mb-3'>
                <input type='text' class="form-control daterange" id="customerDate" name="date"
                    value="{!! isset($_GET['date']) ? $_GET['date'] : '' !!}" />
                <span class="input-group-text">
                    <span class="ti-calendar"></span>
                </span>
            </div>

            <button id="submitRange" class="btn btn-info text-white font-small">Submit Dates</button>
        </form>
    </div>


</div> --}}
<div class="overflow-auto mt-3">
    <div class="row" style="overflow: none">
        <div class="col md-2" style="">
            <div class="container" style="height: 100%;">
                <select id="limitSelector">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>

                <div style="height: 400px; overflow-y: auto;">
                    <table class="table align-middle mb-0 bg-white" id="reservationTable" style="width: 100%;">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Site</th>
                                <th>Type</th>
                                <th>Check In Date</th>
                                <th>Check Out Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table rows go here -->
                        </tbody>
                    </table>
                </div>

                <div id="paginationLinks"></div>
            </div>

        </div>
        <div class="col md-2">
               <p>
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    Add Customer
               </button>
               
               </p>

              <div class="collapse" id="collapseExample">
                <div class="card card-body">
                    <form id="customerForm">
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name</label>
                                    <input type="text" class="form-control" name="fname" id="fname">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name</label>
                                    <input type="text" class="form-control" name="lname" id="lname">
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" id="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contactno">Contact Number</label>
                                    <input type="text" class="form-control" name="contactno" id="contactno">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" name="address" id="address">
                        </div>
                    </form>
               </div>
                <div class="card card-footer">
                    <button type="button" class="btn btn-success" id="saveCustomer">Save</button>
                </div>
              </div>
            <div id="calendar"></div>

        </div>
    </div>
</div>


@extends('reservations.modals.modals')
@extends('reservations.modals.reservations-modal')

@endsection

@push('js')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

@endpush