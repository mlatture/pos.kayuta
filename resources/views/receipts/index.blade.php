@extends('layouts.admin')

@section('title', 'Scan Receipts')
@section('content-header', 'Scan Receipts')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 col-md-6">
            <form action="{{ route('receipts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Upload New Receipt</h5>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Take Photo</label>
                            <input type="file" name="photo" accept="image/*" capture="environment" required class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount ($)</label>
                            <input type="number" name="amount" step="0.01" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Upload Receipt</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($receipts->count())
    <div class="row">
        @foreach($receipts as $receipt)
            <div class="col-6 col-md-3 mb-3">
                <div class="card h-100 shadow-sm">
                    <img src="{{ asset('storage/' . $receipt->photo) }}" class="card-img-top" alt="Receipt Image" style="height: 150px; object-fit: cover;">
                    <div class="card-body p-2">
                        <p class="mb-1"><strong>Amount:</strong> ${{ number_format($receipt->amount, 2) }}</p>
                        <p class="mb-1"><strong>Category:</strong> {{ $receipt->category->name ?? '—' }}</p>
                        <p class="mb-0"><strong>Date:</strong> {{ \Carbon\Carbon::parse($receipt->date)->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
        <div class="alert alert-info">No receipts uploaded yet.</div>
    @endif
</div>
@endsection
