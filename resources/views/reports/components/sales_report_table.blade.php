<table table class="display nowrap table table-hover table-striped border p-0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Transaction Date</th>
            <th>Source</th>
            <th>Confirmation # / POS ID</th>
            <th>Customer</th>
            <th>Site</th>
            <th>Type</th>
            <th>Account</th>
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
                <td>{{ $order->created_at->format('m/d/Y') }}</td>
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
