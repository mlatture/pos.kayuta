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
        @foreach ($orders as $order)
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $order->created_at->format('m/d/Y') }}</td>
                    <td>{{ $order->source ?? 'N/A' }}</td>
                    <td>{{ $order->id ?? 'N/A' }}</td>
                    <td>{{ $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : 'N/A' }}</td>
                    <td>{{ $order->reservations->first()->siteid ?? 'N/A' }}</td>
                    <td>{{ $item->product->taxType->title ?? 'N/A' }}</td>
                    <td>{{ $order->account ?? 'N/A' }}</td>
                    <td>{{ $order->user->name ?? 'Product Charge' }}</td>
                    <td>{{ $order->description ?? 'N/A' }}</td>
                    <td>{{ $order->amount ?? 0 }}</td>
                    <td>{{ $item->quantity ?? 0 }}</td>
                    <td>{{ $order->unit ?? 'N/A' }}</td>
                    <td>{{ $order->total ?? 0 }}</td>
                    <td>{{ $order->user ?? 'N/A' }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
