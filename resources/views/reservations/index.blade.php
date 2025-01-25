@extends('layouts.admin')

@section('title', 'Reservation List')
@section('content-header', 'Reservation List')

@section('content')


    @include('reservations.components.header')

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;

        }

        .card {
            border: none;
            border-radius: 8px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #343a40;
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table thead th {
            background-color: #343A40;
            color: #EFC368;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        .form-select {
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .pagination-links {
            margin-top: 1rem;
        }

        .pagination-links a {
            background: #fff;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination-links a:hover {
            background: #007bff;
            color: white;
        }

        @media (max-width: 768px) {
            .collapse {
                margin-bottom: 1rem;

            }
        }

        .header {
            background-color: #343a40;
            padding: 1rem;
        }

        .header a {
            color: #EFC368;
            margin-right: 20px;
        }

        .header a:hover {
            color: #fff;

        }

        .btn-success {
            background-color: #28a745;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-success:hover {
            background-color: #218838;

        }
    </style>


    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark">
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
                    </div>
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

                                        <th>
                                            Checked In Date
                                        </th>
                                        <th>
                                            Checked Out Date
                                        </th>

                                        <th>Name</th>
                                        <th>Site</th>
                                        <th>Type</th>
                                        <th>Site Lock</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div id="paginationLinks" class="pagination-links text-center"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
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
    <script src="{{ asset('js/reservations/retrievedata.js') }}"></script>
    <script src="{{ asset('js/reservations/reservationmodal.js') }}"></script>

    <script>
        const webdavinci_api = "{{ env('WEBDAVINCI_API') }}";
        const webdavinci_api_key = "{{ env('WEBDAVINCI_API_KEY') }}";

        
    </script>
@endpush
