
<div class="modal fade" id="addRegisterModal" tabindex="-1" aria-labelledby="addRegisterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addRegisterModalLabel">Add User</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('users.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php if (isset($component)) { $__componentOriginal30600fd1d86901c8d1e2118fb7bb2cb7e3d1570f = $component; } ?>
<?php $component = App\View\Components\Forms\Input::resolve(['label' => 'Last name','inputId' => 'l_name','inputName' => 'l_name','value' => old('l_name'),'errors' => $errors->get('l_name'),'placeholder' => 'Enter Last Name','required' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30600fd1d86901c8d1e2118fb7bb2cb7e3d1570f)): ?>
<?php $component = $__componentOriginal30600fd1d86901c8d1e2118fb7bb2cb7e3d1570f; ?>
<?php unset($__componentOriginal30600fd1d86901c8d1e2118fb7bb2cb7e3d1570f); ?>
<?php endif; ?>

                    <button class="btn btn-success btn-block btn-lg" type="submit">Submit</button>
                </form>
            </div>
            
        </div>
    </div>
</div><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\cart\modals\register-modal.blade.php ENDPATH**/ ?>