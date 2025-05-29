@extends('layouts.admin')

@section('title', 'Create FAQ')
@section('content-header', 'Create FAQ')

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

                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="grammar_check" name="auto_correct">
                            <label class="form-check-label" for="grammar_check">Check grammar and spelling (minimal
                                edits)</label>
                        </div>

                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="ai_marketing" name="ai_rewrite">
                            <label class="form-check-label" for="ai_marketing">Rewrite for SEO and marketing
                                purposes</label>
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

                    const aiCheckbox = document.querySelector('#ai_marketing');
                    const grammarCheckbox = document.querySelector('#grammar_check');

                    if (aiCheckbox) {
                        aiCheckbox.addEventListener('change', async function() {
                            const originalText = ckeditorInstance.getData().replace(/<[^>]+>/g, '')
                                .trim();

                            if (this.checked) {
                                if (!originalText) {
                                    alert('Answer field is empty.');
                                    this.checked = false;
                                    return;
                                }

                                ckeditorInstance.setData(
                                    '<em>Rewriting with AI... please wait</em>');

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
                                        this.checked = false;
                                    }
                                } catch (error) {
                                    alert('Error connecting to AI server.');
                                    ckeditorInstance.setData(originalText);
                                    this.checked = false;
                                }
                            }
                        });
                    }

                    if (grammarCheckbox) {
                        grammarCheckbox.addEventListener('change', async function() {
                            const originalText = ckeditorInstance.getData().replace(/<[^>]+>/g, '')
                                .trim();

                            if (this.checked) {
                                if (!originalText) {
                                    alert('Answer field is empty.');
                                    this.checked = false;
                                    return;
                                }

                                ckeditorInstance.setData(
                                '<em>Checking grammar... please wait</em>');

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
                                        this.checked = false;
                                    }
                                } catch (error) {
                                    alert('Error connecting to grammar server.');
                                    ckeditorInstance.setData(originalText);
                                    this.checked = false;
                                }
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
