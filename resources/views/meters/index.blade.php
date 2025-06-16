@extends('layouts.admin')

@section('title', 'Scan Electric Meter')
@section('content-header', 'Scan Electric Meter')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <form action="{{ route('meters.read') }}" method="POST" enctype="multipart/form-data" class="card p-3 shadow-sm">
                @csrf
                <h5 class="mb-3">1. Upload or Take a Photo of the Electric Meter</h5>
                <div class="mb-3">
                    <input type="file" class="form-control" name="photo" accept="image/*" capture="environment" required>
                </div>
                <button type="submit" class="btn btn-primary">Scan and Preview Bill</button>
            </form>
        </div>
    </div>

    @if($overdueSites->count())
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm p-3">
                <h5 class="mb-3">2. Meters Overdue (Not Read in 20+ Days)</h5>
                <ul class="list-group">
                    @foreach($overdueSites as $site)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Site No: <strong>{{ $site->siteno }}</strong>
                            <span class="badge bg-danger">Overdue</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @else
        <p class="text-muted text-center">No overdue meters found.</p>
    @endif
</div>
@endsection
