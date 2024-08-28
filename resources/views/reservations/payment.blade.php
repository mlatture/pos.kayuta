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

    .table-invoice th, .table-invoice td {
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
                        <th>Adults</th>
                        <th>Children 5 and below</th>
                        <th>Children 6 to 17</th>
                        <th>Pets</th>
                        <th>Description</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="">
                        <td>{{ date('D, M d', strtotime($reservation->cid)) }} - {{ date('D, M d', strtotime($reservation->cod)) }}</td>
                        <td>{{ $reservation->siteid }}</td>
                        <td>{{ $reservation->siteclass }}</td>
                        <td>{{ $reservation->adults ?? 0 }}</td>
                        <td>5</td>
                        <td>0</td>
                        <td>{{ $reservation->pets ?? 0 }}</td>
                        @if(Request::is('admin/reservations/invoice/*'))
                            <td>{{ $cart->description }}</td>
                        @else 
                            <td>{{ $reservation->description }}</td>

                        @endif
                        <td>${{ number_format($reservation->subtotal, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="7"></td>
                        <td class="text-end">Subtotal</td>
                        <td>${{ number_format( $reservation->subtotal, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="2"></td>
                        <td>Tax</td>
                        <td></td>
                        <td></td>
                        <td colspan="3">Sales Tax (8.75%)</td>
                        <td>${{ number_format($reservation->totaltax, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="7"></td>
                        <td class="text-end">Total Payments</td>
                        <td >${{ number_format($reservation->total, 2) }}</td>
                    </tr>
                  
                    @if (Request::is('admin/reservations/invoice/*'))
                        @php   $balance = $reservation->total - $payment->payment; @endphp
                        <tr class="total-row">
                            <td colspan="7"></td>
                            <td class="text-end">Balance </td>
                            <td >${{ number_format($balance, 2) }}</td>
                        </tr>
                    @endif
                   
                </tbody>
            </table>
        </div>
    </div>
    <form  id="paymentchoices" method="POST">
        <header class="invoice-header mt-4">
            <h4>Payments</h4>
        </header>
        <div class="container-invoice">
            <div class="form-row mb-3">
                <div class="col">
                    <div class="form-group">
                        <label for="">Transaction Type</label>
                        <select name="transactionType" id="transactionType" class="form-control">
                            <option value="" selected disabled>Select Transaction Type</option>
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                            <option value="Credit Card - Manual">Credit Card - Manual</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Gift Card">Gift Card</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
               
            </div>
            <div class="form-row mb-3">
                <div class="col">
                    <div class="form-group ">
                        <label for="">Total Amount</label>
                        @if(Request::is('admin/reservations/invoice/*'))
                            <input class="form-control" type="text" name="xBalance" id="xAmount" value="{{ number_format($balance, 2) }}" readonly>

                        @else 
                            <input class="form-control" type="text" name="xAmount" id="xAmount" value="{{ number_format( $reservation->total, 2) }}" readonly>

                        @endif
                    </div>
                </div>
                <div class="col">
                    <div class="form-group ">
                        <label for="">Description</label>
                        <input class="form-control" type="text" name="description" id="">
                    </div>
                </div>

            </div>
            <div class="form-row mb-3" id="creditcard-manual"  style="display: none;">
                <div class="col-md-6">
                    <div class="form-group ">
                        <input type="text" maxlength="16" name="xCardNum" id="xCardNum" required
                            class="form-control" placeholder="Credit Card:">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group ">
                        <input type="text" name="xExp" id="xExp" required
                            class="form-control" placeholder="Expiration:" maxlength="5">
                    </div>

                </div>
            </div>
            <div class="form-row mb-3" id="cash" style="display: none;">
                <div class="col">
                    <div class="form-group ">
                        <label for="">Amount Tendered</label>
                        <input type="number"  name="xCash" id="xCash" required
                            class="form-control" placeholder="Cash:">
                    </div>

                </div>
                <div class="col">
                    <div class="form-group ">
                        <label for="">Change Due</label>
                        <input type="text"  id="xChange" readoly
                            class="form-control" placeholder="Change Due:">
                    </div>
                </div>
            </div>

            <div class="form-row mb-3" id="check" style="display: none;">
                <div class="col">
                    <div class="form-group ">
                        <label for="">Check Number</label>
                        <input type="text"  name="xCheckNum" id="xCheck" required
                            class="form-control" placeholder="Check Number:">
                    </div>
                </div>
            </div>
          

            <input type="hidden"  name="cartid" value="{{ $reservation->cartid }}">
            <input type="hidden"  name="id" value="{{ $reservation->id }}">
            <div class="form-row d-flex justify-content-end mr-1">
                @if(Request::is('admin/reservations/invoice/*'))
                    <div class="btn btn-success " id="payBalance"><i class="fa-solid fa-money-bill-transfer"></i> Process</div>
                @else 
                    <div class="btn btn-success " id="payBtn"><i class="fa-solid fa-money-bill-transfer"></i> Process</div>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection
