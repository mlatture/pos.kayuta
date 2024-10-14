

<?php $__env->startSection('title', 'Admins Management'); ?>
<?php $__env->startSection('content-header', 'Admins Management'); ?>
<?php $__env->startSection('content-actions'); ?>
    <a href="<?php echo e(route('admins.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Add New Admin</a>
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
                                        <th>Image</th>
                                        <th>Name</th>

                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Admin Role</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($admin->id); ?></td>
                                            <td><img src="<?php echo e(asset('images/logo.png')); ?>" class="org-img img-fluid" alt=""></td>
                                            <td><span class="name"><?php echo e($admin->name); ?></span></td>

                                            <td><span class="phone"><?php echo e($admin->phone); ?></span></td>
                                            <td><span class="address_2"><?php echo e($admin->email); ?></span></td>
                                            <td><span class="admin-role"><?php echo e($admin->role->name); ?></span></td>

                                            <td>
                                                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(["right","badge","badge-success" => $admin->status, 'badge-danger' => !$admin->status]) ?>"><?php echo e($admin->status ? "Active" : "Inactive"); ?></span>
                                            </td>
                                            <td>
                                                <span class="created_at"><?php echo e($admin->created_at->format('m/d/Y')); ?></span>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('admins.edit',$admin->id)); ?>" class="btn btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                                <form action="<?php echo e(route('admins.destroy',$admin->id)); ?>"
                                                      method="post" class="d-inline">
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\herd\pos.kayuta\resources\views/admin/index.blade.php ENDPATH**/ ?>