<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">

    <style>
        #invoice-POS {
            position: relative;
            padding: 2mm;
            margin: 0 auto;
            width: 80mm;
            background: #FFF;
            z-index: 1;
            /* Make sure it's above the background */
        }

        #invoice-POS::before {
            content: "";
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-image: url('{{ session('receipt.logo') ? asset('storage/receipt_logos/' . session('receipt.logo')) : '' }}');
            background-size: 80%;
            /* Adjust size here */
            background-repeat: no-repeat;
            opacity: 0.05;
            /* Faint watermark effect */
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        /* Make sure all content stays above the watermark */
        #invoice-POS * {
            position: relative;
            z-index: 1;
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

        .info {
            display: block;
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

        .tabletitle {
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

        .text-center {
            text-align: center;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div id="invoice-POS">
        <center id="top">
            @if (session('receipt.headerText'))
                <p class="text-center mb-2" style="font-size: 0.8em;">
                    {{ session('receipt.headerText') }}
                </p>
            @endif

            <div class="info">
                <h5>Order#: {!! $order->id !!}</h5>
                <p>
                    Date: {!! date('d-m-Y') !!}<br>
                    Time: {!! date('H:i', time()) !!}
                </p>
            </div>
        </center>

        <div id="bot">
            {{-- Logo inside the body --}}
            <div class="text-center mb-2">
                <img src="{{ $logoUrl }}" alt="Watermark" style="opacity: 0.05; max-width: 200px; display: block; margin: 0 auto;">

            </div>

            {{-- Order items table --}}
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

                    @foreach ($order->items as $detail)
                        <tr class="service">
                            <td class="tableitem">
                                <p class="itemtext">{{ optional($detail->product)->name ?? 'N/A' }}</p>
                            </td>
                            <td class="tableitem">
                                <p class="itemtext">{{ $detail->quantity }}</p>
                            </td>
                            <td class="tableitem">
                                <p class="itemtext">${{ number_format($detail->product->price, 2) }}</p>
                            </td>
                            <td class="tableitem">
                                <p class="itemtext">${{ number_format($detail->product->price * $detail->quantity, 2) }}
                                </p>
                            </td>
                            <td class="tableitem">
                                <p class="itemtext">${{ number_format($detail->tax, 2) }}</p>
                            </td>
                            <td class="tableitem">
                                <p class="itemtext">${{ number_format($detail->discount, 2) }}</p>
                            </td>
                            @php
                                $subTotal = $detail->price;
                                $totalTax += $detail->tax;
                                $totalDiscount += $detail->discount;
                                $itemTotal += $subTotal;
                            @endphp
                            <td class="tableitem">
                                <p class="itemtext">${{ number_format($subTotal, 2) }}</p>
                            </td>
                        </tr>
                    @endforeach

                    <tr class="tabletitle">
                        <td colspan="5"></td>
                        <td class="Rate">
                            <h2>Tax</h2>
                        </td>
                        <td class="payment">
                            <h2>${{ number_format($totalTax, 2) }}</h2>
                        </td>
                    </tr>

                    @if ($totalDiscount > 0 || $order->gift_card_amount > 0)
                        <tr class="tabletitle">
                            <td colspan="5"></td>
                            <td class="Rate">
                                <h2>Discount</h2>
                            </td>
                            <td class="payment">
                                <h2>- ${{ number_format($totalDiscount, 2) }}</h2>
                            </td>
                        </tr>

                        <tr class="tabletitle">
                            <td colspan="5"></td>
                            <td class="Rate">
                                <h2>Gift Card</h2>
                            </td>
                            <td class="payment">
                                <h2>- ${{ number_format($order->gift_card_amount, 2) }}</h2>
                            </td>
                        </tr>
                    @endif

                    <tr class="tabletitle">
                        <td colspan="5"></td>
                        <td class="Rate">
                            <h2>Total</h2>
                        </td>
                        <td class="payment">
                            <h2>${{ number_format($order->amount, 2) }}</h2>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Footer Text + Thank you --}}
            <div id="legalcopy" class="text-center mt-2">
                @if (session('receipt.footerText'))
                    <p class="text-center" style="font-size: 0.8em; margin-bottom: 4px;">
                        {{ session('receipt.footerText') }}
                    </p>
                @endif

                <p class="legal"><strong>Thank you for your purchase!</strong></p>
            </div>
        </div>
    </div>
</body>

</html>
