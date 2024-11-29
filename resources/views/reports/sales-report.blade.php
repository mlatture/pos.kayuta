@extends('layouts.admin')

@section('title', 'Sales Report Management')
@section('content-header', 'Sales Report Management')

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">

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

        .dataTables_wrapper .dataTables_info {
            font-size: 14px;
        }


        .dt-length select,
        .dt-search input {
            width: 8%;


        }


        .dt-buttons {
            float: right;
        }
    </style>
@endsection

@section('content')
    <div class="row animated fadeInUp">
        <div class="card" style="background-color: #F4F6F9; box-shadow: none; border: none;">
            <div class="card-body" style="border: none;">
                <div class="row mt-3 d-flex justify-content-center">
                    <div class="col-md-3">
                        <label for="">Date Range</label>
                        <div class='input-group mb-3'>
                            <input type='text' class="form-control daterange" id="productDate" autocomplete="off"
                                placeholder="Select Date" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="">Date To Use</label>
                        <div class='input-group mb-3'>
                            <select id="dateToUse" class="form-control">
                                <option value="transaction_date">Transaction Date</option>
                                <option value="payment_date">Payment Date</option>
                                <option value="delivery_date">Delivery Date</option>
                                <option value="order_date">Order Date</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-20">
                        <div class="refresh-btn-container">
                            <button class="btn btn-danger" id="refreshBtn">
                                <i class="fas fa-sync"></i> Refresh
                            </button>
                        </div>
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
                            <thead class="text-center">
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
                            <tbody  class="text-center">
                                @php
                                    $tax = 0;
                                    $discount = 0;
                                @endphp
                                @foreach ($orders as $key => $order)
                             
                                    <tr>
                                        <td>{{ $order->created_at->format('m/d/Y') }}</td>
                                        <td>{{ $order->source ?? '' }}</td>
                                        <td>{{ $order->id ?? '' }}</td>
                                        <td>{{ $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : '' }}</td>
                                        <td>{{ $order->reservations->first()->siteid ?? '' }}</td>
                                        <td>{{ $order->items->first()->product->taxType->title ?? '' }}</td>
                                       
                                        <td>{{ 'Product Charge' }}</td>
                                        <td style="width: 10px !important;">
                                            <span>
                                                <p>
                                                    {{ Str::limit($order->items->first()->product->description ?? '', 20, '...') }}
                                                </p>
                                            </span>
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
                                        <td>{{ $order->admin->name ?? '' }}</td>
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
                    cancelLabel: 'Clear',
                },
            });

            $('#productDate').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                    'MM/DD/YYYY'));
                fetchFilteredOrders();
            });

            $('#productDate').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
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
                dom: 'lBfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    search: 'Search records...'
                },
                pageLength: 10
            });

            $('#refreshBtn').click(function() {
                window.location.reload();
            })
        });
    </script>
@endsection
