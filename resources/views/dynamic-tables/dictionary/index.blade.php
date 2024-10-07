@extends('layouts.admin')

@php
    $formattedTable = ucfirst($table);
@endphp
@section('title', "Dynamic Table: {$formattedTable}")
@section('content-header', "Dynamic Table: {$formattedTable}")

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <a href=""
                           class="btn btn-sm btn-primary"><i
                                class="fas fa-recycle"></i> Reset</a>
                        <a href="{{ route('admin.whitelist') }}"
                           class="btn btn-sm btn-primary"><i
                                class="fas fa-arrow-circle-left"></i> Back to whitelist</a>
                    </div>
                </div>
                <form action="{{ route('admin.update-table', $table) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="table-responsive m-t-40">
                                <table class="table table-sm table-hover table table-striped border">
                                    <thead>
                                    <tr>
                                        <th class="col-md-1 text-center">Viewable</th>
                                        <th class="col-md-1">Original Name</th>
                                        <th class="col-md-2">Dictionary/Display Name</th>
                                        <th>Description</th>
                                        <th class="col-md-1 text-center">Datatype</th>
                                        <th class="col-md-1 text-center">Visibility</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($columns as $column)
                                        <tr>
                                            <td class="col-md-1 text-center">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                           id="dictionary[{{ $column }}][viewable]" {{ isset($dictionary[$column]) && $dictionary[$column]['viewable'] === FALSE ? '' : 'checked' }}
                                                           name="dictionary[{{ $column }}][viewable]">
                                                    <label class="custom-control-label"
                                                           for="dictionary[{{ $column }}][viewable]"></label>
                                                </div>
                                            </td>
                                            <td class="col-md-1">{{ $column }}</td>
                                            <td class="col-md-2">
                                                <input aria-label="" type="text"
                                                       name="dictionary[{{ $column }}][display_name]"
                                                       value="{{ isset($dictionary[$column]) ? $dictionary[$column]['display_name'] : '' }}"
                                                       class="form-control">
                                            </td>
                                            <td>
                                                <input aria-label="" type="text"
                                                       name="dictionary[{{ $column }}][description]"
                                                       value="{{ isset($dictionary[$column]) ? $dictionary[$column]['description'] : '' }}" class="form-control">
                                            </td>
                                            <td class="col-md-1 text-center">
                                                <label class="badge badge-secondary">
                                                    {{ \Illuminate\Support\Facades\Schema::getColumnType($table, $column) }}
                                                </label>
                                            </td>
                                            <td class="col-md-1 text-center">
                                                <select aria-label="" name="dictionary[{{ $column }}][visibility]"
                                                        class="custom-select">
                                                    <option value="" selected>Select anyone</option>
                                                    <option
                                                        value="all" {{ isset($dictionary[$column]) && $dictionary[$column]['visibility'] === 'all' ? 'selected' : '' }}>
                                                        All
                                                    </option>
                                                    <option
                                                        value="read_only" {{ isset($dictionary[$column]) && $dictionary[$column]['visibility'] === 'read_only' ? 'selected' : '' }}>
                                                        Read Only
                                                    </option>
                                                    <option
                                                        value="hidden" {{ isset($dictionary[$column]) && $dictionary[$column]['visibility'] === 'hidden' ? 'selected' : '' }}>
                                                        Hidden
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-sm btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
