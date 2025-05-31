@extends('layouts.admin')

@section('title', 'Edit FAQ')
@section('content-header', 'Edit FAQ')

@section('content')
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('faq.update', $faq->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="question">Question</label>
                            <input type="text" name="title" id="question" class="form-control"
                                placeholder="Enter the question" value="{{ old('title', $faq->title) }}" required>
                        </div>

                        <div class="form-group mt-3">
                            <label for="answer">Answer</label>
                            <textarea name="description" id="answer" class="form-control" placeholder="Provide a clear and concise answer">{{ old('description', $faq->description) }}</textarea>
                        </div>

                        <div class="form-group mt-3">
                            <label for="order_by">Order By</label>
                            <input type="number" name="order_by" id="order_by" class="form-control" min="1"
                                value="{{ old('order_by', $faq->order_by ?? 1) }}">
                        </div>

                        <div class="form-group mt-4 d-flex gap-2">
                            <button type="button" id="btn_grammar_check" class="btn btn-outline-secondary">
                                Check Grammar
                            </button>
                            <button type="button" id="btn_ai_marketing" class="btn btn-outline-info">
                                Rewrite for SEO
                            </button>
                        </div>


                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="show_in_details" name="show_in_details"
                                value="1" {{ old('show_in_details', $faq->show_in_details) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_in_details">Show in Site Details</label>
                        </div>

                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                {{ old('status', $faq->status) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">Visible on FAQ Page</label>
                        </div>

                        <div class="form-group mt-3 d-flex justify-content-end">
                            <a href="{{ route('faq.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update FAQ</button>
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
                .create(document.querySelector('#answer'))
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
                            const originalText = ckeditorInstance.getData().replace(/<[^>]+>/g, '')
                                .trim();

                            if (!originalText) {
                                alert('Answer field is empty.');
                                return;
                            }

                            btnSeoRewrite.disabled = true;
                            ckeditorInstance.setData('<em>Rewriting with AI... please wait</em>');

                            try {
                                const response = await fetch('{{ route('ai.rewrite') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        text: originalText
                                    })
                                });

                                const data = await response.json();

                                if (data.success) {
                                    ckeditorInstance.setData(data.rewritten);
                                } else {
                                    alert(data.message || 'Failed to rewrite.');
                                    ckeditorInstance.setData(originalText);
                                }
                            } catch (error) {
                                alert('Error connecting to AI server.');
                                ckeditorInstance.setData(originalText);
                            } finally {
                                btnSeoRewrite.disabled = false;
                            }
                        });
                    }

                    // Grammar correction button
                    if (btnGrammar) {
                        btnGrammar.addEventListener('click', async function() {
                            const originalText = ckeditorInstance.getData().replace(/<[^>]+>/g, '')
                                .trim();

                            if (!originalText) {
                                alert('Answer field is empty.');
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
                                        text: originalText
                                    })
                                });

                                const data = await response.json();

                                if (data.success) {
                                    ckeditorInstance.setData(data.corrected);
                                } else {
                                    alert(data.message || 'Failed to correct grammar.');
                                    ckeditorInstance.setData(originalText);
                                }
                            } catch (error) {
                                alert('Error connecting to grammar server.');
                                ckeditorInstance.setData(originalText);
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
