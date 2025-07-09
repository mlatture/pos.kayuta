@extends('layouts.admin')

@section('title', 'System Logs')
@section('content-header', 'System Logs')
@push('css')
    <style>
        table#logs-table {
            font-size: 0.875rem;
            /* Smaller font */
        }

        table#logs-table td,
        table#logs-table th {
            padding: 0.4rem 0.5rem !important;
            /* tighter spacing */
            vertical-align: top;
        }

        table#logs-table pre {
            font-size: 0.75rem;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 6px;
            max-height: 120px;
            overflow: auto;
            margin-bottom: 0;
            white-space: pre-wrap;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            padding: 0.3rem 0.5rem;
            font-size: 0.875rem;
        }

        .dt-top-container {
            gap: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="card shadow-sm mb-4 overflow-auto" style="max-height: 80vh">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="logs-table" width="100%">
                    <thead class="table-light">
                        <tr>
                            {{-- <th>#</th> --}}
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Confirmation</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Description</th>
                            <th>Before</th>
                            <th>After</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.system_logs.index') }}',
                columns: [
                    // {
                    //     data: 'DT_RowIndex',
                    //     name: 'DT_RowIndex',
                    //     orderable: false,
                    //     searchable: false
                    // },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'sale_amount',
                        name: 'sale_amount'
                    },
                    {
                        data: 'payment_type',
                        name: 'payment_type'
                    },
                    {
                        data: 'confirmation_number',
                        name: 'confirmation_number'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'customer_email',
                        name: 'customer_email'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'before',
                        name: 'before',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ? `<pre>${data}</pre>` : '-';
                        }

                    },
                    {
                        data: 'after',
                        name: 'after',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ? `<pre>${data}</pre>` : '-';
                        }

                    },
                ],
                responsive: true,
                dom: '<"dt-top-container d-flex justify-content-between align-items-center mb-3"' +
                    '<"dt-left-in-div"f>' +
                    '<"dt-center-in-div custom-filter-checkbox">' +
                    '<"dt-right-in-div"B>' +
                    '>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
                buttons: ['colvis', 'copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    search: 'Search: ',
                    lengthMenu: 'Show _MENU_ entries'
                },

                pageLength: 10
            });
        });
    </script>
@endsection
