@extends('layouts.admin')

@section('title', 'Upload Images Site')
@section('content-header', 'Upload Images Site')

@section('content')
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <div class="container">

                <form method="POST" action="{{ route('sites.upload.images', $site->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="card mb-3 border border-dashed p-3">
                        <h5 class="mb-3">Image Upload Fields</h5>
                        <div id="image-upload-wrapper">
                            <div class="input-group mb-3">
                                <input type="file" name="images[]" class="form-control">
                            </div>
                        </div>

                        <button type="button" id="add-image" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-plus"></i> Add More Images
                        </button>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">Upload Images</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    document.getElementById('add-image').addEventListener('click', function () {
        const wrapper = document.getElementById('image-upload-wrapper');
        const inputGroup = document.createElement('div');
        inputGroup.classList.add('input-group', 'mb-3');

        inputGroup.innerHTML = `
            <input type="file" name="images[]" class="form-control">
            <button class="btn btn-outline-danger remove-image-btn" type="button">
                <i class="fa fa-times"></i>
            </button>
        `;

        wrapper.appendChild(inputGroup);
    });

    // Remove input field
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-image-btn')) {
            const btn = e.target.closest('.remove-image-btn');
            const inputGroup = btn.closest('.input-group');
            inputGroup.remove();
        }
    });
</script>
@endsection

