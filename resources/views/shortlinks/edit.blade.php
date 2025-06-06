@extends('layouts.admin')

@section('title', 'Edit Shortlink')
@section('content-header', 'Edit Shortlink')

@section('content')
<div class="row animated fadeInUp">
    <div class="col-12 col-lg-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('shortlinks.update', $shortlink->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h4 class="mb-4">
                        <i class="fas fa-edit me-1 text-primary"></i>
                        Edit Shortlink <code>{{ $shortlink->slug }}</code>
                    </h4>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="slug" class="form-control"
                               value="{{ old('slug', $shortlink->slug) }}" required>
                        <div class="text-danger small">@error('slug') {{ $message }} @enderror</div>
                    </div>

                    <div class="mb-3">
                        <label for="path" class="form-label">Path or Full URL</label>
                        <input type="text" name="path" id="path" class="form-control"
                               value="{{ old('path', $shortlink->path) }}">
                        <div class="text-danger small">@error('path') {{ $message }} @enderror</div>
                    </div>

                    <div class="mb-3">
                        <label for="source" class="form-label">Source</label>
                        <input type="text" name="source" id="source" class="form-control"
                               value="{{ old('source', $shortlink->source) }}">
                    </div>

                    <div class="mb-3">
                        <label for="medium" class="form-label">Medium</label>
                        <select name="medium" id="medium" class="form-select">
                            <option value="">None</option>
                            @foreach(['social','email','referral','banner','cpc'] as $m)
                                <option value="{{ $m }}" @selected(old('medium', $shortlink->medium) === $m)>
                                    {{ ucfirst($m) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="campaign" class="form-label">Campaign</label>
                        <input type="text" name="campaign" id="campaign" class="form-control"
                               value="{{ old('campaign', $shortlink->campaign) }}">
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('shortlinks.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Shortlink</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
