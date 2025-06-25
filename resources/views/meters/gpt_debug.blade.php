@extends('layouts.admin')

@section('title', 'GPT Debug')
@section('content-header', 'GPT Output Debug')

@section('content')
    <pre>{{ $raw }}</pre>
    <hr>
    <h5>Full Response</h5>
    <pre>{{ json_encode($response, JSON_PRETTY_PRINT) }}</pre>

   
    <div class="text-center my-4">
        <p>⚠️ That doesn't seem right. What would you like to do?</p>
        <div class="d-flex justify-content-center gap-3">
            <form action="{{ route('meters.read') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="photo" value="{{ old('photo') }}"> 
                <button class="btn btn-warning" type="submit">🔁 Try reading this picture again</button>
            </form>

            {{-- <a href="{{ route('meters.read') }}" class="btn btn-secondary">📷 Take another picture</a> --}}
        </div>
    </div>

@endsection
