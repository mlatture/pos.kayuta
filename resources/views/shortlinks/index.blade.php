@extends('layouts.admin')

@section('title', 'Short Links')
@section('content-header', 'Manage Short Links')
@section('content-actions')
    @hasPermission(config('constants.role_modules.short_links.value'))
        <a href="{{ route('shortlinks.create') }}" class="btn btn-success"><i class="fas fa-plus"></i>
            {{ config('constants.role_modules.short_links.name') }}</a>
    @endHasPermission
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
{{-- 
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif --}}

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Actions</th>
                            <th>Slug</th>
                            <th>Full URL</th>
                            <th>Clicks</th>
                            <th>Source</th>
                            <th>Medium</th>
                            <th>Campaign</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shortlinks as $link)
                            <tr>
                                <td>
                                    <a href="{{ route('shortlinks.edit', $link->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="{{ route('shortlinks.show', $link->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <form action="{{ route('shortlinks.destroy', $link->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><id class="fas fa-trash"></i></button>
                                    </form>

                                </td>
                                <td><code>/go/{{ $link->slug }}</code></td>
                                <td style="max-width: 300px; word-break: break-all;">{{ $link->fullredirecturl }}</td>
                                <td>{{ $link->clicks }}</td>
                                <td>{{ $link->source }}</td>
                                <td>{{ $link->medium }}</td>
                                <td>{{ $link->campaign }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
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
@endpush
