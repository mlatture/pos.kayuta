@extends('layouts.admin')

@section('title', "{$formattedTable} Management")
@section('content-header', "{$formattedTable} Management")
@section('content-actions')
    <a href="{{ route('admin.dynamic-module-create-form-data', $table) }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Add New {{ $formattedTable }}
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
                    <div class="row">
                        <div class="table-responsive m-t-40 p-3">
                            <table class="table table-hover table-striped border">
                                <thead>
                                <tr>
                                    <th></th>
                                    @foreach($columns as $column)
                                        <th>
                                            @if($column === 'id')
                                                @continue
                                            @endif
                                            {{ $dictionaryFields[$column]['display_name'] ?? $column }}
                                        </th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($records as $record)
                                    <tr>
                                        <td>
                                            @if(isset($record->id))
                                                <a title="Edit {{ $table }} record"
                                                   href="{{ route('admin.dynamic-module-create-form-data', [$table, $record->id]) }}"
                                                   class="btn btn-sm btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                            @endif
                                        </td>
                                        @foreach($record as $key => $rec)
                                            <td>
                                                @if($key === 'id')
                                                    @continue
                                                @endif
                                                {{ $rec ?? '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
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
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
            $(document).on('click', '.btn-delete', function () {
                $this = $(this);
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to delete this product?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.post($this.data('url'), {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        }, function (res) {
                            $this.closest('tr').fadeOut(500, function () {
                                $(this).remove();
                            })
                        })
                    }
                })
            })
        })
    </script>
@endsection
