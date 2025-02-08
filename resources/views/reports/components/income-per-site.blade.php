<table class="display nowrap table table-hover table-striped border p-0" cellspacing="0" width="100%" id="incomeTableContainer">
    <thead>
        <tr>
            <th>Site ID</th>
            <th>Site Name</th>
            <th>Rate Tier</th>
            <th>Seasonal</th>
            <th>Nights Occupied</th>
            <th>Income from Stays</th>
            <th>Total Sales</th>
            <th>Balance Owed</th>
            <th>Percent Occupancy</th>
            <th>Other Income</th>
            <th>Total Income</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sites as $site)
            <tr>
                <td>{{ $site->site_id }}</td>
                <td>{{ $site->site_name }}</td>
                <td>{{ $site->rate_tier }}</td>
                <td>{{ $site->seasonal ? 'Yes' : 'No' }}</td>
                <td>{{ $site->nights_occupied }}</td>
                <td>{{ number_format($site->income_from_stays, 2) }}</td>
                <td>{{ number_format($site->total_sales, 2) }}</td>
                <td>{{ number_format($site->balance_owed, 2) }}</td>
                <td>{{ $site->percent_occupancy }}%</td>
                <td>{{ number_format($site->other_income, 2) }}</td>
                <td>{{ number_format($site->total_income, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
