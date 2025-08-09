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
                                        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                        $basename = pathinfo($fileName, PATHINFO_BASENAME);

                                        $contractUrl = asset('public/storage/' . str_replace(' ', '%20', $fileName));
                                        $templateUrl = asset('public/storage/' . str_replace(' ', '%20', $template));

                                        $previewSrc = null;
                                        if (in_array($extension, ['doc', 'docx'])) {
                                            $previewSrc =
                                                'https://docs.google.com/gview?embedded=1&url=' .
                                                urlencode($contractUrl);
                                        } elseif ($extension === 'pdf') {
                                            $previewSrc = $contractUrl;
                                        }
                                    @endphp

                                    <tr class="text-center">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $rate->rate_name }}</td>
                                        @if ($fileName)
                                            <td>
                                                {{ pathinfo($fileName, PATHINFO_FILENAME) }}

                                                <div class="mt-2">
                                                    @if ($previewSrc)
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-2"
                                                            data-bs-toggle="modal" data-bs-target="#previewModal"
                                                            data-src="{{ $previewSrc }}"
                                                            data-title="{{ $rate->rate_name }} — {{ $basename }}">
                                                            📄 Preview
                                                        </button>
                                                    @endif

                                                    <a href="{{ $contractUrl }}" download="{{ $basename }}"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        ⬇️ Download
                                                    </a>
                                                </div>
                                            </td>
                                        @else
                                            <td>No Signed Docs yet</td>
                                        @endif


                                        <td class="text-center">
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ $templateUrl }}"
                                                download>
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

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="max-width:90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height:80vh;">
                    <iframe id="previewFrame" src="" width="100%" height="100%" style="border:none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var previewModal = document.getElementById('previewModal');
            previewModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var src = button.getAttribute('data-src');
                var title = button.getAttribute('data-title');

                var iframe = document.getElementById('previewFrame');
                var modalTitle = document.getElementById('previewModalLabel');

                iframe.src = src;
                modalTitle.textContent = title;
            });

            previewModal.addEventListener('hidden.bs.modal', function() {
                document.getElementById('previewFrame').src = "";
            });
        });
    </script>
@endsection
