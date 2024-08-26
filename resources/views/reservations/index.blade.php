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
        /* Base styles for datepicker and dialog */
        .ui-datepicker {
            font-family: Arial, sans-serif;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .ui-datepicker-header {
            background: #007bff;
            color: white;
            border-bottom: 1px solid #ddd;
        }

        .ui-datepicker-title {
            font-weight: bold;
        }

        .ui-datepicker-prev,
        .ui-datepicker-next {
            color: white;
        }

        .ui-datepicker-calendar th {
            background: #f8f9fa;
            color: #333;
        }

        .ui-datepicker-calendar td a {
            color: #007bff;
        }

        .ui-datepicker-calendar td a.ui-state-active {
            background: #007bff;
            color: white;
        }

        .ui-dialog {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 600px;
        }

        .ui-dialog-titlebar {
            background: #007bff;
            color: white;
        }

        .ui-dialog-titlebar-close {
            color: white;
        }

        .ui-dialog-buttonpane button {
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            margin: 0 4px;
        }

        .ui-dialog-buttonpane button:hover {
            background: #0056b3;
        }

        @media (max-width: 768px) {
            .ui-dialog {
                width: 95%;
                max-width: none;
            }
        }

        @media (max-width: 576px) {
            .ui-dialog {
                width: 100%;
                max-width: none;
                padding: 0;
            }

            .ui-dialog-buttonpane button {
                padding: 8px;
                margin: 0;
                font-size: 14px;
            }

            .ui-datepicker-header {
                font-size: 14px;
            }

            .ui-datepicker-title {
                font-size: 16px;
            }
        }
    </style>

    <header class="reservation__head bg-dark py-2">
        <div
            class="d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between px-md-3 px-2">
            <div class="d-flex align-items-center gap-3">

              
              <a href="javacript:void(0)" class="text-white"  data-bs-toggle="collapse" data-bs-target="#collapseExample"    aria-expanded="false" aria-controls="collapseExample"> Add Customer</a>
                <a href="javacript:void(0)" class="text-white" id="openDatePicker">Select Date Range</a>
            </div>
            <div>
                <a href="#" class="text-white text-decoration-none">
                    <img src="{{ asset('images/help-ico.svg') }}" alt="" class="me-2" />
                    Help
                </a>
            </div>
        </div>
    </header>

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
    {{--    
    <div id="calendar"></div> --}}

    <div class="overflow-auto mt-3">
        <div class="row">
            <div class="col-md-6">
                <div class="container" style="height: 100%;">
                    <h5>Paid Customers</h5>
                    <select id="limitSelector">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>

                    <div style="height: 400px; overflow-y: auto; overflow-x: auto;">
                        <table class="table align-middle mb-0 bg-white" id="reservationTable" style="table-layout: fixed;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 200px;">Name</th>
                                    <th style="width: 200px;">Site</th>
                                    <th style="width: 200px;">Type</th>
                                    <th style="width: 200px;">Check In Date</th>
                                    <th style="width: 200px;">Check Out Date</th>
                                    <th style="width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    <div id="paginationLinks"></div>
                </div>
            </div>

            <div class="col-md-6">

                <div class="container" style="height: 100%;">
                    <h5>Not Reserve Customer</h5>
                    <select id="limitSelectorNotReserve">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>

                    <div style="height: 400px; overflow-y: auto; overflow-x: auto;">
                        <table class="table align-middle mb-0 bg-white" id="notReserveTable" style="table-layout: fixed;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 200px;">Name</th>
                                    <th style="width: 200px;">Site</th>
                                    <th style="width: 200px;">Type</th>
                                    <th style="width: 200px;">Check In Date</th>
                                    <th style="width: 200px;">Check Out Date</th>
                                    <th style="width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    <div id="paginationLinks1"></div>
                </div>
            </div>


        </div>

    </div>


    @extends('reservations.modals.modals')
    @extends('reservations.modals.reservations-modal')


@endsection

@push('js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
@endpush
