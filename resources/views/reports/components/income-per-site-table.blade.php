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
            @if ($sites->isEmpty())
                <tr>
                    <td colspan="11" class="text-center">No data available for the selected date range.</td>
                </tr>
            @else
                @foreach ($sites as $site)
                    <tr>
                        <td>{{ $site->site_id ?? 'N/A' }}</td>
                        <td>{{ $site->site_name ?? 'N/A' }}</td>
                        <td>{{ $site->rate_tier ?? 'N/A' }}</td>
                        <td>{{ $site->seasonal ? 'Yes' : 'No' }}</td>
                        <td>{{ $site->nights_occupied ?? 0 }}</td>
                        <td>{{ number_format($site->income_from_stays ?? 0, 2) }}</td>
                        <td>{{ number_format($site->total_sales ?? 0, 2) }}</td>
                        <td>{{ number_format($site->balance_owed ?? 0, 2) }}</td>
                        <td>{{ $site->percent_occupancy ?? 0 }}%</td>
                        <td>{{ number_format($site->other_income ?? 0, 2) }}</td>
                        <td>{{ number_format($site->total_income ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
