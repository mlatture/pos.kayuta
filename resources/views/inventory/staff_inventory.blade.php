@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daily Inventory Tasks</h2>
    
    <ul class="list-group">
        @foreach ($tasks as $task)
            <li class="list-group-item">
                <strong>{{ $task->product->name }}</strong> (Last Checked: {{ $task->product->last_checked_date->format('Y-m-d') }})
                
                @if ($task->product->category == 'food')
                    <p class="text-danger">⚠️ Please check expiration date!</p>
                @endif
                
                <form method="POST" action="{{ route('inventory.update', $task->id) }}">
                    @csrf
                    <div class="mb-2">
                        <label>Updated Quantity</label>
                        <input type="number" name="updated_quantity" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Reason (if different from expected)</label>
                        <input type="text" name="reason" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
@endsection
