<?php if($type != 'checkbox' and $type != 'radio'): ?>
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['form-group','mb-3']) ?>" >
        <?php if($showLabel): ?>
            <label for="<?php echo e($inputId); ?>"><?php echo e($label); ?> <?php if($required): ?> <span class="text-danger" >*</span> <?php endif; ?></label>
        <?php endif; ?>
        <?php if($type != 'textarea' and $type != 'select' and $type != 'password' and $type != 'checkbox'): ?>
            <input type="<?php echo e($type); ?>" name="<?php echo e($inputName); ?>" placeholder="<?php echo e($placeholder); ?>" value="<?php echo e($value); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['form-control','is-invalid' => count($errors) > 0,'is-valid' => $valid == true]) ?>" id="<?php echo e($inputId); ?>" <?php echo e($required ? "required" : ""); ?> step="<?php echo e($step); ?>" min="<?php echo e($numberMin); ?>" accept="<?php echo e($accept); ?>" <?php if($disabled): echo 'disabled'; endif; ?> />
        <?php elseif($type === 'password'): ?>
            <div class="input-group">
                <input type="<?php echo e($type); ?>" name="<?php echo e($inputName); ?>" placeholder="<?php echo e($placeholder); ?>" value="<?php echo e($value); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['form-control','is-invalid' => count($errors) > 0,'is-valid' => $valid == true]) ?>" id="<?php echo e($inputId); ?>" <?php echo e($required ? "required" : ""); ?> step="<?php echo e($step); ?>" min="<?php echo e($numberMin); ?>" accept="<?php echo e($accept); ?>" <?php if($disabled): echo 'disabled'; endif; ?> />
                <div class="input-group-append" onclick="showPassword('<?php echo e($inputId); ?>')" >
                    <span class="input-group-text"><i class="fa fa-eye" ></i></span>
                </div>
            </div>
        <?php elseif($type == 'textarea'): ?>
            <textarea maxlength="300" rows="5" name="<?php echo e($inputName); ?>" id="<?php echo e($inputId); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['form-control','is-invalid' => count($errors) > 0,'is-valid' => $valid]) ?>" placeholder="<?php echo e($placeholder); ?>" <?php if($disabled): echo 'disabled'; endif; ?> ><?php echo e($value); ?></textarea>
        <?php else: ?>
            <?php if(!$multiple): ?>
                <select name="<?php echo e($inputName); ?>" id="<?php echo e($inputId); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['form-control','select2-input','is-invalid' => count($errors) > 0,'is-valid' => $valid]) ?>" <?php echo e($required ? "required" : ""); ?> <?php if($disabled): echo 'disabled'; endif; ?>  >
                    <option value="" ><?php echo e($placeholder); ?></option>
                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option <?php echo e($option['value'] == $value ? "selected" : ""); ?> value="<?php echo e($option['value']); ?>"><?php echo e($option['label']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            <?php else: ?>
                <select data-placeholder="<?php echo e($placeholder); ?>" name="<?php echo e($inputName); ?>" id="<?php echo e($inputId); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['select2','form-control','select2-multiple','select2-input','is-invalid' => count($errors) > 0,'is-valid' => $valid]) ?>" <?php echo e($required ? "required" : ""); ?> multiple <?php if($disabled): echo 'disabled'; endif; ?>  >
                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option <?php echo e((is_array($value) and in_array($option['value'],$value)) ? "selected" : ""); ?> value="<?php echo e($option['value']); ?>"><?php echo e($option['label']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            <?php endif; ?>
        <?php endif; ?>
        <div class="invalid-feedback d-block">
            <?php echo implode("<br/>",$errors); ?>

        </div>
        <div class="valid-feedback">
            <?php echo e($validMessage); ?>

        </div>
    </div>
<?php elseif($type == 'checkbox'): ?>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" <?php echo e((($checked) ? "checked" : "")); ?> value="<?php echo e($value); ?>" id="<?php echo e($inputId); ?>" name="<?php echo e($inputName); ?>" >
        <label class="form-check-label" for="<?php echo e($inputId); ?>">
            <?php echo e($label); ?>

        </label>
    </div>
<?php else: ?>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="<?php echo e($inputName); ?>" id="<?php echo e($inputId); ?>" value="<?php echo e($value); ?>" <?php echo e(($checked) ? "checked" : ""); ?>>
        <label class="form-check-label" for="<?php echo e($inputId); ?>">
            <?php echo e($label); ?>

        </label>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\components\forms\input.blade.php ENDPATH**/ ?>