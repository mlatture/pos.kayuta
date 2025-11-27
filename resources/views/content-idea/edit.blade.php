@extends('layouts.admin')

@section('title', 'Edit Content Idea')
@section('content-header', 'Edit Content Idea')

@section('content')
<div class="card shadow-sm mb-4 overflow-auto" style="max-height: 80vh;">
    <div class="card-body">
        <form action="{{ route('content-ideas.update', $contentIdea) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- CATEGORY --}}
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Select --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $contentIdea->category_id)==$cat->id?'selected':'' }}>
                            {{ $cat->name ?? 'Category #'.$cat->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TITLE --}}
            <div class="mb-3">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control"
                       value="{{ old('title', $contentIdea->title) }}" required>
            </div>

            {{-- SUMMARY --}}
            <div class="mb-3">
                <label class="form-label">Summary</label>
                <textarea name="summary" class="form-control" rows="4">
                    {{ old('summary', $contentIdea->summary) }}
                </textarea>
            </div>

            {{-- RANK --}}
            <div class="mb-3">
                <label class="form-label">Rank</label>
                <input type="number" name="rank" class="form-control"
                       value="{{ old('rank', $contentIdea->rank) }}" min="0">
            </div>

            {{-- STATUS --}}
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">-- Select Status --</option>
                    <option value="draft"     {{ old('status', $contentIdea->status)=='draft'?'selected':'' }}>Draft</option>
                    <option value="idea"      {{ old('status', $contentIdea->status)=='idea'?'selected':'' }}>Idea</option>
                    <option value="approved"  {{ old('status', $contentIdea->status)=='approved'?'selected':'' }}>Approved</option>
                </select>
            </div>

            {{-- AI INPUTS JSON --}}
            <div class="mb-3">
                <label class="form-label">AI Inputs (JSON)</label>
                <textarea name="ai_inputs" rows="6"
                    class="form-control @error('ai_inputs') is-invalid @enderror">{{ old('ai_inputs', json_encode($contentIdea->ai_inputs, JSON_PRETTY_PRINT)) }}</textarea>

                @error('ai_inputs')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
