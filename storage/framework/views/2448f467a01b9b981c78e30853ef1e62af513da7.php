

<?php $__env->startSection('title', 'Organizations Management'); ?>
<?php $__env->startSection('content-header', 'Organizations Management'); ?>
<?php $__env->startSection('content-actions'); ?>
    <a href="<?php echo e(route('organizations.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Add New Organization</a>
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
                            <table class="display nowrap table table-hover table-striped border p-0" cellspacing="0" id="organizations_table"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Status </th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organization): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($organization->id); ?></td>
                                            <td><span class="name"><?php echo e($organization->name); ?></span></td>
                                            <td><span class="address_1"><?php echo e($organization->full_address); ?></span></td>
                                            <td>
                                                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['right','badge','badge-success' => $organization->status == 'Active','badge-danger' => $organization->status == 'Inactive']) ?>"> <?php echo e($organization->status); ?></span>
                                            </td>
                                            <td>
                                                <span class="created_at"><?php echo e($organization->created_at->format('m/d/Y')); ?></span>
                                            </td>
                                            <td>
                                                
                                                <a href="<?php echo e(route('organizations.edit',$organization->id)); ?>" class="btn btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                                <form action="<?php echo e(route('organizations.destroy',$organization->id)); ?>"
                                                      method="post" class="d-inline"">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button class="btn btn-danger btn-delete" type="submit"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>
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
<?php $__env->startPush('js'); ?>
    <script>
        window.onload = function(){
            $('#organizations_table').DataTable();
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\organization\index.blade.php ENDPATH**/ ?>