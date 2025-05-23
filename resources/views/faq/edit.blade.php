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
                            <input type="text" name="title" id="question" class="form-control" placeholder="Enter the question" value="{{ old('title', $faq->title) }}" required>
                        </div>

                        <div class="form-group mt-3">
                            <label for="answer">Answer</label>
                            <textarea name="description" id="answer" class="form-control" placeholder="Provide a clear and concise answer">{{ old('description', $faq->description) }}</textarea>
                        </div>

                        <div class="form-group mt-3">
                            <label for="order_by">Order By</label>
                            <input type="number" name="order_by" id="order_by" class="form-control" min="1" value="{{ old('order_by', $faq->order_by ?? 1) }}">
                        </div>

                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="grammar_check" name="auto_correct" {{ old('auto_correct', $faq->auto_correct) ? 'checked' : '' }}>
                            <label class="form-check-label" for="grammar_check">Check grammar and spelling (minimal edits)</label>
                        </div>

                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="ai_marketing" name="ai_rewrite" {{ old('ai_rewrite', $faq->ai_rewrite) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ai_marketing">Rewrite for SEO and marketing purposes</label>
                        </div>

                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="show_in_details" name="show_in_details" value="1" {{ old('show_in_details', $faq->show_in_details) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_in_details">Show in Site Details</label>
                        </div>

                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $faq->status) ? 'checked' : '' }}>
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
        document.addEventListener('DOMContentLoaded', function () {
            const answerField = document.querySelector('#answer');
            if (answerField) {
                ClassicEditor
                    .create(answerField)
                    .catch(error => {
                        console.error('CKEditor initialization error:', error);
                    });
            }
        });
    </script>
@endsection
