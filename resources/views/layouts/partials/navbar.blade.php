<style>
    .navbar-logo {
        height: 40px;
        width: auto;
    }

    .nav-link {
        padding: 0.5rem 1rem;
        color: #333;
        font-size: 1rem;
    }

    .navbar-nav .nav-item {
        margin-left: 1rem;

    }

    .dropdown-menu {
        min-width: 10rem;
    }

    .dropdown-menu .dropdown-item {
        padding: 0.75rem 1.25rem;

    }

    .dropdown-menu .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .main-header.navbar {
        align-items: center;
        background-color: #041307;
    }

    .navbar-brand {
        margin-right: auto;
    }

    .nav-item .nav-link {
        color: #f8f9fa;
    }

    .nav-link.active {

        color: #EFC368;
    }
</style>

<nav class="main-header navbar navbar-expand  d-flex justify-content-between">
    <a class="navbar-brand" href="{{ url('/') }}">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="navbar-logo">
    </a>

    <ul class="navbar-nav ms-auto  d-flex flex-row">
        <!-- Optional Offcanvas Toggle Button -->
        {{--
        <li class="nav-item">
            <a class="btn btn-info" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button"
                aria-controls="offcanvasExample">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        --}}

        @if (auth()->user()->hasPermission(config('constants.role_modules.dashboard.value')))
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link {{ activeSegment('') }}">
                    <div class="d-flex align-items-center">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <span class="ms-2">Dashboard</span>
                    </div>
                </a>
            </li>
        @endif




        @if (auth()->user()->hasPermission(config('constants.role_modules.list_customers.value')))
            <li class="nav-item">
                <a href="{{ route('customers.index') }}" class="nav-link {{ activeSegment('customers') }}">
                    <div class="d-flex align-items-center">
                        <i class="nav-icon fas fa-users"></i>
                        <span class="ms-2">Customers</span>
                    </div>
                </a>
            </li>
        @endif

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                <i class="fa-solid fa-cash-register"></i>
                <span class="ms-2">POS</span>
            </a>
            <ul class="dropdown-menu">
                @hasPermission(config('constants.role_modules.pos_management.value'))
                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('cart.index') }}">
                        <i class="fa-solid fa-store me-2"></i>
                        <span class="">Order Items</span>
                    </a></li>
                @endHasPermission


                @hasPermission(config('constants.role_modules.reservation_management.value'))

                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('reservations.index') }}">
                        <i class="nav-icon fas fa-calendar-alt me-2"></i>
                        <span>Reservations</span>
                    </a></li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.list_gift_cards.value'))

                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('gift-cards.index') }}">
                        <i class="nav-icon fas fa-gift me-2"></i>
                        <span class="ms-2">Gift Cards</span>
                    </a></li>
                @endHasPermission
            </ul>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                <i class="nav-icon fas fa-store"></i>
                <span class="ms-2">Store Setup</span>
            </a>
            <ul class="dropdown-menu">
                @hasPermission(config('constants.role_modules.list_products.value'))
                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('products.index') }}">
                        <i class="nav-icon fas fa-boxes me-2"></i>
                        <span>Products</span>
                    </a></li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.list_categories.value'))
                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('categories.index') }}">
                        <i class="nav-icon fas fa-tag me-2"></i>
                        <span>Categories</span>
                    </a></li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.list_product_vendors.value'))
                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('product-vendors.index') }}">
                        <i class="nav-icon fas fa-truck me-2"></i>
                        <span>Product Vendors</span>
                    </a></li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.list_tax_types.value'))
                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('tax-types.index') }}">
                        <i class="nav-icon fas fa-percent me-2"></i>
                        <span>Tax Types</span>
                    </a></li>
                @endHasPermission
            </ul>
        </li>


        @if (auth()->user()->hasPermission('organization_management'))
            <li class="nav-item">
                <a href="{{ route('organizations.index') }}" class="nav-link {{ activeSegment('organization') }}">
                    <div class="d-flex align-items-center">
                        <i class="nav-icon fas fa-building"></i>
                        <span class="ms-2">Organization</span>
                    </div>
                </a>
            </li>
    @endif




    <!-- Reports Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                <i class="nav-icon fas fa-chart-pie"></i>
                <span class="ms-2">Reports</span>
            </a>
            <ul class="dropdown-menu">
                @hasPermission(config('constants.role_modules.sales_report.value'))
                <li><a class="dropdown-item" href="{{ route('reports.salesReport') }}">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <span>Sales Report</span>
                    </a></li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.reservation_report.value'))
                <li><a class="dropdown-item" href="{{ route('reports.reservationReport') }}">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <span>Reservation Report</span>
                    </a></li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.gift_card_report.value'))
                <li><a class="dropdown-item" href="{{ route('reports.giftCardReport') }}">
                        <i class="nav-icon fas fa-gift"></i>
                        <span>Gift Card Report</span>
                    </a></li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.tax_report.value'))
                <li><a class="dropdown-item" href="{{ route('reports.taxReport') }}">
                        <i class="nav-icon fas fa-percent"></i>
                        <span>Tax Report</span>
                    </a></li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.payment_report.value'))
                <li><a class="dropdown-item" href="{{ route('reports.paymentReport') }}">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <span>Payment Report</span>
                    </a></li>
                @endHasPermission
            </ul>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                <i class="fa-solid fa-user"></i>
                <span class="ms-2"> {{ ucfirst(auth()->user()->name) }}</span>
            </a>
            <ul class="dropdown-menu">
                @if (auth()->user()->hasPermission('organization_management'))
                    <li><a class="dropdown-item" href="{{ route('admins.index') }}">
                            <div class="d-flex align-items-center">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <span class="ms-2">Admins</span>
                            </div>
                        </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin-roles.index') }}">
                            <i class="nav-icon fas fa-user-tag"></i>
                            <span>Admin Role</span>
                        </a></li>
                @endif
                @hasPermission(config('constants.role_modules.list_sites_management.value'))
                <li><a class="dropdown-item" href="{{ route('sites.index') }}">
                        <i class="nav-icon fas fa-globe"></i>
                        <span>Sites</span>
                    </a></li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.list_sites_management.value'))
                <li><a class="dropdown-item" href="{{ route('admin.whitelist') }}">
                        <i class="nav-icon fas fa-briefcase"></i>
                        <span>Whitelist</span>
                    </a></li>
                @endHasPermission

            </ul>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">

                <div class="d-flex align-items-center">
                    <i class="nav-icon fas fa-power-off"></i>
                    <span class="ms-2">Logout</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                </form>
            </a>
        </li>
    </ul>
</nav>
