@extends('layouts.admin')

@section('title')
    Create Static Page
@endsection

@section('content-header')
    Create Static Content (e.g., About Us, Contact, Terms)
@endsection

@section('content')
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('pages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="type" value="page">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label fw-semibold">Page Title</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    placeholder="Enter static page title (e.g., About Us)" required>
                            </div>

                            <div class="col-md-6">
                                <label for="metatitle" class="form-label fw-semibold">Meta Title</label>
                                <input type="text" name="metatitle" id="metatitle" class="form-control"
                                    placeholder="SEO meta title (optional)">
                            </div>
                        </div>







                        <div class="mb-3">
                            <label for="metadescription" class="form-label fw-semibold">Meta Description</label>
                            <textarea name="metadescription" id="metadescription" class="form-control" rows="2"
                                placeholder="Short meta description for SEO (optional)"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="canonicalurl" class="form-label fw-semibold">Canonical URL</label>
                            <input type="text" name="canonicalurl" id="canonicalurl" class="form-control"
                                placeholder="https://example.com/page-url (optional)">
                        </div>

                        <div class="mb-3">
                            <label for="opengraphtitle" class="form-label fw-semibold">Open Graph Title</label>
                            <input type="text" name="opengraphtitle" id="opengraphtitle" class="form-control"
                                placeholder="Title shown when shared on social media (optional)">
                        </div>

                        <div class="mb-3">
                            <label for="opengraphdescription" class="form-label fw-semibold">Open Graph Description</label>
                            <textarea name="opengraphdescription" id="opengraphdescription" class="form-control" rows="2"
                                placeholder="Description shown when shared (optional)"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="schema_code_pasting" class="form-label fw-semibold">Schema Code (JSON-LD)</label>
                            <textarea name="schema_code_pasting" id="schema_code_pasting" class="form-control" rows="3"
                                placeholder="Paste your schema.org JSON-LD code here (optional)"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Page Content</label>
                            <textarea name="description" id="description" class="form-control" rows="5"
                                placeholder="Enter full page content here..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Open Graph Image</label>
                            <input type="file" name="opengraphimage" class="form-control" accept="image/*">
                            <small class="text-muted">Image shown on social shares (optional)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Main Page Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Hero or featured image (optional)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">File Attachment</label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.zip">
                            <small class="text-muted">Attach a document or downloadable file (optional)</small>
                        </div>

                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                checked>
                            <label class="form-check-label ms-2" for="status">Publish Page</label>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="reset" class="btn btn-light me-2">Reset</button>
                            <button type="submit" class="btn btn-primary">Save Page</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create(document.querySelector('#description'))
                .catch(error => {
                    console.error('CKEditor error:', error);
                });


        });
    </script>
@endsection
