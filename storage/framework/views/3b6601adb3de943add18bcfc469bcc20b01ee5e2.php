

<?php $__env->startSection('title', 'Site Management'); ?>
<?php $__env->startSection('content-header', 'Site Management'); ?>
<?php $__env->startSection('content-actions'); ?>



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
                                        <th> SL </th>
                                        <th> Site ID </th>
                                        <th> Site Name </th>
                                        <th> Site Class </th>
                                        <th> Available </th>
                                        <th> Max Length </th>
                                        <th> Min Length </th>
                                        <th> Right Type </th>
                                        <th> Class </th>
                                        <th> Attributes </th>
                                        <th> Amenities </th>
                                        
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e(++$k); ?></td>
                                            <td>
                                                <?php echo e($site->siteid ?? 'N/A'); ?>

                                            </td>

                                            <td>
                                                <?php echo Str::limit($site->sitename, 20); ?>

                                            </td>

                                            <td>
                                                <?php echo Str::limit($site->siteclass, 20); ?>

                                            </td>

                                            <td>
                                                <?php echo e($site->available ? 'Available' : 'Not Available'); ?>

                                            </td>

                                            <td>
                                                <?php echo e($site->maxlength ?? 'N/A'); ?>

                                            </td>

                                            <td>
                                                <?php echo e($site->minlength ?? 'N/A'); ?>

                                            </td>

                                            <td>
                                                <?php echo e(Str::limit(is_array($site->rigtypes) ? implode(',', $site->rigtypes) : 'No Rigtypes', 20)); ?>

                                            </td>

                                            <td>
                                                <?php echo Str::limit($site->class, 20); ?>

                                            </td>

                                            <td>
                                                <?php echo Str::limit($site->attributes, 20); ?>

                                            </td>

                                            <td>
                                                <?php echo e(Str::limit(is_array($site->amenities) ? implode(',', $site->amenities) : 'No Amenities', 20)); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\sites\index.blade.php ENDPATH**/ ?>