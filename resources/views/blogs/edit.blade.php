@extends('layouts.admin')

@section('title', 'Edit Blog')
@section('content-header', 'Edit Blog')

@section('content')
<div class="row animated fadeInUp">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="type" value="blog">

                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" id="title" class="form-control"
                            value="{{ old('title', $blog->title) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="5">{{ old('description', $blog->description) }}</textarea>
                    </div>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1">Thumbnail</label>
                            <div class="d-flex align-items-center">
                                <label for="thumbnail" class="border border-secondary rounded text-center p-2"
                                    style="cursor: pointer; width: 120px; height: 120px; border-style: dashed;">
                                    <img id="thumbnailPreview"
                                        src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : 'https://placehold.co/500' }}"
                                        class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;">
                                </label>
                                <input type="file" name="thumbnail" id="thumbnail" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <div class="col-md-6 pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status" name="status"
                                    value="1" {{ $blog->status ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="status">Status</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('pages.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Blog</button>
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
    ClassicEditor.create(document.querySelector('#description')).catch(console.error);

    const thumbnailInput = document.getElementById('thumbnail');
    const preview = document.getElementById('thumbnailPreview');

    thumbnailInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
