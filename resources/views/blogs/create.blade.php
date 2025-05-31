@extends('layouts.admin')

@section('title')
    Create {{ ucfirst($type) }}
@endsection

@section('content-header')
    Create {{ ucfirst($type) }}
@endsection

@section('content')
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('pages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="type" value="{{ $type }}">

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                placeholder="Enter the title" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="5"
                                placeholder="Enter a clear and detailed description..."></textarea>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1">Thumbnail</label>
                                <div class="d-flex align-items-center">
                                    <label for="thumbnail" class="d-inline-block border border-secondary rounded text-center p-2" style="cursor: pointer; width: 120px; height: 120px; object-fit: cover; border-style: dashed;">
                                        <img id="thumbnailPreview" src="https://placehold.co/500" alt="Preview" class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;">
                                    </label>
                                    <input type="file" name="thumbnail" id="thumbnail" class="d-none" accept="image/*">
                                </div>
                                <small class="text-muted">Click the box to upload. Ideal ratio 1:1 (e.g., 500x500px)</small>
                            </div>
                            <div class="col-md-6 d-flex align-items-center pt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1">
                                    <label class="form-check-label ms-2" for="status">Status</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="reset" class="btn btn-light me-2">Reset</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
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
        document.addEventListener('DOMContentLoaded', function () {
            ClassicEditor
                .create(document.querySelector('#description'))
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                });

            const thumbnailInput = document.getElementById('thumbnail');
            const preview = document.getElementById('thumbnailPreview');

            thumbnailInput.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = '#';
                    preview.style.display = 'none';
                }
            });
        });
    </script>
@endsection
