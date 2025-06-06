@extends('layouts.admin')

@section('title', ucfirst($type) . ' Manager')
@section('content-header', ucfirst($type) . ' Content')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_faqs.value'))
        <a href="{{ route('pages.create', ['type' => $type]) }}" class="btn btn-success"><i class="fas fa-plus"></i>
            Add {{ ucfirst($type) }}</a>
    @endHasPermission
@endsection
@section('css')
<style>
    .text-truncate-hover {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
        display: inline-block;
        cursor: pointer;
    }

    .text-truncate-hover:hover {
        white-space: normal;
        overflow: visible;
        background: #f9f9f9;
        padding: 5px;
        border-radius: 4px;
        position: absolute;
        z-index: 1000;
        max-width: 400px;
    }
</style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="mb-3">
                <label class="form-label">View By Type:</label>
                <select class="form-select w-auto d-inline-block" onchange="location = this.value;">
                    @foreach (['page', 'article', 'blog'] as $contentType)
                        <option value="{{ route('pages.index', ['type' => $contentType]) }}"
                            {{ $type === $contentType ? 'selected' : '' }}>
                            {{ ucfirst($contentType) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body">


            {{-- @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif --}}

            @if ($type === 'blog' || $type === 'blogs')
                {{-- BLOGS TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Actions</th>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th class="text-end">Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($blogs as $blog)
                                <tr>
                                    <td class="text-start">
                                        <a href="{{ route('blogs.edit', $blog->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('blogs.destroy', $blog->id) }}" method="POST"
                                            class="d-inline-block"
                                            onsubmit="return confirm('Are you sure you want to delete this blog?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $blog->title }}</td>
                                    <td><code>{{ $blog->slug }}</code></td>
                                    <td>
                                        <span class="badge {{ $blog->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $blog->status ? 'Published' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $blog->updated_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No blogs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif ($pages && $pages->count())
                {{-- PAGES TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Actions</th>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th class="text-end">Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pages as $page)
                                <tr>
                                    <td class="text-start">
                                        <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-sm btn-primary">
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
                                    <td>{{ $page->title }}</td>
                                    <td><code>{{ $page->slug }}</code></td>
                                    <td>{{ ucfirst($page->type) }}</td>
                                    <td>
                                        <span class="badge {{ $page->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $page->status ? 'Published' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $page->updated_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif ($type === 'article' && isset($articles))
                {{-- ARTICLES TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Actions</th>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Content</th>
                                <th>Status</th>
                                <th class="text-end">Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($articles as $article)
                                <tr>
                                    <td class="text-start">
                                        <a href="{{ route('article.edit', $article->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST"
                                            class="d-inline-block"
                                            onsubmit="return confirm('Are you sure you want to delete this article?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $article->title }}</td>
                                    <td><code>{{ $article->slug }}</code></td>
                                    <td>
                                        <div class="article-description text-truncate-hover"
                                            title="{{ strip_tags($article->description) }}">
                                            {{ Str::words(strip_tags($article->description), 50, '...') }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge {{ $article->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $article->status ? 'Published' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $article->updated_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No articles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    No {{ $type }} content found.
                </div>
            @endif

        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                responsive: true,
                stateSave: true,
                dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
                buttons: [
                    'colvis',
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],

                language: {
                    search: 'Search: ',
                    lengthMenu: 'Show _MENU_ entries',
                },
                pageLength: 10
            });

        })
    </script>
@endsection
