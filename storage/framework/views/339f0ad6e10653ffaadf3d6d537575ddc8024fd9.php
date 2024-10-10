

<?php $__env->startSection('title', 'Orders List'); ?>
<?php $__env->startSection('content-header', 'Order List'); ?>
<?php $__env->startSection('content-actions'); ?>
<?php if(auth()->user()->hasPermission(config('constants.role_modules.pos_management.value'))): ?>
<a href="<?php echo e(route('cart.index')); ?>" class="btn btn-success">Open POS</a>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card"><!-- Log on to codeastro.com for more projects -->
    <div class="card-body">
        <div class="row">
            <!-- <div class="col-md-3"></div> -->
            <div class="col-md-12">
                <form action="<?php echo e(route('orders.index')); ?>">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="date" name="start_date" class="form-control"
                                value="<?php echo e(request('start_date')); ?>" />
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>" />
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
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <tr>

                        <td><?php echo e($order->id); ?></td>
                        <td><?php echo e($order->getCustomerName()); ?></td>
                        <td><?php echo e(config('settings.currency_symbol')); ?> <?php echo e($order->formattedTotal()); ?></td>
                        <td><?php echo e(config('settings.currency_symbol')); ?> <?php echo e($order->formattedReceivedAmount()); ?></td>
                        <td>
                            <?php if(number_format($order->receivedAmount(), 2) == 0): ?>
                                <span class="badge badge-danger">Not Paid</span>
                            <?php elseif(number_format($order->receivedAmount(), 2) < number_format($order->total(), 2)): ?>
                                <span class="badge badge-warning">Partial</span>
                            <?php elseif(number_format($order->receivedAmount(), 2) == number_format($order->total(), 2)): ?>
                                <span class="badge badge-success">Paid</span>
                            <?php elseif(number_format($order->receivedAmount(), 2) > number_format($order->total(), 2)): ?>
                                <span class="badge badge-info">Change</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(config('settings.currency_symbol')); ?>

                            <?php echo e(number_format($order->total() - $order->receivedAmount(), 2)); ?>

                        </td>
                        <?php $__currentLoopData = $order->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td class="paymentMethod" data-payment_acc_num="<?php echo e($payment->payment_acc_number); ?>" data-paymentmethod="<?php echo e($payment->payment_method); ?>"><?php echo e($payment->payment_method); ?></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td><?php echo e($order->created_at); ?></td>
                        <td>
                            <a href="<?php echo route('orders.generate.invoice', $order->id); ?>" class="label label-info"
                                target="_blank"><i class="fas fa-file-alt"></i>
                            </a>

                            <a href="javascript:void(0)" class="returnOrder label label-info" target="_blank"
                                data-bs-toggle="modal" data-bs-target="#returnModal" data-id="<?php echo e($order->id); ?>">
                                <i class="fa-solid fa-up-right-from-square"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>

            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th><?php echo e(config('settings.currency_symbol')); ?> <?php echo e(number_format($total, 2)); ?></th>
                    <th><?php echo e(config('settings.currency_symbol')); ?> <?php echo e(number_format($receivedAmount, 2)); ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <?php echo e($orders->render()); ?>

    </div>
</div>
<?php echo $__env->make('orders.modals.return-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script>
    $(document).ready(function () {
        var totalAmount = 0;
        var selectedItems = [];

        $('.returnOrder').on('click', function () {
            var id = $(this).data('id');

            var paymentMethod = $(this).closest('tr').find('.paymentMethod').data('paymentmethod');
            var paymentAcc = $(this).closest('tr').find('.paymentMethod').data('payment_acc_num');


            $.ajax({
                url: "<?php echo e(route('orders.to.be.return')); ?>",
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
                        url: "<?php echo e(route('orders.process.refund')); ?>",
                        type: "POST",
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
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


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views/orders/index.blade.php ENDPATH**/ ?>