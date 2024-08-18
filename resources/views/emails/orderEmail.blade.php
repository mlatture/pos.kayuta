<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Order Invoice # {!! $order->id !!} </title>
    <style>
        #invoice-POS {
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
            padding: 2mm;
            margin: 0 auto;
            width: 80mm;
            background: #FFF;

        }

        ::selection {
            background: #f31544;
            color: #FFF;
        }

        ::moz-selection {
            background: #f31544;
            color: #FFF;
        }

        h1 {
            font-size: 1.5em;
            color: #222;
        }

        h2 {
            font-size: .9em;
        }

        h3 {
            font-size: 1.2em;
            font-weight: 300;
            line-height: 2em;
        }

        p {
            font-size: .7em;
            color: #666;
            line-height: 1.2em;
        }

        #top,
        #mid,
        #bot {
            /* Targets all id with 'col-' */
            border-bottom: 1px solid #EEE;
        }

        #top {
            min-height: 100px;
        }

        #mid {
            min-height: 80px;
        }

        #bot {
            min-height: 50px;
        }

        #top .logo {
            / / float: left;
            height: 60px;
            width: 100px;
            background: url('{!! asset('images/logo.png') !!}') no-repeat;
            background-size: 95px 60px;
        }

        .clientlogo {
            float: left;
            height: 60px;
            width: 60px;
            background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
            background-size: 60px 60px;
            border-radius: 50px;
        }

        .info {
            display: block;
            / / float: left;
            margin-left: 0;
        }

        .title {
            float: right;
        }

        .title p {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            / / padding: 5 px 0 5 px 15 px;
            / / border: 1 px solid #EEE
        }

        .tabletitle {
            / / padding: 5 px;
            font-size: .5em;
            background: #EEE;
        }

        .service {
            border-bottom: 1px solid #EEE;
        }

        .item {
            width: 24mm;
        }

        .itemtext {
            font-size: .5em;
        }

        #legalcopy {
            margin-top: 5mm;
        }

        ul.recipt-ul {
            padding-left: 12px;
        }
    </style>
</head>

<body>

    <div id="invoice-POS">

        <center id="top">
            <div class="logo"></div>
            <div class="info">
                {{--            <h2>KozeShake</h2> --}}
                <h5>Order#: {!! $order->id !!}</h5>
                <p>
                    {{-- Franchise Name: {!! !empty($order->franchise->name) ? $order->franchise->name : 'N/A' !!} </br>
                    Address: {!! !empty($oder->franchise->address) ? $oder->franchise->address : 'N/A' !!}</br>
                    Email: {!! !empty($oder->franchise->franchiseManager->email) ? $oder->franchise->franchiseManager->email : 'N/A' !!}</br>
                    Phone: {!! !empty($order->franchise->phone_number) ? $order->franchise->phone_number : 'N/A' !!}</br> --}}
                    Date: {!! date('d-m-Y') !!}</br>
                    Time: {!! date('H:i', time()) !!}</br>
                </p>
            </div><!--End Info-->
        </center><!--End InvoiceTop-->

        <div id="mid">
            <div class="info">
                <h2>Contact Info</h2>
                <p>
                    Name: {!! !empty($order->customer->f_name) ? $order->customer->f_name : 'Walking Customer' !!}</br>
                    @if ($order->customer)
                        {{-- Address: {!! !empty($order->customer->address) ? $order->customer->address : 'N/A' !!}</br> --}}
                        Email: {!! !empty($order->customer->email) ? $order->customer->email : 'N/A' !!}</br>
                        Phone: {!! !empty($order->customer->phone) ? $order->customer->phone : 'N/A' !!}</br>
                    @endif
                </p>
            </div>
        </div><!--End Invoice Mid-->

        <div id="bot">

            <div id="table">
                <table>
                    <tr class="tabletitle">
                        <td class="item">
                            <h2>Item</h2>
                        </td>
                        <td class="Hours">
                            <h2>Qty</h2>
                        </td>
                        <td class="Hours">
                            <h2>Price</h2>
                        </td>
                        <td>
                            <h2>Item Total</h2>
                        </td>
                        <td>
                            <h2>Tax</h2>
                        </td>
                        <td>
                            <h2>Discount</h2>
                        </td>
                        <td class="Rate">
                            <h2>Sub Total</h2>
                        </td>
                    </tr>
                    @php
                        $itemTotal = 0;
                        $subTotal = 0;
                        $totalTax = 0;
                        $totalDiscount = 0;
                    @endphp
                    @if (count($order->items) > 0)
                        @foreach ($order->items as $key => $detail)
                            <tr class="service">
                                <td class="tableitem">
                                    <p class="itemtext">{!! !empty($detail->product) ? $detail->product->name : 'N/A' !!}</p>
                                </td>
                                <td class="tableitem">
                                    <p class="itemtext">{!! $detail->quantity !!}</p>
                                </td>
                                <td class="tableitem">
                                    <p class="itemtext">{!! '$' . number_format($detail->product->price, 2) !!}</p>
                                </td>
                                <td class="tableitem">
                                    <p class="itemtext">{!! '$' . number_format($detail->product->price * $detail->quantity, 2) !!}</p>
                                </td>
                                <td class="tableitem">
                                    <p class="itemtext">{!! '$' . number_format($detail->tax, 2) !!}</p>
                                </td>
                                <td class="tableitem">
                                    <p class="itemtext">{!! '$' . number_format($detail->discount, 2) !!}</p>
                                </td>

                                @php $subTotal = $detail->price @endphp
                                @php $totalTax += $detail->tax @endphp
                                @php $totalDiscount += $detail->discount @endphp
                                @php $itemTotal += $subTotal @endphp
                                <td class="tableitem">
                                    <p class="itemtext">{!! '$' . number_format($subTotal, 2) !!}</p>
                                </td>
                            </tr>
                        @endforeach

                        <tr class="tabletitle">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="Rate">
                                <h2>Tax</h2>
                            </td>
                            <td class="payment">
                                <h2>{!! '$' . number_format($totalTax, 2) !!}</h2>
                            </td>
                        </tr>

                        <tr class="tabletitle">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="Rate">
                                <h2>Discount</h2>
                            </td>
                            <td class="payment">
                                <h2>- {!! '$' . number_format($totalDiscount, 2) !!}</h2>
                            </td>
                        </tr>

                        <tr class="tabletitle">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="Rate">
                                <h2>Gift Card Amount</h2>
                            </td>
                            <td class="payment">
                                <h2>- {!! '$' . number_format($order->gift_card_amount, 2) !!}</h2>
                            </td>
                        </tr>

                        <tr class="tabletitle">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="Rate">
                                <h2>Total</h2>
                            </td>
                            <td class="payment">
                                <h2>{!! '$' . number_format($order->amount, 2) !!}</h2>
                            </td>
                        </tr>
                    @endif
                </table>
            </div><!--End Table-->

            <div id="legalcopy">
                <p class="legal"><strong>Thank you for purchase!</strong>
                </p>
            </div>

        </div><!--End InvoiceBot-->
    </div><!--End Invoice-->

</body>

</html>
