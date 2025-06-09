@extends('layouts.admin')

@section('title', 'Shortlink Details')
@section('content-header', 'Shortlink Details')

@section('content')
@push('js')
    <script>
        $(document).ready(function () {
            const successMessage = localStorage.getItem('shortlinkSuccess');
            if (successMessage) {
                $.toast({
                    heading: 'Success',
                    text: successMessage,
                    icon: 'success',
                    position: 'bottom-left',
                    hideAfter: 3000,
                    stack: 3
                })

                setTimeout(() => {
                    localStorage.removeItem('shortlinkSuccess');
                }, 3000);
            }
        })
    </script>
@endpush   



<div class="row animated fadeInUp">
    <div class="col-12 col-lg-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

             
                <h4 class="mb-4">
                    <i class="fas fa-link me-1 text-primary"></i> 
                    Shortlink: <code>{{ $shortlink->slug }}</code>
                </h4>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item">
                        <strong>Redirect To:</strong><br>
                        <a href="{{ $shortlink->fullredirecturl }}" target="_blank" class="text-decoration-underline text-muted">
                            {{ $shortlink->fullredirecturl }}
                        </a>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Short URL:</strong><br>
                            <a href="{{ $shortUrl }}" target="_blank" class="text-decoration-underline">{{ $shortUrl }}</a>
                        </div>
                        {{-- Optional Copy Button --}}
                        {{-- 
                        <button class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ $shortUrl }}')">
                            Copy
                        </button>
                        --}}
                    </li>

                    <li class="list-group-item">
                        <strong>Source:</strong>
                        <span class="text-muted">{{ $shortlink->source ?? '-' }}</span>
                    </li>

                    <li class="list-group-item">
                        <strong>Medium:</strong>
                        <span class="text-muted">{{ $shortlink->medium ?? '-' }}</span>
                    </li>

                    <li class="list-group-item">
                        <strong>Campaign:</strong>
                        <span class="text-muted">{{ $shortlink->campaign ?? '-' }}</span>
                    </li>

                    <li class="list-group-item">
                        <strong>Clicks:</strong>
                        <span class="badge bg-primary">{{ $shortlink->clicks }}</span>
                    </li>

                    <li class="list-group-item">
                        <strong>QR Code:</strong>
                        <div class="mt-2">
                            <img src="data:image/png;base64,{{ base64_encode($qr) }}" alt="QR Code"
                                 class="img-fluid rounded shadow-sm border" style="max-width: 200px;">
                        </div>
                    </li>
                </ul>

                <div class="text-end mt-3">
                    <a href="{{ route('shortlinks.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
