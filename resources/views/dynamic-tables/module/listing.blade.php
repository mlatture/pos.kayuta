@extends('layouts.admin')

@section('title', "{$formattedTable} Management")
@section('content-header', "{$formattedTable} Management")
@section('content-actions')
    @if (auth()->user()->hasPermission("read_{$table}"))
        <a href="{{ route('admin.whitelist') }}"
           class="btn btn-primary"><i
                class="fas fa-arrow-circle-left"></i> Back to whitelist</a>
        <a href="{{ route('admin.dynamic-module-create-form-data', $table) }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New {{ $formattedTable }}
        </a>
    @endif
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
                            @php
                                foreach ($columns as $column) {
                                    if (!isset($dictionaryFields[$column])) {
                                        $dictionaryFields[$column] = [
                                            'field_name' => $column,
                                            'order' => PHP_INT_MAX,
                                            'display_name' => ucfirst(str_replace('_', ' ', $column))
                                        ];
                                    }
                                    if ($column === 'created_at') {
                                        $dictionaryFields[$column]['display_name'] = 'Created';
                                    }
                                    if ($column === 'updated_at') {
                                        $dictionaryFields[$column]['display_name'] = 'Updated';
                                    }
                                }

                                $fieldsArray = array_values($dictionaryFields);
                                usort($fieldsArray, function ($a, $b) {
                                    return ($a['order'] <=> $b['order']);
                                });
                                $orderedKeys = array_map(function ($field) {
                                    return $field['field_name'];
                                }, $fieldsArray);
                                $orderedKeys = array_diff($orderedKeys, ['id']);
                            @endphp

                            <table class="table table-hover table-striped border">
                                <thead>
                                <tr>
                                    <th></th>
                                    @foreach($orderedKeys as $key)
                                        <th>{{ $dictionaryFields[$key]['display_name'] ?? $key }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($records as $record)
                                    <tr>
                                        <td>
                                            @if(isset($record->id))
                                                @if (auth()->user()->hasPermission("read_{$table}"))
                                                    <a title="Edit {{ $table }} record"
                                                       href="{{ route('admin.dynamic-module-create-form-data', [$table, $record->id]) }}"
                                                       class="btn btn-sm btn-primary"><i
                                                            class="fas fa-edit"></i></a>
                                                @endif
                                            @endif
                                        </td>
                                        @foreach($orderedKeys as $key)
                                            <td>
                                                {{ $record->{$key} ?? '-' }}
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
                const $this = $(this);
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });

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
                            });
                        });
                    }
                });
            });
        });
    </script>
@endsection