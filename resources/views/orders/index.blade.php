@extends('layouts.admin')

@section('title', 'Orders List')
@section('content-header', 'Order List')
@section('content-actions')
@hasPermission(config('constants.role_modules.pos_management.value'))
<a href="{{route('cart.index')}}" class="btn btn-success">Open POS</a>
@endHasPermission
@endsection

@section('content')
<div class="card"><!-- Log on to codeastro.com for more projects -->
    <div class="card-body">
        <div class="row">
            <!-- <div class="col-md-3"></div> -->
            <div class="col-md-12">
                <form action="{{route('orders.index')}}">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="date" name="start_date" class="form-control"
                                value="{{request('start_date')}}" />
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <hr>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th>Remain.</th>
                    <th>Payment Method</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->getCustomerName() }}</td>
                        <td>{{ config('settings.currency_symbol') }} {{ number_format(is_numeric($order->formattedTotal()) ? $order->formattedTotal() : 0, 2) }}</td>
                        <td>{{ config('settings.currency_symbol') }} {{ number_format(is_numeric($order->formattedReceivedAmount()) ? $order->formattedReceivedAmount() : 0, 2) }}</td>
                        <td>
                            @if(number_format($order->receivedAmount(), 2) == 0)
                                <span class="badge badge-danger">Not Paid</span>
                            @elseif(number_format($order->receivedAmount(), 2) < number_format($order->formattedTotal(), 2))
                                <span class="badge badge-warning">Partial</span>
                            @elseif(number_format($order->receivedAmount(), 2) == number_format($order->formattedTotal(), 2))
                                <span class="badge badge-success">Paid</span>
                            @elseif(number_format($order->receivedAmount(), 2) > number_format($order->formattedTotal(), 2))
                                <span class="badge badge-info">Change</span>
                            @endif
                        </td>
                        <td>{{ config('settings.currency_symbol') }}
                            {{ number_format($order->formattedTotal() - $order->receivedAmount(), 2) }}
                        </td>
                        @foreach($order->posPayments as $payment)
                            <td class="paymentMethod" data-payment_acc_num="{{ $payment->payment_acc_number }}" data-paymentmethod="{{ $payment->payment_method }}">
                                {{ $payment->payment_method }}
                            </td>
                        @endforeach
                        <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <a href="{!! route('orders.generate.invoice', $order->id) !!}" class="label label-info" target="_blank">
                                <i class="fas fa-file-alt"></i>
                            </a>
                            <a href="javascript:void(0)" class="returnOrder label label-info" target="_blank"
                                data-bs-toggle="modal" data-bs-target="#returnModal" data-id="{{ $order->id }}">
                                <i class="fa-solid fa-up-right-from-square"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        {{ $orders->render() }}
    </div>
</div>
@include('orders.modals.return-modal')
<script>
    $(document).ready(function () {
        var totalAmount = 0;
        var selectedItems = [];

        $('.returnOrder').on('click', function () {
            var id = $(this).data('id');

            var paymentMethod = $(this).closest('tr').find('.paymentMethod').data('paymentmethod');
            var paymentAcc = $(this).closest('tr').find('.paymentMethod').data('payment_acc_num');


            $.ajax({
                url: "{{ route('orders.to.be.return') }}",
                type: "GET",
                dataType: "json",
                data: { order_id: id },
                success: function (response) {
                    var modalBody = '<ul class="list-group">';
                    if (response.length > 0) {
                        response.forEach(function (item) {
                            modalBody += `
                        <li class="list-group-item" data-product-id="${item.product.id}" data-quantity="${item.quantity}" data-price="${item.price}" style="cursor: pointer;">
                            <strong>Product:</strong> ${item.product.name} <br>
                            <strong>Price:</strong> $${parseFloat(item.price).toFixed(2)} <br>
                            <strong>Quantity:</strong> ${item.quantity}
                        </li>`;
                        });
                    } else {
                        modalBody = '<p>No products found for this order.</p>';
                    }
                    modalBody += '</ul>';

                    $('#returnModal .modal-body').html(modalBody);

                    $('.list-group-item').on('click', function () {
                        $(this).toggleClass('selected');
                        var price = parseFloat($(this).data('price'));
                        var productId = $(this).data('product-id');
                        var quantity = $(this).data('quantity');
                        if ($(this).hasClass('selected')) {
                            totalAmount += price;
                            selectedItems.push({ product_id: productId, price: price, quantity: quantity });
                        } else {
                            totalAmount -= price;
                            selectedItems = selectedItems.filter(item => item.product_id !== productId);
                        }

                        $('#totalAmount').text('Total Amount to Refund: $' + totalAmount.toFixed(2));
                    });
                },
                error: function () {
                    $('#returnModal .modal-body').html('<p>Something went wrong. Please try again later.</p>');
                }
            });


            $('#returnModal .btn-danger').on('click', function () {
                var totalAmountText = $("#totalAmount").text();
                var amount = totalAmountText.replace('Total Amount to Refund: $', '');
                var numericAmount = parseFloat(amount);
                if (selectedItems.length > 0) {
                    $.ajax({
                        url: "{{ route('orders.process.refund') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            order_id: id,
                            items: selectedItems,
                            payment_method: paymentMethod,
                            payment_acc_number: paymentAcc || null,
                            total_amount: numericAmount,
                            
                        },
                        success: function (response) {

                            toastr.success('Refund processed successfully.');
                            $('#returnModal').modal('hide');
                            setTimeout(function () {

                                window.location.reload();
                            }, 2000);
                        },

                        error: function () {
                            alert('An error occurred while processing the refund.');
                        }
                    });
                } else {
                    alert('No items selected for refund.');
                }
            });
        });
    });
</script>


@endsection