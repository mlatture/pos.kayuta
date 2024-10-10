

<?php $__env->startSection('title', 'Gift Card Management'); ?>
<?php $__env->startSection('content-header', 'Gift Card Management'); ?>
<?php $__env->startSection('content-actions'); ?>
    <?php if(auth()->user()->hasPermission(config('constants.role_modules.create_gift_cards.value'))): ?>
        <a href="<?php echo e(route('gift-cards.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Add New Gift Card</a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('plugins/sweetalert2/sweetalert2.min.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <table class="table table-hover table-responsive">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        
                        <th>User Email</th>
                        <th>Barcode</th>
                        
                        <th>Amount </th>
                        
                        <th>Expiry Date</th>
                        
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Modified By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $giftCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $gift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($key + 1); ?></td>
                            
                            <td><?php echo e($gift->user_email ?? ''); ?></td>
                            <td><?php echo e($gift->barcode ?? ''); ?></td>
                            
                            <td><?php echo e($gift->amount ?? 0); ?></td>
                            
                            <td><?php echo e(date('Y, M d', strtotime($gift->expire_date))); ?></td>
                            
                            <td><?php echo e($gift->status ? 'Active' : 'Inactive'); ?></td>
                            <td><?php echo e($gift->created_at); ?></td>
                            <td><?php echo e($gift->modified_by); ?></td>
                            <td>
                                <?php if(auth()->user()->hasPermission(config('constants.role_modules.edit_gift_cards.value'))): ?>
                                    <a href="<?php echo e(route('gift-cards.edit', $gift)); ?>" class="btn btn-primary"><i
                                            class="fas fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasPermission(config('constants.role_modules.delete_gift_cards.value'))): ?>
                                    <button class="btn btn-danger btn-delete"
                                        data-url="<?php echo e(route('gift-cards.destroy', $gift)); ?>"><i
                                            class="fas fa-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php echo e($giftCards->render()); ?>

        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('plugins/sweetalert2/sweetalert2.min.js')); ?>"></script>
    <script>
        $(document).ready(function() {
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views/gift-cards/index.blade.php ENDPATH**/ ?>