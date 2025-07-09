@extends('layouts.admin')

@section('title', 'Edit Product')
@section('content-header', 'Edit Product')

@section('content')

    <div class="card shadow-sm mb-4 overflow-auto" style="max-height: 80vh;">
        <div class="card-body">

            <form enctype="multipart/form-data" id="editProductForm">
                @csrf

                <input type="hidden" name="id" value="{{ $product->id }}">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        id="name" placeholder="Name" value="{{ old('name', $product->name) }}" minlength="3" required>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror"
                        id="category_id">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tax_type_id">Tax Type</label>
                    <select name="tax_type_id" class="form-control @error('tax_type_id') is-invalid @enderror"
                        id="tax_type_id">
                        <option value="">Select Tax Type</option>
                        @foreach ($taxTypes as $tax)
                            <option value="{{ $tax->id }}" {{ $product->tax_type_id === $tax->id ? 'selected' : '' }}>
                                {{ $tax->title }}</option>
                        @endforeach
                    </select>
                    @error('tax_type_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description"
                        placeholder="description">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image"
                        id="image">
                    @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="barcode">Barcode</label>
                    <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror"
                        id="barcode" placeholder="barcode" value="{{ old('barcode', $product->barcode) }}">
                    @error('barcode')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="cost" class="form-label">Item Cost</label>
                        <input type="number" step="any" name="cost"
                            class="form-control 
                        @error('cost') is-invalid @enderror" id="cost"
                            placeholder="Item Cost" value="{{ old('cost', $product->cost) }}" required>
                        @error('cost')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="price" class="form-label">Item Price</label>
                        <input type="number" step="any" name="price"
                            class="form-control @error('price') is-invalid @enderror" id="price"
                            placeholder="Item Price" value="{{ old('price', $product->price) }}" required>
                        @error('price')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>


                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="text" name="price" class="form-control @error('price') is-invalid @enderror"
                        id="price" placeholder="price" value="{{ old('price', $product->price) }}" required>
                    @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="text" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                        id="quantity" placeholder="Quantity" value="{{ old('quantity', $product->quantity) }}"
                        required>
                    @error('quantity')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <x-forms.input label="Vendor" placeholder="Select Product Vendor" :options="$productVendors
                    ->map(fn($vendor) => ['label' => $vendor->name, 'value' => $vendor->id])
                    ->toArray()"
                    input-name="product_vendor_id" input-id="product_vendor_id" :required="false" :value="old('product_vendor_id', $product->product_vendor_id)"
                    type="select" />

                <div class="form-group">
                    <label for="discount_type">Discount Type</label>
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
                    @error('discount_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="discount">Discount</label>
                    <input type="number" name="discount" class="form-control @error('discount') is-invalid @enderror"
                        id="discount" placeholder="Enter Discount" value="{{ old('discount', $product->discount) }}">
                    @error('discount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" id="status">
                        <option value="1" {{ old('status', $product->status) === 1 ? 'selected' : '' }}>Active
                        </option>
                        <option value="0" {{ old('status', $product->status) === 0 ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="">Turn on to allow this product as an add-on when a guest is making a
                        booking.</label>
                    <input type="checkbox" class="form-check-control suggested-addon-toggle"
                        data-id="{{ $product->id }}" {{ $product->suggested_addon ? 'checked' : '' }}>

                </div>

                <button class="btn btn-success btn-block btn-lg" id="save-changes" type="submit">Save Changes</button>
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


        $('#save-changes').click(function(e) {
            e.preventDefault();

            var formData = new FormData($("#editProductForm")[0]);
            console.log({{ $product->id }});

            $.ajax({
                url: `{{ route('products.update') }}`,
                type: 'post',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#save-changes').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                        });

                        setTimeout(() => {
                            window.location.href = "{{ route('products.index') }}";
                        }, 2000);

                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    $('.invalid-feedback').remove();
                    $('.is-invalid').removeClass('is-invalid');

                    $.each(errors, function(field, messages) {
                        let input = $('[name"' + field + '"]');
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedbacl">' + messages[0] + '</div>');
                    });
                },
                complete: function() {
                    $('#save-changes').prop('disabled', false).text('Save Changes');
                }



            });
        });


        $(document).on('change', '.suggested-addon-toggle', function() {
            const isChecked = $(this).is(':checked');
            const productId = $(this).data('id');

            $.ajax({
                url: "{{ route('products.toggle-suggested-addon') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: productId,
                    suggested_addon: isChecked ? 1 : 0
                },
                success: function(response) {
                    const $badge = $(`.suggested-addon-badge[data-id="${productId}"]`);

                    if (isChecked) {
                        $badge.removeClass('badge-secondary').addClass('badge-success').text('Yes');
                    } else {
                        $badge.removeClass('badge-success').addClass('badge-secondary').text('No');
                    }


                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            });
        });
    </script>
@endsection
