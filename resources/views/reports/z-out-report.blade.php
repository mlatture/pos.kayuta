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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .section-title {
            font-size: 18px;
            margin: 20px 0 10px;
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
                        <input type="text" class="form-control daterange" id="productDate" autocomplete="off"
                            placeholder="Select Date">
                    </div>

                    <!-- Station -->
                    <div class="col-md-3">
                        <label for="station" class="form-label">Station:</label>
                        <select id="station" class="form-control">
                         
                        </select>
                    </div>

                    <!-- User -->
                    <div class="col-md-3">
                        <label for="user" class="form-label">User:</label>
                        <select id="user" class="form-control">

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
    <div class="card">
        <div class="card-body">
            <h2 class="section-title">Sales Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Gross Sales</th>
                        <th>Gross Returns</th>
                        <th>Net</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sales</td>
                        <td>$689.16</td>
                        <td>$0.00</td>
                        <td>$689.16</td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td>$20.89</td>
                        <td>$0.00</td>
                        <td>$20.89</td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td>$710.05</td>
                        <td>$0.00</td>
                        <td>$710.05</td>
                    </tr>
                    <tr>
                        <td>Trans. Count</td>
                        <td>30</td>
                        <td>0</td>
                        <td>30</td>
                    </tr>
                </tbody>
            </table>

            <h2 class="section-title">Sales Activity</h2>
            <table>
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Tax</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>General Merchandise - Regular Sales Tax</td>
                        <td>$199.58</td>
                        <td>$17.47</td>
                        <td>$217.05</td>
                    </tr>
                    <tr>
                        <td>Grocery - No Tax</td>
                        <td>$343.59</td>
                        <td>$0.00</td>
                        <td>$343.59</td>
                    </tr>
                    <tr>
                        <td>Apparel - Has Clothing Tax</td>
                        <td>$72.00</td>
                        <td>$3.42</td>
                        <td>$75.42</td>
                    </tr>
                    <tr>
                        <td>Site Rental</td>
                        <td>$73.99</td>
                        <td>$0.00</td>
                        <td>$73.99</td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td>$689.16</td>
                        <td>$20.89</td>
                        <td>$710.05</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="section-title">Payment Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Trans. Count</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Credit Card</td>
                        <td>8</td>
                        <td>$124.88</td>
                    </tr>
                    <tr>
                        <td>Cash</td>
                        <td>24</td>
                        <td>$585.17</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h2 class="section-title">Credit Card Listing</h2>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Visa: xxxx-7812</td>
                        <td>Joey Chapman</td>
                        <td>$25.01</td>
                    </tr>
                    <tr>
                        <td>Visa: xxxx-0856</td>
                        <td>David Williams</td>
                        <td>$35.89</td>
                    </tr>
                    <tr>
                        <td>Visa: xxxx-0035</td>
                        <td>James Rando</td>
                        <td>$23.64</td>
                    </tr>
                    <tr>
                        <td>Visa: xxxx-5398</td>
                        <td>Joseph Guffey</td>
                        <td>$4.11</td>
                    </tr>
                    <tr>
                        <td>Visa: xxxx-6530</td>
                        <td>Samantha Longtin</td>
                        <td>$15.00</td>
                    </tr>
                    <tr>
                        <td>Visa: xxxx-3842</td>
                        <td>Tanya Elthorp</td>
                        <td>$21.23</td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td></td>
                        <td>$124.88</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="section-title">User Activity</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Station</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>12 AM</th>
                        <th>1 AM</th>
                        <th>2 AM</th>
                        <th>3 AM</th>
                        <th>4 AM</th>
                        <th>5 AM</th>
                        <th>6 AM</th>
                        <th>7 AM</th>
                        <th>8 AM</th>
                        <th>9 AM</th>
                        <th>10 AM</th>
                        <th>11 AM</th>
                        <th>12 PM</th>
                        <th>1 PM</th>
                        <th>2 PM</th>
                        <th>3 PM</th>
                        <th>4 PM</th>
                        <th>5 PM</th>
                        <th>6 PM</th>
                        <th>7 PM</th>
                        <th>8 PM</th>
                        <th>9 PM</th>
                        <th>10 PM</th>
                        <th>11 PM</th>
                        <th>12 PM</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>7/6/2024</td>
                        <td>Register 1</td>
                        <td>Steph Postol</td>
                        <td>30</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>5</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>7</td>
                        <td></td>
                        <td></td>
                        <td>7</td>
                        <td>4</td>
                        <td>9</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

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
            };


            function ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }
            // Fetch User Names for dropdown
            $.ajax({
                url: "{{ route('user.name') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#user').empty();

                    $('#user').append('<option disabled selected> Select a user </option>');

                    for (let res of response) {
                        let name = ucfirst(res.name);
                        let option = `<option value="${res.id}"> ${name} </option>`;
                        $('#user').append(option);
                    }

                },
                error: function(xhr, error) {
                    console.error('Error: ', xhr, error);
                }
            });

            // Fetch Station No: 
            $.ajax({
                url: "{{ route('registers.station_name') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#station').empty();

                    $('#station').append('<option disabled selected> Select a station </option>');


                    for (let res of response) {
                        let statName = ucfirst(res.name);
                        let option = `<option value="${res.id}"> ${statName} </option>`;

                        console.log(res);
                        $('#station').append(option);
                    }
                },
                error: function(xhr, error) {
                    console.error('Error', xhr, error);
                }
            });
        });
    </script>
@endsection
