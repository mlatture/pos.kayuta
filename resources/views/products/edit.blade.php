@extends('layouts.admin')

@section('title', 'Edit Product')
@section('content-header', 'Edit Product')

@section('content')

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Product Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                    id="name" placeholder="Enter product name" value="{{ old('name', $product->name) }}" minlength="3" maxlength="15" required>
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" id="category_id" required>
                    <option value="" disabled>Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Tax Type -->
            <div class="mb-3">
                <label for="tax_type_id" class="form-label">Tax Type</label>
                <select name="tax_type_id" class="form-select @error('tax_type_id') is-invalid @enderror" id="tax_type_id" required>
                    <option value="" disabled>Select Tax Type</option>
                    @foreach ($taxTypes as $tax)
                        <option value="{{ $tax->id }}" {{ old('tax_type_id', $product->tax_type_id) == $tax->id ? 'selected' : '' }}>
                            {{ $tax->title }}
                        </option>
                    @endforeach
                </select>
                @error('tax_type_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                    id="description" placeholder="Enter short description">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Product Image -->
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image">
                @error('image')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Barcode -->
            <div class="mb-3">
                <label for="barcode" class="form-label">Barcode</label>
                <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror" 
                    id="barcode" placeholder="Enter barcode number" value="{{ old('barcode', $product->barcode) }}">
                @error('barcode')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Cost and Price -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="cost" class="form-label">Item Cost</label>
                    <input type="number" step="any" name="cost" class="form-control @error('cost') is-invalid @enderror" 
                        id="cost" placeholder="Enter item cost" value="{{ old('cost', $product->cost) }}" required>
                    @error('cost')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="price" class="form-label">Item Price</label>
                    <input type="number" step="any" name="price" class="form-control @error('price') is-invalid @enderror" 
                        id="price" placeholder="Enter item price" value="{{ old('price', $product->price) }}" required>
                    @error('price')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Quantity -->
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                    id="quantity" placeholder="Quantity" value="{{ old('quantity', $product->quantity) }}" required>
                @error('quantity')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Vendor -->
            <x-forms.input label="Vendor" placeholder="Select Product Vendor" :options="$productVendors
                ->map(fn($vendor) => ['label' => $vendor->name, 'value' => $vendor->id])
                ->toArray()"
                input-name="product_vendor_id" input-id="product_vendor_id" :required="false" 
                :value="old('product_vendor_id', $product->product_vendor_id)" type="select" />

            <!-- Status -->
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" id="status">
                    <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $product->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Discontinue Checkbox -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="discontinue" {{ old('discontinue', $product->discontinue) ? 'checked' : '' }}>
                <label class="form-check-label" for="discontinue">Discontinue This Product</label>
            </div>

            <!-- Discount Section -->
            <div class="row g-3 mb-3" id="discount-section" style="display: none;">
                <div class="col-md-6">
                    <label for="discount_type" class="form-label">Discount Type</label>
                    <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror" id="discount_type">
                        <option value="">Select Discount Type</option>
                        <option value="fixed_amount" {{ old('discount_type', $product->discount_type) == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage" {{ old('discount_type', $product->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                    @error('discount_type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="discount" class="form-label">Discount</label>
                    <input type="number" step="any" name="discount" class="form-control @error('discount') is-invalid @enderror" 
                        id="discount" placeholder="Enter discount amount" value="{{ old('discount', $product->discount) }}">
                    @error('discount')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <button class="btn btn-success btn-lg w-100 mt-3" type="submit">Save Changes</button>
        </form>
    </div>
</div>

@endsection


@section('js')
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(document).ready(function() {
        bsCustomFileInput.init();

     
        $('#discontinue').on('change', function() {
            if ($(this).is(':checked')) {
                $('#discount-section').slideDown();
            } else {
                $('#discount-section').slideUp();
            }
        });

       
        if ($('#discontinue').is(':checked')) {
            $('#discount-section').show();
        } else {
            $('#discount-section').hide();
        }
    });
</script>
@endsection