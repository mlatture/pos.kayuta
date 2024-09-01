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

        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
        }

        .form-select {
            appearance: none;
            padding: 0.375rem 1.75rem 0.375rem 0.75rem;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .pagination-links {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1rem;
        }

        .pagination-links a {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            color: #007bff;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            background: #fff;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination-links a:hover {
            background: #007bff;
            color: #fff;
            text-decoration: none;
        }

        .pagination-links a.disabled {
            color: #6c757d;
            pointer-events: none;
            border-color: #ddd;
            background: #f8f9fa;
        }

        .pagination-links span {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            color: #6c757d;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            background: #f8f9fa;
        }

        .pagination-links .active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
      

    </style>

    @include('reservations.components.header')

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Paid Customers Section -->
            <div class="col-md-8">
                <div class="card shadow-sm w-100 h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Confirmed Customers</h5>

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
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 150px;">Name</th>
                                        <th style="min-width: 150px;">Site</th>
                                        <th style="min-width: 150px;">Type</th>
                                  
                                        <th style="min-width: 100px;">Status</th>
                                        <th style="min-width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div id="paginationLinks" class="pagination-links mt-3 text-center"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Non-Reserved Customers</h5>
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
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 150px;">Name</th>
                                        {{-- <th style="min-width: 150px;">Site</th>
                                        <th style="min-width: 150px;">Type</th>
                                        <th style="min-width: 150px;">Check In Date</th>
                                        <th style="min-width: 150px;">Check Out Date</th> --}}
                                        <th style="min-width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <div id="paginationLinks1" class="pagination-links mt-3 text-center"></div>
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
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
@endpush
