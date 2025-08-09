@extends('layouts.admin')

@section('content')
<div class="container-fluid mt-4">
    <h4 class="mb-4 text-primary">
        📄 Contracts
        <div class="mt-4">
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
                        // Keep $fileName / $template usage as-is (your requirement)
                        $encodedContractPath = $encodeSegments($fileName);
                        $encodedTemplatePath = $encodeSegments($template);

                        // Build public URLs under public_html/public/storage/...
                        $contractUrl = asset('storage/' . $encodedContractPath);   // <-- CHANGED to 'storage'
                        $templateUrl = asset('storage/' . $encodedTemplatePath);   // <-- CHANGED to 'storage'

                        $ext        = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $basename   = pathinfo($fileName, PATHINFO_BASENAME);
                        $dispName   = pathinfo($fileName, PATHINFO_FILENAME);
                        $templateBase = pathinfo($template, PATHINFO_BASENAME);

                        // Follow your preview pattern exactly (but using 'storage')
                        // was: $templateUrl = asset('shared_storage/' . $fileName);
                        // now: use the actual contract URL (public storage path)
                        $isDocx     = \Illuminate\Support\Str::endsWith(strtolower($fileName), '.docx');
                        $previewUrl = $isDocx
                            ? 'https://docs.google.com/viewer?embedded=true&url=' . urlencode($contractUrl)
                            : $contractUrl;

                        $docs[] = [
                            'title'        => $rate->rate_name . ' — ' . $basename,
                            'contractUrl'  => $contractUrl,
                            'templateUrl'  => $templateUrl,
                            'previewUrl'   => $previewUrl,
                            'ext'          => $ext,
                            'displayName'  => $dispName,
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
                                <iframe id="previewFrame" class="w-100 h-100" style="border:none;"></iframe>
                            </div>

                            <div class="card-footer bg-white d-flex gap-2">
                                <a id="downloadContractBtn" href="#" class="btn btn-outline-secondary btn-sm" download>
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
                                @foreach($docs as $i => $d)
                                    <a href="{{ $d['templateUrl'] }}" download
                                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span class="text-truncate">{{ $d['templateBase'] ?? ('Template ' . ($i+1)) }}</span>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const docs = @json($docs);

    const previewTitle        = document.getElementById('previewTitle');
    const previewFrame        = document.getElementById('previewFrame');
    const downloadContractBtn = document.getElementById('downloadContractBtn');

    function loadDoc(index) {
        const d = docs[index];
        if (!d) return;

        // Title
        previewTitle.textContent = d.title;

        // Download button
        downloadContractBtn.href = d.contractUrl;
        downloadContractBtn.setAttribute('download', d.displayName + '.' + d.ext);

        // Preview: follow your pattern (Google viewer for .docx, direct for others)
        previewFrame.src = d.previewUrl;
    }

    // Initialize with the first doc
    if (docs.length) {
        loadDoc(0);
    } else {
        previewTitle.textContent = 'Preview';
        previewFrame.src = '';
    }
});
</script>
@endsection
