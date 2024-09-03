
<header class="reservation__head bg-dark py-2 mb-2">
    <div
        class="d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between px-md-3 px-2">
        <div class="d-flex align-items-center gap-3">
            <a href="javacript:void(0)" class="text-white" style="text-decoration: none"> Point Of Sale</a>
        </div>
        <div class="d-flex align-items-center gap-4">
            <a href="javacript:void(0)" class="text-white text-decoration-none">
                Station:
                <select name="" id="station" id="">
                    <option value="" selected disabled hidden>Select Register</option>
                    <option value="Register 1">Register 1</option>
                    <option value="Register 2">Register 2</option>
                </select>
            </a>
            <a href="javacript:void(0)" class="text-white text-decoration-none">
                <i class="fa-solid fa-cart-arrow-down"></i>
                New Sale
            </a>
            <a href="javacript:void(0)" class="text-white text-decoration-none">

                Actions:
                <select id="actions" style="width: 200px;">
                    <option value="Process Return"></i> Process Return</option>
                    <option value="Open Cash Drawer">Open Cash Drawer</option>
                    <option value="Paid In/Out">Paid In/Out</option>
                </select>
            </a>
            <a href="javacript:void(0)" class="text-white text-decoration-none">
                <i class="fa-solid fa-hand"></i>
                Held Orders
            </a>
            <a href="javacript:void(0)" class="text-white text-decoration-none">
                <i class="fa-solid fa-bars-progress"></i>
                In Progress
            </a>
            <a href="javacript:void(0)" class="text-white text-decoration-none">
                <i class="fa-solid fa-clock-rotate-left"></i>
                History
            </a>
            <a href="#" class="text-white text-decoration-none">
                <img src="{{ asset('images/help-ico.svg') }}" alt="" class="me-2" />
                Help
            </a>
        </div>
    </div>
</header>
