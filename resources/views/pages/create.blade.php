@extends('layouts.admin')

@section('title')
    Create Static Page
@endsection

@section('content-header')
    Create Static Content (e.g., About Us, Contact, Terms)
@endsection
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

@section('css')
    <style>
        #quill-editor img {
            max-width: 100%;
            height: auto;
            max-height: 300px
        }
    </style>
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
                                <label class="form-label fw-semibold mb-2 d-block">Quick Actions</label>
                                <div class="d-flex gap-2">
                                    <button type="button" id="btn_grammar_check" class="btn btn-outline-secondary btn-sm">
                                        Check Grammar
                                    </button>
                                    <button type="button" id="btn_ai_marketing" class="btn btn-outline-info btn-sm">
                                        Rewrite for SEO
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            {{-- <label for="description" class="form-label fw-semibold">Page Content</label>
                            <textarea name="description" id="description" class="form-control" rows="6"
                                placeholder="Enter full page content here..."></textarea> --}}

                            <input type="hidden" name="description" id="description">
                            <div id="quill-editor" style="height: 300px;"></div>

                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Main Page Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended size: 1600x600px</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">File Attachment</label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.zip">
                            <small class="text-muted">Attach a document or downloadable file (optional)</small>
                        </div>

                        <hr class="my-4">
                        <h5 class="fw-bold">SEO and Social Media</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="metatitle" class="form-label fw-semibold">Meta Title</label>
                                <input type="text" name="metatitle" id="metatitle" class="form-control"
                                    placeholder="SEO meta title (optional)">
                            </div>
                            <div class="col-md-6">
                                <label for="canonicalurl" class="form-label fw-semibold">Canonical URL</label>
                                <input type="text" name="canonicalurl" id="canonicalurl" class="form-control"
                                    placeholder="https://example.com/page-url (optional)">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="metadescription" class="form-label fw-semibold">Meta Description</label>
                            <textarea name="metadescription" id="metadescription" class="form-control" rows="2"
                                placeholder="Short meta description for SEO (optional)"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="opengraphtitle" class="form-label fw-semibold">Social Media Title</label>
                            <input type="text" name="opengraphtitle" id="opengraphtitle" class="form-control"
                                placeholder="Title shown when shared on social media (optional)">
                        </div>

                        <div class="mb-3">
                            <label for="opengraphdescription" class="form-label fw-semibold">Social Media
                                Description</label>
                            <textarea name="opengraphdescription" id="opengraphdescription" class="form-control" rows="2"
                                placeholder="Description shown on social platforms (optional)"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Social Media Image</label>
                            <input type="file" name="opengraphimage" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended size: 1200x630px</small>
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
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>

    <script>
        let quill;

        
        document.addEventListener('DOMContentLoaded', function() {
            quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Enter full page content here...',
                modules: {
                    toolbar: [
                        [{
                            header: [1, 2, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        ['link', 'image'],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }],
                        ['clean']
                    ],
                    imageResize: {
                        displayStyles: {
                            backgroundColor: 'black',
                            border: 'none',
                            color: 'white'
                        },
                        modules: ['Resize', 'DisplaySize']
                    }

                }
            });

            quill.on('text-change', function () {
                const html = quill.root.innerHTML;
                document.querySelector('#description').value = html;
            })

            // Rewrite for SEO button
            const btnSeoRewrite = document.querySelector('#btn_ai_marketing');
            if (btnSeoRewrite) {
                btnSeoRewrite.addEventListener('click', async function() {
                    const titleInput = document.querySelector('#title');
                    const originalTitle = titleInput.value.trim();
                    const originalContent = quill.getText().trim();

                    if (!originalTitle || !originalContent) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Fields',
                            text: 'Please enter a page title and content before rewriting.',
                        });
                        return;
                    }

                    btnSeoRewrite.disabled = true;
                    quill.setText('Rewriting with AI... please wait');

                    try {
                        const response = await fetch('{{ route('ai.article.rewrite') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                title: originalTitle,
                                description: originalContent,
                                type: 'page'
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            if (data.title) titleInput.value = data.title;
                            if (data.description) {
                                quill.root.innerHTML = data.description;
                            } else {
                                quill.root.innerHTML = originalContent;
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'AI Rewrite Failed',
                                text: data.message ||
                                    'The AI server could not process the content.',
                            });
                            quill.root.innerHTML = originalContent;
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Could not connect to the AI server.',
                        });
                        quill.root.innerHTML = originalContent;
                    } finally {
                        btnSeoRewrite.disabled = false;
                    }
                });
            }

            // Grammar correction (optional - needs to be adjusted)
            const btnGrammar = document.querySelector('#btn_grammar_check');
            if (btnGrammar) {
                btnGrammar.addEventListener('click', async function() {
                    const titleInput = document.querySelector('#title');
                    const originalTitle = titleInput.value.trim();
                    const plainText = quill.getText().trim();

                    if (!originalTitle || !plainText) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Fields',
                            text: 'Please enter both title and content.',
                        });
                        return;
                    }

                    btnGrammar.disabled = true;
                    quill.setText('Checking grammar... please wait');

                    try {
                        const response = await fetch('{{ route('ai.grammar') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                question: originalTitle,
                                answer: plainText
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            if (data.question) titleInput.value = data.question;
                            if (data.answer) quill.root.innerHTML = data.answer;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Grammar Correction Failed',
                                text: data.message || 'Could not process the grammar check.',
                            });
                            quill.root.innerHTML = plainText;
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Could not connect to the AI server.',
                        });
                        quill.root.innerHTML = plainText;
                    } finally {
                        btnGrammar.disabled = false;
                    }
                });
            }
        });
    </script>
@endsection
