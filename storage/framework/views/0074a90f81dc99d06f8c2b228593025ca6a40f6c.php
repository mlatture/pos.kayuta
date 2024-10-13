

<?php $__env->startSection('content-header', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Log on to codeastro.com for more projects -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3>..</h3>
                        <p>POS System </p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dolly-flatbed"></i>
                    </div>
                    <a href="<?php echo e(route('cart.index')); ?>" target="_blank" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3>..</h3>
                        <p>Manage Products</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dolly-flatbed"></i>
                    </div>
                    <a href="<?php echo e(route('products.index')); ?>" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo e($site_count); ?></h3>
                        <p>Total Sites</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dolly-flatbed"></i>
                    </div>
                    <a href="<?php echo e(route('sites.index')); ?>" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-teal">
                    <div class="inner">
                        <h3><?php echo e($reservation_count); ?></h3>
                        <p>Total Reservations</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <a href="<?php echo e(route('reservations.index')); ?>" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3><?php echo e($today_reservations); ?></h3>
                        <p>Today Reservations</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <a href="<?php echo e(route('reservations.index')); ?>" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- ./col -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo e(config('settings.currency_symbol')); ?> <?php echo e(number_format($income, 2)); ?></h3>
                        <p>Total Income</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <a href="<?php echo e(route('orders.index')); ?>" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3><?php echo e(config('settings.currency_symbol')); ?> <?php echo e(number_format($income_today, 2)); ?></h3>

                        <p>Today's Income</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <a href="<?php echo e(route('orders.index')); ?>" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-teal">
                    <div class="inner">
                        <h4>Best Selling Product</h4>
                        <p><?php echo e($best_products->name ?? ''); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <a href="<?php echo e(route('orders.index')); ?>" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

        </div>

        <div class="row"><!-- Log on to codeastro.com for more projects -->
            
            <!-- ./col -->

            
            <!-- ./col -->
        </div>
    </div><!-- Log on to codeastro.com for more projects -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\herd\pos.kayuta\resources\views/home.blade.php ENDPATH**/ ?>