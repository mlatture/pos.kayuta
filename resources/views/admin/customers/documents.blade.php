@extends('layouts.admin')

@section('content')
    <div class="card shadow-sm rounded-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h4 class="mb-0">Documents — {{ $user->name ?? $user->f_name . ' ' . $user->l_name }}</h4>

            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Category filter --}}
                <div class="input-group input-group-sm" style="min-width:260px;">
                    <span class="input-group-text bg-white"><i class="fas fa-filter"></i></span>
                    <select id="categoryFilter" class="form-select">
                        <option value="">All categories</option>
                        <option value="contracts">Contracts</option>
                        <option value="renewals">Renewals</option>
                        <option value="non_renewals">Non Renewals</option>
                        <option value="waivers">Waivers</option>
                        <option value="vaccinations">Vaccinations</option>
                        <option value="ids">IDs</option>
                    </select>
                </div>

                {{-- Show expired switch --}}
                <div class="form-check form-switch ms-1">
                    <input class="form-check-input" type="checkbox" id="showExpired" checked>
                    <label class="form-check-label" for="showExpired">Show expired</label>
                </div>

                {{-- Bulk delete --}}
                <button id="bulkDeleteBtn" class="btn btn-sm btn-danger ms-1" disabled>
                    <i class="fas fa-trash"></i> <span class="d-none d-sm-inline">Bulk Delete</span>
                </button>

                {{-- Find Waivers (unattached) --}}
                <button class="btn btn-sm btn-success ms-1"
                        data-bs-toggle="modal"
                        data-bs-target="#findWaiversModal"
                        id="findWaiversBtn">
                    Find Waivers
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="documentsTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px" class="text-center">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Expires</th>
                            <th>Added</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal shell for Unattached Waivers --}}
    <div class="modal fade" id="findWaiversModal" tabindex="-1" aria-labelledby="findWaiversModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="findWaiversModalLabel">Unattached Waivers</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">✕</button>
          </div>
          <div class="modal-body" id="findWaiversBody">
            <div class="text-center py-5">Loading…</div>
          </div>
        </div>
      </div>
    </div>
@endsection

@push('css')
    <style>
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #f8fafc !important;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .card {
            border-radius: 14px;
        }

        .card-header {
            background: #ffffff;
        }

        tr.row-expired {
            background-color: #fff6db !important;
        }

        .input-group-text {
            border-right: 0;
        }

        .input-group .form-select {
            border-left: 0;
        }

        #bulkDeleteBtn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        @media (max-width: 576px) {
            .card-header > .d-flex:last-child {
                width: 100%;
            }

            .input-group {
                flex: 1 1 auto;
            }

            #bulkDeleteBtn {
                width: 100%;
            }
        }
    </style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ---------- UNATTACHED WAIVERS MODAL + DATATABLE ----------

    const body      = document.getElementById('findWaiversBody');
    const btn       = document.getElementById('findWaiversBtn');
    const htmlUrl   = @json(route('customers.waivers', ['user' => $user->id]));
    const dataUrl   = @json(route('customers.waivers.data', ['user' => $user->id]));
    const attachUrl = @json(route('customers.waivers.attach', ['user' => $user->id]));
    const deleteUrl = @json(route('waivers.bulkDelete'));

    let waiversTable = null;
    let waiversLoaded = false;

    if (btn) {
        btn.addEventListener('click', function () {
            if (waiversLoaded) return; // already loaded once

            body.innerHTML = '<div class="text-center py-5">Loading…</div>';

            fetch(htmlUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                .then(r => r.text())
                .then(html => {
                    body.innerHTML = html;
                    waiversLoaded = true;
                    initWaiversDataTable();
                })
                .catch(() => {
                    body.innerHTML = '<div class="alert alert-danger">Failed to load waivers.</div>';
                });
        });
    }

    function initWaiversDataTable() {
        // DataTable init for unattached waivers table
        waiversTable = $('#w_table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.date_from = $('#w_date_from').val();
                    d.date_to   = $('#w_date_to').val();
                    d.email     = $('#w_email').val();
                    d.name      = $('#w_name').val();
                }
            },
            order: [[1, 'desc']],
            columns: [
                { data: 'checkbox', orderable:false, searchable:false },
                { data: 'created_at', name: 'created_at' },
                { data: 'name',       name: 'name' },
                { data: 'email',      name: 'email' },
                { data: 'site_text',  name: 'site_text' },
                { data: 'booking_id', name: 'booking_id' },
                { data: 'ip',         name: 'ip' },
                { data: 'download',   orderable:false, searchable:false },
                { data: 'status_badge', orderable:false, searchable:false },
            ],
            drawCallback: function(){
                $('#w_all').off('change').on('change', function(){
                    $('.row-check').prop('checked', this.checked);
                });
            },
            createdRow: function(row) {
                $(row).addClass('align-middle');
            },
            columnDefs: [
                { targets: [0,7,8], className: 'text-center' }
            ],
        });

        // Filter apply
        $(document).on('click', '#w_filter', function () {
            waiversTable.ajax.reload();
        });

        function selectedIds() {
            return $('.row-check:checked').map(function(){ return this.value; }).get();
        }

        // Attach selected waivers to this customer
        $(document).on('click', '#w_attach', function () {
            const ids = selectedIds();
            if (!ids.length) return alert('Select at least one waiver.');
            if (!confirm('Attach selected waivers to this customer?')) return;

            fetch(attachUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token())
                },
                body: JSON.stringify({ waiver_ids: ids })
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return alert(res.message || 'Failed.');
                waiversTable.ajax.reload(null, false);
                alert('Attached.');
            })
            .catch(() => alert('Request failed.'));
        });

        // Soft delete selected waivers
        $(document).on('click', '#w_delete', function () {
            const ids = selectedIds();
            if (!ids.length) return alert('Select at least one waiver.');
            if (!confirm('Soft delete selected waivers?')) return;

            fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token())
                },
                body: JSON.stringify({ waiver_ids: ids })
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return alert(res.message || 'Failed.');
                waiversTable.ajax.reload(null, false);
                alert('Deleted.');
            })
            .catch(() => alert('Request failed.'));
        });
    }
});
</script>

<script>
    $(function() {
        // ---------- EXISTING DOCUMENTS DATATABLE (GuestFile) ----------

        const selected = new Set();

        const table = $('#documentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('customers.documents.data', $user->id) }}',
                data: function(d) {
                    d.category = $('#categoryFilter').val() || '';
                    d.show_expired = $('#showExpired').is(':checked') ? 1 : 0;
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'category_badge', name: 'file_category', orderable: false, searchable: false },
                { data: 'expires_h', name: 'expiration_date', orderable: false, searchable: false },
                { data: 'added_h', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
            ],
            order: [[4, 'desc']],
            pageLength: 10,
            responsive: true,
            drawCallback: function() {
                // re-check selected rows
                $('#documentsTable .row-check').each(function() {
                    const id = $(this).data('id');
                    if (selected.has(id)) $(this).prop('checked', true);
                });
                $('#checkAll').prop('checked', false);
                toggleBulkButton();
            },
            createdRow: function(row) {
                $('td:eq(0)', row).addClass('text-center');
            }
        });

        // Filters
        $('#categoryFilter, #showExpired').on('change', function() {
            table.ajax.reload();
        });

        // Single select
        $(document).on('change', '.row-check', function() {
            const id = $(this).data('id');
            if (this.checked) selected.add(id);
            else selected.delete(id);
            toggleBulkButton();
        });

        // Check all
        $('#checkAll').on('change', function() {
            const checked = this.checked;
            $('#documentsTable .row-check').each(function() {
                $(this).prop('checked', checked).trigger('change');
            });
        });

        function toggleBulkButton() {
            $('#bulkDeleteBtn').prop('disabled', selected.size === 0);
        }

        // SweetAlert helpers
        const showLoading = (title = 'Working...') =>
            Swal.fire({
                title,
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false,
                allowEscapeKey: false
            });

        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        // Single delete
        $(document).on('click', '.btn-single-del', function() {
            const url = $(this).data('url');

            Swal.fire({
                title: 'Delete this file?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((res) => {
                if (!res.isConfirmed) return;

                showLoading('Deleting...');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        Swal.close();
                        toast.fire({
                            icon: 'success',
                            title: 'File deleted'
                        });
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.close();
                        const msg = xhr.status === 403
                            ? 'You do not have permission to delete this file.'
                            : (xhr.responseJSON?.message || 'Delete failed.');
                        Swal.fire({
                            icon: 'error',
                            title: 'Delete failed',
                            text: msg
                        });
                    }
                });
            });
        });

        // Bulk delete
        $('#bulkDeleteBtn').on('click', function() {
            if (selected.size === 0) return;

            Swal.fire({
                title: `Delete ${selected.size} file(s)?`,
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete all',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((res) => {
                if (!res.isConfirmed) return;

                showLoading('Deleting...');
                $.ajax({
                    url: '{{ route('file.bulkDestroy') }}',
                    type: 'POST',
                    data: {
                        ids: Array.from(selected),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        Swal.close();
                        if (res && res.success) {
                            const failedCount = res.failed ? Object.keys(res.failed).length : 0;
                            const msg = `Deleted: ${res.deleted_count}` + (failedCount ? ` | Failed: ${failedCount}` : '');
                            Swal.fire({
                                icon: 'success',
                                title: 'Bulk delete complete',
                                text: msg
                            });
                            selected.clear();
                            $('#checkAll').prop('checked', false);
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Bulk delete failed',
                                text: (res && res.message) ? res.message : 'Unknown error'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Bulk delete failed',
                            text: xhr.responseJSON?.message || 'Unknown error'
                        });
                    }
                });
            });
        });
    });
</script>
@endpush
