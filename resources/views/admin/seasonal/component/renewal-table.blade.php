@if ($filteredRenewals->count())
    <h5 class="mb-3 text-primary">🗂 Seasonal Renewals</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle" id="seasonalRenewalsTable">
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
                    <th>Card/Account</th>
                    <th>Day of Month</th>
                    <th>Action</th>
                    {{-- <th>Contract</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($filteredRenewals as $renewal)
                    <tr>
                        <td>{{ $renewal->id }}</td>
                        <td>{{ $renewal->customer->f_name }} {{ $renewal->customer->l_name }}</td>
                        <td>{{ $renewal->customer->email }}</td>
                        <td>{{ $renewal->allow_renew ? 'Yes' : 'No' }}</td>
                        <td>{{ $renewal->status ?? '' }}</td>
                        <td>${{ number_format($renewal->initial_rate, 2) }}</td>
                        <td>{{ $renewal->discount_percent }}%</td>
                        <td>${{ number_format($renewal->discount_amount, 2) }}</td>
                        <td>{{ $renewal->discount_note }}</td>
                        <td>${{ number_format($renewal->rate, 2) }}</td>
                        <td>{{ str_replace('_', ' ', $renewal->payment_plan) }}</td>
                        <td>{{ $renewal->selected_card }}</td>
                        <td>{{ $renewal->day_of_month }}</td>
                        <td>
                            <div class="d-flex justify-content-between w-100 px-2 gap-2">
                                <a href="{{ route('seasonal.user.statements', $renewal->customer_email) }}"
                                    class="btn btn-sm btn-outline-info">
                                    📄 View Statements
                                </a>
                                <a href="{{ route('seasonal.user.view.contract', $renewal->customer_email) }}"
                                    class="btn btn-sm btn-outline-info">
                                    📄 View Contract
                                </a>
                            </div>
                        </td>


                        {{-- <td>
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
                                        file_exists(
                                            public_path("storage/contracts/{$rate->template->name}/{$fileName}"),
                                        )
                                    ) {
                                        $templateName = $rate->template->name;
                                        break;
                                    }
                                }

                                $contractPath = $templateName ? "storage/contracts/{$templateName}/{$fileName}" : null;
                            @endphp

                            @if ($contractPath && file_exists(public_path($contractPath)))
                                <a href="{{ asset($contractPath) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    Download PDF
                                </a>
                            @else
                                <span class="text-muted">Not generated</span>
                            @endif
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info">No Seasonal Renewals Yet.</div>
@endif

@if ($nonRenewals->count())
    <h5 class="mt-5 text-danger">🚫 Non-Renewals</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle table-striped table-danger bg-light"
            id="nonRenewalsTable">
            <thead class="table-danger">
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    {{-- <th>Initial Rate</th> --}}
                    {{-- <th>Contract</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($nonRenewals as $renewal)
                    <tr>
                        <td>{{ $renewal->id }}</td>
                        <td>{{ $renewal->customer_name ?? $renewal->customer->f_name . ' ' . $renewal->customer->l_name }}
                        </td>
                        <td>{{ $renewal->customer_email ?? $renewal->customer->email }}</td>
                        <td class="text-danger fw-bold">{{ $renewal->status }}</td>
                        {{-- <td>${{ number_format($renewal->initial_rate, 2) }}</td>
                        <td>
                            @php
                                $fileName = "contract_{$renewal->customer->l_name}_{$renewal->customer_id}.pdf";
                                $contractPath = "storage/contracts/non-renewal/{$fileName}";
                            @endphp

                            @if (file_exists(public_path($contractPath)))
                                <a href="{{ asset($contractPath) }}" target="_blank"
                                    class="btn btn-sm btn-outline-danger">
                                    View Letter
                                </a>
                            @else
                                <span class="text-muted">Not generated</span>
                            @endif
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
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
