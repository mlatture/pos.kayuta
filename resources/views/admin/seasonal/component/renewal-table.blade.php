@if ($renewals->count())
    <table class="table table-bordered table-striped bg-light" id="seasonalRenewalsTable">
        <thead class="table-secondary">
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Allow Renew</th>
                <th>Status</th>
                <th>Initial Rate</th>
                <th>Discount %</th>
                <th>Discount $</th>
                <th>Discount Note</th>
                <th>Final Rate</th>
                <th>Payment Plan</th>
                <th>Payments</th>
                <th>Card/Account</th>
                <th>Day of Month</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($renewals as $renewal)
                <tr>
                    <td>{{ $renewal->id }}</td>
                    <td>{{ $renewal->customer->f_name }} {{ $renewal->customer->l_name }}</td>
                    <td>{{ $renewal->customer->email }}</td>
                    <td>{{ $renewal->allow_renew ? 'Yes' : 'No' }}</td>
                    <td>{{ $renewal->status ?? '' }}</td>
                    <td>${{ $renewal->initial_rate }}</td>
                    <td>{{ $renewal->discount_percent }}%</td>
                    <td>${{ $renewal->discount_amount }}</td>
                    <td>{{ $renewal->discount_note }}</td>
                    <td>${{ $renewal->rate }}</td>
                    <td>{{ $renewal->payment_plan }}</td>
                    <td>
                        @if ($renewal->linked_plan_id)
                            <a href="{{ route('payment-plans.show', $renewal->linked_plan_id) }}"
                                target="_blank">View</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $renewal->masked_account }}</td>
                    <td>{{ $renewal->day_of_month }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <li class="list-group-item">No Seasonal Renewals Yet.</li>

@endif
