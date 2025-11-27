@extends('layouts.admin')

@section('title', 'Content Ideas')
@section('content-header', 'Content Ideas')

@section('content-actions')
    <a href="{{ route('content-ideas.create') }}" class="btn btn-success mb-2 me-2">
        <i class="fas fa-plus"></i> Add New Content Idea
    </a>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
@endsection

@section('content')
<div class="row animated fadeInUp">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive m-t-40 p-0">
                    <table class="display nowrap table table-hover table-striped border p-0" width="100%">
                        <thead>
                        <tr>
                            <th>Actions</th>
                            <th>ID</th>
                            <th>Tenant</th>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Summary</th>
                            <th>Rank</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Updated</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($contentIdeas as $idea)
                            <tr>
                               <td class="d-flex gap-2">

    {{-- EDIT BUTTON --}}
    <a href="{{ route('content-ideas.edit', $idea) }}"
       class="btn btn-sm btn-primary me-1">
        <i class="fas fa-edit"></i>
    </a>

    {{-- DELETE BUTTON --}}
    <form action="{{ route('content-ideas.destroy', $idea) }}"
          method="POST" class="d-inline-block delete-form">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger btn-delete">
            <i class="fas fa-trash"></i>
        </button>
    </form>

</td>


                                <td>{{ $idea->id }}</td>
                                <td>{{ optional($idea->tenant)->name ?? $idea->tenant_id }}</td>
                                <td>{{ optional($idea->category)->name ?? $idea->category_id }}</td>
                                <td>{{ $idea->title }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($idea->summary, 80) }}</td>
                                <td>{{ $idea->rank }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $idea->status ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $idea->created_at }}</td>
                                <td>{{ $idea->updated_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $contentIdeas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
$(document).ready(function () {

    $('.table').DataTable({
        responsive: true,
        stateSave: true,
        dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
        buttons: ['colvis','copy','csv','excel','pdf','print'],
        language: {
            search: 'Search: ',
            lengthMenu: 'Show _MENU_ entries',
        },
        pageLength: 10,
    });

    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this content idea?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection
