<!DOCTYPE html>
<html>

<head>
    <title>{{ $title ?? 'Z-Out Report' }}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 2px;
            text-align: center;
          
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;

            font-size: 8px;
        }

       
        h1 {
            text-align: center;
            font-size: 14px
        }

        h2 {
            margin-bottom: 10px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <h1>{{ $title }}</h1>

    <h2>Sales Summary</h2>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Gross Sales</th>
                <th>Gross Returns</th>
                <th>Net</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sales</td>
                <td>{{ $grossSales }}</td>
                <td>$0.00</td>
                <td>{{ $netSales }}</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td>{{ $salesTax }}</td>
                <td>$0.00</td>
                <td>{{ $netTax }}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>{{ $totalSales }}</td>
                <td>$0.00</td>
                <td>{{ $netTotalSales }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Sales Activity</h2>
    <table>
        <thead>
            <tr>
                <th>Account</th>
                <th>Amount</th>
                <th>Tax</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salesActivity as $activity)
                <tr>
                    <td>{{ $activity['account'] }}</td>
                    <td>{{ $activity['amount'] }}</td>
                    <td>{{ $activity['tax'] }}</td>
                    <td>{{ $activity['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Payment Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Payment Method</th>
                <th>Transaction Count</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paymentSummary as $payment)
                <tr>
                    <td>{{ $payment['method'] }}</td>
                    <td>{{ $payment['transactionCount'] }}</td>
                    <td>{{ $payment['amount'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Credit Card Listing</h2>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($creditCardListing as $card)
                <tr>
                    <td>{{ $card['type'] }}</td>
                    <td>{{ $card['name'] }}</td>
                    <td>{{ $card['amount'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>User Activity</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Stat.</th>
                <th>User</th>
                <th>Total</th> 
                  @for ($i = 0; $i < 24; $i++)
                    <th>{{ $i }}:00</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($userActivity as $activity)
                <tr>
                    <td>{{ $activity['date'] }}</td>
                    <td>{{ $activity['statName'] }}</td>
                    <td>{{ $activity['userName'] }}</td>
                    <td>{{ $activity['totalCount'] }}</td>
                    @foreach ($activity['hourlyCounts'] as $hourlyCount)
                        <td>{{ $hourlyCount }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>


</body>

</html>
