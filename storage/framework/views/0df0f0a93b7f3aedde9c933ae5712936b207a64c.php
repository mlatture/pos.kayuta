

<?php $__env->startSection('title', 'Customer Management'); ?>
<?php $__env->startSection('content-header', 'Customer Management'); ?>
<?php $__env->startSection('content-actions'); ?>
    <?php if(auth()->user()->hasPermission(config('constants.role_modules.create_customers.value'))): ?>
        <a href="<?php echo e(route('customers.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Add New Customer</a>
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
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($key + 1); ?></td>
                                            <td><?php echo e($customer->f_name); ?> <?php echo e($customer->l_name); ?></td>
                                            <td><?php echo e($customer->email); ?></td>
                                            <td><?php echo e($customer->phone); ?></td>
                                            <td><?php echo e($customer->street_address); ?></td>
                                            <td><?php echo e($customer->created_at); ?></td>
                                            <td>
                                                <?php if(auth()->user()->hasPermission(config('constants.role_modules.edit_customers.value'))): ?>
                                                <a href="<?php echo e(route('customers.edit', $customer)); ?>"
                                                   class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                                <?php endif; ?>
                                                <?php if(auth()->user()->hasPermission(config('constants.role_modules.delete_customers.value'))): ?>
                                                <button class="btn btn-danger btn-delete"
                                                        data-url="<?php echo e(route('customers.destroy', $customer)); ?>"><i
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
                    text: "Do you really want to delete this customer?",
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\customers\index.blade.php ENDPATH**/ ?>