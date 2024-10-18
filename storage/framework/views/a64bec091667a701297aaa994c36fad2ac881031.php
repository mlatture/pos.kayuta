

<?php $__env->startSection('title', 'Create Reservation'); ?>
<?php $__env->startSection('content-header', 'Create Reservation'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <form action="<?php echo e(route('reservations.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginal30600fd1d86901c8d1e2118fb7bb2cb7e3d1570f = $component; } ?>
<?php $component = App\View\Components\Forms\Input::resolve(['label' => 'Customer','type' => 'select','required' => true,'placeholder' => 'Select Customer','inputName' => 'customer_id','inputId' => 'customer_id','value' => old('customer_id'),'options' => $customers->map(fn($customer) => ['value' => $customer->id,'label' => $customer->full_name])->toArray()] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                <a href="javascript:void(0)" onclick="openCustomerModal()">Add New Customer?</a>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <?php if (isset($component)) { $__componentOriginal30600fd1d86901c8d1e2118fb7bb2cb7e3d1570f = $component; } ?>
<?php $component = App\View\Components\Forms\Input::resolve(['label' => 'Check in Date','type' => 'date','required' => true,'placeholder' => 'Select Check In Date','inputName' => 'cid','inputId' => 'cid','value' => old('cid')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                    </div>
                    <div class="col-md-4">
                        <?php if (isset($component)) { $__componentOriginal30600fd1d86901c8d1e2118fb7bb2cb7e3d1570f = $component; } ?>
<?php $component = App\View\Components\Forms\Input::resolve(['label' => 'Check out Date','type' => 'date','required' => true,'placeholder' => 'Select Check Out Date','inputName' => 'cod','inputId' => 'cod','value' => old('cod')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                    </div>
                    <div class="col-md-4">
                        <?php if (isset($component)) { $__componentOriginal30600fd1d86901c8d1e2118fb7bb2cb7e3d1570f = $component; } ?>
<?php $component = App\View\Components\Forms\Input::resolve(['label' => 'Site Class','type' => 'select','required' => true,'placeholder' => 'Select Site Class','inputName' => 'siteclass','inputId' => 'siteclass','value' => old('siteclass'),'options' => $classes->map(fn($class) => ['value' => str_replace(' ','_',$class->siteclass),'label' => $class->siteclass])->toArray()] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                    </div>
                </div>
                <div class="row" id="rv_sites_div" >
                    <div class="col-md-6">
                        <?php if (isset($component)) { $__componentOriginal30600fd1d86901c8d1e2118fb7bb2cb7e3d1570f = $component; } ?>
<?php $component = App\View\Components\Forms\Input::resolve(['label' => 'Rig Length','inputName' => 'riglength','inputId' => 'riglength','value' => old('riglength'),'errors' => $errors->get('riglength'),'placeholder' => 'Enter Rig Length','type' => 'number','numberMin' => 3,'step' => '1'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                    </div>
                    <div class="col-md-6">
                        <?php if (isset($component)) { $__componentOriginal30600fd1d86901c8d1e2118fb7bb2cb7e3d1570f = $component; } ?>
<?php $component = App\View\Components\Forms\Input::resolve(['label' => 'Hookup','inputName' => 'hookup','inputId' => 'hookup','value' => old('hookup'),'errors' => $errors->get('hookup'),'placeholder' => 'Select Hookup','type' => 'select','options' => $hookups->map(fn($hookup) => ['value' => $hookup->sitehookup,'label' => $hookup->sitehookup])->toArray()] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                    </div>
                </div>
                <button class="btn btn-success btn-block btn-lg" type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="modal fade customer--modal" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Create Customer</h5>
                </div>
                <div class="modal-body">
                        <div class="alert alert-danger d-none"></div>
                        <div class="alert alert-success d-none"></div>
                    <form id="customerForm" method="post" action="<?php echo e(route('customers.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" class="form-control"
                                   id="first_name"
                                   placeholder="First Name" required>
                        </div>
                        <input type="hidden" name="is_modal" value="1">
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" class="form-control"
                                   id="last_name"
                                   placeholder="Last Name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control"
                                   id="email"
                                   placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Contact Number</label>
                            <input type="text" name="phone" class="form-control"
                                   id="phone"
                                   placeholder="Contact Number" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" name="address" class="form-control"
                                   id="address"
                                   placeholder="Address" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveCustomer(this)">Submit</button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $(function(){
           $('.select2-input').select2();
        });
        let rvSiteClass = '<?php echo e(str_replace(' ','_',$classes[0]->siteclass)); ?>';

        window.onload = function(){
            $("#siteclass").val(rvSiteClass).trigger('change');
        }

        $("#siteclass").on('change',function(e){
            if($(this).val() === rvSiteClass) {
                $("#rv_sites_div").removeClass('d-none');
            }
            else {
                $("#rv_sites_div").addClass('d-none');
            }
        });

        function openCustomerModal(){
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $('input').removeClass('is-invalid');
            $('.error--msg').remove();
            $('.customer--modal').modal('toggle');
        }

        function closeModal(){
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text();
            $('input').removeClass('is-invalid');
            $('.error--msg').remove();
            $('.customer--modal').modal('hide');
        }

        function saveCustomer(input){
            $(input).attr('disabled', true);
            $('input').removeClass('is-invalid');
            $('.error--msg').remove();
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $.post($('#customerForm').attr('action'), $('#customerForm').serialize()).done(function(res) {
                if(res.status == "success"){
                    var newOption = $('<option>', {
                        value: res.data.id,
                        text: res.data.f_name+' '+res.data.l_name
                    });
                    $('#customer_id').find('option').eq(1).before(newOption);
                    $('#customer_id').val(res.data.id).trigger('change');
                    $('.alert-success').removeClass('d-none').text(res.message);
                    setTimeout(function(){
                        $('.customer--modal').modal('hide');
                        $('.alert-success').addClass('d-none').text('');
                        $('.alert-danger').addClass('d-none').text('');
                    }, 1500);
                }else{
                    $(input).attr('disabled', false);
                    $('.alert-danger').removeClass('d-none').text(res.message)
                }
            })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    $(input).attr('disabled', false);
                    if (jqXHR.status === 422) {
                        $.each(jqXHR.responseJSON.errors, function(k,v){
                           $(`#${k}`).addClass('is-invalid').after(`<span class="error--msg" role="alert"><strong class="text-danger">${v[0]}</strong></span>`);
                        });
                    }
                });
        }

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\reservations\create.blade.php ENDPATH**/ ?>