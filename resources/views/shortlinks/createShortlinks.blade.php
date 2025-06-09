@extends('layouts.admin')

@section('title', 'Create Short Link')
@section('content-header', 'Create Trackable Short Link')

@section('content')
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form id="shortlinkForm">
                        @csrf

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slug" class="form-control">
                            <small class="text-muted">This will form the short URL (e.g.
                                <code>/go/gassaver</code>)</small>
                            <div class="text-danger small" id="error-slug"></div>

                        </div>




                        <div class="mb-3">
                            <label for="path" class="form-label">Path or Full URL</label>
                            <input type="text" name="path" id="path" class="form-control">
                            <small class="text-muted">Where should the visitor be redirected? If blank, defaults to booking
                                site homepage</small>
                        </div>

                        <div class="mb-3">
                            <label for="source" class="form-label">Source</label>
                            <input type="text" name="source" id="source" class="form-control">
                            <small class="text-muted">Where the visitor is coming from (e.g. <code>
                                    facebook, google,
                                    tiktok</code>)

                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="medium" class="form-label">Medium</label>
                            <select name="medium" id="medium" class="form-select">
                                <option value="">None</option>
                                <option value="social">Social</option>
                                <option value="email">Email</option>
                                <option value="referral">Referral</option>
                                <option value="banner">Banner</option>
                                <option value="cpc">CPC</option>
                            </select>
                            <small class="text-muted">
                                Type of link (e.g. <code>social, email, cpc</code> )
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="campaign" class="form-label">Campaign</label>
                            <input type="text" name="campaign" id="campaign" class="form-control">
                            <small class="text-muted">Short friendly name like <code>cabin_spring_ads</code></small>
                        </div>


                    </form>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('shortlinks.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button class="btn btn-primary" id="shortlinksbutton">Create Short Link</button>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#shortlinksbutton').on('click', function(e) {
                e.preventDefault();

                console.log('AJAX triggered');

                $('[id^=error-]').text('');
                let formData = $('#shortlinkForm').serialize();

                $.ajax({
                    url: "{{ route('shortlinks.store') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Shortlink created:', response);
                        localStorage.setItem('shortlinkSuccess',
                            'Shortlink created successfully!');
                        window.location = response.redirect_url;

                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const field in errors) {
                                $(`#error-${field}`).text(errors[field][0]);
                            }
                        } else {
                            alert('An unexpected error occurred.');
                            console.error(xhr.responseText);
                        }
                    }
                });
            });
        });
    </script>
@endsection
