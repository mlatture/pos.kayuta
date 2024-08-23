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
                        <td>{{ $reservation->pets ?? 0}}</td>
                        <td>Daily Rate</td>
                        <td>$100</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="7"></td>
                        <td class="text-end">Subtotal</td>
                        <td>$300</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="2"></td>
                        <td>Tax</td>
                        <td></td>
                        <td></td>
                        <td colspan="3">Sales Tax (8.75%)</td>
                        <td>$39.38</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="7"></td>
                        <td class="text-end">Total Payments</td>
                        <td>$500</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <form action="" id="paymentchoices">
        <header class="invoice-header mt-4">
            <h4>Payments</h4>
        </header>
        <div class="container-invoice">
            <div class="form-row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="adults">Transaction Type</label>
                        <input type="number" class="form-control" id="adults" 
                        name="adults" placeholder="Enter Number of Adults">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="under">Payment Type</label>
                        <input type="number" class="form-control" id="under" 
                        name="under" placeholder="Enter Number of Childrens">
                    </div>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="adults">Credit Card Type</label>
                        <input type="number" class="form-control" id="adults" 
                        name="adults" placeholder="Enter Number of Adults">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="under">Credit Card Number</label>
                        <input type="number" class="form-control" id="under" 
                        name="under" placeholder="Enter Number of Childrens">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
