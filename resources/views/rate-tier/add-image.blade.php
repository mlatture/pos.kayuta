@extends('layouts.admin')

@section('title', 'Upload Images Rate Tier')
@section('content-header', 'Upload Images: ' . $rate_tiers->tier )

@section('content')
    @if (session('success'))
        <script>
            window.onload = function() {
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            }
        </script>
    @endif
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <div class="container">

                @if (!empty($rate_tiers->images))
                    <div class="row mt-4">
                        @php
                            $images = is_string($rate_tiers->images)
                                ? json_decode($rate_tiers->images, true)
                                : $rate_tiers->images;
                        @endphp

                        @foreach ($images as $filename)
                            <div class="col-md-3 mb-3 position-relative">
                                <div class="card shadow-sm">
                                    <!-- Delete button -->
                                    <button
                                        class="btn btn-sm btn-danger rounded-circle delete-image-btn position-absolute top-0 end-0 m-1"
                                        data-filename="{{ $filename }}" data-rate_tiers="{{ $rate_tiers->id }}"
                                        title="Delete Image">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>



                                    <!-- Image display -->
                                    <img src="{{ asset('storage/rate_tiers/' . $filename) }}"
                                        onerror="this.onerror=null; this.src='https://www.cams-it.com/wp-content/uploads/2015/05/default-placeholder-300x200.png'"
                                        class="card-img-top" alt="Site Image" style="max-height: 200px; object-fit: cover;">

                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No images uploaded for this site.</p>
                @endif


                <form method="POST" action="{{ route('rate-tier.upload.images', $rate_tiers->id) }}"
                    enctype="multipart/form-data">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add image input
            document.getElementById('add-image').addEventListener('click', function() {
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

            // Remove image input
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-image-btn')) {
                    e.target.closest('.input-group').remove();
                }
            });

            // SweetAlert delete confirmation
            document.querySelectorAll('.delete-image-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const filename = this.dataset.filename;
                    const rate_tiersId = this.dataset.rate_tiers;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This image will be permanently deleted.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/admin/rate_tiers/${rate_tiersId}/images/${filename}`,
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                        .attr('content')
                                },
                                success: function(data) {
                                    if (data.success) {
                                        Swal.fire('Deleted!', data.message,
                                            'success');
                                        button.closest('.col-md-3').remove();
                                    } else {
                                        Swal.fire('Error', data.message ||
                                            'Something went wrong.', 'error'
                                        );
                                    }
                                },
                                error: function() {
                                    Swal.fire('Error',
                                        'Failed to delete image.', 'error');
                                }
                            });

                        }
                    });
                });
            });


        });
    </script>


@endsection
