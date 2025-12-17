<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Print Reservation - {{ $reservation->first()->customernumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.4;
        }

        .container {
            width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .customer-info {
            width: 50%;
        }

        .confirmation-box {
            width: 45%;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: -1px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .summary-row td {
            border-top: 2px solid #000;
            text-align: right;
        }

        .no-border {
            border: none !important;
        }

        .text-right {
            text-align: right;
        }

        @media print {
            body {
                margin: 0;
            }

            .container {
                width: 100%;
            }

            button {
                display: none;
            }

            /* Hide print button when printing */
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">

        <div class="header-section">
            <div class="customer-info">

                <strong>{{ $user->f_name ?? '' }} {{ $user->l_name ?? '' }}</strong><br>
                {{ $user->street_address }}<br>
                {{ $user->city }}, {{ $user->state }} {{ $user->zip }}<br>
                {{ $user->email }}
            </div>
            <div class="confirmation-box">
                <div style="text-align: right; margin-bottom: 10px;">
                    Confirmation Number:
                    @foreach ($reservation as $res)
                        <strong>{{ $res->cartid }}</strong>,
                    @endforeach
                </div>
                <table>
                    <tr>
                        <th>Site</th>
                        <th>Site Type</th>
                        <th>Arrival</th>
                        <th>Departure</th>
                    </tr>
                    @foreach ($reservation as $res)
                        <tr>
                            <td>{{ $res->siteid }}</td>
                            <td>{{ str_replace('_', ' ', $res->siteclass) }}</td>
                            <td>{{ \Carbon\Carbon::parse($res->cid)->format('l, F j, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($res->cod)->format('l, F j, Y') }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Site</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Description</th>
                    <th>Each</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $accumulatedTotal = 0;
                    $accumulatedTax = 0;
                    $accumulatedSubTotal = 0;
                    $paymentTotal = 0;
                @endphp
                @foreach ($reservation as $res)
                    @php
                        $accumulatedSubTotal += (float) $res->subtotal;
                        $accumulatedTotal += (float) $res->totalcharges;
                        $accumulatedTax += (float) $res->totaltax;
                        $each = $res->subtotal / $res->nights;

                        $uniquePayments = $reservation->pluck('payment')->unique('receipt')->filter();
                    @endphp
                    <tr>

                        <td>{{ \Carbon\Carbon::parse($res->cid)->format('m/d/Y') }} -
                            {{ \Carbon\Carbon::parse($res->cod)->format('m/d/Y') }}</td>
                        <td>{{ $res->siteid }}</td>
                        <td>{{ $res->customertype }}</td>
                        <td>{{ $res->nights }}</td>
                        <td>Night</td>
                        <td>Site Rate for {{ $res->nights }} nights</td>
                        <td>${{ number_format($each, 2) }}</td>
                        <td>${{ number_format($res->subtotal, 2) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="7" class="text-right"><strong>Sub-Total</strong></td>
                    <td><strong>${{ number_format($accumulatedSubTotal, 2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="3">Tax</td>
                    <td colspan="4">{{ $accumulatedTax < 0 ? 'No Tax' : '' }}</td>
                    <td>${{ number_format($accumulatedTax, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="7" class="text-right"><strong>Total Charges</strong></td>
                    <td><strong>${{ number_format($accumulatedTotal, 2) }}</strong></td>
                </tr>
                @foreach ($uniquePayments as $payment)
                    @php
                        $paymentTotal += $payment->payment;
                        $balance = $accumulatedTotal - $paymentTotal;
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('m/d/Y') }}</td>
                        <td colspan="2">
                            @if ($payment->payment <= 0)
                                Declined
                            @else
                                {{ $payment->payment >= $accumulatedTotal ? 'Full Payment' : 'Partial Payment' }}
                            @endif
                        </td>
                        <td>{{ ucfirst($payment->method) }}</td>
                        <td colspan="3">

                            {{ ucfirst($payment->method) }}:
                            @if ($payment->method === 'Visa' && $payment->cardOnFile)
                                {{ $payment->cardOnFile->xmaxkedcardnumber }} -
                            @endif
                            Online Payment
                            @if ($payment->payment <= 0)
                                (Declined Amt:$0.00)
                            @endif
                        </td>
                        <td>${{ number_format($payment->payment, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" class="text-right"><strong>Payment Total</strong></td>
                    <td><strong>${{ number_format($paymentTotal, 2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-right"><strong>Balance Due</strong></td>
                    <td><strong>${{ number_format(max(0, $balance), 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
