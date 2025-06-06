@extends('layouts.admin')

@section('title')
    Edit Page
@endsection

@section('content-header')
    Edit Static Content Page
@endsection

@section('content')
<div class="row animated fadeInUp">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="type" value="page">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label fw-semibold">Page Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title', $page->title) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="metatitle" class="form-label fw-semibold">Meta Title</label>
                            <input type="text" name="metatitle" id="metatitle" class="form-control"
                                value="{{ old('metatitle', $page->metatitle) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="metadescription" class="form-label fw-semibold">Meta Description</label>
                        <textarea name="metadescription" id="metadescription" class="form-control" rows="2">{{ old('metadescription', $page->metadescription) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="canonicalurl" class="form-label fw-semibold">Canonical URL</label>
                        <input type="text" name="canonicalurl" id="canonicalurl" class="form-control"
                            value="{{ old('canonicalurl', $page->canonicalurl) }}">
                    </div>

                    <div class="mb-3">
                        <label for="opengraphtitle" class="form-label fw-semibold">Open Graph Title</label>
                        <input type="text" name="opengraphtitle" id="opengraphtitle" class="form-control"
                            value="{{ old('opengraphtitle', $page->opengraphtitle) }}">
                    </div>

                    <div class="mb-3">
                        <label for="opengraphdescription" class="form-label fw-semibold">Open Graph Description</label>
                        <textarea name="opengraphdescription" id="opengraphdescription" class="form-control" rows="2">{{ old('opengraphdescription', $page->opengraphdescription) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="schema_code_pasting" class="form-label fw-semibold">Schema Code</label>
                        <textarea name="schema_code_pasting" id="schema_code_pasting" class="form-control" rows="3">{{ old('schema_code_pasting', $page->schema_code_pasting) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Page Content</label>
                        <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description', $page->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Open Graph Image</label>
                        <input type="file" name="opengraphimage" class="form-control" accept="image/*">
                        @if($page->opengraphimage)
                            <small class="d-block mt-1 text-muted">Current: {{ $page->opengraphimage }}</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Main Page Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        @if($page->image)
                            <small class="d-block mt-1 text-muted">Current: {{ $page->image }}</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Attachment</label>
                        <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.zip">
                        @if($page->attachment)
                            <small class="d-block mt-1 text-muted">Current: {{ $page->attachment }}</small>
                        @endif
                    </div>

                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                            {{ $page->status ? 'checked' : '' }}>
                        <label class="form-check-label ms-2" for="status">Publish Page</label>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('pages.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Page</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error('CKEditor error:', error);
            });
    </script>
@endsection
