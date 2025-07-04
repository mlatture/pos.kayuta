<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seasonal Guest Renewal Contract</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            line-height: 1.5;
            color: #333;
        }

        .contract-container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
        }

        h1, h2, h3 {
            text-align: center;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        td, th {
            padding: 8px;
            border: 1px solid #ccc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .signature {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .signature div {
            width: 45%;
            text-align: center;
        }

        .gray-box {
            background-color: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
        }

    </style>
</head>
<body>
    <div class="contract-container">
        <h1>Seasonal Guest Renewal Agreement</h1>

        <div class="section">
            <p><strong>Guest Name:</strong> {{ $first_name }} {{ $last_name }}</p>
            <p><strong>Email:</strong> {{ $email }}</p>
            @if(!empty($site_number))
                <p><strong>Site Number:</strong> {{ $site_number }}</p>
            @endif
        </div>

        <div class="section">
            <h3 class="section-title">Rate Information</h3>
            <table>
                <tr>
                    <th>Initial Rate</th>
                    <td>${{ number_format($initial_rate, 2) }}</td>
                </tr>
                @if(!empty($discount_percent))
                <tr>
                    <th>Discount (%)</th>
                    <td>{{ $discount_percent }}%</td>
                </tr>
                @endif
                @if(!empty($discount_amount))
                <tr>
                    <th>Discount ($)</th>
                    <td>${{ number_format($discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <th>Final Rate</th>
                    <td><strong>${{ number_format($final_rate, 2) }}</strong></td>
                </tr>
                <tr>
                    <th>Final Payment Due</th>
                    <td>{{ $deadline }}</td>
                </tr>
            </table>
        </div>

        @if(!empty($addons))
        <div class="section">
            <h3 class="section-title">Add-Ons</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Max Allowed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($addons as $addon)
                        <tr>
                            <td>{{ $addon->seasonal_add_on_name }}</td>
                            <td>${{ number_format($addon->seasonal_add_on_price, 2) }}</td>
                            <td>{{ $addon->max_allowed }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="section">
            <div class="gray-box">
                <p>This agreement confirms the renewal of your seasonal stay. By signing below, you acknowledge and accept the terms and rates outlined above.</p>
            </div>
        </div>

        <div class="signature">
            <div>
                ____________________________<br>
                Guest Signature
            </div>
            <div>
                ____________________________<br>
                Date
            </div>
        </div>
    </div>
</body>
</html>
