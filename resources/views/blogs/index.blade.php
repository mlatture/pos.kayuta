@extends('layouts.admin')

@section('title', ucfirst($type) . ' Manager')
@section('content-header', ucfirst($type) . ' Content')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_faqs.value'))
        <a href="{{ route('pages.create', ['type' => $type]) }}" class="btn btn-success"><i class="fas fa-plus"></i>
           Add {{ ucfirst($type) }}</a>
    @endHasPermission
@endsection
@section('content')
    <div class="card">
        
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">View By Type:</label>
                <select class="form-select w-auto d-inline-block" onchange="location = this.value;">
                    @foreach (['page', 'article', 'blog', 'landing'] as $contentType)
                        <option value="{{ route('pages.index', ['type' => $contentType]) }}"
                            {{ $type === $contentType ? 'selected' : '' }}>
                            {{ ucfirst($contentType) }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($pages->count())
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Updated</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pages as $page)
                                <tr>
                                    <td>{{ $page->title }}</td>
                                    <td><code>{{ $page->slug }}</code></td>
                                    <td>{{ ucfirst($page->type) }}</td>
                                    <td>
                                        <span class="badge {{ $page->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $page->status ? 'Published' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td>{{ $page->updated_at->format('M d, Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('pages.destroy', $page->id) }}" method="POST"
                                            class="d-inline-block"
                                            onsubmit="return confirm('Are you sure you want to delete this page?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- <div class="mt-3">
                    {{ $pages->appends(['type' => $type])->links() }}
                </div> --}}
            @else
                <div class="alert alert-info">
                    No {{ $type }} content found.
                </div>
            @endif
        </div>
    </div>
@endsection
