@extends('layouts.admin')

@section('title', 'Sales Report Management')
@php
    $firstTransactionDate = $orders->first() ? $orders->first()->created_at->format('l, F j, Y') : '';
    $lastTransactionDate = $orders->last() ? $orders->last()->created_at->format('l, F j, Y') : '';
@endphp
@section('content-header', "Sales ($firstTransactionDate - $lastTransactionDate)")

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
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="productDate" class="mr-2">Date Range:</label>
                        <input type='text' class="form-control daterange" id="productDate" autocomplete="off"
                            placeholder="Select Date" />
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="dateToUse" class="">Date To Use:</label>
                        <select id="dateToUse" class="form-control">
                            <option value="transaction_date">Transaction Date</option>
                            <option value="checkin_date">Date Checked In</option>
                            <option value="arrival_date">Arrival Date</option>
                            <option value="staying_on">Staying On</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button class="btn btn-success" id="refreshBtn">
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
                                    <th>Transaction Date</th>
                                    <th>Source</th>
                                    <th>Confirmation # / POS ID</th>
                                    <th>Customer</th>
                                    <th>Site</th>
                                    <th>Type</th>

                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Total</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $tax = 0;
                                    $discount = 0;
                                @endphp
                                @foreach ($orders as $key => $order)

                                    <tr>
                                        <td>
                                            @if($order->source === 'POS')
                                                {{ $order->created_at->format('m/d/Y') }}
                                            @elseif($order->source === 'Reservation')
                                                {{ $order->reservations->first()->created_at->format('m/d/Y') }}  
                                            @endif 
                                        </td>
                                        <td>{{ $order->source ?? '' }}</td>
                                        <td>{{ $order->id ?? '' }}</td>
                                        <td>{{ $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : '' }}
                                        </td>
                                        <td>{{ $order->reservations->first()->siteid ?? '' }}</td>
                                        <td>{{ $order->items->first()->product->taxType->title ?? '' }}</td>

                                        <td>{{ 'Product Charge' }}</td>
                                        <td>
                                            {{ $order->items->first()->product->description ?? '' }}
                                        </td>
                                        <td>
                                            @if ($order->source === 'POS')
                                                @php
                                                    $item = $order->items->first();
                                                    $price = $item ? $item->price : 0;
                                                    $quantity = $item ? $item->quantity : 1;
                                                    $amount = $quantity != 0 ? $price / $quantity : 0;
                                                @endphp
                                                ${{ number_format($amount, 2) }}
                                            @elseif($order->source === 'Reservation')
                                                ${{ $order->reservations->first() ? number_format($order->reservations->first()->base, 2) : 0 }}
                                            @else
                                                $0
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->source === 'POS')
                                                {{ $order->items->first() ? $order->items->first()->quantity : 0 }}
                                            @elseif($order->source === 'Reservation')
                                                {{ $order->reservations->first()->nights ?? 0 }}
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->source === 'Reservation')
                                                @if ($order->reservations->first()->nights == 7)
                                                    Week
                                                @elseif ($order->reservations->first()->nights == 30)
                                                    Month
                                                @elseif ($order->reservations->first()->nights <= 7)
                                                    Day
                                                @else
                                                @endif
                                            @else
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->source === 'POS')
                                                ${{ $order->items->first() ? number_format($order->items->first()->price, 2) : 0 }}
                                            @elseif($order->source === 'Reservation')
                                                ${{ $order->reservations->first() ? number_format($order->reservations->first()->total, 2) : 0 }}
                                            @else
                                                $0
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($order->admin->name) }}</td>
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
                $(this).val('');
                fetchFilteredOrders();
            });

            $('#dateToUse').change(function() {
                fetchFilteredOrders();
            });

            function fetchFilteredOrders() {
                var dateRange = $('#productDate').val();
                var dateToUse = $('#dateToUse').val();

                if (dateRange) {
                    var dates = dateRange.split(' - ');
                    var startDate = moment(dates[0], 'MM/DD/YYYY').format('YYYY-MM-DD');
                    var endDate = moment(dates[1], 'MM/DD/YYYY').format('YYYY-MM-DD');


                    $.ajax({
                        url: "{{ route('reports.salesReport') }}",
                        type: 'GET',
                        data: {
                            date_range: dateRange,
                            date_to_use: dateToUse
                        },
                        success: function(response) {
                            console.log('Orders fetched:', response);
                            $('#dataTableContainer').html(
                                response);
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
            });
        });
    </script>
@endsection
