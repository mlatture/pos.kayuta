@extends('layouts.admin')

@section('title', 'Create Gift Card')
@section('content-header', 'Create Gift Card')

@section('content')

    <div class="card">
        <div class="card-body">

            <form id="giftCardForm" method="POST" enctype="multipart/form-data">
                @csrf
                {{--
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           id="title"
                           placeholder="Title" value="{{ old('title') }}" minlength="3" maxlength="15" >
                    @error('title')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div> --}}

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="user_barcode">{{ $dictionaryFields['user_email'] ?? 'User Email' }}</label>
                            <input type="email" name="user_email"
                                class="form-control @error('user_email') is-invalid @enderror" id="user_email"
                                placeholder="User Email" value="{{ old('user_email') }}">
                            @if(!empty($dictionaryFieldsDesc['user_email']))
                                <small class="form-text text-muted"><span
                                        class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['user_email'] }}
                                </small>
                            @endif
                            @error('user_email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                    </div>
                    <div class="col">

                        <div class="form-group">
                            <label for="barcode">{{ $dictionaryFields['barcode'] ?? 'Barcode' }}</label>
                            <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror"
                                id="barcode" placeholder="Barcode" value="{{ old('barcode') }}" maxlength="20">
                            @if(!empty($dictionaryFieldsDesc['barcode']))
                                <small class="form-text text-muted"><span
                                        class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['barcode'] }}
                                </small>
                            @endif
                            @error('barcode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- <div class="form-group">
                    <label for="discount_type">discount_type</label>
                    <select name="discount_type" id="discount_type"  class="form-control @error('discount_type') is-invalid @enderror">
                        <option value="">Select Discount Type</option>
                        <option value="percentage" {{old('discount_type') == 'percentage' ? 'selected' : ''}}>Percentage</option>
                        <option value="fixed_amount" {{old('discount_type') == 'fixed_amount' ? 'selected' : ''}}>Fixed Amount</option>
                    </select>
                    @error('discount_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div> --}}

                {{-- <div class="form-group">
                    <label for="discount">Discount</label>
                    <input type="number" name="discount" class="form-control @error('discount') is-invalid @enderror" id="discount"
                           placeholder="Discount" value="{{ old('discount') }}" >
                    @error('discount')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div> --}}
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="amount">{{ $dictionaryFields['amount'] ?? 'Amount' }}</label>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                id="amount" placeholder="Amount" value="{{ old('amount') }}">
                            @if(!empty($dictionaryFieldsDesc['amount']))
                                <small class="form-text text-muted"><span
                                        class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['amount'] }}
                                </small>
                            @endif
                            @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col">

                        <div class="form-group">
                            <label for="expire_date">{{ $dictionaryFields['expire_date'] ?? 'Expire Date' }}</label>
                            <input type="date" name="expire_date"
                                class="form-control @error('expire_date') is-invalid @enderror" id="expire_date"
                                placeholder="Expire Date" value="{{ old('expire_date') }}">
                            @if(!empty($dictionaryFieldsDesc['expire_date']))
                                <small class="form-text text-muted"><span
                                        class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['expire_date'] }}
                                </small>
                            @endif
                            @error('expire_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                           id="start_date"
                           placeholder="Start Date" value="{{ old('start_date') }}" >
                    @error('start_date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div> --}}


                {{-- <div class="form-group">
                    <label for="min_purchase">Minimum Purchase</label>
                    <input type="number" name="min_purchase"
                        class="form-control @error('min_purchase') is-invalid @enderror" id="min_purchase"
                        placeholder="Min Purchase" value="{{ old('min_purchase', 0) }}">
                    @error('min_purchase')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div> --}}

                {{-- <div class="form-group">
                    <label for="max_discount">Maximum Discount</label>
                    <input type="number" name="max_discount"
                        class="form-control @error('max_discount') is-invalid @enderror" id="max_discount"
                        placeholder="Maximum Discount" value="{{ old('max_discount') }}">
                    @error('max_discount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div> --}}
                {{--
                <div class="form-group">
                    <label for="limit">Limit</label>
                    <input type="number" name="limit" class="form-control @error('limit') is-invalid @enderror"
                        id="limit" placeholder="Limit" value="{{ old('limit') }}">
                    @error('limit')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div> --}}

                <div class="form-group">
                    <label for="status">{{ $dictionaryFields['status'] ?? 'Status' }}</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" id="status">
                        <option value="1" {{ old('status') === 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') === 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @if(!empty($dictionaryFieldsDesc['status']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['status'] }}
                        </small>
                    @endif
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                @php

                @endphp
                <input type="hidden" name="modified_by" value="{{ auth()->user()->name }}" id="">
                <button class="btn btn-success btn-block btn-lg" id="submitGiftCard" type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();


            $('#submitGiftCard').click(function(e) {
                e.preventDefault();
                var formData = new FormData($('#giftCardForm')[0]);

                $.ajax({
                    url: "{{ route('gift-cards.store') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,

                    success: function(data) {
                        toastr.success(data.success);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error:', textStatus, errorThrown);

                    }
                })
            });
        });
    </script>
@endsection
