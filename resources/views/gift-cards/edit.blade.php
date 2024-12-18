@extends('layouts.admin')

@section('title', 'Update Gift Card')
@section('content-header', 'Update Gift Card')

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('gift-cards.update', $giftCard) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                        id="title" placeholder="Title" value="{{ old('title', $giftCard->title) }}" required minlength="3" maxlength="15">
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="user_barcode">User Email</label>
                    <input type="email" name="user_email" class="form-control @error('user_email') is-invalid @enderror"
                        id="user_email" placeholder="User Email" value="{{ old('user_email', $giftCard->user_email) }}" required>
                    @error('user_email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="barcode">Barcode</label>
                    <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror"
                        id="barcode" placeholder="Barcode" value="{{ old('barcode', $giftCard->barcode) }}" maxlength="20" required>
                    @error('barcode')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="discount_type">discount_type</label>
                    <select name="discount_type" id="discount_type" required
                        class="form-control @error('discount_type') is-invalid @enderror">
                        <option value="">Select Discount Type</option>
                        <option value="percentage"
                            {{ old('discount_type', $giftCard->discount_type) == 'percentage' ? 'selected' : '' }}>
                            Percentage
                        </option>
                        <option value="fixed_amount"
                            {{ old('discount_type', $giftCard->discount_type) == 'fixed_amount' ? 'selected' : '' }}>Fixed
                            Amount</option>
                    </select>
                    @error('discount_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="discount">Discount</label>
                    <input type="number" name="discount" class="form-control @error('discount') is-invalid @enderror"
                        id="discount" placeholder="Discount" value="{{ old('discount', $giftCard->discount) }}" required>
                    @error('discount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                        id="start_date" placeholder="Start Date" value="{{ old('start_date', $giftCard->start_date) }}" required>
                    @error('start_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="expire_date">Expire Date</label>
                    <input type="date" name="expire_date" class="form-control @error('expire_date') is-invalid @enderror"
                        id="expire_date" placeholder="Expire Date"
                        value="{{ old('expire_date', $giftCard->expire_date) }}" required>
                    @error('expire_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="min_purchase">Minimum Purchase</label>
                    <input type="number" name="min_purchase"
                        class="form-control @error('min_purchase') is-invalid @enderror" id="min_purchase"
                        placeholder="Min Purchase" value="{{ old('min_purchase', $giftCard->min_purchase) }}">
                    @error('min_purchase')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="max_discount">Maximum Discount</label>
                    <input type="number" name="max_discount"
                        class="form-control @error('max_discount') is-invalid @enderror" id="max_discount"
                        placeholder="Maximum Discount" value="{{ old('max_discount', $giftCard->max_discount) }}">
                    @error('max_discount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="limit">Limit</label>
                    <input type="number" name="limit" class="form-control @error('limit') is-invalid @enderror"
                        id="limit" placeholder="Limit" value="{{ old('limit', $giftCard->limit) }}">
                    @error('limit')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" id="status">
                        <option value="1" {{ old('status', $giftCard->status) === 1 ? 'selected' : '' }}>Active
                        </option>
                        <option value="0" {{ old('status', $giftCard->status) === 0 ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <button class="btn btn-success btn-block btn-lg" type="submit">Save Changes</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
