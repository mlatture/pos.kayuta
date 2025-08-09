@extends('layouts.admin')

@section('content')
    <div class="container-fluid mt-4">
        <h4 class="mb-4 text-primary">📄 Contracts</h4>

        @if (session('error'))
            <div class="alert alert-warning">{{ session('error') }}</div>
        @endif

        @if ($rates->isEmpty())
            <div class="alert alert-info">
                No contract templates found for this customer.
            </div>
        @else
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Customer Name:</strong> {{ $user->f_name }} {{ $user->l_name }}<br>
                        <strong>Customer Email:</strong> {{ $user->email }}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-secondary">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Document Name</th>
                                    <th>Actions</th>
                                    <th>Downloadable Files</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rates as $index => $rate)
                                    @php

                                        $rowFile = public_path('storage/' . $fileName);
                                        $rowTemplate = public_path('storage/' . $template);
                                        $basename = pathinfo($fileName, PATHINFO_BASENAME);
                                    @endphp
                                    <tr class="text-center">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $rate->rate_name }}</td>
                                        <td>
                                            {{-- Show the filename (no extension) --}}
                                            {{ pathinfo($fileName, PATHINFO_FILENAME) }}

                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary me-2"
                                                    data-bs-toggle="modal" data-bs-target="#previewModal"
                                                    data-src="{{ $rowFile }}"
                                                    data-title="{{ $rate->rate_name }} — {{ $basename }}">
                                                    📄 Preview
                                                </button>
                                                <a href="{{ $rowFile }}" download
                                                    class="btn btn-sm btn-outline-secondary">⬇️ Download</a>

                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <a href="{{ $rowTemplate }}" target="_blank"
                                                class="btn btn-sm btn-outline-secondary">
                                                ⬇️ Download Template
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">⬅️ Back to Renewals</a>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    {{-- Loading state --}}
                    <div id="previewLoading" class="d-flex align-items-center justify-content-center py-5">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <span class="ms-2">Loading preview…</span>
                    </div>

                    {{-- Frame container --}}
                    <div id="previewFrameWrap" class="ratio ratio-16x9 d-none">
                        <iframe id="previewFrame" title="Contract preview" loading="lazy" style="border:0;"
                            allow="fullscreen"></iframe>
                    </div>

                    {{-- Fallback notice --}}
                    <div id="previewUnsupported" class="alert alert-warning m-3 d-none">
                        This file cannot be previewed. Please download to view it.
                    </div>
                </div>

                <div class="modal-footer">
                    <a id="previewDownload" href="#" class="btn btn-primary" download>⬇️ Download</a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            var $modal = $('#previewModal');
            var $title = $('#previewModalLabel');
            var $wrap = $('#previewFrameWrap');
            var $frame = $('#previewFrame');
            var $loading = $('#previewLoading');
            var $unsupported = $('#previewUnsupported');
            var $download = $('#previewDownload');

            function resetModal() {
                $frame.attr('src', 'about:blank');
                $wrap.addClass('d-none');
                $unsupported.addClass('d-none');
                $loading.removeClass('d-none');
            }

            $modal.on('hidden.bs.modal', function() {
                resetModal();
            });

            $modal.on('show.bs.modal', function(e) {
                resetModal();

                var $btn = $(e.relatedTarget);
                var fileUrl = $btn.data('src'); // MUST be a URL (asset(...)), not public_path
                var title = $btn.data('title') || 'Preview';

                $title.text(title);
                $download.attr('href', fileUrl);

                var lower = (fileUrl || '').toLowerCase();
                var isPdf = /\.pdf(\?|$)/i.test(lower);
                var isDocx = /\.docx(\?|$)/i.test(lower);

                // When iframe loads, show it
                $frame.off('load').on('load', function() {
                    $loading.addClass('d-none');
                    $wrap.removeClass('d-none');
                });

                if (isPdf) {
                    // Native PDF preview
                    $frame.attr('src', fileUrl + (fileUrl.includes('?') ? '&' : '?') + 'inline=1');
                } else if (isDocx) {
                    // Google Docs Viewer requires a publicly accessible URL
                    var gdocs = 'https://docs.google.com/viewer?embedded=true&url=' + encodeURIComponent(
                        fileUrl);
                    $frame.attr('src', gdocs);

                    // Fallback if viewer is blocked / never finishes
                    setTimeout(function() {
                        // If still loading after 6s and iframe didn't fire load, show fallback
                        if (!$wrap.is(':visible')) {
                            $loading.addClass('d-none');
                            $unsupported.removeClass('d-none');
                        }
                    }, 6000);
                } else {
                    // Unsupported types
                    $loading.addClass('d-none');
                    $unsupported.removeClass('d-none');
                }
            });
        });
    </script>
@endpush
