@extends('layouts.admin')

@section('title', 'Z - Out Report')




@section('content-header', 'POS Z - Out Report')

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">

    <style>
        .daterangepicker {
            z-index: 9999 !important;
        }

        .refresh-btn-container {
            display: flex;
            justify-content: flex-end;
            padding: 10px 0;
        }

        .refresh-btn-container button {
            padding: 8px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .refresh-btn-container button:hover {
            background-color: #218838;
        }



        div.dt-top-container {
            display: flex;

            text-align: center;
        }

        div.dt-center-in-div {
            margin: 0 auto;
            display: inline-block;
            text-align: center;
        }

        div.dt-filter-spacer {
            margin: 10px 0;
        }

        td.highlight {
            background-color: #F4F6F9 !important;
        }

        div.dt-left-in-div {
            float: left;
        }

        div.dt-right-in-div {
            float: right;
        }
    </style>
@endsection

@section('content')
    <div class="row ">
        <div class="card" style="background-color: #F4F6F9; box-shadow: none; border: none;">
            <div class="card-body" style="border: none;">
                <div class="row mt-3 d-flex justify-content-center align-items-center">
        
                    <!-- Date Range -->
                    <div class="col-md-3">
                        <label for="productDate" class="form-label">Date Range:</label>
                        <input type="text" class="form-control daterange" id="productDate" autocomplete="off" placeholder="Select Date">
                    </div>
        
                    <!-- Station -->
                    <div class="col-md-3">
                        <label for="station" class="form-label">Station:</label>
                        <select id="station" class="form-control">
                            <option value="arrival_date">Arrival Date</option>
                            <option value="checkin_date">Date Checked In</option>
                            <option value="transaction_date">Transaction Date</option>
                            <option value="staying_on">Staying On</option>
                        </select>
                    </div>
        
                    <!-- User -->
                    <div class="col-md-3">
                        <label for="user" class="form-label">User:</label>
                        <select id="user" class="form-control">
                            <option value="arrival_date">Arrival Date</option>
                            <option value="checkin_date">Date Checked In</option>
                            <option value="transaction_date">Transaction Date</option>
                            <option value="staying_on">Staying On</option>
                        </select>
                    </div>
        
                    <!-- Cash Begin -->
                    <div class="col-md-3">
                        <label for="cash_begin" class="form-label">Cash Begin:</label>
                        <input type="text" id="cash_begin" class="form-control">
                    </div>
        
                    <!-- Cash Leave -->
                    <div class="col-md-3">
                        <label for="cash_leave" class="form-label">Cash Leave:</label>
                        <input type="text" id="cash_leave" class="form-control">
                    </div>
        
                    <!-- Actual Cash Count -->
                    <div class="col-md-3">
                        <label for="actual_cash_count" class="form-label">Actual Cash Count:</label>
                        <input type="text" id="actual_cash_count" class="form-control">
                    </div>
        
                    <!-- Refresh Button -->
                    <div class="col-md-2 d-flex align-items-center">
                        <button class="btn btn-success mt-3" id="refreshBtn">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive m-t-40 p-0" id="dataTableContainer">
                        <table table class="display nowrap table table-hover table-striped border p-0" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>Site ID</th>
                                    <th>Site Name</th>
                                    <th>Site Type</th>
                                    <th>Seasonal</th>
                                    <th>Nights Occupied</th>
                                    <th>Income from Stays</th>
                                    <th>Percent Occupancy</th>
                                    <th>Other Income</th>
                                    <th>Total Income from Site</th>
                                </tr>
                            </thead>
                            <tbody>
                             
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

                autoApply: false,
                autoUpdateInput: true,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MM/DD/YYYY',
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                opens: 'auto',
            });

            $('#productDate').on('apply.daterangepicker', function(ev, picker) {
                fetchFilteredOrders();
            });

            $('#productDate').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(''); // Clear the input field
            });

            function fetchFilteredOrders() {
                var dateRange = $('#productDate').val();
                var dateToUse = $('#dateToUse').val();

                console.log("Date Range:", dateRange);

                if (dateRange) {
                    var dates = dateRange.split(' - ');
                    var startDate = dates[0];
                    var endDate = dates[1];

                    $.ajax({
                        url: "{{ route('reports.salesReport') }}",
                        type: 'GET',
                        data: {
                            date_range: dateRange,
                        },
                        success: function(response) {
                            $('#dataTableContainer').html(response);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching orders:', error);
                        }
                    });
                } else {
                    console.log("No date range selected");
                }
            }

            $('.table').DataTable({
                responsive: true,
                dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
                buttons: ['colvis', 'copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    search: 'Search: ',
                    lengthMenu: 'Show _MENU_ entries',
                },
                pageLength: 10
            });

            $('#refreshBtn').click(function() {
                window.location.reload();
            })
        });
    </script>
@endsection
