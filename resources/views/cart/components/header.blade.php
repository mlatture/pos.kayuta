<header class="reservation__head bg-dark py-1 mb-2">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
        <a href="javascript:void(0)" class="text-white text-decoration-none fs-2">
            Point Of Sale
        </a>
        <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle text-white" type="button" id="stationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Station: Select Register
                </button>
                <ul class="dropdown-menu" aria-labelledby="stationDropdown">
                    <li><a class="dropdown-item" href="#">Register 1</a></li>
                    <li><a class="dropdown-item" href="#">Register 2</a></li>
                </ul>
            </div>
            <button class="btn btn-dark text-white cart-empty" type="button">
                <i class="fa-solid fa-cart-arrow-down "></i> New Sale
            </button>
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle text-white" type="button" id="actionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <ul class="dropdown-menu" aria-labelledby="actionsDropdown">
                    <li><a class="dropdown-item" href="#">Process Return</a></li>
                    <li><a class="dropdown-item" href="#">Open Cash Drawer</a></li>
                    <li><a class="dropdown-item" href="#">Paid In/Out</a></li>
                </ul>
            </div>
            <button class="btn btn-dark text-white">
                <i class="fa-solid fa-hand"></i> Held Orders
            </button>
            <button class="btn btn-dark text-white">
                <i class="fa-solid fa-bars-progress"></i> In Progress
            </button>
            @hasPermission(config('constants.role_modules.orders.value'))
                <a href="{{ route('orders.index') }}" class="btn btn-dark text-white">
                    <i class="nav-icon fas fa-box me-2"></i> History
                </a>
            @endHasPermission
            <a href="#" class="btn btn-dark text-white">
                <img src="{{ asset('images/help-ico.svg') }}" alt="Help Icon" class="me-2" />
                Help
            </a>
        </div>
    </div>
</header>