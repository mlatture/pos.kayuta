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
        @include('cart.components.header')
        <div id="cart">
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <div class="row mb-2">
                        <div class="col">
                            <form>
                                <input type="text" class="form-control" placeholder="Scan Barcode..." value=""
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
                   @include('cart.summary')
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
                    @include('cart.tabpanel')
                   
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
        var cartStoreUrl = "{{ route('cart.store') }}";
        var cartChangeUrl = "{{ route('cart.changeQty') }}"
        var cartDeleteUrl = "{{ route('cart.delete') }}"
        var cartEmptyUrl = "{{ route('cart.empty') }}"
        var cartCategoryUrl = "{{ route('category.products') }}";
        var cartAllCategoryUrl = "{{ route('category.all') }}"
        var cartOrderStoreUrl = "{{ route('orders.store') }}"
        var giftCard = "{{ route('gift-cards.apply') }}"
        var addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
        // function limitText(text, maxLength) {
        //     if (text.length > maxLength) {
        //         return text.substring(0, maxLength) + "..."; 
        //         return text;
        //     }
        // }

     
    </script>
@endpush
