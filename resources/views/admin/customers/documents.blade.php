@extends('layouts.admin')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h4 class="mb-0">Documents — {{ $user->name ?? $user->f_name . ' ' . $user->l_name }}</h4>

            {{-- <div class="d-flex align-items-center gap-2">
                <div class="form-check me-2">
                    <input class="form-check-input" type="checkbox" id="showExpired" checked>
                    <label class="form-check-label" for="showExpired">Show expired</label>
                </div>
                <select id="categoryFilter" class="form-select form-select-sm" style="min-width: 200px">
                    <option value="">All categories</option>
                </select>
            </div> --}}
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="documentsTable" style="width:100%">
                <thead class="table-light sticky-top">
                    <tr>
                        <th style="min-width: 280px">Name</th>
                        <th>Category</th>
                        <th>Expires</th>
                        <th>Added</th>
                        <th class="text-end" style="min-width: 220px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($files as $f)
                        @php
                            $isExpired = $f->expiration_date && \Carbon\Carbon::parse($f->expiration_date)->isPast();
                            $category = str_replace('_', ' ', $f->file_category);
                            $openUrl = asset('storage/' . $f->file_path); // public URL (fixes public_path issue)
                            $added = \Carbon\Carbon::parse($f->created_at)->toFormattedDateString(); // DB has DATE only
                            $expires = $f->expiration_date
                                ? \Carbon\Carbon::parse($f->expiration_date)->toDateString()
                                : null;
                            // Category badge class
                            $catClassMap = [
                                'contracts' => 'bg-primary',
                                'renewals' => 'bg-info text-dark',
                                'non renewals' => 'bg-secondary',
                                'waivers' => 'bg-warning text-dark',
                                'vaccinations' => 'bg-success',
                                'ids' => 'bg-dark',
                            ];
                            $badgeClass = $catClassMap[strtolower($category)] ?? 'bg-light text-dark';
                        @endphp
                        <tr data-expired="{{ $isExpired ? '1' : '0' }}">
                            <td class="text-truncate" style="max-width: 420px" title="{{ $f->name }}">
                                <i class="fas fa-file-alt me-2"></i>{{ $f->name }}
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ ucwords($category) }}</span>
                            </td>
                            <td>
                                @if ($isExpired)
                                    <span class="badge bg-warning text-dark">Expired {{ $expires }}</span>
                                @elseif($expires)
                                    <span class="text-muted">{{ $expires }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $added }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ $openUrl }}" target="_blank"
                                        title="Open">
                                        <i class="fas fa-up-right-from-square"></i> Open
                                    </a>
                                   
                                    @php $protected = in_array($f->file_category, ['contracts','renewals','non_renewals']); @endphp
                                    @if (!$protected || auth()->user()->hasPermission('Delete Contracts'))
                                        <form class="d-inline" method="POST" action="{{ route('file.destroy', $f) }}"
                                            onsubmit="return confirm('Delete this file? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <div>No documents found.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $files->links() }}
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

        .text-truncate {
            max-width: 420px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dataTables_wrapper .dt-top-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: .75rem;
        }
    </style>
@endpush

@push('js')
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
@endpush
