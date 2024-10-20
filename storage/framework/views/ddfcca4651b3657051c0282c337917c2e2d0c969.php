

<?php $__env->startSection('title', 'Admins Roles Management'); ?>
<?php $__env->startSection('content-header', 'Admins Roles Management'); ?>
<?php $__env->startSection('content-actions'); ?>
    <a href="<?php echo e(route('admin-roles.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Add New Admin
        Role</a>
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
                                    <th>Module Access</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $permissions = [];
                                ?>

                                <?php $__currentLoopData = $adminRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adminRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($adminRole->id); ?></td>
                                        <td><span class="name"><?php echo e($adminRole->name); ?></span></td>
                                        <td class="text-wrap">
                                            <?php $__currentLoopData = $adminRole->module_access; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $access): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                <?php
                                                    $permissionName = isset(config('constants.role_modules')[$access])
                                                        ? config('constants.role_modules')[$access]['name']
                                                        : ucwords(str_replace('_', ' ', $access));
                                                ?>
                                                <span class="badge badge-info"><?php echo e($permissionName); ?></span>
                                                
                                                
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td>
                                            <span  class="<?php echo \Illuminate\Support\Arr::toCssClasses(["right","badge","badge-success" => $adminRole->status,'badge-danger' => !$adminRole->status]) ?>"><?php echo e($adminRole->status  ? "Active" : "Inactive"); ?></span>
                                        </td>
                                        <td>
                                            <span
                                                class="created_at"><?php echo e($adminRole->created_at->format('m/d/Y')); ?></span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('admin-roles.edit',$adminRole->id)); ?>"
                                               class="btn btn-primary"><i
                                                    class="fas fa-edit"></i></a>
                                            <form action="<?php echo e(route('admin-roles.destroy',$adminRole->id)); ?>"
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\herd\pos.kayuta\resources\views/admin-roles/index.blade.php ENDPATH**/ ?>