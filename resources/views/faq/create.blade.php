@extends('layouts.admin')

@section('title', 'Create FAQ')
@section('content-header', 'Create FAQ')
@section('css')
    <style>
        button[data-cke-tooltip-text="Insert media"],
        button[data-cke-tooltip-text="Insert image"],
        button[data-cke-tooltip-text="Link (⌘K)"],
        button[data-cke-tooltip-text="Bold (⌘B)"],
        /* button[data-cke-tooltip-text="Italic (⌘I)"] */
        button[data-cke-tooltip-text="Decrease Indent"],
        button[data-cke-tooltip-text="Increase Indent"]
        
        {
        display: none !important;
        }
    </style>
@endsection
@section('content')
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('faq.store') }}" method="post">
                        @csrf

                        <div class="form-group">
                            <label for="question">Question</label>
                            <input type="text" name="title" id="question" class="form-control"
                                placeholder="Enter the question" required>
                        </div>

                        <div class="form-group mt-3">
                            <label for="answer">Answer</label>
                            <textarea name="description" id="answer" class="form-control" placeholder="Provide a clear and concise answer"></textarea>
                        </div>

                        <div class="form-group mt-3">
                            <label for="order_by">Order By</label>
                            <input type="number" value="1" name="order_by" id="order_by" class="form-control"
                                placeholder="Sorting order number" min="1">
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
                                value="1">
                            <label class="form-check-label" for="show_in_details">Show in Site Details</label>
                        </div>

                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                checked>
                            <label class="form-check-label" for="status">Visible on FAQ Page</label>
                        </div>


                        <div class="form-group mt-3 d-flex justify-content-end">
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
                            const questionInput = document.querySelector('#question');
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
                                const response = await fetch('{{ route('ai.rewrite') }}', {
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

                                console.log('data seo', data);
                                if (data.success) {
                                    if (data.question) {
                                        questionInput.value = data.question;
                                    }
                                    if (data.answer) {
                                        ckeditorInstance.setData(data.answer);
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
        });
    </script>
@endsection
