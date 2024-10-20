<header class="reservation__head bg-dark  mb-2">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
        <a href="javascript:void(0)" class="text-white text-decoration-none fs-5">
            Point Of Sale
        </a>
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <button class="btn btn-dark  text-white" type="button" 
                 aria-expanded="false">
                Station: <?php echo e(ucfirst(auth()->user()->name)); ?>

            </button>
            <button class="btn btn-dark text-white cart-empty" type="button">
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
</header><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views/cart/components/header.blade.php ENDPATH**/ ?>