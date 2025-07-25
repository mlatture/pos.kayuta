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
                    <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
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

                        <div class="form-group mt-4 d-flex gap-2">
                            <button type="button" id="btn_grammar_check" class="btn btn-outline-secondary">
                                Check Grammar
                            </button>
                            <button type="button" id="btn_ai_marketing" class="btn btn-outline-info">
                                Rewrite for SEO
                            </button>
                        </div>


                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1">Thumbnail</label>
                                <div class="d-flex align-items-center">
                                    <label for="thumbnail"
                                        class="d-inline-block border border-secondary rounded text-center p-2"
                                        style="cursor: pointer; width: 120px; height: 120px; object-fit: cover; border-style: dashed;">
                                        <img id="thumbnailPreview" src="https://placehold.co/500" alt="Preview"
                                            class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;">
                                    </label>
                                    <input type="file" name="thumbnail" id="thumbnail" class="d-none" accept="image/*">
                                </div>
                                <small class="text-muted">Click the box to upload. Ideal ratio 1:1 (e.g., 500x500px)</small>
                            </div>
                            <div class="col-md-6 d-flex align-items-center pt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status" name="status"
                                        value="1">
                                    <label class="form-check-label ms-2" for="status">Status</label>
                                </div>
                            </div>


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
                                    placeholder="https://example.com/blog-title">
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
                                placeholder="Social media title (optional)">
                        </div>

                        <div class="mb-3">
                            <label for="opengraphdescription" class="form-label fw-semibold">Social Media
                                Description</label>
                            <textarea name="opengraphdescription" id="opengraphdescription" class="form-control" rows="2"
                                placeholder="Social media description (optional)"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Social Media Image</label>
                            <input type="file" name="opengraphimage" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended size: 1200x630px</small>
                           
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
                            const questionInput = document.querySelector('#title');
                            const questionText = questionInput.value.trim();
                            const answerText = ckeditorInstance.getData().replace(/<[^>]+>/g, '')
                                .trim();

                            if (!questionText || !answerText) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Missing Fields',
                                    text: 'Sure! Please provide me with the FAQ content that you`d like me to rewrite.',
                                });
                                return;
                            }

                            btnSeoRewrite.disabled = true;
                            ckeditorInstance.setData('<em>Rewriting with AI... please wait</em>');

                            try {
                                const response = await fetch('{{ route('ai.article.rewrite') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        title: questionText,
                                        description: answerText,
                                        type: 'blogs',

                                    })
                                });

                                const data = await response.json();

                                if (data.success) {
                                    if (data.title) {
                                        questionInput.value = data.title;
                                    }
                                    if (data.description) {
                                        ckeditorInstance.setData(data.description);
                                    } else {
                                        ckeditorInstance.setData(answerText);
                                    }
                                } else {
                                    alert(data.message || 'Failed to rewrite.');
                                    ckeditorInstance.setData(answerText);
                                }

                            } catch (error) {
                                alert('Error connecting to AI server.');
                                ckeditorInstance.setData(answerText);
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

            const thumbnailInput = document.getElementById('thumbnail');
            const preview = document.getElementById('thumbnailPreview');

            thumbnailInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
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
