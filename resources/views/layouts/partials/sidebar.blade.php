<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-purple elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('images/poslg.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>
    <!-- Log on to codeastro.com for more projects -->

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        {{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->getFullname() }}</a>
            </div>
        </div> --}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                @if (auth()->user()->hasPermission(config('constants.role_modules.dashboard.value')))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('home') }}" class="nav-link {{ activeSegment('') }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                @endif
                @hasPermission(config('constants.role_modules.pos_management.value'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('cart.index') }}" class="nav-link {{ activeSegment('cart') }}">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>POS System</p>
                        </a>
                    </li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.reservation_management.value'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('reservations.index') }}" class="nav-link {{ activeSegment('reservations') }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Reservations</p>
                        </a>
                    </li>
                @endHasPermission
                @if (auth()->user()->hasPermission(config('constants.role_modules.list_customers.value')))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('customers.index') }}" class="nav-link {{ activeSegment('customers') }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Customers</p>
                        </a>
                    </li>
                @endif
                @hasPermission(config('constants.role_modules.list_gift_cards.value'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('gift-cards.index') }}" class="nav-link {{ activeSegment('gift-cards') }}">
                            <i class="nav-icon fas fa-gift"></i>
                            <p>Gift Cards</p>
                        </a>
                    </li>
                @endHasPermission
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <span class="ps-3">Store Setup</span>
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">

                                <ul class="list-unstyled">
                                    @hasPermission(config('constants.role_modules.list_products.value'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('products.index') }}"
                                                class="nav-link {{ activeSegment('products') }}">
                                                <i class="nav-icon fas fa-boxes"></i>
                                                <p>Products</p>
                                            </a>
                                        </li>
                                    @endHasPermission
                                    @hasPermission(config('constants.role_modules.list_categories.value'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('categories.index') }}"
                                                class="nav-link {{ activeSegment('categories') }}">
                                                <i class="nav-icon fas fa-boxes"></i>
                                                <p>Categories</p>
                                            </a>
                                        </li>
                                    @endHasPermission
                                    @hasPermission(config('constants.role_modules.list_product_vendors.value'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('product-vendors.index') }}"
                                                class="nav-link {{ activeSegment('product-vendors') }}">
                                                <i class="nav-icon fas fa-boxes"></i>
                                                <p>Product Vendors</p>
                                            </a>
                                        </li>
                                    @endHasPermission
                                    @hasPermission(config('constants.role_modules.list_tax_types.value'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('tax-types.index') }}"
                                                class="nav-link {{ activeSegment('tax-types') }}">
                                                <i class="nav-icon fas fa-boxes"></i>
                                                <p>Tax Types</p>
                                            </a>
                                        </li>
                                    @endHasPermission
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                @if (auth()->user()->hasPermission('organization_management'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('organizations.index') }}"
                            class="nav-link {{ activeSegment('organization') }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p> Organization</p>
                        </a>
                    </li>
                @endif

                @hasPermission(config('constants.role_modules.orders.value'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('orders.index') }}" class="nav-link {{ activeSegment('orders') }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Orders</p>
                        </a>
                    </li>
                @endHasPermission


                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <span class="ps-3">Settings</span>
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">

                                <ul class="list-unstyled">
                                    @if (auth()->user()->hasPermission('organization_management'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('admins.index') }}"
                                                class="nav-link {{ activeSegment('admins') }}">
                                                <i class="nav-icon fas fa-user"></i>
                                                <p>Admins</p>
                                            </a>
                                        </li>
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('admin-roles.index') }}"
                                                class="nav-link {{ activeSegment('admin-role') }}">
                                                <i class="nav-icon fas fa-network-wired"></i>
                                                <p>Admin Role</p>
                                            </a>
                                        </li>
                                    @endif
                                    @hasPermission(config('constants.role_modules.list_sites_management.value'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('sites.index') }}"
                                                class="nav-link {{ activeSegment('sites') }}">
                                                <i class="nav-icon fas fa-boxes"></i>
                                                <p>Sites</p>
                                            </a>
                                        </li>
                                    @endHasPermission
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>






                {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ activeSegment('settings') }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Settings</p>
                    </a>
                </li> --}}


                {{--                <li class="nav-item"> --}}
                {{--                    <a href="#" class="nav-link"> --}}
                {{--                        <i class="nav-icon fas fa-chart-line"></i> --}}
                {{--                        <p> --}}
                {{--                            Reports --}}
                {{--                            <i class="fas fa-angle-left right"></i> --}}
                {{--                        </p> --}}
                {{--                    </a> --}}
                {{--                    <ul class="nav nav-treeview"> --}}
                {{--                        <li class="nav-item"> --}}
                {{--                            <a href="{{ route('reports.salesReport') }}" class="nav-link {{ activeSegment('sales-report',3) }}"> --}}
                {{--                                <i class="far fa-circle nav-icon"></i> --}}
                {{--                                <p>Sales Report</p> --}}
                {{--                            </a> --}}
                {{--                        </li> --}}
                {{--                    </ul> --}}
                {{--                </li> --}}


                {{--                <li class="nav-item"> --}}
                {{--                    <a href="#" class="nav-link"> --}}
                {{--                        <i class="nav-icon fas fa-chart-line"></i> --}}
                {{--                        <p> --}}
                {{--                            Reports --}}
                {{--                            <i class="right fas fa-angle-left"></i> --}}
                {{--                        </p> --}}
                {{--                    </a> --}}
                {{--                    <ul class="nav nav-treeview"> --}}
                {{--                        <li class="nav-item"> --}}
                {{--                            <a href="{{ route('reports.salesReport') }}" class="nav-link {{ activeSegment('sales-report',3) }}"> --}}
                {{--                                <i class="far fa-circle nav-icon"></i> --}}
                {{--                                <p>Sales Report</p> --}}
                {{--                            </a> --}}
                {{--                        </li> --}}
                {{--                    </ul> --}}
                {{--                </li> --}}




                {{-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                </li> --}}

                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <span class="ps-3"> Reports</span>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">

                                <ul class="list-unstyled">
                                    @hasPermission(config('constants.role_modules.sales_report.value'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('reports.salesReport') }}"
                                                class="nav-link {{ activeSegment('sales-report', 3) }}">
                                                <i class="nav-icon fas fa-chart-line"></i>
                                                <p class="ps-3">Sales Report</p>
                                            </a>
                                        </li>
                                    @endHasPermission
                                    @hasPermission(config('constants.role_modules.reservation_report.value'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('reports.reservationReport') }}"
                                                class="nav-link {{ activeSegment('reservation-report', 3) }}">
                                                <i class="nav-icon fas fa-chart-line"></i>
                                                <p class="ps-3">Reservation Report</p>
                                            </a>
                                        </li>
                                    @endHasPermission
                                    @hasPermission(config('constants.role_modules.gift_card_report.value'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('reports.giftCardReport') }}"
                                                class="nav-link {{ activeSegment('gift-card-report', 3) }}">
                                                <i class="nav-icon fas fa-chart-line"></i>
                                                <p class="ps-3">Gift Card Report</p>
                                            </a>
                                        </li>
                                    @endHasPermission
                                    @hasPermission(config('constants.role_modules.tax_report.value'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('reports.taxReport') }}"
                                                class="nav-link {{ activeSegment('tax-report', 3) }}">
                                                <i class="nav-icon fas fa-chart-line"></i>
                                                <p class="ps-3">Tax Report</p>
                                            </a>
                                        </li>
                                    @endHasPermission
                                    @hasPermission(config('constants.role_modules.payment_report.value'))
                                        <li class="nav-item has-treeview">
                                            <a href="{{ route('reports.paymentReport') }}"
                                                class="nav-link {{ activeSegment('payment-report', 3) }}">
                                                <i class="nav-icon fas fa-chart-line"></i>
                                                <p class="ps-3">Payment Report</p>
                                            </a>
                                        </li>
                                    @endHasPermission
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- @hasPermission(config('constants.role_modules.sales_report.value'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('reports.salesReport') }}"
                            class="nav-link {{ activeSegment('sales-report', 3) }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Sales Report</p>
                        </a>
                    </li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.reservation_report.value'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('reports.reservationReport') }}"
                            class="nav-link {{ activeSegment('reservation-report', 3) }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Reservation Report</p>
                        </a>
                    </li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.gift_card_report.value'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('reports.giftCardReport') }}"
                            class="nav-link {{ activeSegment('gift-card-report', 3) }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Gift Card Report</p>
                        </a>
                    </li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.tax_report.value'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('reports.taxReport') }}"
                            class="nav-link {{ activeSegment('tax-report', 3) }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Tax Report</p>
                        </a>
                    </li>
                @endHasPermission
                @hasPermission(config('constants.role_modules.payment_report.value'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('reports.paymentReport') }}"
                            class="nav-link {{ activeSegment('payment-report', 3) }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Payment Report</p>
                        </a>
                    </li>
                @endHasPermission --}}
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>Logout</p>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                            @csrf
                        </form>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div><!-- Log on to codeastro.com for more projects -->
    <!-- /.sidebar -->
</aside>
