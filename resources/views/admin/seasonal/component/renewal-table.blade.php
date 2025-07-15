

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
                {{-- <th>Payments</th> --}}
                <th>Card/Account</th>
                <th>Day of Month</th>

                <th>Contract</th>

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
                    <td>{{ str_replace('_', ' ', $renewal->payment_plan) }}</td>
                    {{-- <td>
                        @if ($renewal->linked_plan_id)
                            <a href="{{ route('payment-plans.show', $renewal->linked_plan_id) }}"
                                target="_blank">View</a>
                        @else
                            —
                        @endif
                    </td> --}}
                    <td>{{ $renewal->selected_card }}</td>
                    <td>{{ $renewal->day_of_month }}</td>

                    <td>
                        @php
                            $fileName = "contract_{$renewal->customer->l_name}_{$renewal->customer_id}.pdf";

                            $templateName = null;

                            $seasonalIds = is_array($renewal->customer->seasonal)
                                ? $renewal->customer->seasonal
                                : json_decode($renewal->customer->seasonal, true);

                            foreach ($seasonalIds ?? [] as $rateId) {
                                $rate = $seasonalRates->firstWhere('id', $rateId);
                                if (
                                    $rate &&
                                    $rate->template &&
                                    file_exists(public_path("storage/contracts/{$rate->template->name}/{$fileName}"))
                                ) {
                                    $templateName = $rate->template->name;
                                    break;
                                }
                            }

                            $contractPath = $templateName ? "storage/contracts/{$templateName}/{$fileName}" : null;
                        @endphp

                        @if ($contractPath && file_exists(public_path($contractPath)))
                            <a href="{{ asset($contractPath) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                Download PDF
                            </a>
                        @else
                            <span class="text-muted">Not generated</span>
                        @endif
                    </td>


                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <li class="list-group-item">No Seasonal Renewals Yet.</li>

@endif
@section('js')
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                responsive: true,
                stateSave: true,
                dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
                buttons: [
                    'colvis',
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],

                language: {
                    search: 'Search: ',
                    lengthMenu: 'Show _MENU_ entries',
                },
                pageLength: 10
            });

        })
    </script>
@endsection