@extends('layouts.admin')

@section('title', 'GPT Debug')
@section('content-header', 'GPT Output Debug')

@section('content')
    <pre>{{ $raw }}</pre>
    <hr>
    <h5>Full Response</h5>
    <pre>{{ json_encode($response, JSON_PRETTY_PRINT) }}</pre>
@endsection
