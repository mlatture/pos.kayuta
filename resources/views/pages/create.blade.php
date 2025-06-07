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
                            <label for="description" class="form-label fw-semibold">Page Content</label>
                            <textarea name="description" id="description" class="form-control" rows="6"
                                placeholder="Enter full page content here..."></textarea>
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
                            <input class="form-check-input" type="checkbox" id="status" name="status"
                                value="1" checked>
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
        let ckeditorInstance;

        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create(document.querySelector('#description'))


                .then(editor => {
                    function debounce(func, delay) {
                        let timeout;
                        return function() {
                            clearTimeout(timeout);
                            timeout = setTimeout(func, delay);
                        };
                    }


                    ckeditorInstance = editor;

                    const btnGrammar = document.querySelector('#btn_grammar_check');
                    const btnSeoRewrite = document.querySelector('#btn_ai_marketing');

                    // Rewrite for SEO button
                    if (btnSeoRewrite) {
                        btnSeoRewrite.addEventListener('click', async function() {
                            const titleInput = document.querySelector('#title');
                            const originalTitle = titleInput.value.trim();
                            const originalContent = ckeditorInstance.getData().replace(/<[^>]+>/g,
                                '').trim();

                            if (!originalTitle || !originalContent) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Missing Fields',
                                    text: 'Please enter a page title and content before rewriting.',
                                });
                                return;
                            }

                            btnSeoRewrite.disabled = true;
                            ckeditorInstance.setData(
                                '<p><em>Rewriting with AI... please wait</em></p>');

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
                                        ckeditorInstance.setData(data.description);
                                    } else {
                                        ckeditorInstance.setData(originalContent);
                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'AI Rewrite Failed',
                                        text: data.message ||
                                            'The AI server could not process the content.',
                                    });
                                    ckeditorInstance.setData(originalContent);
                                }
                            } catch (error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Server Error',
                                    text: 'Could not connect to the AI server.',
                                });
                                ckeditorInstance.setData(originalContent);
                            } finally {
                                btnSeoRewrite.disabled = false;
                            }
                        });
                    }


                    // Grammar correction button
                    if (btnGrammar) {
                        btnGrammar.addEventListener('click', async function() {
                            const questionInput = document.querySelector('#question');
                            const questionText = questionInput.value.trim();
                            const answerText = ckeditorInstance.getData().replace(/<[^>]+>/g, '')
                                .trim();

                            if (!questionText || !answerText) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Missing Fields',
                                    text: 'Question and Answer fields must not be empty.',
                                });
                                return;
                            }

                            btnGrammar.disabled = true;
                            ckeditorInstance.setData('<em>Checking grammar... please wait</em>');

                            try {
                                const response = await fetch('{{ route('ai.grammar') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        question: questionText,
                                        answer: answerText
                                    })
                                });

                                const data = await response.json();

                                if (data.success) {
                                    if (data.question) {
                                        questionInput.value = data.question;
                                    }
                                    if (data.answer) {
                                        ckeditorInstance.setData(data.answer);
                                    }
                                } else {
                                    alert(data.message || 'Failed to correct grammar.');
                                    ckeditorInstance.setData(answerText);
                                }
                            } catch (error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Server error',
                                    text: 'Error connection to server'
                                });
                                ckeditorInstance.setData(answerText);
                            } finally {
                                btnGrammar.disabled = false;
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                });

        });
    </script>
@endsection
