@extends('layouts.admin')

@section('title', 'Update Customer')
@section('content-header', 'Update Customer')

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="first_name">{{ $dictionaryFields['first_name'] ?? 'First Name' }}</label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                           id="first_name"
                           placeholder="First Name" value="{{ old('first_name', $customer->f_name) }}" required>
                    @if(!empty($dictionaryFieldsDesc['first_name']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['first_name'] }}
                        </small>
                    @endif
                    @error('first_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_name">{{ $dictionaryFields['last_name'] ?? 'Last Name' }}</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                           id="last_name"
                           placeholder="Last Name" value="{{ old('last_name', $customer->l_name) }}" required>
                    @if(!empty($dictionaryFieldsDesc['last_name']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['last_name'] }}
                        </small>
                    @endif
                    @error('last_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">{{ $dictionaryFields['email'] ?? 'Email' }}</label>
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" id="email"
                           placeholder="Email" value="{{ old('email', $customer->email) }}" readonly>
                    @if(!empty($dictionaryFieldsDesc['email']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['email'] }}
                        </small>
                    @endif
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">{{ $dictionaryFields['phone'] ?? 'Phone' }}</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone"
                           placeholder="Phone" value="{{ old('phone', $customer->phone) }}">
                    @if(!empty($dictionaryFieldsDesc['phone']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['phone'] }}
                        </small>
                    @endif
                    @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">{{ $dictionaryFields['address'] ?? 'Address' }}</label>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                           id="address"
                           placeholder="Address" value="{{ old('address', $customer->street_address) }}">
                    @if(!empty($dictionaryFieldsDesc['address']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['address'] }}
                        </small>
                    @endif
                    @error('address')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="avatar">{{ $dictionaryFields['avatar'] ?? 'Avatar' }}</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="avatar" id="avatar">
                        <label class="custom-file-label" for="avatar">Choose file</label>
                    </div>
                    @if(!empty($dictionaryFieldsDesc['avatar']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['avatar'] }}
                        </small>
                    @endif
                    @error('avatar')
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
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
@endsection
