@extends('layouts.admin')

@section('title', 'Edit Product')
@section('content-header', 'Edit Product')

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">{{ $dictionaryFields['name'] ?? 'Name' }}</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           id="name" placeholder="Name" value="{{ old('name', $product->name) }}" minlength="3"
                           maxlength="15" required>
                    @if(!empty($dictionaryFieldsDesc['name']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['name'] }}
                        </small>
                    @endif
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category_id">{{ $dictionaryFields['category'] ?? 'Category' }}</label>
                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror"
                            id="category_id">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @if(!empty($dictionaryFieldsDesc['category_id']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['category_id'] }}
                        </small>
                    @endif
                    @error('category_id')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tax_type_id">{{ $dictionaryFields['tax_type_id'] ?? 'Tax Type' }}</label>
                    <select name="tax_type_id" class="form-control @error('tax_type_id') is-invalid @enderror"
                            id="tax_type_id">
                        <option value="">Select Tax Type</option>
                        @foreach ($taxTypes as $tax)
                            <option value="{{ $tax->id }}" {{ $product->tax_type_id === $tax->id ? 'selected' : '' }}>
                                {{ $tax->title }}</option>
                        @endforeach
                    </select>
                    @if(!empty($dictionaryFieldsDesc['tax_type_id']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['tax_type_id'] }}
                        </small>
                    @endif
                    @error('tax_type_id')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="description">{{ $dictionaryFields['description'] ?? 'Description' }}</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                              id="description"
                              placeholder="description">{{ old('description', $product->description) }}</textarea>
                    @if(!empty($dictionaryFieldsDesc['description']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['description'] }}
                        </small>
                    @endif
                    @error('description')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="image">{{ $dictionaryFields['image'] ?? 'Image' }}</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image" id="image">
                        <label class="custom-file-label" for="image">Choose file</label>
                    </div>
                    @if(!empty($dictionaryFieldsDesc['image']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['image'] }}
                        </small>
                    @endif
                    @error('image')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="barcode">{{ $dictionaryFields['barcode'] ?? 'Barcode' }}</label>
                    <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror"
                           id="barcode" placeholder="barcode" value="{{ old('barcode', $product->barcode) }}">
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

                <div class="form-group">
                    <label for="price">{{ $dictionaryFields['price'] ?? 'Price' }}</label>
                    <input type="text" name="price" class="form-control @error('price') is-invalid @enderror"
                           id="price" placeholder="price" value="{{ old('price', $product->price) }}" required>
                    @if(!empty($dictionaryFieldsDesc['price']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['price'] }}
                        </small>
                    @endif
                    @error('price')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="quantity">{{ $dictionaryFields['quantity'] ?? 'Quantity' }}</label>
                    <input type="text" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                           id="quantity" placeholder="Quantity" value="{{ old('quantity', $product->quantity) }}"
                           required>
                    @if(!empty($dictionaryFieldsDesc['quantity']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['quantity'] }}
                        </small>
                    @endif
                    @error('quantity')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <x-forms.input label="Vendor" placeholder="Select Product Vendor"
                               :options="$productVendors->map(fn($vendor) => ['label' => $vendor->name,'value' => $vendor->id])->toArray()"
                               input-name="product_vendor_id" input-id="product_vendor_id" :required="false"
                               :value="old('product_vendor_id', $product->product_vendor_id)" type="select"
                />

                <div class="form-group">
                    <label for="discount_type">{{ $dictionaryFields['discount_type'] ?? 'Discount Type' }}</label>
                    <select name="discount_type" class="form-control @error('discount_type') is-invalid @enderror"
                            id="discount_type">
                        <option value="">Select Discount Type</option>
                        <option value="fixed_amount" {{ $product->discount_type === 'fixed_amount' ? 'selected' : '' }}>
                            Fixed Amount
                        </option>
                        <option value="percentage" {{ $product->discount_type === 'percentage' ? 'selected' : '' }}>
                            Percentage
                        </option>
                    </select>
                    @if(!empty($dictionaryFieldsDesc['discount_type']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['discount_type'] }}
                        </small>
                    @endif
                    @error('discount_type')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="discount">{{ $dictionaryFields['discount'] ?? 'Discount' }}</label>
                    <input type="number" name="discount" class="form-control @error('discount') is-invalid @enderror"
                           id="discount" placeholder="Enter Discount" value="{{ old('discount', $product->discount) }}">
                    @if(!empty($dictionaryFieldsDesc['discount']))
                        <small class="form-text text-muted"><span
                                class="fas fa-info-circle"></span> {{ $dictionaryFieldsDesc['discount'] }}
                        </small>
                    @endif
                    @error('discount')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">{{ $dictionaryFields['status'] ?? 'Status' }}</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" id="status">
                        <option value="1" {{ old('status', $product->status) === 1 ? 'selected' : '' }}>Active
                        </option>
                        <option value="0" {{ old('status', $product->status) === 0 ? 'selected' : '' }}>Inactive
                        </option>
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
