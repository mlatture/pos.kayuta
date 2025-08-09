@extends('layouts.admin')

@section('content')
    <div class="container-fluid mt-4">
        <h4 class="mb-4 text-primary">📄 Contracts <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">⬅️ Back to Renewals</a>
            </div>
        </h4>

        @if (session('error'))
            <div class="alert alert-warning">{{ session('error') }}</div>
        @endif

        @if ($rates->isEmpty())
            <div class="alert alert-info">No contract templates found for this customer.</div>
        @else
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <strong>Customer Name:</strong> {{ $user->f_name }} {{ $user->l_name }}<br>
                        <strong>Customer Email:</strong> {{ $user->email }}
                    </div>

                    @php
                        // Helper to URL-encode each path segment (handles spaces safely)
                        $encodeSegments = function ($path) {
                            return implode('/', array_map('rawurlencode', explode('/', $path)));
                        };

                        $docs = [];
                        foreach ($rates as $index => $rate) {
                            // Per your instruction, keep $fileName / $template usage as-is
                            $encodedContractPath = $encodeSegments($fileName);
                            $encodedTemplatePath = $encodeSegments($template);

                            // Public URLs under public_html/public/storage/...
                            $contractUrl = asset('storage/' . $encodedContractPath);
                            $templateUrl = asset('storage/' . $encodedTemplatePath);

                            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $basename = pathinfo($fileName, PATHINFO_BASENAME);
                            $title = $rate->rate_name . ' — ' . $basename;
                            $dispName = pathinfo($fileName, PATHINFO_FILENAME);
                            $templateBase = pathinfo($template, PATHINFO_BASENAME);

                            $docs[] = [
                                'title' => $title,
                                'contractUrl' => $contractUrl,
                                'templateUrl' => $templateUrl,
                                'ext' => $ext,
                                'displayName' => $dispName,
                                'templateBase' => $templateBase,
                            ];
                        }
                    @endphp

                    <div class="row g-3">
                        <!-- LEFT: Preview + Actions -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white">
                                    <h5 id="previewTitle" class="mb-0">Preview</h5>
                                </div>

                                <div class="card-body" style="height:70vh;">
                                    <!-- DOCX container (HTML-rendered) -->
                                    <div id="docxContainer" class="d-none h-100" style="overflow:auto;"></div>
                                    <!-- PDF iframe -->
                                    <iframe id="previewFrame" class="w-100 h-100" style="border:none;"></iframe>
                                </div>

                                <div class="card-footer bg-white d-flex gap-2">
                                    <a id="downloadContractBtn" href="#" class="btn btn-outline-secondary btn-sm"
                                        download>
                                        ⬇️ Download Contract
                                    </a>

                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Download Templates list (direct links) -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">Download Templates</h6>
                                </div>
                                <div class="list-group list-group-flush" id="docList">
                                    @foreach ($docs as $i => $d)
                                        <a href="{{ $d['templateUrl'] }}" download
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <span class="text-truncate">{{ $templateName ?? 'Template ' . ($i + 1) }}</span>
                                            <span class="badge bg-secondary">Download</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div> <!-- /.row -->

                </div>
            </div>
        @endif


    </div>

    {{-- DOCX renderer --}}
    <script src="https://unpkg.com/docx-preview/dist/docx-preview.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const docs = @json($docs);

            const previewTitle = document.getElementById('previewTitle');
            const previewFrame = document.getElementById('previewFrame');
            const docxContainer = document.getElementById('docxContainer');
            const downloadContractBtn = document.getElementById('downloadContractBtn');
            const downloadTemplateBtn = document.getElementById('downloadTemplateBtn');

            async function renderDocx(url) {
                docxContainer.innerHTML = '';
                const res = await fetch(url, {
                    credentials: 'same-origin'
                });
                if (!res.ok) throw new Error('Failed to fetch DOCX: ' + res.status);
                const blob = await res.blob();
                await window.docx.renderAsync(blob, docxContainer, {
                    className: 'docx',
                    inWrapper: true
                });
            }

            async function loadDoc(index) {
                const d = docs[index];
                if (!d) return;

                // Title
                previewTitle.textContent = d.title;

                // Action buttons
                downloadContractBtn.href = d.contractUrl;
                downloadContractBtn.setAttribute('download', d.displayName + '.' + d.ext);
                downloadTemplateBtn.href = d.templateUrl;

                // Preview behavior
                const ext = (d.ext || '').toLowerCase();
                if (ext === 'doc' || ext === 'docx') {
                    previewFrame.classList.add('d-none');
                    docxContainer.classList.remove('d-none');
                    try {
                        await renderDocx(d.contractUrl);
                    } catch (e) {
                        console.error(e);
                        docxContainer.innerHTML =
                            '<div class="alert alert-warning">Unable to preview this document.</div>';
                    }
                } else if (ext === 'pdf') {
                    docxContainer.classList.add('d-none');
                    previewFrame.classList.remove('d-none');
                    previewFrame.src = d.contractUrl;
                } else {
                    docxContainer.classList.remove('d-none');
                    previewFrame.classList.add('d-none');
                    docxContainer.innerHTML = '<div class="alert alert-info">Preview not supported for .' +
                        ext + ' files.</div>';
                }
            }

            // Initialize with the first doc, if any
            if (docs.length) {
                loadDoc(0);
            } else {
                // Clear preview area if nothing
                document.getElementById('previewTitle').textContent = 'Preview';
                document.getElementById('previewFrame').src = '';
                document.getElementById('docxContainer').innerHTML = '';
            }
        });
    </script>
@endsection
