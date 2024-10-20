<header class="reservation__head bg-dark  mb-2">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
        <a href="javascript:void(0)" class="text-white text-decoration-none fs-5">
            Point Of Sale
        </a>
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <div class="dropdown">
                <button class="btn btn-dark text-white dropdown-toggle" type="button" id="registerDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Station: <?php echo e(session('current_register_name', 'Select Register')); ?>

                </button>
                <div class="dropdown-menu" aria-labelledby="registerDropdown">
                    <?php $__currentLoopData = $registers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $register): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a class="dropdown-item" href="#"
                            onclick="setRegister(<?php echo e($register->id); ?>, '<?php echo e($register->name); ?>')"><?php echo e($register->name); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if(\App\CPU\Helpers::module_permission_check('pos_admin')): ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" id="addNewStationRegister">Add new Register</a>
                    <?php endif; ?>
                </div>
            </div>

            <button class="btn btn-dark text-white new-sale" id="new-sale" type="button">
                <i class="fa-solid fa-cart-arrow-down "></i> New Sale
            </button>
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle text-white" type="button" id="actionsDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <ul class="dropdown-menu" aria-labelledby="actionsDropdown">
                    <li>

                        <?php if(auth()->user()->hasPermission(config('constants.role_modules.orders.value'))): ?>
                            <a class="dropdown-item" href="<?php echo e(route('orders.index')); ?>">
                                <i class="fa-solid fa-up-right-from-square"></i>
                                Process Return
                            </a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="fa-solid fa-cash-register"></i>
                            Open Cash Drawer
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            Paid In/Out
                        </a>
                    </li>
                </ul>
            </div>
            <button class="btn btn-dark text-white">
                <i class="fa-solid fa-hand"></i> Held Orders
            </button>
            <button class="btn btn-dark text-white">
                <i class="fa-solid fa-bars-progress"></i> In Progress
            </button>
            <?php if(auth()->user()->hasPermission(config('constants.role_modules.orders.value'))): ?>
                <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-dark text-white">
                    <i class="nav-icon fas fa-box me-2"></i> History
                </a>
            <?php endif; ?>
            <a href="#" class="btn btn-dark text-white">
                <img src="<?php echo e(asset('images/help-ico.svg')); ?>" alt="Help Icon" class="me-2" />
                Help
            </a>
        </div>
    </div>
</header>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function setRegister(registerId, registerName) {

        $.ajax({
            url: '<?php echo e(route('registers.set')); ?>',
            method: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                register_id: registerId,
                register_name: registerName
            },
            success: function() {

                $('#registerDropdown').text('Station: ' + registerName);
            }
        });
    }

    $('#addNewStationRegister').click(function () {
    Swal.fire({
        title: 'Register New Station',
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: `Don't save`,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: "<?php echo e(route('registers.create')); ?>",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        resolve(response); 
                        console.log(response.new_register)
                    },
                    error: function (xhr) {
                        reject(xhr.responseJSON);
                    }
                });
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value.success) {
        
            Swal.fire('Success', 'New register created successfully', 'success');
            location.reload(); 
        } else if (result.isDenied || result.dismiss) {
            Swal.fire('Cancelled', 'No new register was added', 'info');
        } else if (result.value && !result.value.success) {
            Swal.fire('Error', 'There was an issue creating the register', 'error');
        }
    }).catch((error) => {
        Swal.fire('Error', 'Failed to create new register: ' + error.message, 'error');
    });
});

</script>
<?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views\cart\components\header.blade.php ENDPATH**/ ?>