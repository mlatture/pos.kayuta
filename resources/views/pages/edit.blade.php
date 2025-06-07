@extends('layouts.admin')

@section('title')
    Edit Page
@endsection

@section('content-header')
    Edit Static Content Page
@endsection

@section('content')
<div class="row animated fadeInUp">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="type" value="page">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label fw-semibold">Page Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title', $page->title) }}" required>
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
                            placeholder="Enter full page content here...">{{ old('description', $page->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Main Page Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Recommended size: 1600x600px</small>
                        @if($page->image)
                            <small class="d-block mt-1 text-muted">Current: {{ $page->image }}</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Attachment</label>
                        <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.zip">
                        @if($page->attachment)
                            <small class="d-block mt-1 text-muted">Current: {{ $page->attachment }}</small>
                        @endif
                    </div>

                    <hr class="my-4">
                    <h5 class="fw-bold">SEO and Social Media</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="metatitle" class="form-label fw-semibold">Meta Title</label>
                            <input type="text" name="metatitle" id="metatitle" class="form-control"
                                value="{{ old('metatitle', $page->metatitle) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="canonicalurl" class="form-label fw-semibold">Canonical URL</label>
                            <input type="text" name="canonicalurl" id="canonicalurl" class="form-control"
                                value="{{ old('canonicalurl', $page->canonicalurl) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="metadescription" class="form-label fw-semibold">Meta Description</label>
                        <textarea name="metadescription" id="metadescription" class="form-control" rows="2">{{ old('metadescription', $page->metadescription) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="opengraphtitle" class="form-label fw-semibold">Social Media Title</label>
                        <input type="text" name="opengraphtitle" id="opengraphtitle" class="form-control"
                            value="{{ old('opengraphtitle', $page->opengraphtitle) }}">
                    </div>

                    <div class="mb-3">
                        <label for="opengraphdescription" class="form-label fw-semibold">Social Media Description</label>
                        <textarea name="opengraphdescription" id="opengraphdescription" class="form-control" rows="2">{{ old('opengraphdescription', $page->opengraphdescription) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Social Media Image</label>
                        <input type="file" name="opengraphimage" class="form-control" accept="image/*">
                        <small class="text-muted">Recommended size: 1200x630px</small>
                        @if($page->opengraphimage)
                            <small class="d-block mt-1 text-muted">Current: {{ $page->opengraphimage }}</small>
                        @endif
                    </div>

                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                            {{ old('status', $page->status) ? 'checked' : '' }}>
                        <label class="form-check-label ms-2" for="status">Publish Page</label>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('pages.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Page</button>
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

    document.addEventListener('DOMContentLoaded', function () {
        ClassicEditor
            .create(document.querySelector('#description'))
            .then(editor => {
                ckeditorInstance = editor;

                const btnGrammar = document.querySelector('#btn_grammar_check');
                const btnSeoRewrite = document.querySelector('#btn_ai_marketing');

                if (btnSeoRewrite) {
                    btnSeoRewrite.addEventListener('click', async function () {
                        const titleInput = document.querySelector('#title');
                        const originalTitle = titleInput.value.trim();
                        const originalContent = ckeditorInstance.getData().replace(/<[^>]+>/g, '').trim();

                        if (!originalTitle || !originalContent) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Missing Fields',
                                text: 'Please enter a page title and content before rewriting.',
                            });
                            return;
                        }

                        btnSeoRewrite.disabled = true;
                        ckeditorInstance.setData('<p><em>Rewriting with AI... please wait</em></p>');

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
                                    text: data.message || 'The AI server could not process the content.',
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

                if (btnGrammar) {
                    btnGrammar.addEventListener('click', async function () {
                        const titleInput = document.querySelector('#title');
                        const titleText = titleInput.value.trim();
                        const contentText = ckeditorInstance.getData().replace(/<[^>]+>/g, '').trim();

                        if (!titleText || !contentText) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Missing Fields',
                                text: 'Title and content fields must not be empty.',
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
                                    question: titleText,
                                    answer: contentText
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                if (data.question) titleInput.value = data.question;
                                if (data.answer) ckeditorInstance.setData(data.answer);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Grammar Check Failed',
                                    text: data.message || 'Failed to correct grammar.',
                                });
                                ckeditorInstance.setData(contentText);
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: 'Error connecting to grammar service.',
                            });
                            ckeditorInstance.setData(contentText);
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
