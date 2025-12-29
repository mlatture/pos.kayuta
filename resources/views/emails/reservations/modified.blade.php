<!DOCTYPE html>
<html>
<head>
    <title>Reservation Modification</title>
</head>
<body>
    <h2>Your Reservation has been Modified</h2>
    <p>Dear Customer,</p>
    <p>Your reservation modification has been processed successfully.</p>
    
    <h3>Details</h3>
    <p><strong>Old Reservation #:</strong> {{ $oldCartId }}</p>
    <p><strong>New Reservation #:</strong> {{ $newCartId }}</p>
    <p><strong>Credit Applied from Old Reservation:</strong> ${{ number_format(abs($creditAmount), 2) }}</p>

    <h3>New Reservation Items</h3>
    <ul>
        @foreach($newReservations as $res)
            <li>
                Site: {{ $res['siteid'] }} <br>
                Check-in: {{ \Carbon\Carbon::parse($res['cid'])->format('M d, Y') }} <br>
                Check-out: {{ \Carbon\Carbon::parse($res['cod'])->format('M d, Y') }} <br>
                Total: ${{ number_format($res['total'], 2) }}
            </li>
        @endforeach
    </ul>

    <p>Thank you for choosing us!</p>
</body>
</html>
