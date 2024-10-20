

<?php $__env->startSection('title', 'Relocate & Re-Schedule'); ?>
<?php $__env->startSection('content-header', 'Relocate & Re-Schedule'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                Relocate
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if($reservation): ?>
                                <div class="row">
                                    <div class="col">
                                        <label for="">Name</label>
                                        <input type="text" class="form-control"
                                            value="<?php echo e($reservation->fname); ?> <?php echo e($reservation->lname); ?>" readonly>
                                    </div>
                                    <div class="col">
                                        <label for="">Site Class</label>
                                        <input type="text" class="form-control" value="<?php echo e($reservation->siteclass); ?>"
                                            readonly>
                                    </div>
                                    <div class="col">
                                        <label for="">Site ID</label>
                                        <input type="text" class="form-control" value="<?php echo e($reservation->siteid); ?>"
                                            readonly>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p>No reservation found for this Cart ID.</p>
                            <?php endif; ?>
                            
                            <?php if($siteclasses): ?>
                                <div class="row mt-3">
                                    <div class="col">
                                        <label for="">Select new Site Class</label>
                                        <select name="siteclass" class="form-control" id="sitelclass">
                                            <?php $__currentLoopData = $siteclasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $siteclass): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($siteclass->id); ?>"><?php echo e($siteclass->siteclass); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                                        </select>
                                    </div>
                                </div>
                            <?php else: ?>   
                                <p>No site classes found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                Re-Schedule
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>



    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\reservations\relocate.blade.php ENDPATH**/ ?>