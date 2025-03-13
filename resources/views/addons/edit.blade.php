@extends('layouts.admin')

@section('title', 'Edit Addon')
@section('content-header', 'Edit Addon')

@section('content')
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <div class="container">
                <form action="{{ route('addons.update', $addon->id) }}" method="POST" >
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="addon_name">Add On Name</label>
                        <input type="text" name="addon_name" class="form-control @error('addon_name') is-invalid @enderror"
                            id="addon_name" value="{{ old('addon_name', $addon->addon_name) }}">
                        @error('addon_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="text" name="price" class="form-control @error('price') is-invalid @enderror"
                            id="sitename" value="{{ old('price', $addon->price) }}">
                        @error('price')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>




                    <div class="form-group">
                        <label for="addon_type">Add On Type</label>

                        <input type="text" name="addon_type"
                            class="form-control @error('addon_type') is-invalid @enderror" id="addon_type"
                            value="{{ old('addon_type', $addon->addon_type) }}">
                        @error('addon_type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>



                    <div class="form-group">
                        <label for="capacity">Capacity</label>

                        <input type="number" name="capacity"
                        class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                        value="{{ old('capacity', $addon->capacity) }}">
                        @error('capacity')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
            




                  
                    



                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-secondary" type="reset">Reset</button>
                        <button class="btn btn-success" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
