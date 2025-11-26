@extends('layouts.admin')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/front/css/kayuta.css') }}">
    <script src="https://aframe.io/releases/1.2.0/aframe.min.js"></script>
    <style>
        /* Set black background for Flatpickr calendar */
        /*.flatpickr-calendar {*/
        /*    background-color: black !important;*/
        /*    color: white !important;*/
        /*}*/
        .flatpickr-current-month input.cur-year {
            background: black;
        }

        /*!* Style the month dropdown *!*/
        /*.flatpickr-monthDropdown-months {*/
        /*    background-color: black !important;*/
        /*    color: white !important;*/
        /*    border: none;*/
        /*}*/

        /*!* Style the year input field *!*/
        .flatpickr-current-month input.cur-year {
            background-color: white !important;
            color: black !important;
            border: none;
        }

        /*!* Style the days and selected dates *!*/
        /*.flatpickr-day {*/
        /*    background-color: black !important;*/
        /*    color: white !important;*/
        /*    border: none;*/
        /*}*/

        /*.flatpickr-day.selected {*/
        /*    background-color: #0e5fd8 !important; !* Blue for selected date *!*/
        /*    color: white !important;*/
        /*}*/

        /*!* Adjust hover effects for days *!*/
        /*.flatpickr-day:hover {*/
        /*    background-color: #444 !important;*/
        /*    color: white !important;*/
        /*}*/

        /*!* Optional: Customize the calendar border *!*/
        /*.flatpickr-calendar {*/
        /*    border: 1px solid #333 !important;*/
        /*    border-radius: 8px;*/
        /*}*/


        .single-slide-wrapper img {
            height: 500px;
            object-fit: cover;
        }

        * {
            box-sizing: border-box
        }

        .mySlides {
            display: none
        }

        img {
            vertical-align: middle;
        }

        /* Slideshow container */
        .slideshow-container {
            max-width: 1000px;
            position: relative;
            margin: auto;
        }

        /* Next & previous buttons */
        .prev,
        .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
        }

        /* Position the "next button" to the right */
        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        /* On hover, add a black background color with a little bit see-through */
        .prev:hover,
        .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        /* Caption text */
        .text {
            color: #f2f2f2;
            font-size: 15px;
            padding: 8px 12px;
            position: absolute;
            bottom: 8px;
            width: 100%;
            text-align: center;
        }

        /* Number text (1/3 etc) */
        /*.numbertext {*/
        /*    color: #f2f2f2;*/
        /*    font-size: 12px;*/
        /*    padding: 8px 12px;*/
        /*    position: absolute;*/
        /*    top: 0;*/
        /*}*/

        /* The dots/bullets/indicators */
        .dot {
            cursor: pointer;
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.6s ease;
        }

        .active,
        .dot:hover {
            background-color: #717171;
        }

        /* Fading animation */
        .fade {
            animation-name: fade;
            animation-duration: 1.5s;
        }

        @keyframes fade {
            from {
                opacity: .4
            }

            to {
                opacity: 1
            }
        }

        /* On smaller screens, decrease text size */
        @media only screen and (max-width: 300px) {

            .prev,
            .next,
            .text {
                font-size: 11px
            }
        }

        .kayuta-lake-sec {
            padding: 124px 0px 100px 0px !important;
        }

        .single-slide-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: auto;
        }

        .single-slide-wrapper figure {
            margin: 0;
            width: 100%;
            text-align: center;
        }

        .full-image {
            width: 100%;
            height: auto !important; /* Forces the image to keep its original aspect ratio */
            max-height: unset !important; /* Ensures no forced cropping */
            object-fit: contain !important; /* Makes sure the whole image is visible */
        }

    </style>
@endpush

@section('content')

    <?php
    $BLANK_TEXT = ["<br>", "<br/>", ""];

    ?>

    @php
        $booking = Session::get('booking');


        if(isset($booking['no_dates']))
        {
            $uscid = null;
            $uscod = null;
        }
    @endphp
    {{-- page header starts here --}}
    {{-- <section class="page-header" style="background-image: url('{{ asset('assets/front/img/theme-page-bg.webp') }}')">
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-heading-wrapper text-center">
                        <h2>Site Details</h2>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    {{-- page header ends here --}}



    <section class="kayuta-lake-sec">
        <div class="container">

            {{--            @dd($siteDetail);--}}
            {{-- <div class="row">
                <div class="col-lg-7">

                    <div class="card">
                        <div class="section-2-img">
                            <div class="slideshow-container">
                                <div class="mySlides fade_to_Nothing" style="display: block;">
                                    <div class="numbertext">1 / 1</div> <img src="{{ asset('assets/front/img/(D07).jpg') }}"
                                        style="width:100%">
                                    <div class="text"><a href="{{ asset('assets/front/img/(D07).jpg') }}"
                                            target="_new">Click
                                            here for
                                            full
                                            size image</a></div>
                                </div><a href="{{ asset('assets/front/img/(D07).jpg') }}" target="_new"> </a><a
                                    class="prev" onclick="plusSlides(-1)">❮</a>
                                <a class="next" onclick="plusSlides(1)">❯</a>
                            </div>
                        </div>
                        <br>
                        <div style="text-align:center">
                            <span class="dot active" onclick="currentSlide(1)"></span>
                        </div>
                    </div>
                    <p>
                    </p>
                    <div class="card-wide">
                        <p>Attributes: {{ $siteDetail->attributes ?? 'N/A' }}</p>
                        <p>Amenities: {{ $siteDetail->amenities ?? 'N/A' }}</p>
                    </div>
                    <br>
                    <div class="site-details w-100">Policies</div>
                    <div class="card">
                        <h5>
                            <b>CANCELLATIONS / DATE CHANGES / REFUNDS:</b>
                            <p>
                                Cancellations 10 days or more prior to arrival date are subject to a 15%
                                cancellation fee. There are no refunds for cancellations within 10 days
                                prior to the arrival date. There are no date changes allowed within 10
                                days prior to the arrival date. There are no refunds in the event of
                                forced closures due to COVID, other diseases, disasters, or due to other
                                reasons. For stays that qualify, only rain checks will be issued in the
                                event of forced closure. There are no refunds or discounts if an amenity,
                                activity, or event is not available, closed, or canceled.
                            </p>
                            <p>
                                YOUR ENTRY INTO THE PARK INDICATES YOUR ACCEPTANCE OF KAYUTA LAKE'S
                                POLICIES, TERMS, AND CONDITIONS.
                            </p>
                        </h5>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="site-details w-100">{{ $siteDetail->sitename }}</div>
                    @if ($uscid != $uscod && $siteclass != 'Amenity' && isset($siteDetail) && isset($rateTier))
                        <div class="card small-card">
                            <div class="card-body">
                                <p class="card-text mb-0">
                                    This site is available for your selected dates.<br>
                                    <span class="text-monospace">{{ $uscid }} - {{ $uscod }}</span><br>
                                    For a {{ $lengthofStay }} night stay the price is
                                    {{$dynamicPricing == 0 ? \App\CPU\Helpers::format_currency_usd($platformFee['total_price']) : '__'}}
                                    The average nightly rate
                                    is:
                                    {{$dynamicPricing == 0 ? \App\CPU\Helpers::format_currency_usd($platformFee['average_nightly']) : '__'}}
                                    <br>
                                    <br>
                                </p>
                                
                                <p class="card-text">
                                    {{ $bookingmessage }}<br>
                                </p>

                                <p>
                                    @if ($siteLock == 'On')
                                        {{ $siteLockMessage }}
                                    @endif
                                </p>
                                <p>
                                </p>
                                <div align="center">
                                    @if ($thissiteisavailable)
                                        @php
                                            $eventname = filter_var($eventname, FILTER_SANITIZE_SPECIAL_CHARS);
                                        @endphp
                                        <form action="{{ route('front.add-to-cart') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="cartid" value="{{ uniqid() }}">
                                            <input type="hidden" name="cid" value="{{ $uscid }}">
                                            <input type="hidden" name="cod" value="{{ $uscod }}">
                                            <input type="hidden" name="base" value="{{ $base }}">
                                            <input type="hidden" name="rateadjustment" value="{{ $rateadjustment }}">
                                            <input type="hidden" name="extracharge" value="{{ $extracharge }}">
                                            <input type="hidden" name="riglength" value="{{ $riglength }}">
                                            <input type="hidden" name="siteid" value="{{ $siteid }}">
                                            <input type="hidden" name="siteclass" value="{{ $siteclass }}">
                                            <input type="hidden" name="taxrate" value="{{ $taxrate }}">
                                            <input type="hidden" name="nights" value="{{ $lengthofStay }}">
                                            <input type="hidden" name="description"
                                                value="{{ $lengthofStay }} nights in site {{ $siteid }}">
                                            <input type="hidden" name="email" value="">
                                            <input type="hidden" name="subtotal" value="{{ $platformFee['total_price'] }}">
                                            <input type="hidden" name="rid" value="uc">
                                            <input type="hidden" name="events" value="{{ $eventname }}">
                                            <input type="checkbox" name="sitelock" checked=""> Site Lock fee
                                            {{ \App\CPU\Helpers::format_currency_usd($siteLockFee) }} 
                                            
                                            <input type="image" type="submit"
                                                src="{{ asset('assets/front/img/addtocart.png') }}" alt="Add to Cart"
                                                width="180">
                                        </form>
                                    @else
                                        <a xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                            class="btn">
                                            <img src="{{ asset('assets/front/img/changesearch1.png') }}" x="425" y="100"
                                                width='180' alt="Change Search" />
                                        </a>
                                    @endif
                                </div>
                                <p></p>
                            </div>
                        </div>
                    @else
                        <div class="card small-card">
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($uscid != '')
                                        This site is not available for your selected dates.<br> {{ $uscid }} -
                                        {{ $uscod }}<br>
                                    @else
                                        Check availability for this site<br>
                                    @endif
                                <div align="center">
                                    <a xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                        class="btn">
                                        <image src="{{ asset('assets/front/img/booknow1.png') }}" class="w-75" />
                                    </a>
                                </div>
                                <br><br></b>
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div> --}}

            {{--            @dd(Session::get('booking'));--}}

            {{--            @dd($uscid,$uscod);--}}


            <div class="row">
                <div class="col-lg-6">
                    <div class="site-detail-wrapper">
                        <h3>{{ $siteDetail->sitename }}</h3>

                        {{--                        @if(!$direct_link)--}}
                        @if ($uscid != $uscod && $siteclass != 'Amenity' && isset($siteDetail) && isset($rateTier))
                            @if ($lengthofStay < $minimumstay)
                                <p class="card-text mb-0">
                                    Site {{ $siteid }} is not available for your selected dates as there is a longer
                                    minimum stay required.<br>
                                    please adjust dates below.<br>
                                </p>
                            @else




                                @if (!$siteDetail->availableonline || !$siteDetail->available || $siteDetail->seasonal)
                                    <p class="card-text mb-0 priceDetails">
                                        This site is not available.
                                    </p>
                                @else


                                    @if($rangesIsInCampingSeason && $checkReservation)
                                        <p class="card-text mb-0 priceDetails">
                                            Site {{ $siteid }} is available for your selected dates.<br>
                                            {{ date('l, F jS Y', strtotime($uscid)) }} - To -
                                            {{ date('l, F jS Y', strtotime($uscod)) }}<br>
                                            For a {{ $lengthofStay }} night stay the price is
                                            {{$dynamicPricing == 0 ? $workingtotal : '__'}}
                                            The average nightly rate
                                            is:
                                            {{$dynamicPricing == 0 ? $avgnightlyrate : '__'}}
                                            <br>
                                            <br>
                                        </p>
                                        
                                        <br>

                                    @else
                                        @php
                                            $booking = Session::get('booking');
                                        @endphp
                                        @if(isset($booking['no_dates']))
                                            <p class="card-text mb-0 priceDetails">
                                                Check availability for this site
                                            </p>
                                        @elseif(!$checkReservation)
                                            <!-- <p class="card-text mb-0 priceDetails">
                                            <p class="card-text mb-0 priceDetails">
                                                Already booked on those
                                                dates( {{ \Carbon\Carbon::parse($uscid)->format('F j, Y') }}
                                                to {{ \Carbon\Carbon::parse($uscod)->format('F j, Y') }} )
                                            </p>
                                            </p> -->
                                        @else
                                            <p class="card-text mb-0 priceDetails">
                                                We are not open on those dates
                                            </p>
                                        @endif


                                    @endif




                                @endif
                            @endif




                            <p>
                                @if ($rangesIsInCampingSeason && $siteLock == 'On')
                                    {{ $siteLockMessage }}
                                @endif
                            </p>
                            <p>
                            </p>

                            @if ($thissiteisavailable && $rangesIsInCampingSeason)
                                @php
                                    $eventname = filter_var($eventname, FILTER_SANITIZE_SPECIAL_CHARS);
                                @endphp
                                <form action="{{ route('front.add-to-cart') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="hookups" value="{{ $siteDetail->hookup ?? '' }}">
                                    <input type="hidden" name="cartid" value="{{ uniqid() }}">
                                    <input type="hidden" name="cid" value="{{ $uscid }}">
                                    <input type="hidden" name="cod" value="{{ $uscod }}">
                                    <input type="hidden" name="base" value="{{ $base }}">
                                    <input type="hidden" name="rateadjustment" value="{{ $rateadjustment }}">
                                    @php


                                        $holdMinutes = (int) App\Model\BusinessSetting::where('type', 'cart_hold_time')->value('value');
$holdTime = Carbon\Carbon::now()->addMinutes($holdMinutes);

                                    @endphp

                                    <input type="hidden" name="holduntil" value="{{$holdTime}}">
                                    <input type="hidden" name="extracharge" value="{{ $extracharge }}">
                                    <input type="hidden" name="riglength" value="{{ $riglength }}">
                                    <input type="hidden" name="siteid" value="{{ $siteid }}">
                                    <input type="hidden" name="siteclass" value="{{ $siteclass }}">
                                    <input type="hidden" name="taxrate" value="{{ $taxrate }}">
                                    <input type="hidden" name="nights" value="{{ $lengthofStay }}">
                                    <input type="hidden" name="description"
                                           value="{{ $lengthofStay }} nights in site {{ $siteid }}">

                                    <input type="hidden" name="description"
                                           value="{{ $lengthofStay }} nights in site {{ $siteid }}">

                                    <input type="hidden" name="base_price" value="{{ $platformFee['average_nightly'] }}">
                                    <input type="hidden" name="email" value="">
                                    <input type="hidden" name="subtotal" value="{{ $platformFee['total_price'] }}">
                                    <input type="hidden" name="rid" value="uc">
                                    <input type="hidden" name="events" value="{{ $eventname }}">
                                    <input type="checkbox" name="sitelock" checked=""> Site Lock fee
                                    {{ \App\CPU\Helpers::format_currency_usd($siteLockFee) }}


                                    <div class="btn-wrapper mt-3">
                                        @if($siteDetail->taxType && $siteDetail->taxType->tax > 0 )
                                            <input type="checkbox" name="sitelock" checked disabled> Tax
                                            Fee: {{ $siteDetail->taxType->tax }} % of subtotal
                                        @else
                                        <!-- <p>No tax fee applicable.</p> -->
                                        @endif

                                        <div class="d-flex align-items-center" style="padding-top: 20px">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <button {{ $dynamicPricing == 0 ? '' : 'disabled'}} type="submit"
                                                            class="btn btn-primary me-3 cartButton">Add to Cart
                                                    </button>
                                                </div>

                                                {{--                                                    <div class="col-auwe to">--}}
                                                {{--                                                        <div class="form-item form-group mb-0" id="headerDateRange">--}}
                                                {{--                                                            <input type="text" id="headerDateRangeInput" class="form-input form-control detail-dateRange" placeholder="Select Check-in and Check-out Dates" required>--}}
                                                {{--                                                            <!-- Hidden fields for form submission -->--}}
                                                {{--                                                            <input type="hidden" class="header-checkin" name="cid" value="{{ old('cid') }}">--}}
                                                {{--                                                            <input type="hidden" class="header-checkout" name="cod" value="{{ old('cod') }}">--}}
                                                {{--                                                        </div>--}}
                                                {{--                                                    </div>--}}


                                                <div class="col-auto pe-4">
                                                    <button type="button" data-bs-toggle="modal"
                                                            data-bs-target="#availabilityModal" class="btn btn-primary">
                                                        Adjust Dates
                                                    </button>
                                                </div>

                                            </div>

                                            <div class="d-inline-flex align-items-center">
                                                @if(!empty($siteDetail->photo_360_url))
                                                    <button type="button" id="360_button" data-bs-toggle="modal"
                                                            data-image="{{ asset('shared_storage/sites/' . $siteDetail->photo_360_url) }}"
                                                            data-bs-target="#fullscreenModal"
                                                            class="btn btn-primary me-2">
                                                        View 360
                                                    </button>
                                                @endif
                                                @if(!empty($siteDetail->virtual_link))
                                                    <a target="_blank" href="{{ $siteDetail->virtual_link }}"
                                                       class="btn btn-primary text-dark">
                                                        Virtual Tour
                                                    </a>
                                                @endif
                                            </div>

                                        </div>
                                </form>
                            @else

                                @if (!$siteDetail->availableonline || !$siteDetail->available || $siteDetail->seasonal)

                                @else
                                    <button xlink:href="#" data-bs-toggle="modal" data-bs-target="#availabilityModal"
                                            class="btn btn-primary">Adjust Dates
                                    </button>
                                @endif

                            @endif
                        @else
                            <p class="card-text">
                                @if ($uscid != '')

                                    @php
                                        $booking = Session::get('booking');
                                    @endphp

                                    @if(isset($booking['no_dates']))
                                        Check availability for this site<br>

                                        <button xlink:href="#" data-bs-toggle="modal"
                                                data-bs-target="#availabilityModal"
                                                class="btn btn-primary">Check Availability
                                        </button>

                                    @else
                                        Site {{ $siteid }} is not available for your selected dates.<br>

                                    <!-- {{ $uscid }} -
                                            {{ $uscod }} -->

                                        <br>

                                        <button xlink:href="#" data-bs-toggle="modal"
                                                data-bs-target="#availabilityModal"
                                                class="btn btn-primary">Adjust Dates
                                        </button>
                                    @endif

                                @else
                                    Check availability for this site<br>



                            <div class="d-inline-flex align-items-center">

                                <button xlink:href="#" data-bs-toggle="modal" data-bs-target="#availabilityModal"
                                        class="btn btn-primary me-2">Check Availability
                                </button>
                                @if(!empty($siteDetail->photo_360_url))
                                    <button type="button" id="360_button" data-bs-toggle="modal"
                                            data-image="{{ asset('shared_storage/sites/' . $siteDetail->photo_360_url) }}"
                                            data-bs-target="#fullscreenModal" class="btn btn-primary me-2">
                                        View 360
                                    </button>
                                @endif
                                @if(!empty($siteDetail->virtual_link))
                                    <a target="_blank" href="{{ $siteDetail->virtual_link }}"
                                       class="btn btn-primary text-dark">
                                        Virtual Tour
                                    </a>
                                @endif
                            </div>
                            {{--                            <div class="row">--}}
                            {{--                                <div class="col-lg-12">--}}
                            {{--                                    <div class="form-item form-group mb-3" id="headerDateRange">--}}
                            {{--                                        --}}{{--                                        <label for="headerDateRange" class="form-label">Date Range</label>--}}
                            {{--                                        <input type="text" id="headerDateRangeInput" class="form-input form-control detail-dateRange" placeholder="Select Check-in and Check-out Dates" required>--}}

                            {{--                                        <!-- Hidden fields for form submission -->--}}
                            {{--                                        <input type="hidden" class="header-checkin" name="cid" value="{{ old('cid') }}">--}}
                            {{--                                        <input type="hidden" class="header-checkout" name="cod" value="{{ old('cod') }}">--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
                        @endif

                        {{-- <a xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn">
                        <image src="{{ asset('assets/front/img/booknow1.png') }}" class="w-75" />
                    </a> --}}
                        <br><br></b>
                        </p>
                        @endif

                        {{--                        @else--}}
                        {{--                            <div class="btn-wrapper mt-3">--}}
                        {{--                                <button xlink:href="#" type="button" data-bs-toggle="modal"--}}
                        {{--                                        data-bs-target="#availabilityModal" class="btn btn-primary">Check Availability</button>--}}
                        {{--                            </div>--}}
                        {{--                        @endif--}}
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                {{-- <div align="center">
                    <div class="btn-wrapper d-flex justify-content-end gap-2 mt-3">
                        <button xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"
                            class="btn btn-primary">Book Now</button>
                        <button xlink:href="#" data-bs-toggle="modal" data-bs-target="#BookModal"
                            class="btn btn-primary">Adjust Date</button>
                    </div>
                </div> --}}
            </div>
            <div class="col-lg-12 mt-4">
                <div class="slide-wrapper">

                   @php
    $images = $siteDetail->images;
    if (is_string($images)) {
        $decoded = json_decode($images, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $images = $decoded;
        } else {
            $images = []; // fallback to empty array on decode error
        }
    }
@endphp

@if (!empty($images) && count($images) > 0)
    @foreach ($images as $image)
        <div class="single-slide-wrapper">
            <figure>
                <img src="{{ asset('shared_storage/sites/' . $image) }}" class="full-image"
                     onerror="this.src='{{ asset('assets/front/img/(D07).jpg') }}'"
                     alt="Site Image">
            </figure>
        </div>
    @endforeach
@else
    <div class="single-slide-wrapper">
        <figure>
            <img src="{{ asset('assets/front/img/(D07).jpg') }}" class="full-image"
                 alt="Default Image">
        </figure>
    </div>
@endif



                </div>
            </div>
            <div class="col-lg-12">
                <div class="site-detail-wrapper">

                    @if($siteDetail->siteclass != "Cabin" && $siteDetail->maxlength > 5)
                        <div class="attr-wrapper my-2">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-center">
                                    <span class="property fw-bold">Maximum Rig Length : </span>
                                    <span class="value text-primary fw-bold ms-3">
                    {{$siteDetail->maxlength}} feet
                </span>
                                </li>
                            </ul>
                        </div>
                    @endif



                    @if( ($siteDetail->description) )
                        <div class="attr-wrapper my-2">
                            <span class="property fw-bold">Description : </span>
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-center">
                                    <span class="value text-primary fw-bold ms-3">
                                        {!! ($siteDetail->description) && !in_array($siteDetail->description, $BLANK_TEXT) ? $siteDetail->description : 'N/A' !!}</span>
                                </li>
                            </ul>
                        </div>
                    @endif

                    <div class="attr-wrapper my-2">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-center">
                                <span class="property fw-bold">Amenities : </span>
                                <span class="value text-primary fw-bold ms-3">
                                    {{ (is_array($siteDetail->amenities) ? str_replace('_', ' ', implode(',', $siteDetail->amenities)) : 'N/A') ?? 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>


                    <div class="attr-wrapper my-2">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-center">
                                <span class="property fw-bold">Attributes : </span>
                                <span class="value text-primary fw-bold ms-3">
                                    {!! $siteDetail->attributes ?? 'N/A' !!}</span>
                            </li>
                        </ul>
                    </div>


                    {{--                    @if(!$direct_link)--}}
                    @if ($uscid != $uscod && $siteclass != 'Amenity' && isset($siteDetail) && isset($rateTier))
                        <p class="card-text">
                            {{-- Site {{ $siteid }} is available for your selected dates.<br> {{ $uscid }} -
                                {{ $uscod }}<br>
                                For a {{ $lengthofStay }} night stay the price is
                                {{ \App\CPU\Helpers::format_currency_usd($platformFee['total_price']) }} The average nightly rate
                                is:
                                {{ \App\CPU\Helpers::format_currency_usd($platformFee['average_nightly']) }}<br> --}}
                            @if (!Str::contains($bookingmessage, '$0.00'))
                                {{ $bookingmessage }}
                            @endif
                            <br>
                        </p>

                        <p>
                        </p>
                        <p></p>
                    @else
                        {{-- <p class="card-text">
                                @if ($uscid != '')
                                    Site {{ $siteid }} is not available for your selected dates.<br>
                                    {{ $uscid }} -
                                    {{ $uscod }}<br>
                                @else
                                    Check availability for this site<br>
                                @endif
                            <div align="center">
                                <div class="btn-wrapper mt-3">
                                    <button xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                        class="btn btn-primary">Book Now</button>
                                </div>
                                <a xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn">
                                    <image src="{{ asset('assets/front/img/booknow1.png') }}" class="w-75" />
                                </a>
                            </div>
                            <br><br></b>
                            </p> --}}
                    @endif
                    {{--                    @endif--}}
                </div>
            </div>
            <div class="col-lg-12">
                <div class="site-detail-wrapper">
                    <!-- Table for Displaying Informations -->
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th colspan="2" class="text-center bg-primary text-white">Important Information</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($informations as $info)
                            <!-- Title Row -->
                            <tr>
                                {{--                                <td class="fw-bold" style="width: 150px;">Title</td>--}}
                                <td class="text-primary">
                                    {{ $info['title'] ?? 'N/A' }}
                                </td>
                            </tr>

                            <!-- Description Row (CKEditor Content) -->
                            <tr>
                                {{--                                <td class="fw-bold">Description</td>--}}
                                <td class="text-primary">
                                    <!-- Render HTML Content from CKEditor -->
                                    {!! $info['description'] ?? 'N/A' !!}
                                </td>
                            </tr>
                        @endforeach

                        <!-- Example Static Content (Optional) -->
                        {{--                        <tr>--}}
                        {{--                            <td class="fw-bold">Price</td>--}}
                        {{--                            <td class="text-success">$199.99</td>--}}
                        {{--                        </tr>--}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{--        @if(count($business_settings) != 0)--}}
            {{--                <div>--}}
            {{--                    @foreach ($business_settings as $info)--}}
            {{--                        <div class="policy-wrapper py-5">--}}
            {{--                            <div class="txt-wrap mt-4">--}}
            {{--                                <h3>{{ $info->title }}</h3>--}}
            {{--                                <br>--}}
            {{--                                {!! $info->description !!}--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    @endforeach--}}
            {{--                </div>--}}
            {{--            @endif--}}
        </div>


        {{-- </div> --}}
        </div>

    </section>
    <div class=" modal fade" id="BookModal" tabindex="-1" aria-labelledby="exampleBookModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg	 modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title text-center w-100 text-uppercase" id="exampleBookModalLabel">Adjust
                        Dates</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="ri-close-line"></i></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-item form-group mb-3">
                                    <label for="input1" class="form-label ">Check In Date</label>
                                    <input class="form-input form-control" type="date" placeholder="Check In Date"
                                           name="cid" id="cid_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-item form-group mb-3">
                                    <label for="input1" class="form-label">Check Out Date </label>
                                    <input class="form-input form-control" id="cod_date" type="date"
                                           placeholder="Check Out Date" name="cod" size="5" required>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-item book-now form-group mb-3">
                                    <button type="submit" class="btn btn1 btn-primary  w-100">Book Now</button>
                                </div>
                            </div>
                            {{-- <div class="col-lg-6">
                                <div class="form-item book-now form-group mb-3">
                                    <button href="" class="btn btn1 btn-primary border-background  w-100">My Dates
                                        are
                                        Flexible</button>
                                </div>
                            </div> --}}
                        </div>
                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Book Now</button>
                                            <button type="button" class="btn btn-primary">My Dates are Flexible</button> -->
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="availabilityModal" tabindex="-1" aria-labelledby="exampleBookModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title text-center w-100 " id="exampleBookModalLabel">Select Dates</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                <form id="availabilityForm" action="" method="GET" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-item form-group mb-3" id="headerDateRange">
                                    <input type="text" id="headerDateRangeInput"
                                           class="form-input form-control detail-dateRange"
                                           placeholder="Select Check-in and Check-out Dates" required>
                                    <input type="hidden" class="header-checkin" name="cid" value="{{ old('cid') }}">
                                    <input type="hidden" class="header-checkout" name="cod" value="{{ old('cod') }}">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" class="" name="siteId" value="{{ $siteid }}">

                        <div class="row">

                            {{--                            <div class="col-lg-6">--}}
                            {{--                                <div class="form-item form-group mb-3">--}}
                            {{--                                    <label for="input1" class="form-label">Site Class</label>--}}
                            {{--                                    <select name="siteclass" class="form-select form-input form-control siteclass-active" data-form-input>--}}
                            {{--                                        @foreach ($siteClasses as $siteclass)--}}
                            {{--                                            <option value="{{$siteclass->siteclass}}">--}}
                            {{--                                                {{ $siteclass->siteclass }}--}}
                            {{--                                            </option>--}}
                            {{--                                        @endforeach--}}
                            {{--                                    </select>--}}
                            {{--                                    <span class="after-field">Select the type of site you are looking for.</span>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}

                            {{--                            <div class="col-lg-6">--}}
                            {{--                                <div class="row other-sites">--}}
                            {{--                                    <div class="col-lg-12">--}}
                            {{--                                        <div class="form-item form-group mb-3">--}}
                            {{--                                            <label for="input1" class="form-label">Rig length</label>--}}
                            {{--                                            <input type="text" name="riglength" size="3" value="" class="form-input form-control header-riglength">--}}
                            {{--                                        </div>--}}
                            {{--                                        <p class="mb-0">This is the total length of your camper.</p>--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
                        </div>

                        <div class="row other-sites">
                            <div class="col-lg-12" style="display:none">
                                <div class="form-item form-group mb-3">
                                    <label for="input1" class="form-label">Hookup</label>
                                    <select name="hookup" id="header-hookup" class="form-select form-input form-control"
                                            data-form-input>
                                        @foreach ($siteHookups as $siteHookup)
                                            <option value="{{ $siteHookup->sitehookup }}">
                                                {{ $siteHookup->sitehookup }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p>Hookup for water, sewer, electric (30 or 50 amps).</p>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-item book-now form-group mb-3">
                                    <button type="button" class="btn btn-primary w-100" id="checkAvailabilityBtn">Check
                                        Dates
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fullscreenModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0" id="modalBody">
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="dateRangeModal" tabindex="-1" aria-labelledby="dateRangeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {{--                <form method="POST" action="">--}}
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="dateRangeModalLabel">Adjust Date Ranges</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-item form-group mb-3" id="headerDateRange">
                                <label for="headerDateRange" class="form-label">Date Range</label>
                                <input type="text" id="headerDateRangeInput"
                                       class="form-input form-control detail-dateRange"
                                       placeholder="Select Check-in and Check-out Dates" required>

                                <!-- Hidden fields for form submission -->
                                <input type="hidden" class="header-checkin" name="cid" value="{{ old('cid') }}">
                                <input type="hidden" class="header-checkout" name="cod" value="{{ old('cod') }}">
                            </div>

                            <!-- Button to redirect to site-details with query parameters -->
                            <button type="button" id="submitDates" class="btn btn-primary">Check Dates</button>
                        </div>
                    </div>

                </div>
                {{--                    <div class="modal-footer">--}}
                {{--                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>--}}
                {{--                        <button type="submit" class="btn btn-primary">Save changes</button>--}}
                {{--                    </div>--}}
                {{--                </form>--}}
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>

        function formatDateWithSuffix(dateString) {
            const date = new Date(dateString);
            const day = date.getDate();
            const suffix = getDaySuffix(day);

            const month = date.toLocaleString('default', {month: 'long'});
            const year = date.getFullYear();

            return `${month} ${day}${suffix}, ${year}`;
        }

        function getDaySuffix(day) {
            if (day % 10 === 1 && day !== 11) return 'st';
            if (day % 10 === 2 && day !== 12) return 'nd';
            if (day % 10 === 3 && day !== 13) return 'rd';
            return 'th';
        }


        const webdavinci_api = "{{ env('WEBDAVINCI_API') }}";
        const webdavinci_api_key = "{{ env('WEBDAVINCI_API_KEY') }}";
        var uscid = @json($uscid);
        var uscod = @json($uscod);
        var siteid = @json($siteid);
        var rangesIsInCampingSeason = @json($rangesIsInCampingSeason);
        var checkReservation = @json($checkReservation);
        var dynamicPricing = @json($dynamicPricing);


    //     $(document).ready(function () {
    //         var dynamicPricing = @json($dynamicPricing);
    //         if (
    //             uscid &&
    //             uscod &&
    //             siteid &&
    //             rangesIsInCampingSeason &&
    //             checkReservation &&
    //             dynamicPricing == 1) {
    //             $.ajax({
    //                 url: `${webdavinci_api}/api/get_pricing`,
    //                 method: "POST",
    //                 contentType: "application/json",
    //                 data: JSON.stringify({
    //                     start_date: uscid, // Use Laravel variable
    //                     end_date: uscod,  // Use Laravel variable
    //                     site_id: siteid,  // Use Laravel variable
    //                 }),
    //                 headers: {
    //                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    //                     "X-API-KEY": `${webdavinci_api_key}`,
    //                     "X-DOMAIN":
    //                         window.location.protocol +
    //                         "//" +
    //                         window.location.hostname +
    //                         (window.location.port ? `:${window.location.port}` : ""),
    //                 },
    //                 success: function (api_data) {
    //                     console.log(api_data.total_price);
    //                     const total_price = api_data.total_price || 0;
    //                     const avg_nightly_rate = (total_price / api_data.number_of_nights);

    //                     $(".priceDetails").html(`
    //     Site ${siteid} is available for your selected dates.<br>
    //     ${formatDateWithSuffix(uscid)} - To - ${formatDateWithSuffix(uscod)}<br>
    //     For a ${api_data.number_of_nights} night stay, the total price is
    //     ${formatCurrency(total_price)}.<br>
    //     The average nightly rate is: ${formatCurrency(avg_nightly_rate)}.<br><br>
    // `);
    //                     $('input[name="base_price"]').val(avg_nightly_rate);
    //                     $('input[name="subtotal"]').val(total_price);

    //                     $(".cartButton").prop("disabled", false);
    //                 },

    //                 error: function (xhr, status, error) {
    //                     console.log("Error:", error);
    //                     console.log("Status:", status);
    //                     console.log("Response:", xhr.responseText);
    //                 },
    //             });
    //         }
    //     });

        // Function to dynamically update site availability
        function updateSiteAvailability(api_data) {
            const siteAvailability = $("#siteAvailability");
            const {pricing_data, total_price, number_of_nights} = api_data;

            // Calculate minimum stay requirement (e.g., 3 nights)
            const minimumStay = 3;

            if (number_of_nights < minimumStay) {
                siteAvailability.html(`
            <p class="card-text mb-0">
                Site ${siteid} is not available for your selected dates as there is a longer minimum stay required.<br>
                Please adjust dates below.<br>
            </p>
        `);
            } else {
                const formattedStartDate = formatDate(uscid);
                const formattedEndDate = formatDate(uscod);
                const avgNightlyRate = total_price / number_of_nights;

                siteAvailability.html(`
            <p class="card-text mb-0">
                Site ${siteid} is available for your selected dates.<br>
                ${formattedStartDate} - To - ${formattedEndDate}<br>
                For a ${number_of_nights}-night stay, the total price is ${formatCurrency(total_price)}.<br>
                The average nightly rate is: ${formatCurrency(avgNightlyRate)}<br>
            </p>
        `);
            }
        }

        // Helper functions
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});
        }

        function formatCurrency(amount) {
            return `$${amount.toFixed(2)}`;
        }


        // // Function to update subtotal (example implementation)
        // function updateSubtotal(api_data) {
        //     // Example logic for updating subtotal based on API response
        //     console.log("Updating subtotal with data:", api_data);
        // }

        $(document).ready(function () {
            // Listen for changes in the date range input
            $('#headerDateRangeInput').on('change', function () {
                const dateRange = $(this).val().split(' to '); // Format: 'checkin to checkout'
                const checkin = dateRange[0];
                const checkout = dateRange[1];

                // Set the hidden inputs if both dates are available
                if (checkin && checkout) {
                    $('.header-checkin').val(checkin);
                    $('.header-checkout').val(checkout);
                }
            });

            // Listen for "Check" button click
            $('#checkAvailabilityBtn').on('click', function () {
                // Get form values
                const checkin = $('.header-checkin').val();
                const checkout = $('.header-checkout').val();
                const siteclass = $('select[name="siteclass"]').val();
                const riglength = $('input[name="riglength"]').val();
                const hookup = $('select[name="hookup"]').val();
                const siteid = $('input[name="siteId"]').val();

                // Construct URL with query parameters if dates are available
                if (checkin && checkout) {
                    $.ajax({
                        url: '{{route('check-availability')}}', // Adjust to your actual route
                        method: 'POST',
                        data: {
                            checkin: checkin,
                            checkout: checkout,
                            siteclass: siteclass,
                            riglength: riglength,
                            hookup: hookup,
                            siteid: siteid,
                            _token: '{{ csrf_token() }}' // CSRF token for security
                        },
                        success: function (response) {
                            let url = new URL(window.location.href);
                            window.location.href = url.toString();
                        },
                        error: function (xhr) {
                            alert('An error occurred. Please try again.');
                        }
                    });
                } else {
                    alert('Please select both check-in and check-out dates.');
                }
            });
        });

        // $(document).ready(function () {
        //         // Assuming a date range picker is used for selecting the date range
        //         $('#headerDateRangeInput').on('change', function () {
        //             const dateRange = $(this).val().split(' to '); // assuming the value format is 'checkin to checkout'
        //             const checkin = dateRange[0];
        //             const checkout = dateRange[1];
        //
        //             // If both checkin and checkout dates are available
        //             if (checkin && checkout) {
        //                 $('.header-checkin').val(checkin);
        //                 $('.header-checkout').val(checkout);
        //
        //                 // Get the current URL
        //                 let currentUrl = window.location.href;
        //                 let url = new URL(currentUrl);
        //
        //                 // Update the query parameters
        //                 url.searchParams.set('checkin', checkin);
        //                 url.searchParams.set('checkout', checkout);
        //
        //                 // Redirect to the updated URL
        //                 window.location.href = url.toString();
        //             } else {
        //                 // alert('Please select both check-in and check-out dates.');
        //             }
        //         });
        //     });




        document.querySelectorAll('.detail-dateRange').forEach(function (el) {
            flatpickr(el, {
                mode: "range",
                minDate: "today",
                dateFormat: "Y-m-d",
                defaultDate: [],
                onChange: function (selectedDates) {
                    if (selectedDates.length === 2) {
                        let checkin = flatpickr.formatDate(selectedDates[0], "Y-m-d");
                        let checkout = flatpickr.formatDate(selectedDates[1], "Y-m-d");
                        document.querySelectorAll('.header-checkin').forEach(el => el.value = checkin);
                        document.querySelectorAll('.header-checkout').forEach(el => el.value = checkout);
                    }
                }
            });
        });

        $(document).ready(function () {
            $('#fullscreenModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var imageUrl = button.data('image');

                var modalBody = $(this).find('#modalBody');
                modalBody.empty();

                var aScene = $('<a-scene style="height: 100vh;">' +
                    '<a-sky id="modalImage" src="' + imageUrl + '" alt="360 view"></a-sky>' +
                    '</a-scene>');

                modalBody.append(aScene);
            });

            $('#fullscreenModal').on('hidden.bs.modal', function () {
                $(this).find('#modalBody').empty();
                location.reload();
            });
        });

        let slideIndex = 1;
        showSlides(slideIndex);

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");
            if (n > slides.length) {
                slideIndex = 1
            }
            if (n < 1) {
                slideIndex = slides.length
            }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex - 1].style.display = "block";
            dots[slideIndex - 1].className += " active";
        }
    </script>
@endpush
