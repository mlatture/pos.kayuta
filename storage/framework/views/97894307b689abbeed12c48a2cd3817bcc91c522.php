

<?php $__env->startSection('title', 'Category Management'); ?>
<?php $__env->startSection('content-header', 'Category Management'); ?>
<?php $__env->startSection('content-actions'); ?>
    <?php if(auth()->user()->hasPermission(config('constants.role_modules.create_categories.value'))): ?>
        <a href="<?php echo e(route('categories.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Add New Category</a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('plugins/sweetalert2/sweetalert2.min.css')); ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card product-list">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive m-t-40 p-0">
                            <table class="display nowrap table table-hover table-striped border p-0" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr><!-- Log on to codeastro.com for more projects -->
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($category->id); ?></td>
                                            <td><?php echo e($category->name); ?></td>
                                            <td>
                                                <span
                                                    class="right badge badge-<?php echo e($category->status ? 'success' : 'danger'); ?>"><?php echo e($category->status ? 'Active' : 'Inactive'); ?></span>
                                            </td>
                                            <td><?php echo e($category->created_at); ?></td>
                                            <td><?php echo e($category->updated_at); ?></td>
                                            <td>
                                                <?php if(auth()->user()->hasPermission(config('constants.role_modules.edit_categories.value'))): ?>
                                                <a href="<?php echo e(route('categories.edit', $category)); ?>"
                                                   class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                                <?php endif; ?>
                                                <?php if(auth()->user()->hasPermission(config('constants.role_modules.delete_categories.value'))): ?>
                                                <button class="btn btn-danger btn-delete"
                                                    data-url="<?php echo e(route('categories.destroy', $category)); ?>"><i
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\categories\index.blade.php ENDPATH**/ ?>