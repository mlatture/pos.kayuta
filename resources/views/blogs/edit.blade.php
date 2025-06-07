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
                                value="{{ old('title', preg_replace('/["\']/', '', $blog->title)) }}" </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="5">{{ old('description', $blog->description) }}</textarea>
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
                                        <label for="thumbnail" class="border border-secondary rounded text-center p-2"
                                            style="cursor: pointer; width: 120px; height: 120px; border-style: dashed;">
                                            <img id="thumbnailPreview"
                                                src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : 'https://placehold.co/500' }}"
                                                class="img-fluid rounded"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </label>
                                        <input type="file" name="thumbnail" id="thumbnail" class="d-none"
                                            accept="image/*">
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

                            <hr class="my-4">
                            <h5 class="fw-bold">SEO and Social Media</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="metatitle" class="form-label fw-semibold">Meta Title</label>
                                    <input type="text" name="metatitle" id="metatitle" class="form-control"
                                        value="{{ old('metatitle', $blog->metatitle ?? '') }}"
                                        placeholder="SEO meta title (optional)">
                                </div>
                                <div class="col-md-6">
                                    <label for="canonicalurl" class="form-label fw-semibold">Canonical URL</label>
                                    <input type="text" name="canonicalurl" id="canonicalurl" class="form-control"
                                        value="{{ old('canonicalurl', $blog->canonicalurl ?? '') }}"
                                        placeholder="https://example.com/blog-title">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="metadescription" class="form-label fw-semibold">Meta Description</label>
                                <textarea name="metadescription" id="metadescription" class="form-control" rows="2"
                                    placeholder="Short meta description for SEO (optional)">{{ old('metadescription', $blog->metadescription ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="opengraphtitle" class="form-label fw-semibold">Social Media Title</label>
                                <input type="text" name="opengraphtitle" id="opengraphtitle" class="form-control"
                                    value="{{ old('opengraphtitle', $blog->opengraphtitle ?? '') }}"
                                    placeholder="Social media title (optional)">
                            </div>

                            <div class="mb-3">
                                <label for="opengraphdescription" class="form-label fw-semibold">Social Media
                                    Description</label>
                                <textarea name="opengraphdescription" id="opengraphdescription" class="form-control" rows="2"
                                    placeholder="Social media description (optional)">{{ old('opengraphdescription', $blog->opengraphdescription ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Social Media Image</label>
                                <input type="file" name="opengraphimage" class="form-control" accept="image/*">
                                <small class="text-muted">Recommended size: 1200x630px</small>
                                @if (!empty($blog->opengraphimage))
                                    <small class="d-block mt-1 text-muted">Current: {{ $blog->opengraphimage }}</small>
                                @endif
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
                reader.onload = e => preview.src = e.target.result;
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
