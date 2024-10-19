

<?php $__env->startSection('title', 'Open POS'); ?>

<?php $__env->startPush('css'); ?>
    <link href="<?php echo asset('plugins/toast-master/css/jquery.toast.css'); ?>" rel="stylesheet">
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

        .table-container {
            max-height: 300px;

            overflow-y: auto;

        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table thead th {
            position: sticky;
            top: 0;

            background-color: #f8f9fa;

            z-index: 1;

        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<section class="content">
    <?php echo $__env->make('cart.components.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                            <option value="add_new_user">Add New User</option>

                            
                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($customer->id); ?>"
                                    data-name="<?php echo e($customer->f_name . ' ' . $customer->l_name); ?>">
                                    <?php echo e($customer->f_name . ' ' . $customer->l_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>
                    </div>
                   
                    
                         
                        
                </div>
                <div class="user-cart">
                    <div class="card">
                        <h3 class="m-2">Current Order</h3>
                        <div class="table-container">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Tax</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $subtotal = 0;
                                        $totalDiscount = 0;
                                        $totalTax = 0;
                                    ?>
                                    <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cartItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $productPrice = $cartItem->price * $cartItem->pivot->quantity;
                                            $subtotal += $productPrice;
                                            $totalDiscount += $cartItem->pivot->discount ?? 0;
                                            $totalTax += $cartItem->pivot->tax ?? 0;
                                        ?>
                                        <tr>
                                            <td><?php echo e(Str::limit($cartItem->name, 15) ?? ''); ?></td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm qty product-quantity"
                                                    data-id="<?php echo e($cartItem->id); ?>"
                                                    value="<?php echo e($cartItem->pivot->quantity ?? 0); ?>">
                                                <button class="btn btn-danger btn-sm product-delete"
                                                    data-id="<?php echo e($cartItem->id); ?>">
                                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                            <td class="text-right" id="discount">$
                                                <?php echo e($cartItem->pivot->discount ? number_format($cartItem->pivot->discount, 2) : 0); ?>

                                            </td>
                                            <td class="text-right">$
                                                <?php echo e($cartItem->pivot->tax ? number_format($cartItem->pivot->tax, 2) : 0); ?>

                                            </td>
                                            <td class="text-right">$
                                                <?php echo e($productPrice ? number_format($productPrice, 2) : 0); ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
                <?php echo $__env->make('cart.components.summary', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-danger btn-block cart-empty">Cancel</button>
                    </div>
                    <!-- <div class="col">
                        <button type="button" class="btn btn-success btn-block apply-gift-card">Apply
                            Gift Card
                        </button>
                    </div> -->
                    <div class="col">
                        <button type="button" class="btn btn-info btn-block submit-order text-light">Pay</button>
                    </div>
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
                        
                        <div class="mb-2 mx-2"><input type="text" id="search-product" class="form-control"
                                placeholder="Search Product...">
                        </div>
                    </ul>
                    <?php echo $__env->make('cart.tabpanel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>

        </div>
    </div>
</section>


<?php echo $__env->make('cart.modals.user-add-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('cart.modals.register-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
    <script src="<?php echo asset('plugins/toast-master/js/jquery.toast.js'); ?>"></script>

    <script>
        var cartStoreUrl = "<?php echo e(route('cart.store')); ?>";
        var cartChangeUrl = "<?php echo e(route('cart.changeQty')); ?>"
        var cartDeleteUrl = "<?php echo e(route('cart.delete')); ?>"
        var cartEmptyUrl = "<?php echo e(route('cart.empty')); ?>"
        var cartCategoryUrl = "<?php echo e(route('category.products')); ?>";
        var cartAllCategoryUrl = "<?php echo e(route('category.all')); ?>"
        var cartOrderStoreUrl = "<?php echo e(route('orders.store')); ?>"
        var giftCard = "<?php echo e(route('gift-cards.apply')); ?>"
        var processGiftCard = "<?php echo e(route('orders.process.gift.card')); ?>";
        var updateGiftCardBalance = "<?php echo e(route('orders.process.gift.card.balance')); ?>";
        var processCreditCard = "<?php echo e(route('orders.process.credit.card')); ?>";
        var processTerminal = "<?php echo e(route('orders.process.terminal')); ?>";
        var cartOrderUpdateUrl = "<?php echo e(route('orders.update')); ?>"
        var sentInvoiceEmail = "<?php echo e(route('orders.send.invoice')); ?>";
        var addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
        // function limitText(text, maxLength) {
        //     if (text.length > maxLength) {
        //         return text.substring(0, maxLength) + "...";
        //         return text;
        //     }
        // }


        $('#pending_customer').on('change', function() {
            $.ajax({
                url: "<?php echo e(route('cart.partialpayment')); ?>",
                type: "GET",
                dataType: "json",
             
                success: function(data) {
                   console.log(data);
                }
            })
        })

    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views/cart/index.blade.php ENDPATH**/ ?>