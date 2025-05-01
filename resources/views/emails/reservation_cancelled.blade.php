<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reservation Cancelled</title>
</head>

<body>
    <h2>Your Reservation Has Been Cancelled</h2>

    <p>Confirmation Number: <strong>{{ $cartid }}</strong></p>

    <p>The following site(s) were cancelled:</p>

    <ul>
        @foreach ($sites as $site)
            <li>Site ID: <strong>{{ $site['siteid'] }}</strong>, Amount Refunded:
                <strong>${{ number_format($site['base'] * 0.85, 2) }}</strong></li>
        @endforeach
    </ul>

    <p>Refund Method: <strong>{{ ucfirst(str_replace('-', ' ', $refundMethod)) }}</strong></p>

    <p>If you have any questions or concerns, feel free to contact our team. Thank you.</p>

    <p>— Kayuta Lake Campground</p>
</body>

</html>
