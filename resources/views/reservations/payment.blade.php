@extends('layouts.admin')

@section('content')
    @push('css')
        <style>
            .invoice-header {
                text-align: center;
                margin-bottom: 10px;
            }

            .invoice-header h4 {
                font-size: 24px;
                font-weight: bold;
                margin: 0;
            }

            .container-invoice {
                background-color: #77898d;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 10px;
            }

            .table-invoice {
                width: 100%;
                border-collapse: collapse;
            }

            .table-invoice th,
            .table-invoice td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

            .table-invoice th {
                background-color: #f4f4f4;
                font-weight: bold;
            }

            .table-invoice tbody tr:hover {
                background-color: #f1f1f1;
            }

            .total-row {
                background-color: #f4f4f4;
                font-weight: bold;
            }

            .total-row td {
                border: none;
            }

            .total-row .text-end {
                text-align: right;
            }

            .fw-bold {
                font-weight: bold;
            }

            .invoice-footer {
                text-align: right;
                margin-top: 20px;
            }
        </style>
    @endpush
    @php
        use Illuminate\Support\Facades\Request;
    @endphp
    <div class="overflow-auto ">
        <header class="invoice-header">
            <h4>Invoice</h4>
        </header>
        <div class="container-invoice">
            <div class="table-responsive">
                <table class="table table-bordered table-invoice">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Site</th>
                            <th>Type</th>
                            <th>Number of Guests</th>
                            <th>Description</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="">
                            <td>{{ date('D, M d', strtotime($reservation->cid)) }} -
                                {{ date('D, M d', strtotime($reservation->cod)) }}</td>
                            <td>{{ $reservation->siteid }}</td>
                            <td>{{ $reservation->siteclass }}</td>
                            <td>{{ $reservation->number_of_guests ?? 0 }}</td>

                            @if (Request::is('admin/reservations/invoice/*'))
                                <td>{{ $cart->description }}</td>
                            @else
                                <td>{{ $reservation->description }}</td>
                            @endif
                            <td>${{ number_format($reservation->subtotal, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="4"></td>
                            <td class="text-end">Subtotal</td>
                            <td>${{ number_format($reservation->subtotal, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="2"></td>
                            <td>Tax</td>
                            <td></td>
                            <td>Sales Tax (8.75%)</td>
                            <td>${{ $reservation->taxrate }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="2"></td>
                            <td>Tax</td>
                            <td></td>
                            <td>Total Tax</td>
                            <td>${{ number_format($reservation->totaltax, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="4"></td>
                            <td class="text-end">Total Payments</td>
                            <td>${{ number_format($reservation->total, 2) }}</td>
                        </tr>

                        @if (Request::is('admin/reservations/invoice/*'))
                            @php    $balance = $reservation->total - $payment->payment; @endphp
                            <tr class="total-row">
                                <td colspan="4"></td>
                                <td class="text-end">Balance </td>
                                <td>${{ number_format($balance, 2) }}</td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
        <form id="paymentchoices" method="POST">
            <header class="invoice-header mt-4">
                <h4>Payments</h4>
            </header>
            <div class="container-invoice">
                <div class="form-row mb-3">
                    <div class="col">
                        <div class="form-group">
                            <label for="transactionType">Transaction Type</label>
                            <select name="transactionType" id="transactionType" class="form-control">
                                <option value="Full" selected>Full Payment</option>
                                <option value="Partial">Partial Payment</option>
                            </select>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <label for="paymentType">Payment Type</label>
                            <select name="paymentType" id="paymentType" class="form-control">
                                <option value="" selected disabled>Select Payment Type</option>
                                <option value="Cash">Cash</option>
                                <option value="Check">Check</option>
                                <option value="Manual">Credit Card - Manual</option>
                                <option value="Terminal">Credit Card</option>
                                <option value="Gift Card">Gift Card</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row mb-3">
                    <div class="col">
                        <div class="form-group">
                            <label for="xAmount">Total Amount</label>
                            <input class="form-control" type="text" name="xAmount" id="xAmount"
                                value="{{ number_format(Request::is('admin/reservations/invoice/*') ? $balance : $reservation->total, 2) }}"
                                readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input class="form-control" type="text" name="description" id="description">
                        </div>
                    </div>
                </div>

                <!-- Payment Type Specific Fields -->
                <div class="form-row mb-3" id="creditcard-manual" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" maxlength="16" name="xCardNum" id="xCardNum" required
                                class="form-control" placeholder="Card Number">
                        </div>
                    </div>
                    <div class="col-md-6" id="xExpGroup">
                        <div class="form-group">
                            <input type="text" name="xExp" id="xExp" required class="form-control"
                                placeholder="Expiration" maxlength="5">
                        </div>
                    </div>
                </div>

                <div class="form-row mb-3" id="checkDetails" hidden>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="xCash">Account Number</label>

                            <input type="text" name="xAccount" id="xAccount" required class="form-control"
                                placeholder="Account Number">
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="xCash">Routing Number</label>

                            <input type="text" name="xRouting" id="xRouting" required class="form-control"
                                placeholder="Routing Number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="xCash">Full Name</label>

                            <input type="text" name="xName" id="xName" required class="form-control"
                                placeholder="Name">
                        </div>
                    </div>
                </div>

                <div class="form-row mb-3" id="gift-card" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="xBarcode" id="xBarcode" required class="form-control"
                                placeholder="Barcode">
                        </div>
                        <div id="gift-card-message"></div>

                    </div>
                </div>

                <div class="form-row mb-3" id="cash" style="display: none;">
                    <div class="col">
                        <div class="form-group">
                            <label for="xCash">Amount Tendered</label>
                            <input type="number" name="xCash" id="xCash" required class="form-control"
                                placeholder="Cash">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="xChange">Change Due</label>
                            <input type="text" id="xChange" readonly class="form-control" placeholder="Change Due">
                        </div>
                    </div>
                </div>

                <div class="form-row mb-3" id="creditcard-terminal" style="display: none;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h2>Start Terminal Transaction</h2>
                        </div>
                    </div>
                </div>

                <div id="loader" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Waiting for card insertion...</p>
                </div>

                <input type="hidden" name="cartid" id="cartid" value="{{ $reservation->cartid }}">
                <input type="hidden" name="id" value="{{ $reservation->id }}">

                <div class="form-row d-flex justify-content-end mr-1 gap-2">
                    @if (Request::is('admin/reservations/payment/*'))
                        <div class="btn btn-danger" id="addToCart">
                            <i class="fa-solid fa-cart-shopping"></i> Add To Cart
                        </div>
                    @endif
                    <div class="btn btn-success"
                        id="{{ Request::is('admin/reservations/invoice/*') ? 'payBalance' : 'payBtn' }}">
                        <i class="fa-solid fa-money-bill-transfer"></i> Pay
                    </div>
                </div>

            </div>

        </form>

    </div>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        var checkGiftCart = "{{ route('check.gift-card') }}";
        var deleteAddToCart = "{{ route('reservations.delete.add-to-cart') }}";

        var pusher = new Pusher('3da072d963b1708b31a3', {
            cluster: 'mt1',
        });

      
        var channel = pusher.subscribe('cart-deletions');

        channel.bind('pusher:subscription_succeeded', function() {
            console.log('Successfully subscribed to cart-deletions channel!');
        });

        channel.bind('App\\Events\\CartDeleted', function(data) {
            var cartid = data.cartid;
            console.log('Received event: Cart with ID ' + cartid + ' has been deleted.');
            toastr.success("Cart with ID " + cartid + " has been deleted");

            setTimeout(function() {
                window.location.href = "/admin/reservations";
            }, 1000);
        });

      
        pusher.connection.bind('error', function(err) {
            console.error('Pusher error:', err);
        });
    </script>
@endsection
