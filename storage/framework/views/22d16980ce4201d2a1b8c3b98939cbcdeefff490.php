

<?php $__env->startSection('title', 'Tax Report Management'); ?>
<?php $__env->startSection('content-header', 'Tax Report Management'); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('plugins/sweetalert2/sweetalert2.min.css')); ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row animated fadeInUp">
        <div class="card">
            <div class="card-body">
                
                <!--                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>-->
                <form action="<?php echo route('reports.taxReport'); ?>" method="GET">
                    <div class="row mt-3">
                        
                        <div class="col-md-7">
                            <div class='input-group mb-3'>
                                <input type='text' class="form-control daterange" id="productDate" name="date" autocomplete="off"
                                    value="<?php echo isset($_GET['date']) ? $_GET['date'] : ''; ?>" />
                                <span class="input-group-text">
                                    <span class="ti-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <button type="submit"
                                class="search-btn btn btn-primary waves-effect waves-light text-white w-100 height-55">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive m-t-40 p-0">
                        <table table class="display nowrap table table-hover table-striped border p-0" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>Transaction Date</th>
                                    <!-- <th>Source</th> -->
                                    <th>Customer #/POS id</th>
                                    <th>Customer</th>
                                    <th>Order Number</th>
                                    <!-- <th width="20%">Products</th> -->
                                    <th>Tax</th>
                                    <!-- <th>Discount</th> -->
                                    <th>Amount</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $tax = 0;
                                    $discount = 0;
                                ?>
                                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(date('m/d/Y', strtotime($order->created_at))); ?></td>
                                        <!-- <td>POS (Orders)</td> -->
                                        <td><?php echo e($order->customer->id ?? 'N/A'); ?></td>
                                        <td><?php echo e($order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : 'N/A'); ?>

                                        </td>
                                        <td><?php echo e($order->id ?? 'N/A'); ?>

                                        </td>
                                        <!-- <td>
                                            <ul>
                                                <?php $__empty_1 = true; $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <?php
                                                        $tax += $detail->tax;
                                                        $discount += $detail->discount;
                                                    ?>
                                                    <li><?php echo e($detail->product ? $detail->product->name . " ($detail->quantity)" : 'N/A'); ?>

                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    No Products Found
                                                <?php endif; ?>
                                            </ul>
                                        </td> -->
                                        <td><?php echo e($tax ?? 0); ?></td>
                                        <!-- <td><?php echo e($discount ?? 0); ?></td> -->
                                        <td><?php echo e($order->amount ?? 0); ?></td>
                                        <td><?php echo e($order->created_at); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('plugins/sweetalert2/sweetalert2.min.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            $('#productDate').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#productDate').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                    'MM/DD/YYYY'));
            });

            $('#productDate').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            $('#productDate').attr("placeholder", "Select Date");

            $('.table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                searching: false
            });

            $(document).on('click', '.btn-delete', function() {
                $this = $(this);
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to delete this Gift Card?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.post($this.data('url'), {
                            _method: 'DELETE',
                            _token: '<?php echo e(csrf_token()); ?>'
                        }, function(res) {
                            $this.closest('tr').fadeOut(500, function() {
                                $(this).remove();
                            })
                        })
                    }
                })
            })
        })
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\reports\tax-report.blade.php ENDPATH**/ ?>