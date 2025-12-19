@extends('layouts.admin')

@section('title', 'Customer Account')
@section('content-header', 'Customer Account')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <noscript>
        <div class="alert alert-warning mb-3">This page requires JavaScript to load balances, receipts, and email tools.
        </div>
    </noscript>

    <div class="card" id="customerAccountApp" data-customer-id="{{ $customer->id }}">
        <div class="card-body">

            {{-- Header / Identity --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="mb-0">
                        {{ $customer->name ?? trim(($customer->f_name ?? '') . ' ' . ($customer->l_name ?? '')) }}
                        <small class="text-muted">#{{ $customer->id }}</small>
                    </h4>
                    @if ($customer->email)
                        <div class="text-muted small">{{ $customer->email }}</div>
                    @endif
                </div>
                <div>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>

            {{-- Balance (big, color-coded) --}}
            <div class="card mb-4" aria-live="polite">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Current Balance</div>
                            <div id="balanceNumber" class="display-6 fw-bold">—</div>
                            <div id="balanceBreakdown" class="small text-muted mt-2">
                                <span class="placeholder col-8 placeholder-wave">&nbsp;</span>
                            </div>
                        </div>
                        <div id="balanceStatus" class="text-end">
                            <span class="badge bg-secondary">Loading…</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Two-column layout on desktop --}}
            <div class="row g-3">
                <div class="col-lg-7">
                    {{-- Receipts --}}
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Receipts & Transactions</span>
                            <div class="small text-muted" id="receiptsMeta">—</div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="receiptsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:120px;">Date</th>
                                            <th>Type</th>
                                            <th>Reference</th>
                                            <th class="text-end" style="width:140px;">Amount</th>
                                            <th style="width:110px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="spinner-border spinner-border-sm me-2" role="status"
                                                    aria-hidden="true"></div>
                                                Loading receipts…
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Documents (placeholder) --}}
                    <div class="card mb-3" id="documentsSection">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Documents</span>
                            <div>
                                <button class="btn btn-sm btn-outline-primary" disabled title="Coming soon">Upload</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-muted">No documents to display yet.</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    {{-- Seasonal Schedule (placeholder) --}}
                    <div class="card mb-3" id="seasonalSection">
                        <div class="card-header">Seasonal Payment Schedule</div>
                        <div class="card-body">
                            <div class="text-muted">No seasonal schedule found.</div>
                        </div>
                    </div>

                    {{-- Prepaid Items (placeholder) --}}
                    <div class="card mb-3" id="prepaidSection">
                        <div class="card-header">Loyalty Rewards & Prepaid Items</div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Used</th>
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="prepaidBody">
                                        <tr>
                                            <td colspan="3" class="text-muted text-center py-3">None yet.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{-- Later: link “Redeem via POS” --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reservations (with Email button) --}}
            <div class="card mb-4" id="reservationsSection">
                <div class="card-header">Reservations</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="reservationsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:140px;">Check-in</th>
                                    <th style="width:140px;">Check-out</th>
                                    <th>Site</th>
                                    <th>Status</th>
                                    <th>Confirmation #</th>
                                    <th style="width:120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No reservations to display.</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Email Modal --}}
    <div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form id="emailForm" method="POST"
                    action="{{ route('admin.customers.account.email.send', $customer->id) }}">
                    @csrf
                    <input type="hidden" name="reservation_id" id="emailReservationId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="emailModalTitle">Resend Confirmation Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">To</label>
                                <input type="text" class="form-control" name="to" id="emailTo"
                                    value="{{ $customer->email }}" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CC</label>
                                <input type="text" class="form-control" name="cc" id="emailCc"
                                    placeholder="Optional, comma-separated" autocomplete="off">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email Content</label>
                                <textarea class="form-control" name="content" id="emailContent" rows="12" placeholder="Loading template…"></textarea>
                                <div class="form-text">This content is pre-filled from your existing confirmation template
                                    and is fully editable.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="me-auto small" id="emailFeedback" aria-live="polite"></div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="emailSendBtn">
                            <span class="js-send-text">Send</span>
                            <span class="js-send-spinner d-none spinner-border spinner-border-sm ms-2" role="status"
                                aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        console.log('Customer Account script loaded ✅');
        $(function() {
            // Ensure jQuery exists and stack is rendered

            const csrf = $('meta[name="csrf-token"]').attr('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            });

            // Helpers
            function money(n) {
                const num = Number(n ?? 0);
                return num < 0 ? `- $${Math.abs(num).toFixed(2)}` : `$${num.toFixed(2)}`;
            }

            function setBalanceColor(total) {
                const balanceEl = $('#balanceNumber');
                const statusEl = $('#balanceStatus');

                if (total > 0) {
                    balanceEl.css('color', '#dc3545'); 
                    statusEl.html('<span class="badge bg-danger">Due</span>');
                } else if (total < 0) {
                    balanceEl.css('color', '#198754'); 
                    statusEl.html('<span class="badge bg-success">Credit</span>');
                } else {
                    balanceEl.css('color', '#000000'); 
                    statusEl.html('<span class="badge bg-secondary">Settled</span>');
                }
            }

            /* ---------------- Balance ---------------- */
            $('#balanceStatus').html('<span class="badge bg-secondary">Loading…</span>');
            $.ajax({
                    url: '{{ route('admin.customers.account.balance', $customer->id) }}',
                    method: 'GET',
                    dataType: 'json',
                    timeout: 15000,
                    beforeSend: function() {
                        $('#balanceBreakdown').html(
                            '<span class="placeholder col-8 placeholder-wave">&nbsp;</span>');
                    }
                })
                .done(function(data) {
                    console.log('Balance API response:', data);

                    const total = Number(data.total ?? 0);
                    $('#balanceNumber').text(money(total));
                    setBalanceColor(total);

                    const p = data.parts || {};
                    const items = [];
                    if (p.r) items.push(`Reservations: ${money(p.r.due ?? 0)}`);
                    if (p.u) items.push(`Utilities: ${money(p.u.due ?? 0)}`);
                    if (p.s) items.push(`Seasonal: ${money(p.s.due ?? 0)}`);
                    if (p.p) items.push(`POS: ${money(p.p.due ?? 0)}`);
                    if (p.g) items.push(`Gift Cards (credit): ${money(-(p.g.credit ?? 0))}`);

                    $('#balanceBreakdown').text(items.length ? items.join(' • ') : 'No balance entries.');
                    $('#balanceStatus').html(
                        `<span class="badge ${ total===0 ? 'bg-secondary' : (total < 0 ? 'bg-success' : 'bg-danger') }">
                            ${ total===0 ? 'Settled' : (total<0 ? 'Credit' : 'Due') }
                        </span>`
                    );
                })
                .fail(function(xhr, status, err) {
                    console.error('Balance API error:', status, err, xhr?.responseText);
                    $('#balanceStatus').html('<span class="badge bg-warning text-dark">Failed to load</span>');
                    $('#balanceBreakdown').text('—');
                    $('#balanceNumber').text('—').css('color', '#000');
                });

            /* ---------------- Receipts ---------------- */
            const $tbody = $('#receiptsTable tbody');
            $.ajax({
                    url: '{{ route('admin.customers.account.receipts', $customer->id) }}',
                    method: 'GET',
                    dataType: 'json',
                    timeout: 15000,
                    beforeSend: function() {
                        $tbody.html(
                            `<tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></div>
                                    Loading receipts…
                                </td>
                            </tr>`
                        );
                    }
                })
                .done(function(data) {
                    $tbody.empty();
                    const rows = (data && data.rows) ? data.rows : [];
                    $('#receiptsMeta').text(`${rows.length} item(s)`);
                    if (!rows.length) {
                        $tbody.html(
                            `<tr><td colspan="5" class="text-center py-4 text-muted">No receipts found.</td></tr>`
                        );
                        return;
                    }
                    rows.forEach(function(row) {
                        $tbody.append(
                            `<tr>
                            <td>${row.date ?? ''}</td>
                            <td>${row.type ?? ''}</td>
                            <td>${row.reference ?? ''}</td>
                            <td class="text-end">${money(row.amount)}</td>
                            <td>${row.status_badge ?? ''}</td>
                            </tr>`
                        );
                    });
                })
                .fail(function(xhr, status, err) {
                    console.error('Receipts API error:', status, err, xhr?.responseText);
                    $tbody.html(
                        `<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load receipts.</td></tr>`
                    );
                });

            /* ---------------- Email Modal (open) ---------------- */
            $(document).on('click', '.js-email', function() {
                const $btn = $(this);
                const reservationId = $btn.data('reservation-id') || '';
                const conf = $btn.data('confirmation') || '';
                const email = $btn.data('customer-email') || @json($customer->email);

                $('#emailReservationId').val(reservationId);
                $('#emailTo').val(email);
                $('#emailCc').val('');
                $('#emailModalTitle').text(`Resend Confirmation Email — ${conf || 'Reservation'}`);

                const url = '{{ route('admin.customers.account.email.template', $customer->id) }}' + (
                    reservationId ? (`?reservation_id=${reservationId}`) : '');
                $.ajax({
                        url,
                        method: 'GET',
                        dataType: 'html',
                        timeout: 15000,
                        beforeSend: function() {
                            $('#emailContent').val('Loading template…');
                        }
                    })
                    .done(function(html) {
                        $('#emailContent').val(html || '[[ Confirmation template content here ]]');
                        $('#emailModal').modal('show');
                    })
                    .fail(function(xhr, status, err) {
                        console.error('Email template error:', status, err, xhr?.responseText);
                        $('#emailContent').val('[[ Confirmation template content here ]]');
                        $('#emailModal').modal('show');
                    });
            });

            const $emailForm = $('#emailForm');
            const $sendBtn = $('#emailSendBtn');
            const $sendText = $sendBtn.find('.js-send-text');
            const $sendSpinner = $sendBtn.find('.js-send-spinner');
            const $feedback = $('#emailFeedback');

            $emailForm.on('submit', function(e) {
                e.preventDefault();

                $sendBtn.prop('disabled', true);
                $sendSpinner.removeClass('d-none');
                $sendText.text('Sending…');
                $feedback.text('').removeClass('text-success text-danger');

                const formData = new FormData(this);

                $.ajax({
                        url: '{{ route('admin.customers.account.email.send', $customer->id) }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        timeout: 20000
                    })
                    .done(function() {
                        $feedback.text('Email sent successfully.').addClass('text-success').removeClass(
                            'text-danger');
                    })
                    .fail(function(xhr) {
                        console.error('Email send error:', xhr?.status, xhr?.statusText, xhr
                            ?.responseText);
                        let msg = 'SMTP error. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON
                            .message;
                        $feedback.text(msg).addClass('text-danger').removeClass('text-success');
                    })
                    .always(function() {
                        $sendBtn.prop('disabled', false);
                        $sendSpinner.addClass('d-none');
                        $sendText.text('Send');
                    });
            });
        });
    </script>
@endpush
