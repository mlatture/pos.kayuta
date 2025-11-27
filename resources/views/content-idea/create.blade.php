@extends('layouts.admin')

@section('title', 'Create Content Idea')
@section('content-header', 'Create Content Idea')

@section('content')
<div class="card shadow-sm mb-4 overflow-auto" style="max-height: 80vh;">
    <div class="card-body">
        <form action="{{ route('content-ideas.store') }}" method="POST">
            @csrf

            {{-- CATEGORY --}}
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Select --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>
                            {{ $cat->name ?? 'Category #'.$cat->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TITLE --}}
            <div class="mb-3">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control"
                       value="{{ old('title') }}" required>
            </div>

            {{-- SUMMARY --}}
            <div class="mb-3">
                <label class="form-label">Summary</label>
                <textarea name="summary" class="form-control" rows="4">{{ old('summary') }}</textarea>
            </div>

            {{-- RANK --}}
            <div class="mb-3">
                <label class="form-label">Rank</label>
                <input type="number" name="rank" class="form-control"
                       value="{{ old('rank') }}" min="0">
            </div>

            {{-- STATUS --}}
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">-- Select Status --</option>
                    <option value="draft"     {{ old('status')=='draft'?'selected':'' }}>Draft</option>
                    <option value="idea"      {{ old('status')=='idea'?'selected':'' }}>Idea</option>
                    <option value="approved"  {{ old('status')=='approved'?'selected':'' }}>Approved</option>
                </select>
            </div>

            {{-- AI INPUTS (JSON) --}}
            <div class="mb-3">
                <label class="form-label">AI Inputs (JSON)</label>
                <textarea name="ai_inputs" rows="6"
                    class="form-control @error('ai_inputs') is-invalid @enderror"
                    placeholder='{"tone":"funny","platform":"instagram"}'>{{ old('ai_inputs') }}</textarea>

                @error('ai_inputs')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
