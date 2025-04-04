@extends('layouts.admin')

@section('title', "{$module} {$formattedTable} Record")
@section('content-header', "{$module} {$formattedTable} Record")

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST"
                  action="{{ $isEdit ? route('admin.dynamic-module-update-form-data', [$table, $moduleData->id]) : route('admin.dynamic-module-store-form-data', $table) }}">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif
                <div class="row">
                    @forelse($columns as $column)
                        @if($column === 'id')
                            @continue;
                        @endif
                        <div class="{{ isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'hidden' ? 'd-none' : 'col-md-6' }}">
                            <div class="form-group">
                                <label for="{{ $column }}" {{ isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'read_only' ? 'readonly' : '' }}>
                                    {{ isset($dictionaryFields[$column]) ? $dictionaryFields[$column]['display_name'] : $column }}
                                    {!! isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'read_only' ? '<span class="text-danger">(not editable)</span>' : '' !!}
                                </label>

                                @php
                                    $datatype = \Illuminate\Support\Facades\Schema::getColumnType($table, $column);
                                    $fieldType = $datatype === 'string' ? 'text' :
                                                 ($datatype === 'integer' ? 'number' :
                                                 ($datatype === 'datetime' ? 'datetime-local' : $datatype));
                                @endphp

                                @if(in_array($datatype, ['text', 'longtext', 'json']))
                                    <textarea aria-label="{{ $column }}" type="{{ $fieldType }}" name="{{ $column }}" class="form-control @error($column) is-invalid @enderror" id="{{ $column }}"
                                              {{ isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'read_only' ? 'readonly' : '' }}
                                        {{ isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'hidden' ? 'disabled' : '' }}>
                                        {{ $isEdit ? $moduleData->$column : old($column) }}
                                    </textarea>
                                @else
                                    <input aria-label="{{ $column }}" type="{{ $fieldType }}" name="{{ $column }}"
                                           class="form-control @error($column) is-invalid @enderror" id="{{ $column }}"
                                           {{ isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'read_only' ? 'readonly' : '' }}
                                           {{ isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'hidden' ? 'disabled' : '' }}
                                           value="{{ $isEdit ? $moduleData->$column : old($column) }}">
                                @endif
                                @if(!empty($dictionaryFieldsDesc[$column]))
                                    <small class="form-text text-muted"><span
                                            class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc[$column] }}
                                    </small>
                                @endif
                                @error($column)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
                <button class="btn {{ $isEdit ? 'btn-warning' : 'btn-success' }} btn-block btn-lg" type="submit">
                    @if($isEdit)
                        Update
                    @else
                        Save
                    @endif
                </button>
            </form>
        </div>
    </div>
@endsection
