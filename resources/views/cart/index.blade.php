@extends('layouts.admin')

@section('title', 'Open POS')

@push('css')
    <link href="{!! asset('plugins/toast-master/css/jquery.toast.css') !!}" rel="stylesheet">
    <style>
        .nav-tabs .nav-link.active {
            background-color: #EFC368;
            color: #fff;
            outline: none;
            border-radius: .25rem;
        }

        .nav-tabs .nav-link {
            background-color: #041307;
            color: #fff;
            margin-right: 10px;
            border-radius: .25rem;
        }

        .nav-tabs {
            border-bottom: none;
        }

        .container-tab {
            background: #041307;
            color: #fff;
            height: 100%;
            width: 100%;
            padding: 10px;
            overflow-y: auto;
        }

        .btn-products-container {

            background-color: #EFC368;
            border: none;
            color: white;

            border-radius: .25rem;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;

        }

        .btn-products-container h5 {
            margin: 0;
            font-size: 10px;
            text-align: center;
        }
    </style>
@endpush

@section('content')
    {{-- <div id="cart"></div> --}}
    <section class="content">
        <div id="cart">
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <div class="row mb-2">
                        <div class="col">
                            <form><input type="text" class="form-control" placeholder="Scan Barcode..." value=""
                                    id="searchterm">
                            </form>
                        </div>

                        <div class="col">
                            <select class="form-control select2" name="customer_id" id="customer_id">
                                <option value="0">Walk-in Customer</option>
                                <option value="add_new_user">Add New User</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->f_name . ' ' . $customer->l_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{--                        <div class="col"> --}}
                        {{--                            <button type="button" class="btn w-100 btn-primary" data-bs-toggle="modal" --}}
                        {{--                                data-bs-target="#addUserModal">Add User</button> --}}
                        {{--                        </div> --}}
                    </div>
                    <div class="user-cart">
                        <div class="card">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Tax</th>
                                        <th class="text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $subtotal = 0;
                                        $totalDiscount = 0;
                                        $totalTax = 0;
                                    @endphp
                                    @foreach ($cart as $key => $cartItem)
                                        @php
                                            $productPrice = $cartItem->price * $cartItem->pivot->quantity;
                                            $subtotal += $productPrice;
                                            $totalDiscount += $cartItem->pivot->discount ?? 0;
                                            $totalTax += $cartItem->pivot->tax ?? 0;
                                        @endphp
                                        <tr>
                                            <td>{{ Str::limit($cartItem->name, 15) ?? '' }}</td>
                                            <td>
                                                <input type="text"
                                                    class="form-control form-control-sm qty product-quantity"
                                                    data-id="{{ $cartItem->id }}"
                                                    value="{{ $cartItem->pivot->quantity ?? 0 }}">
                                                <button class="btn btn-danger btn-sm product-delete"
                                                    data-id="{{ $cartItem->id }}">
                                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                            <td class="text-right">$
                                                {{ $cartItem->pivot->discount ? number_format($cartItem->pivot->discount, 2) : 0 }}
                                            </td>
                                            <td class="text-right">$
                                                {{ $cartItem->pivot->tax ? number_format($cartItem->pivot->tax, 2) : 0 }}
                                            </td>
                                            <td class="text-right">$
                                                {{ $productPrice ? number_format($productPrice, 2) : 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">Sub Total:</div>
                        <div class="col text-right">$ {{ number_format($subtotal, 2) }}</div>
                    </div>
                    <div class="row">
                        <div class="col">Discount:</div>
                        <div class="col text-right">$ {{ number_format($totalDiscount, 2) }}</div>
                    </div>
                    <div class="row">
                        <div class="col">Tax:</div>
                        <div class="col text-right">$ {{ number_format($totalTax, 2) }}</div>
                    </div>
                    <div class="row">
                        <div class="col">Gift Card Discount:</div>
                        <div class="col text-right show-gift-discount">$ 0</div>
                    </div>
                    <div class="row">
                        <div class="col">Total:</div>
                        <input type="hidden" class="total-amount"
                            value="{{ number_format($subtotal - $totalDiscount + $totalTax, 2) }}" id="total-amount">
                        <input type="hidden" class="subtotal-amount"
                            value="{{ number_format($subtotal - $totalDiscount + $totalTax, 2) }}" id="subtotal-amount">

                        <input type="hidden" name="gift_card_id" id="gift_card_id">
                        <input type="hidden" name="gift_card_discount" id="gift_card_discount">

                        <div class="col text-right show-total-amount">$
                            {{ number_format($subtotal - $totalDiscount + $totalTax, 2) }}</div>
                    </div>
                    <div class="row">
                        <div class="col"><button type="button"
                                class="btn btn-danger btn-block cart-empty">Cancel</button></div>
                        <div class="col"><button type="button" class="btn btn-success btn-block apply-gift-card">Apply
                                Gift
                                Card</button></div>
                        <div class="col"><button type="button"
                                class="btn btn-success btn-block submit-order">Submit</button></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6">
                    <div class="container-tab">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="quick-tab" data-bs-toggle="tab" data-bs-target="#quick"
                                    type="button" role="tab" aria-controls="quick" aria-selected="true">Quick
                                    Pick</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="catgories-tab" data-bs-toggle="tab" data-bs-target="#catgories"
                                    type="button" role="tab" aria-controls="catgories"
                                    aria-selected="false">Categories</button>
                            </li>
                            {{-- <li class="nav-item" role="presentation">
                                <button class="nav-link" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent"
                                    type="button" role="tab" aria-controls="recent" aria-selected="false">Recent</button>
                            </li> --}}
                            <div class="mb-2 mx-2"><input type="text" id="search-product" class="form-control"
                                    placeholder="Search Product...">
                            </div>
                        </ul>
                        <div class="tab-content mt-2" id="myTabContent">
                            <div class="tab-pane fade show active" id="quick" role="tabpanel"
                                aria-labelledby="quick-tab">
                                <div class="order-product product-section">

                                    <div class="row">
                                        @foreach ($products as $product)
                                            <div class="col-md-3" style="cursor: pointer">
                                                <div class="card product-item" data-barcode="{{ $product->barcode }}"
                                                    data-id="{{ $product->id }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-html="true"
                                                    title="Product Name: {{ $product->name }}">
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        {{ $product->quantity < 0 ? 0 : $product->quantity }}
                                                        <span class="visually-hidden">unread messages</span>
                                                    </span>
                                                    @php
                                                        $imagePath = 'storage/products/' . $product->image;
                                                        $fallbackImageUrl = 'images/product-thumbnail.jpg';
                                                        $imageUrl = file_exists(public_path($imagePath)) ? asset($imagePath) : asset($fallbackImageUrl);
                                                    @endphp
                                                
                                                <img src="{{ $imageUrl }}" class="rounded mx-auto d-block img-fluid" alt="Product Image">
                                                


                                                    <div class="card-body">
                                                        <div class="btn-products-container">
                                                            <p class="card-text t">{{ Str::limit($product->name, 5) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    {{-- <div class="item product-item" data-barcode="{{ $product->barcode }}"
                                        data-id="{{ $product->id }}"><img src="{{ $product->image_url }}"
                                            class="rounded mx-auto d-block" alt="Product Image">
                                      <div class="btn-products-container">
                                        <h5>{{ Str::limit($product->name, 10) }}</h5>
                                      </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="tab-pane fade" id="catgories" role="tabpanel" aria-labelledby="catgories-tab">
                                <div class="order-product  ">
                                    @foreach ($categories as $category)
                                        <div class="item category-item" data-id="{{ $category->id }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ $category->name }}"><img
                                                src="{{ asset('images/product-thumbnail.jpg') }}"
                                                class="rounded mx-auto d-block" alt="Product Image">
                                            <div class="btn-products-container">
                                                <h5>{{ Str::limit($category->name, 10) }} </h5>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- <div class="tab-pane fade" id="recent" role="tabpanel" aria-labelledby="recent-tab">...</div> --}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- user add modal starts here --}}
    @include('cart.modals.user-add-modal')
@endsection

@push('js')
    <script src="{!! asset('plugins/toast-master/js/jquery.toast.js') !!}"></script>

    <script>
        // document.addEventListener('DOMContentLoaded', function () {
        //     // Initialize all tooltips on the page
        //     var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        //     var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        //         return new bootstrap.Tooltip(tooltipTriggerEl);
        //     });
        // });
        function limitText(text, maxLength) {
            if (text.length > maxLength) {
                return text.substring(0, maxLength) + "..."; // Add ellipsis (...) to indicate the text has been truncated
            } else {
                return text;
            }
        }

        function storeCart(barcode = null, productId = null) {
            let data = {};
            if (barcode) {
                data.barcode = barcode;
            } else if (productId) {
                data.product_id = productId;
            }
            $.ajax({
                url: '{!! route('cart.store') !!}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data,
                success: function(response) {
                    $.toast({
                        heading: 'Success',
                        text: response.message,
                        position: 'top-right',
                        // bgColor: '#FF1356',
                        loaderBg: '#00c263',
                        icon: 'success',
                        hideAfter: 2000,
                        stack: 6
                    });
                    window.location.reload();
                },
                error: function(reject) {
                    if (reject.status === 422) {
                        var errors = $.parseJSON(reject.responseText);
                        $.each(errors.errors, function(key, val) {
                            $.toast({
                                heading: 'Error',
                                text: val,
                                position: 'top-right',
                                // bgColor: '#FF1356',
                                loaderBg: '#a94442',
                                icon: 'error',
                                hideAfter: 4000,
                                stack: 6
                            });
                        });
                    }
                    if (reject.status === 401) {
                        var errors = $.parseJSON(reject.responseText);
                        $.toast({
                            heading: 'Error',
                            text: errors.message,
                            position: 'top-right',
                            // bgColor: '#FF1356',
                            loaderBg: '#a94442',
                            icon: 'error',
                            hideAfter: 4000,
                            stack: 6
                        });
                    }

                    if (reject.status === 400) {
                        var errors = $.parseJSON(reject.responseText);
                        $.each(errors.errors, function(key, val) {
                            $.toast({
                                heading: 'Error',
                                text: val,
                                position: 'top-right',
                                // bgColor: '#FF1356',
                                loaderBg: '#a94442',
                                icon: 'error',
                                hideAfter: 4000,
                                stack: 6
                            });
                        });
                    }
                }
            });
        }

        var addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));

        $(document).ready(function() {
            // $(".select2").select2();
            $("#customer_id").select2();

            $("#customer_id").on('change', function(e) {
                if (e.target.value === 'add_new_user') {
                    addUserModal.show();
                }
            });


            // $('.select2').selectize({
            //     sortField: 'text'
            // });

            var isFocused = false;
            var isSelectFocused = false;
            $(document).on('focusin', 'input, textarea', function() {
                isFocused = true;
                console.log('focused');
            });
            $(document).on('focusout', 'input, textarea', function() {
                isFocused = false;
                console.log('unfocused');
            });

            $(".select2").on('select2:open', function(e) {
                isSelectFocused = true;
            });
            $(".select2").on('select2:close', function(e) {
                isSelectFocused = false;
            });

            var storeCartTimeout = null;
            $(window).keypress(function(event) {
                if (!isFocused && !isSelectFocused) {
                    if (storeCartTimeout) {
                        clearTimeout(storeCartTimeout);
                    }
                    // Process barcode input if no form field element is focused
                    var barcode = String.fromCharCode(event.which);
                    // Do something with the barcode value
                    var code = event.which || event.keyCode;
                    var character = String.fromCharCode(code);
                    barcode += character;
                    var currentValue = $('#searchterm').val();
                    if (currentValue == null) {
                        currentValue = '';
                    }
                    $('#searchterm').val(currentValue + character);
                    storeCartTimeout = setTimeout(function() {
                        storeCart(currentValue + character);
                    }, 500)
                }
            });

            $(document).on('click', '.product-item', function() {
                var barcode = $(this).data('barcode');
                var productId = $(this).data('id');
                storeCart(barcode, productId);
            });

            $(document).on('change', '.product-quantity', function() {
                var productId = $(this).data('id');
                var qty = $(this).val();

                $.ajax({
                    url: '{!! route('cart.changeQty') !!}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        product_id: productId,
                        quantity: qty
                    },
                    success: function(response) {
                        $.toast({
                            heading: 'Success',
                            text: response.message,
                            position: 'top-right',
                            // bgColor: '#FF1356',
                            loaderBg: '#00c263',
                            icon: 'success',
                            hideAfter: 2000,
                            stack: 6
                        });
                        window.location.reload();
                    },
                    error: function(reject) {
                        if (reject.status === 422) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                        if (reject.status === 401) {
                            var errors = $.parseJSON(reject.responseText);
                            $.toast({
                                heading: 'Error',
                                text: errors.message,
                                position: 'top-right',
                                // bgColor: '#FF1356',
                                loaderBg: '#a94442',
                                icon: 'error',
                                hideAfter: 4000,
                                stack: 6
                            });
                        }

                        if (reject.status === 400) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.product-delete', function() {
                var productId = $(this).data('id');

                $.ajax({
                    url: '{!! route('cart.delete') !!}',
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        product_id: productId,
                    },
                    success: function(response) {
                        $.toast({
                            heading: 'Success',
                            text: response.message,
                            position: 'top-right',
                            // bgColor: '#FF1356',
                            loaderBg: '#00c263',
                            icon: 'success',
                            hideAfter: 2000,
                            stack: 6
                        });
                        window.location.reload();
                    },
                    error: function(reject) {
                        if (reject.status === 422) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                        if (reject.status === 401) {
                            var errors = $.parseJSON(reject.responseText);
                            $.toast({
                                heading: 'Error',
                                text: errors.message,
                                position: 'top-right',
                                // bgColor: '#FF1356',
                                loaderBg: '#a94442',
                                icon: 'error',
                                hideAfter: 4000,
                                stack: 6
                            });
                        }

                        if (reject.status === 400) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.cart-empty', function() {
                $.ajax({
                    url: '{!! route('cart.empty') !!}',
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {},
                    success: function(response) {
                        $.toast({
                            heading: 'Success',
                            text: response.message,
                            position: 'top-right',
                            // bgColor: '#FF1356',
                            loaderBg: '#00c263',
                            icon: 'success',
                            hideAfter: 2000,
                            stack: 6
                        });
                        window.location.reload();
                    },
                    error: function(reject) {
                        if (reject.status === 422) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                        if (reject.status === 401) {
                            var errors = $.parseJSON(reject.responseText);
                            $.toast({
                                heading: 'Error',
                                text: errors.message,
                                position: 'top-right',
                                // bgColor: '#FF1356',
                                loaderBg: '#a94442',
                                icon: 'error',
                                hideAfter: 4000,
                                stack: 6
                            });
                        }

                        if (reject.status === 400) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.category-item', function() {
                var category_id = $(this).data('id');
                $.ajax({
                    url: '{!! route('category.products') !!}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        category_id: category_id,
                    },
                    success: function(response) {
                        let products = response.response.data;

                        if (products.length > 0) {
                            var html = '';
                            $.each(products, function(key, val) {
                                console.log(val.barcode)
                                html += `<div class="item product-item" data-barcode="${val.barcode}" data-id="${val.id}" ><img
                                            src="${val.image_url}" class="rounded mx-auto d-block"
                                            alt="Product Image">
                                        <h5>${limitText(val.name, 10)} (${val.quantity})</h5>
                                    </div>`;
                            });
                            $(".category-section").html(html);
                        } else {
                            html = `<div class="col-md-6">
                                        <h2 class="font-weight-lighter">
                                            No Products
                                        </h2>
                                    </div>`;
                            $(".category-section").html(html);
                        }
                    },
                    error: function(reject) {
                        if (reject.status === 422) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                        if (reject.status === 401) {
                            var errors = $.parseJSON(reject.responseText);
                            $.toast({
                                heading: 'Error',
                                text: errors.message,
                                position: 'top-right',
                                // bgColor: '#FF1356',
                                loaderBg: '#a94442',
                                icon: 'error',
                                hideAfter: 4000,
                                stack: 6
                            });
                        }

                        if (reject.status === 400) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                    }
                });
            });

            $(document).on('change', '#search-product', function() {
                var search = $(this).val();
                $.ajax({
                    url: '{!! route('category.products') !!}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        search: search,
                    },
                    success: function(response) {
                        let products = response.response.data;

                        if (products.length > 0) {
                            var html = '';
                            $.each(products, function(key, val) {

                                html += `<div class="item product-item" data-barcode="${val.barcode}" data-id="${val.id}" ><img
                                            src="${val.image_url}" class="rounded mx-auto d-block"
                                            alt="Product Image">
                                        <h5>${limitText(val.name, 10)} (${val.quantity})</h5>
                                    </div>`;
                            });
                            $(".product-section").html(html);
                        }
                    },
                    error: function(reject) {
                        if (reject.status === 422) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                        if (reject.status === 401) {
                            var errors = $.parseJSON(reject.responseText);
                            $.toast({
                                heading: 'Error',
                                text: errors.message,
                                position: 'top-right',
                                // bgColor: '#FF1356',
                                loaderBg: '#a94442',
                                icon: 'error',
                                hideAfter: 4000,
                                stack: 6
                            });
                        }

                        if (reject.status === 400) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                    }
                });
            });

            $(document).on('click', '#catgories-tab', function() {
                $.ajax({
                    url: '{!! route('category.all') !!}',
                    type: 'GET',
                    success: function(response) {
                        let categories = response.response.data;
                        if (categories.length > 0) {
                            var html = '';
                            $.each(categories, function(key, val) {
                                html += `<div class="item category-item" data-id="${val.id}}}"><img
                                            src="{{ asset('images/product-thumbnail.jpg') }}"
                                            class="rounded mx-auto d-block" alt="Product Image">
                                        <h5>${limitText(val.name, 10)}</h5>
                                    </div>`;
                            });
                            $(".category-section").html(html);
                        }
                    },
                    error: function(reject) {
                        if (reject.status === 422) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                        if (reject.status === 401) {
                            var errors = $.parseJSON(reject.responseText);
                            $.toast({
                                heading: 'Error',
                                text: errors.message,
                                position: 'top-right',
                                // bgColor: '#FF1356',
                                loaderBg: '#a94442',
                                icon: 'error',
                                hideAfter: 4000,
                                stack: 6
                            });
                        }

                        if (reject.status === 400) {
                            var errors = $.parseJSON(reject.responseText);
                            $.each(errors.errors, function(key, val) {
                                $.toast({
                                    heading: 'Error',
                                    text: val,
                                    position: 'top-right',
                                    // bgColor: '#FF1356',
                                    loaderBg: '#a94442',
                                    icon: 'error',
                                    hideAfter: 4000,
                                    stack: 6
                                });
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.submit-order', function() {
                var totalAmount = $("#total-amount").val();
                Swal.fire({
                    title: "Enter Order Amount",
                    input: "text",
                    inputAttributes: {
                        autocapitalize: "off"
                    },
                    showCancelButton: true,
                    confirmButtonText: "Submit",
                    showLoaderOnConfirm: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return "Please Enter Amount!";
                        }
                    },
                    // showLoaderOnConfirm: true,
                    // preConfirm: async (amount) => {

                    // },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {

                        let amount = result.value;
                        let customer_id = $('#customer_id').val();
                        let gift_card_id = $('#gift_card_id').val();
                        let gift_card_discount = $('#gift_card_discount').val();

                        let change = totalAmount - amount;
                        if ((change) < 0) {
                            change = change * -1;
                        } else {
                            change = 0;
                        }

                        Swal.fire({
                            title: "Change is : " + change.toFixed(2) +
                                ". Do you want to proceed?",
                            showDenyButton: true,
                            showCancelButton: true,
                            confirmButtonText: "Save",
                            denyButtonText: `Don't save`,
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                return new Promise((resolve) => {
                                    $.ajax({
                                        url: '{!! route('orders.store') !!}',
                                        type: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': $(
                                                'meta[name="csrf-token"]'
                                            ).attr('content')
                                        },
                                        data: {
                                            amount: amount,
                                            customer_id: customer_id,
                                            gift_card_id: gift_card_id,
                                            gift_card_discount: gift_card_discount
                                        },
                                        success: function(response) {
                                            resolve(response);
                                        },
                                        error: function(reject) {
                                            // Error handling code
                                            resolve(reject);
                                        }
                                    });
                                });
                            }
                        }).then((result) => {
                            // if (result.isConfirmed) {
                            //     $.ajax({
                            //         url: '{!! route('orders.store') !!}',
                            //         type: 'POST',
                            //         headers: {
                            //             'X-CSRF-TOKEN': $(
                            //                 'meta[name="csrf-token"]').attr(
                            //                 'content')
                            //         },
                            //         data: {
                            //             amount: amount,
                            //             customer_id: customer_id,
                            //             gift_card_id: gift_card_id,
                            //             gift_card_discount: gift_card_discount
                            //         },
                            //         success: function(response) {
                            //             $.toast({
                            //                 heading: 'Success',
                            //                 text: response.message,
                            //                 position: 'top-right',
                            //                 // bgColor: '#FF1356',
                            //                 loaderBg: '#00c263',
                            //                 icon: 'success',
                            //                 hideAfter: 2000,
                            //                 stack: 6
                            //             });
                            //             window.location.reload();
                            //         },
                            //         error: function(reject) {
                            //             if (reject.status === 422) {
                            //                 var errors = $.parseJSON(reject
                            //                     .responseText);
                            //                 $.each(errors.errors, function(
                            //                     key, val) {
                            //                     $.toast({
                            //                         heading: 'Error',
                            //                         text: val,
                            //                         position: 'top-right',
                            //                         // bgColor: '#FF1356',
                            //                         loaderBg: '#a94442',
                            //                         icon: 'error',
                            //                         hideAfter: 4000,
                            //                         stack: 6
                            //                     });
                            //                 });
                            //             }
                            //             if (reject.status === 401) {
                            //                 var errors = $.parseJSON(reject
                            //                     .responseText);
                            //                 $.toast({
                            //                     heading: 'Error',
                            //                     text: errors
                            //                         .message,
                            //                     position: 'top-right',
                            //                     // bgColor: '#FF1356',
                            //                     loaderBg: '#a94442',
                            //                     icon: 'error',
                            //                     hideAfter: 4000,
                            //                     stack: 6
                            //                 });
                            //             }

                            //             if (reject.status === 400) {
                            //                 var errors = $.parseJSON(reject
                            //                     .responseText);
                            //                 $.each(errors.errors, function(
                            //                     key, val) {
                            //                     $.toast({
                            //                         heading: 'Error',
                            //                         text: val,
                            //                         position: 'top-right',
                            //                         // bgColor: '#FF1356',
                            //                         loaderBg: '#a94442',
                            //                         icon: 'error',
                            //                         hideAfter: 4000,
                            //                         stack: 6
                            //                     });
                            //                 });
                            //             }
                            //         }
                            //     });
                            // } else if (result.isDenied) {
                            //     Swal.fire("Changes are not saved", "", "info");
                            // }

                            if (result.isConfirmed && result.value) {
                                $.toast({
                                    heading: 'Success',
                                    text: result.value.message,
                                    position: 'top-right',
                                    loaderBg: '#00c263',
                                    icon: 'success',
                                    hideAfter: 2000,
                                    stack: 6
                                });
                                window.location.reload();
                            } else if (result.isDenied) {
                                Swal.fire("Changes are not saved", "", "info");
                            }
                        });


                        // Swal.fire({
                        //     title: `${result.value.login}'s avatar`,
                        //     imageUrl: result.value.avatar_url
                        // });
                    }
                });
            });

            $(document).on('click', '.apply-gift-card', function() {
                var customer_id = $("#customer_id").val();

                Swal.fire({
                    title: "Enter Gift Card",
                    input: "text",
                    inputAttributes: {
                        autocapitalize: "off"
                    },
                    showCancelButton: true,
                    confirmButtonText: "Submit",
                    showLoaderOnConfirm: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return "Please Enter Gift Card!";
                        }
                    },
                    preConfirm: async (code) => {
                        try {
                            const apiUrl = '{!! route('gift-cards.apply') !!}';

                            const requestBody = {
                                customer_id: customer_id,
                                code: code
                            };

                            const response = await fetch(apiUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': $(
                                        'meta[name="csrf-token"]'
                                    ).attr('content')
                                },
                                body: JSON.stringify(requestBody),
                            });

                            if (!response.ok) {
                                const responseJson = await response.json()
                                console.log(responseJson);
                                return Swal.showValidationMessage(`${responseJson.message}`);
                            }
                            return response.json();

                        } catch (error) {
                            Swal.showValidationMessage(`
                                Request failed: ${error}
                            `);
                        }
                        // return new Promise((resolve) => {
                        //     $.ajax({
                        //         url: '{!! route('gift-cards.apply') !!}',
                        //         type: 'POST',
                        //         headers: {
                        //             'X-CSRF-TOKEN': $(
                        //                 'meta[name="csrf-token"]'
                        //             ).attr('content')
                        //         },
                        //         data: {

                        //         },
                        //         success: function(response) {
                        //             resolve(response);
                        //         },
                        //         error: function(reject) {
                        //             // Error handling code
                        //             Swal.getConfirmButton();
                        //             Swal.enableButtons();
                        //             resolve(reject);
                        //         }
                        //     });
                        // });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const response = result.value;
                        console.log(response);
                        $('#gift_card_discount').val(response.response.data.gift_card_discount);
                        $('#gift_card_id').val(response.response.data.gift_card.id);
                        $('.show-gift-discount').text('$ ' + response.response.data
                            .gift_card_discount);
                        var totalamount = $('#subtotal-amount').val() - response.response.data
                            .gift_card_discount;
                        $('#total-amount').val(totalamount.toFixed(2));
                        $('.show-total-amount').text('$ ' + totalamount.toFixed(2));

                        console.log(result);
                        $.toast({
                            heading: 'Success',
                            text: result.value.message,
                            position: 'top-right',
                            loaderBg: '#00c263',
                            icon: 'success',
                            hideAfter: 2000,
                            stack: 6
                        });

                    } else if (result.isDenied) {
                        Swal.fire("Changes are not saved", "", "info");
                    }
                });
            });

        });
    </script>
@endpush
