<?php $__env->startSection('title', "{$module} {$formattedTable} Record"); ?>
<?php $__env->startSection('content-header', "{$module} {$formattedTable} Record"); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                <a href="<?php echo e(route('admin.dynamic-module-records', $table)); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-arrow-circle-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST"
                  action="<?php echo e($isEdit ? route('admin.dynamic-module-update-form-data', [$table, $moduleData->id]) : route('admin.dynamic-module-store-form-data', $table)); ?>">
                <?php echo csrf_field(); ?>
                <?php if($isEdit): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>
                <div class="row">
                    <input type="hidden" name="created_at" value="<?php echo e(now()); ?>">
                    <input type="hidden" name="updated_at" value="<?php echo e(now()); ?>">
                    <?php
                        usort($columns, function($a, $b) use ($dictionaryFields) {
                                $orderA = $dictionaryFields[$a]['order'] ?? PHP_INT_MAX;
                                $orderB = $dictionaryFields[$b]['order'] ?? PHP_INT_MAX;

                                return $orderA <=> $orderB;
                            });
                        $columns = array_diff($columns, ['id', 'created_at', 'updated_at']);
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="<?php echo e(isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'hidden' ? 'd-none' : 'col-md-6'); ?>">
                            <div class="form-group">
                                <label for="<?php echo e($column); ?>" <?php echo e(isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'read_only' ? 'readonly' : ''); ?>>
                                    <?php echo e(isset($dictionaryFields[$column]) && !empty($dictionaryFields[$column]['display_name']) ? $dictionaryFields[$column]['display_name'] : $column); ?>

                                    <?php echo isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'read_only' ? '<span class="text-danger">(not editable)</span>' : ''; ?>

                                </label>

                                <?php
                                    $datatype = \Illuminate\Support\Facades\Schema::getColumnType($table, $column);
                                    $fieldType = $datatype === 'string' ? 'text' :
                                                 ($datatype === 'integer' ? 'number' :
                                                 ($datatype === 'datetime' ? 'datetime-local' : $datatype));
                                ?>

                                <?php if(in_array($datatype, ['text', 'longtext', 'json'])): ?>
                                    <textarea aria-label="<?php echo e($column); ?>" type="<?php echo e($fieldType); ?>" name="<?php echo e($column); ?>" class="form-control <?php $__errorArgs = [$column];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="<?php echo e($column); ?>"
                                              <?php echo e(isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'read_only' ? 'readonly' : ''); ?>

                                        <?php echo e(isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'hidden' ? 'disabled' : ''); ?>>
                                        <?php echo e($isEdit ? $moduleData->$column : old($column)); ?>

                                    </textarea>
                                <?php else: ?>
                                    <input aria-label="<?php echo e($column); ?>" type="<?php echo e($fieldType); ?>" name="<?php echo e($column); ?>"
                                           class="form-control <?php $__errorArgs = [$column];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="<?php echo e($column); ?>"
                                           <?php echo e(isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'read_only' ? 'readonly' : ''); ?>

                                           <?php echo e(isset($dictionaryFields[$column]) && $dictionaryFields[$column]['visibility'] === 'hidden' ? 'disabled' : ''); ?>

                                           value="<?php echo e($isEdit ? $moduleData->$column : old($column)); ?>">
                                <?php endif; ?>
                                <?php if(!empty($dictionaryFieldsDesc[$column])): ?>
                                    <small class="form-text text-muted"><span
                                            class="fas fa-info-circle"></span> <?php echo e($dictionaryFieldsDesc[$column]); ?>

                                    </small>
                                <?php endif; ?>
                                <?php $__errorArgs = [$column];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php endif; ?>
                </div>
                <button class="btn <?php echo e($isEdit ? 'btn-warning' : 'btn-success'); ?> btn-block btn-lg" type="submit">
                    <?php if($isEdit): ?>
                        Update
                    <?php else: ?>
                        Save
                    <?php endif; ?>
                </button>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\herd\pos.kayuta\resources\views/dynamic-tables/module/form.blade.php ENDPATH**/ ?>