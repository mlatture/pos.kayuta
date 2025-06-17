<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Electric Bill</title>
</head>
<body>
    <h2>Electric Bill Summary</h2>

    <p><strong>Customer Name:</strong> {{ $details['customer']->name ?? 'N/A' }}</p>
    <p><strong>Site:</strong> {{ $details['site']->sitename ?? $details['site']->siteid ?? 'N/A' }}</p>
    <p><strong>Billing Period:</strong> {{ \Carbon\Carbon::parse($details['start_date'])->format('F j, Y') }} to {{ \Carbon\Carbon::parse($details['end_date'])->format('F j, Y') }}</p>
    <p><strong>Usage:</strong> {{ number_format($details['usage'], 2) }} kWh</p>
    <p><strong>Rate:</strong> ${{ number_format($details['rate'], 2) }} per kWh</p>
    <p><strong>Days:</strong> {{ $details['days'] }} days</p>
    <hr>
    <p><strong>Total Bill:</strong> <strong>${{ number_format($details['total'], 2) }}</strong></p>

    <br>
    <p>If you have any questions, please contact our team.</p>
</body>
</html>
