@extends('layouts.admin')

@section('title','Social Connections')

@section('content')
<div class="card shadow border-0 bg-white rounded-4 overflow-hidden">
  <div class="card-header d-flex justify-content-between align-items-center"
       style="background: linear-gradient(90deg, #00b09b, #96c93d);">
    <h4 class="mb-0"><i class="bi bi-plug me-2"></i> Social Connections</h4>
    <a href="{{ route('admin.content-hub.settings',['tab'=>'overview']) }}" class="btn btn-outline-dark btn-sm">
      <i class="bi bi-gear"></i> Settings
    </a>
  </div>

  <div class="card-body">
    @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
    @error('oauth') <div class="alert alert-danger">{{ $message }}</div> @enderror

    <div class="row g-3">
      @php
        $providers = [
          ['key'=>'facebook','label'=>'Facebook / Instagram','icon'=>'bi-facebook'],
          ['key'=>'tiktok','label'=>'TikTok','icon'=>'bi-tiktok'],
          ['key'=>'pinterest','label'=>'Pinterest','icon'=>'bi-pinterest'],
          ['key'=>'google','label'=>'Google Business','icon'=>'bi-google'],
        ];
      @endphp

      @foreach($providers as $p)
        @php
          $rows = $connections->where('channel',$p['key']);
        @endphp
        <div class="col-md-6">
          <div class="border rounded-3 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="mb-0">
                <i class="bi {{ $p['icon'] }} me-2"></i>{{ $p['label'] }}
              </h5>
              <a class="btn btn-sm btn-success" href="{{ route('oauth.redirect', ['provider'=>$p['key']]) }}">
                <i class="bi bi-plus-circle"></i> Connect
              </a>
            </div>

            @forelse($rows as $conn)
              <div class="d-flex align-items-center justify-content-between border rounded-3 p-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                  @if($conn->connection_metadata['avatar'] ?? false)
                    <img src="{{ $conn->connection_metadata['avatar'] }}" alt="" width="30" height="30" class="rounded-circle">
                  @endif
                  <div>
                    <div class="fw-semibold">{{ $conn->account_name }}</div>
                    <div class="small text-muted">ID: {{ $conn->account_id }}</div>
                    <div class="small">
                      Status:
                      @if($conn->health_status === 'healthy')
                        <span class="badge text-bg-success">Healthy</span>
                      @elseif($conn->health_status === 'warning')
                        <span class="badge text-bg-warning">Warning</span>
                      @else
                        <span class="badge text-bg-danger">Error</span>
                      @endif
                      @if($conn->token_expires_at)
                        <span class="text-muted"> · Expires {{ $conn->token_expires_at->diffForHumans() }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <form method="POST" action="{{ route('admin.content-hub.connections.disconnect',$conn->id) }}">
                  @csrf @method('DELETE')
                  <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x"></i> Disconnect</button>
                </form>
              </div>
            @empty
              <div class="text-muted">No {{ $p['label'] }} accounts connected yet.</div>
            @endforelse
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
