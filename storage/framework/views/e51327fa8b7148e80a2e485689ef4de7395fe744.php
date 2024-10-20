
<header class="reservation__head bg-dark py-2">
    <div
        class="d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between px-md-3 px-2">
        <div class="d-flex align-items-center gap-3">

            <a href="javacript:void(0)" class="text-white" data-bs-toggle="collapse" data-bs-target="#collapsePlanner"
            aria-expanded="false" aria-controls="collapsePlanner"> Planner</a>
            <a href="javacript:void(0)" class="text-white" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                aria-expanded="false" aria-controls="collapseExample"> Add Customer</a>
            <a href="javacript:void(0)" class="text-white" id="openDatePicker">Quick Add</a>
        </div>
        <div>
            <a href="#" class="text-white text-decoration-none">
                <img src="<?php echo e(asset('images/help-ico.svg')); ?>" alt="" class="me-2" />
                Help
            </a>
        </div>
    </div>
</header>


<div class="collapse px-5" id="collapseExample">
    <div class="card card-body">
        <form id="customerForm">
            <div class="form-row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fname">First Name</label>
                        <input type="text" class="form-control" name="fname" id="fname">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lname">Last Name</label>
                        <input type="text" class="form-control" name="lname" id="lname">
                    </div>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contactno">Contact Number</label>
                        <input type="text" class="form-control" name="contactno" id="contactno">
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="address">Address</label>
                <input type="text" class="form-control" name="address" id="address">
            </div>
        </form>
    </div>
    <div class="card card-footer">
        <button type="button" class="btn btn-success" id="saveCustomer">Save</button>
    </div>
</div>


<?php echo $__env->make('reservations.components.planner', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views/reservations/components/header.blade.php ENDPATH**/ ?>