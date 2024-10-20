<<<<<<< HEAD
=======


<?php $__env->startSection('title', 'Product Management'); ?>
<?php $__env->startSection('content-header', 'Product Management'); ?>
<?php $__env->startSection('content-actions'); ?>
    <?php if(auth()->user()->hasPermission(config('constants.role_modules.create_products.value'))): ?>
        <a href="<?php echo e(route('products.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Add New Product</a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('plugins/sweetalert2/sweetalert2.min.css')); ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive m-t-40 p-0">
                            <table class="display nowrap table table-hover table-striped border p-0" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr><!-- Log on to codeastro.com for more projects -->
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        <th>Barcode</th>
                                        <th>Item Cost</th>
                                        <th>Item Price</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        
                                       
                                        <tr>
                                            <td><?php echo e($product->id); ?></td>
                                            <td><?php echo e(Str::limit($product->name, 20)); ?></td>

                                            <td>
                                                <img class="product-img img-thumbnail"
                                                    src="<?php echo e($product->image && Storage::disk('public')->exists('products/' . $product->image) ? Storage::url('products/' . $product->image) : Storage::url('product-thumbnail.jpg')); ?>"
                                                    width="60px" height="60px" alt="<?php echo e($product->name); ?>">



                                            </td>

                                            <td><?php echo e($product->barcode); ?></td>
                                            <td><?php echo e(config('settings.currency_symbol')); ?><?php echo e($product->cost); ?></td>
                                            <td><?php echo e(config('settings.currency_symbol')); ?><?php echo e($product->price); ?></td>
                                            <td><?php echo e($product->quantity); ?></td>
                                            <td>
                                                <span
                                                    class="right badge badge-<?php echo e($product->status ? 'success' : 'danger'); ?>">
                                                    <?php echo e($product->status ? 'Active' : 'Inactive'); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($product->created_at); ?></td>
                                            <td><?php echo e($product->updated_at); ?></td>
                                            <td>
                                                <?php if(auth()->user()->hasPermission(config('constants.role_modules.edit_products.value'))): ?>
                                                    <a href="<?php echo e(route('products.edit', $product)); ?>" class="btn btn-primary"><i
                                                            class="fas fa-edit"></i></a>
                                                <?php endif; ?>
                                                <?php if(auth()->user()->hasPermission(config('constants.role_modules.delete_products.value'))): ?>
                                                    <button class="btn btn-danger btn-delete"
                                                        data-url="<?php echo e(route('products.destroy', $product)); ?>"><i
                                                            class="fas fa-trash"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>

                            </table>
                        </div>
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
            $('.table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
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
                    text: "Do you really want to delete this product?",
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views/products/index.blade.php ENDPATH**/ ?>
>>>>>>> main
