@extends('layouts.admin')

@section('content')
    <div class="card shadow-sm rounded-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h4 class="mb-0">Documents — {{ $user->name ?? $user->f_name . ' ' . $user->l_name }}</h4>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Category filter with icon -->
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

                <!-- Show expired as a switch -->
                <div class="form-check form-switch ms-1">
                    <input class="form-check-input" type="checkbox" id="showExpired" checked>
                    <label class="form-check-label" for="showExpired">Show expired</label>
                </div>

                <!-- Bulk delete -->
                <button id="bulkDeleteBtn" class="btn btn-sm btn-danger ms-1" disabled>
                    <i class="fas fa-trash"></i> <span class="d-none d-sm-inline">Bulk Delete</span>
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
@endsection

@push('css')
    <style>
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
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

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .table thead th {
            background-color: #f8fafc !important;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Soft highlight for expired rows (if you add a 'row-expired' class in JS) */
        tr.row-expired {
            background-color: #fff6db !important;
        }

        /* Compact input group icon */
        .input-group-text {
            border-right: 0;
        }

        .input-group .form-select {
            border-left: 0;
        }

        /* Disabled bulk delete looks intentional */
        #bulkDeleteBtn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        /* Stack controls on small screens */
        @media (max-width: 576px) {
            .card-header>.d-flex:last-child {
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
        $(function() {
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
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category_badge',
                        name: 'file_category',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'expires_h',
                        name: 'expiration_date',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'added_h',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    },
                ],
                order: [
                    [4, 'desc']
                ],
                pageLength: 10,
                responsive: true,
                drawCallback: function() {
                    // re-check rows we had selected
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

            // ---- SweetAlert helpers ----
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

            // Single delete (SweetAlert confirm)
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
                            const msg = xhr.status === 403 ?
                                'You do not have permission to delete this file.' :
                                (xhr.responseJSON?.message || 'Delete failed.');
                            Swal.fire({
                                icon: 'error',
                                title: 'Delete failed',
                                text: msg
                            });
                        }
                    });
                });
            });

            // Bulk delete (SweetAlert confirm)
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
                                const failedCount = res.failed ? Object.keys(res.failed)
                                    .length : 0;
                                const msg = `Deleted: ${res.deleted_count}` + (
                                    failedCount ? ` | Failed: ${failedCount}` : '');
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
                                    text: (res && res.message) ? res.message :
                                        'Unknown error'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Bulk delete failed',
                                text: xhr.responseJSON?.message ||
                                    'Unknown error'
                            });
                        }
                    });
                });
            });

        });
    </script>
@endpush
