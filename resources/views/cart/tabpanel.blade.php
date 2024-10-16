<div class="tab-content mt-2" id="myTabContent">
    <div class="tab-pane fade show active" id="quick" role="tabpanel" aria-labelledby="quick-tab">
        <div class="order-product product-section">
            <div class="row product-list" id="product-list">
                @foreach ($products as $product)
                    @if ($product->quantity != 0)
                        <div class="col-md-3" style="cursor: pointer">
                            <div class="card product-item" data-barcode="{{ $product->barcode }}"
                                data-id="{{ $product->id }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-html="true" title="Product Name: {{ $product->name }}">
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $product->quantity < 0 ? '*' : $product->quantity }}

                                </span>
                                @php
                                    $imagePath = 'images/products/' . $product->image;
                                    $fallbackImageUrl = asset('images/product-thumbnail.jpg');

                                    if (!empty($product->image) && file_exists(public_path($imagePath))) {
                                        $imageUrl = asset($imagePath);
                                    } else {
                                        $imageUrl = $fallbackImageUrl;
                                    }
                                @endphp

                                <img src="{{ $imageUrl }}" class="rounded mx-auto d-block img-fluid"
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
        <div class="order-product category-section">
            <div class="row">
                @foreach ($categories as $category)
                    <div class="col-md-3" style="cursor: pointer">
                        <div class="card category-item" data-id="{{ $category->id }}" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="{{ $category->name }}">
                            <img src="{{ asset('images/product-thumbnail.jpg') }}"
                                class="rounded mx-auto d-block img-fluid" alt="Product Image">


                            <div class="card-body">
                                <div class="btn-products-container">
                                    <p class="card-text t">{{ Str::limit($category->name, 10) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>



    {{-- 
    <div class="tab-pane fade show active" id="quick" role="tabpanel" aria-labelledby="quick-tab" >
        <div class="order-product category-product-section">
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-md-3" style="cursor: pointer">
                        <div class="card category-product-item" data-barcode="{{ $product->barcode }}"
                            data-id="{{ $product->id }}" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-html="true" title="Product Name: {{ $product->name }}">
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $product->quantity < 0 ? 0 : $product->quantity }}
                               
                            </span>
                            @php
                                $imagePath = 'storage/products/' . $product->image;
                                $fallbackImageUrl = 'images/product-thumbnail.jpg';
                                $imageUrl = file_exists(public_path($imagePath))
                                    ? asset($imagePath)
                                    : asset($fallbackImageUrl);
                            @endphp

                            <img src="{{ $imageUrl }}" class="rounded mx-auto d-block img-fluid"
                                alt="Product Image">



                            <div class="card-body">
                                <div class="btn-products-container">
                                    <p class="card-text t">{{ Str::limit($product->name, 10) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div> --}}
</div>
