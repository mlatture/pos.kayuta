<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'WebDaVinci Flow')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css"
        rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/daterangepicker/3.1/daterangepicker.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/autofill/2.7.0/css/autoFill.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">

    @yield('css')
    @stack('css')

    <style>
        div.dt-top-container {
            display: flex;

            text-align: center;
        }

        div.dt-center-in-div {
            margin: 0 auto;
            display: inline-block;
            text-align: center;
        }

        div.dt-filter-spacer {
            margin: 10px 0;
        }

        td.highlight {
            background-color: #F4F6F9 !important;
        }

        div.dt-left-in-div {
            float: left;
        }

        div.dt-right-in-div {
            float: right;
        }
    </style>

    <script>
        window.APP = @json([
            'currency_symbol' => config('settings.currency_symbol'),
            'warning_quantity' => config('settings.warning_quantity'),
        ]);


        setTimeout(() => {
            $('.alert-success, .alert-danger').fadeOut('slow');
        }, 4000);

        let routes = {
            reservationMgmtHome: @json(route('admin.reservation_mgmt.index', ['admin' => auth()->user()->id])),
            availability: @json(route('admin.reservation_mgmt.availability', ['admin' => auth()->user()->id])),
            cartAdd: @json(route('admin.reservation_mgmt.cart.add', ['admin' => auth()->user()->id])),
            cartItems: @json(route('admin.reservation_mgmt.cart.item.cartItems', ['admin' => auth()->user()->id])),
            custSearch: @json(route('admin.reservation_mgmt.customer.search', ['admin' => auth()->user()->id])),
            custCreate: @json(route('admin.reservation_mgmt.customer.create', ['admin' => auth()->user()->id])),
            couponApply: @json(route('admin.reservation_mgmt.coupon.apply', ['admin' => auth()->user()->id])),
            checkout: @json(route('admin.reservation_mgmt.checkout', ['admin' => auth()->user()->id])),
            giftcardLookup: @json(route('admin.reservation_mgmt.giftcard.lookup', ['admin' => auth()->user()->id])),
            cartItemRemove: @json(route('admin.reservation_mgmt.cart.item.remove', ['admin' => auth()->user()->id])),
            viewSiteDetails: @json(route('admin.reservation_mgmt.view.site.details', ['admin' => auth()->user()->id])),
            getCart: @json(route('admin.reservation_mgmt.get.cart', ['admin' => auth()->user()->id])),
            viewMap: @json(route('admin.reservation_mgmt.map.view', ['admin' => auth()->user()->id])),
            information: @json(route('admin.reservation_mgmt.map.information', ['admin' => auth()->user()->id])),

        };
    </script>
</head>

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">

        @include('layouts.partials.navbar')
        @include('layouts.partials.sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper" style="margin-left: 0">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>@yield('content-header')</h1>
                        </div>
                        <div class="col-sm-6 text-right">
                            @extends('components.receipt_printing')
                            @yield('content-actions')
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content" id="content-actions">
                @include('layouts.partials.alert.success')
                @include('layouts.partials.alert.error')
                @yield('content')
            </section>

        </div>
        <!-- /.content-wrapper -->

        @include('layouts.partials.footer')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark"></aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- Scripts -->
    <!-- Use the latest version of jQuery only once -->

    <!-- Bootstrap JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/daterangepicker/3.1/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Plugin JS -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js'></script>
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/autofill/2.7.0/js/dataTables.autoFill.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <!-- Custom JS -->
    <!-- <script src="{{ asset('js/app.js') }}"></script> -->
    <script src="{{ asset('js/reservations/payments.js') }}"></script>
    <script src="{{ asset('js/reservations/create.js') }}"></script>

    <script src="{{ asset('js/cart/create.js') }}"></script>
    <script src="{{ asset('js/cart/random.js') }}"></script>
    <script src="{{ asset('js/cart/paymentmethod.js') }}?v={{ time() }}"></script>

    <script>
        var cardknoxApiKey = "{{ env('CARDKNOX_API_KEY') }}";
    </script>
    @yield('js')
    @stack('js')
</body>

</html>
