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

        .gantt .grid-header {
            fill: #2E7D32 !important;
            stroke: #e0e0e0;
            stroke-width: 1.4;
        }

        .gantt .grid-header text {
            fill: #ffffff !important;
            font-size: 14px;
            font-weight: bold;
        }

        .gantt .date text {
            fill: #ffffff !important;
            font-size: 12px;
            font-weight: bold;
        }

        .gantt .grid-row {
            fill: #4A4A4A !important;
        }

        .gantt .grid-row:nth-child(even) {
            fill: #5a5a5a !important;
        }

        .gantt .bar-progress {
            fill: #2E7D32 !important;
            stroke: #ffffff;

            stroke-width: 1;
            rx: 4;
            ry: 4;
        }

        .gantt .bar-label {
            fill: #ffffff !important;
            font-size: 14px;
            font-weight: bold;
            text-anchor: middle;
            dominant-baseline: central;
        }



        .gantt .unavailable-bar {
            fill: #D32F2F !important;
            opacity: 0.7;
            stroke: #B71C1C;
            stroke-width: 1;
            stroke-dasharray: 5;
        }

        .gantt .date {
            position: sticky;
            top: 0;
            background: #37474F !important;
            z-index: 2;
        }

        .gantt-container {
            display: flex;
            flex-direction: row;
            gap: 15px;
        }

        .gantt-table {
            border-collapse: collapse;
            width: 350px;
            font-size: 14px;
        }

        .gantt-table th,
        .gantt-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .gantt-table th {
            background-color: #455a64;
            color: white;
        }



        .gantt .bar-progress {
            fill: #4CAF50 !important;
            opacity: 0.85;
        }


        .gantt .bar:hover {
            filter: brightness(1.2);
        }

        .gantt-tooltip {
            position: absolute;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 8px;
            border-radius: 4px;
            font-size: 12px;
            display: none;
            pointer-events: none;
            z-index: 1000;
        }
    </style>


    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col">
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
                        {{-- <div class="mb-3 d-flex justify-content-between align-items-center">
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

                        <div id="paginationLinks" class="pagination-links text-center"></div> --}}

                        <div class="gantt-container" style="max-height: 600px;">
                            <div id="ganttTable"></div>
                            <div id="ganttReservations"></div>

                        </div>


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
    @extends('reservations.modals.modals')
    @extends('reservations.modals.reservations-modal')
@endsection

@push('js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script src="{{ asset('js/reservations/retrievedata.js') }}"></script>
    <script src="{{ asset('js/reservations/reservationmodal.js') }}"></script>

    <script>
        const webdavinci_api = "{{ config('app.webdavinci_api') }}";
        const webdavinci_api_key = "{{ config('app.webdavinci_api_key') }}";
    </script>
@endpush
