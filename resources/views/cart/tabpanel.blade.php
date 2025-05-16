<div class="tab-content mt-2" id="myTabContent">
    <div class="tab-pane fade show active" id="quick" role="tabpanel" aria-labelledby="quick-tab">
        <div class="order-product product-section">
            <div class="row product-list" id="product-list">
                @foreach ($products as $product)
                    @if ($product->status == 1 && $product->quick_pick == 1)
                        <div class="col-md-3" style="cursor: pointer">
                            <div class="card product-item" data-barcode="{{ $product->barcode }}"
                                data-id="{{ $product->id }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-html="true" title="Product Name: {{ $product->name }}">
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ ($product->quantity < 0 || $product->quantity === 0) ? '*' : $product->quantity }}

                                </span>


                                <img src="{{ $product->image && file_exists(public_path('storage/products/' . $product->image)) ? asset('storage/products/' . $product->image) : asset('images/product-thumbnail.jpg') }}"
                                    alt="Product Image">



                                <div class="card-body">
                                    <div class="btn-products-container">
                                        <p class="card-text t">{{ Str::limit($product->name, 10) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="catgories" role="tabpanel" aria-labelledby="catgories-tab">
        <div class="order-product category-section px-3">

            <div class="mb-3">
                <label for="categoryDropdown" class="form-label">Select Category</label>
                <select id="categoryDropdown" class="form-select">
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        @if ($category->show_in_pos)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="row" id="category-products"></div>
        </div>
    </div>





</div>
