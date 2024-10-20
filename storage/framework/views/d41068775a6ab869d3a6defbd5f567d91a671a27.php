

<?php $__env->startSection('title', 'Checkout'); ?>
<?php $__env->startSection('content-header', 'Checkout'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <div class="row pb-5">
                <div class="col-lg-12">
                    <div class="cart-name-wrapper mb-5 ">
                        <p><strong>Customer Name</strong> : <?php echo e($customer->full_name); ?></p>
                        <p><strong>Customer Email</strong> : <?php echo e($customer->email); ?></p>
                    </div>
                    <div class="cart-table-wrapper bg-white">
                        <div class="table-responsive">
                            <table class="table" style="background-color: unset !important;">
                                <thead>
                                <tr>
                                    <th scope="col">Action</th>
                                    <th scope="col">SNO</th>
                                    <th scope="col">Nights</th>
                                    <th scope="col">Site</th>
                                    <th scope="col">Check-In</th>
                                    <th scope="col">Check-Out</th>
                                    <th scope="col">Subtotal</th>
                                    <th scope="col">Tax</th>
                                    <th scope="col">Sitelock Fee</th>
                                    <th scope="col">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(count($items) > 0): ?>
                                    <?php
                                        $total = 0;
                                    ?>
                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $sitelockFee = (isset($cart['sitelock']) && $cart['sitelock'] == 'on')? 20 : 0;
                                        ?>
                                        <tr>
                                            <td class="close-row">
                                                <button class="btn btn-danger"
                                                    onclick="location.href='<?php echo e(route('reservations.remove-cart', [$bookingId, $cart['cartid']])); ?>'">X</button>
                                            </td>
                                            <td class="close-row">
                                                <span><?php echo e(++$key); ?></span>
                                            </td>
                                            <td class="pro-img">
                                                <span><?php echo e($cart['nights']); ?></span>
                                            </td>
                                            <td class="pro-name"><?php echo e($cart['siteid']); ?></td>
                                            <td class="total-price"><?php echo e(date('D Y-m-d', strtotime($cart['cid']))); ?></td>
                                            <td class="total-price"><?php echo e(date('D Y-m-d', strtotime($cart['cod']))); ?></td>
                                            <td class="sub-total">
                                                <?php echo e(\App\CPU\Helpers::format_currency_usd($cart['subtotal'])); ?></td>
                                            <td class="sub-total">
                                                <?php echo e(\App\CPU\Helpers::format_currency_usd($cart['taxrate'])); ?></td>
                                            <td class="sub-total">
                                                <?php echo e(\App\CPU\Helpers::format_currency_usd($sitelockFee)); ?></td>
                                            <td class="sub-total">
                                                <?php echo e(\App\CPU\Helpers::format_currency_usd($cart['subtotal'] + $cart['taxrate'] + $sitelockFee)); ?>

                                            </td>
                                            <?php
                                                $total += $cart['subtotal'] + $cart['taxrate'] + $sitelockFee;
                                            ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="button-group text-end">
                            <ul class="list-unstyled w-50  px-5 mb-0">
                                <li class="d-flex justify-content-between">
                                    <span class="property text-dark">Subtotal
                                    </span>
                                    <span class="value text-secondary fw-bold">
                                        <?php echo e(\App\CPU\Helpers::format_currency_usd($total)); ?>

                                    </span>
                                </li>
                                <li class="d-flex justify-content-between">
                                    <span class="property text-dark">Discount
                                    </span>
                                    <span class="value text-secondary fw-bold discount-value">
                                        $0.00
                                    </span>
                                </li>
                                <li class="d-flex justify-content-between">
                                    <span class="property text-dark">Cart Total
                                    </span>
                                    <span class="value text-secondary fw-bold total-value">
                                        <?php echo e(\App\CPU\Helpers::format_currency_usd($total)); ?>

                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row pb-5">
                <div class="col-lg-12">
                    <div class="cart-name-wrapper mb-5 ">
                        <h4>Enter a Coupon Code</h4>
                    </div>
                    <form>
                        <?php echo csrf_field(); ?>
                        <div class="form-group my-2">
                            <label for="voucher">Enter a Coupon Code :</label>
                            <input type="text" name="coupon_code" id="coupon_code"
                                   class="form-control mt-3" />
                        </div>
                        <div class="form-group my-2">
                            <button type="button" id="apply-coupon" class="btn btn-primary mt-3">Apply Coupon</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row pb-5">
                <div class="col-lg-12">
                    <div class="cart-name-wrapper mb-5 ">
                        <h4>Enter your Credit Card Info</h4>
                    </div>
                    <form method="post" action="<?php echo e(route('reservations.do-checkout', [$bookingId])); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="saved-payment-method">
                            <div class="add-new-method">
                                <div class="form-group mb-3">
                                    <input type="text" maxlength="16" name="xCardNum" id="xCardNum" required
                                           class="form-control" placeholder="Credit Card:">
                                </div>
                                <input type="hidden" name="applicable_coupon" id="applicable_coupon">
                                <div class="form-group mb-3">
                                    <input type="text" name="xExp" id="xExp" required
                                           class="form-control" placeholder="Expiration:" maxlength="5">
                                </div>
                                <input type="hidden" name="xAmount" id="xAmount" value="<?php echo e($total); ?>">

                                <button type="submit" class="btn btn-primary mt-3">Submit</button>
                                <div id="validationerrors"></div>
                            </div>
                            <!-- Saved payments Method -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        const inputField = document.getElementById('xExp');

        inputField.addEventListener('input', function () {
            const value = inputField.value;

            // Remove any non-digit characters
            const cleanValue = value.replace(/\D/g, '');

            if (cleanValue.length >= 2) {
                // Automatically insert a "/" after the second character
                const formattedValue = cleanValue.slice(0, 2) + '/' + cleanValue.slice(2);
                inputField.value = formattedValue;
            }
        });


        $('#apply-coupon').on('click', function() {
            var code = $('#coupon_code').val();
            var amount = $('#xAmount').val();
            if (!code) {
                alert('Please Enter Coupon Code First!');
                return false;
            }

            if (!amount) {
                alert('Amount is not valid!');
                return false;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            });
            $.post({
                url: '<?php echo e(route('reservations.apply-coupon')); ?>',
                data: {
                    coupon_code: code,
                    amount:amount,
                },
                success: function(data) {
                    console.log(data.data);
                    if (data.code) {
                        var dataa = data.data;
                        $('.discount-value').text(dataa.discount);
                        $('#applicable_coupon').val(dataa.coupon.code);
                        $('.total-value').text('$'+(formatNumber(amount-dataa.discount_amount)));
                        alert(data.message);

                    } else {
                        for (var i = 0; i < data.errors.length; i++) {
                            alert(data.errors[i].message);
                        }
                    }

                },
                error: function(error) {
                    $.each(error.responseJSON.errors, function(key, value) {
                        alert(value);
                    });
                }
            });
        });

        function formatNumber(number){
            return new Intl.NumberFormat('en-US', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\reservations\checkout.blade.php ENDPATH**/ ?>