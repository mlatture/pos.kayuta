<section class="kayuta-lake-sec">
    <div class="container">

        <div class="row">
            <div class="col-lg-6">
                <div class="site-detail-wrapper">
                    <h3>{{ $siteDetail->sitename }}</h3>
                    @if ($uscid != $uscod && $siteclass != 'Amenity' && isset($siteDetail) && isset($rateTier))
                        @if ($lengthofStay < $minimumstay)
                            <p class="card-text mb-0">
                                Site {{ $siteid }} is not available for your selected dates as there is a longer minimum stay required.<br>
                                please adjust dates below.<br>
                            </p>
                        @else
                            <p class="card-text mb-0">
                                Site {{ $siteid }} is available for your selected dates.<br>
                                {{ date('l, F jS Y', strtotime($uscid)) }} - To -
                                {{ date('l, F jS Y', strtotime($uscod)) }}<br>
                                For a {{ $lengthofStay }} night stay the price is
                                {{ \App\CPU\Helpers::format_currency_usd($workingtotal) }} The average nightly rate
                                is:
                                {{ \App\CPU\Helpers::format_currency_usd($avgnightlyrate) }}<br>
                                <br>
                            </p>
                        @endif

                        <p>
                            @if ($siteLock == 'On')
                                {{ $siteLockMessage }}
                            @endif
                        </p>
                        <p>
                        </p>

                        @if ($thissiteisavailable)
                            @php
                                $eventname = filter_var($eventname, FILTER_SANITIZE_SPECIAL_CHARS);
                            @endphp
                            <form id="reservationCartForm" action="{{route('reservations.add-to-cart')}}" method="post">
                                @csrf
                                <input type="hidden" name="cartid" value="{{ uniqid() }}">
                                <input type="hidden" name="cid" value="{{ $uscid }}">
                                <input type="hidden" name="bookingId" value="{{ $bookingId }}">
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
                                <input type="hidden" name="subtotal" value="{{ $workingtotal }}">
                                <input type="hidden" name="rid" value="uc">
                                <input type="hidden" name="events" value="{{ $eventname }}">
                                <input type="checkbox" name="sitelock" checked=""> Site Lock fee
                                {{ \App\CPU\Helpers::format_currency_usd($siteLockFee) }}
                                <div class="btn-wrapper mt-3">
                                    <button type="button" onclick="addSiteToCart(this)" class="btn btn-primary">Add to Cart</button>
{{--                                    <button xlink:href="#" type="button" data-bs-toggle="modal"--}}
{{--                                            data-bs-target="#exampleModal" class="btn btn-primary">Adjust Date</button>--}}
                                </div>
                            </form>
                        @else
                                Site not available in this dates, Please change the date from the start.
{{--                            <button xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"--}}
{{--                                    class="btn btn-primary">Adjust Date</button>--}}
                        @endif
                    @else
                        <p class="card-text">
                            @if ($uscid != '')
                                Site {{ $siteid }} is not available for your selected dates.<br>
                                {{ $uscid }} -
                                {{ $uscod }}<br>
                            @else
                                Check availability for this site<br>
                    @endif

                    {{-- <a xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn">
                    <image src="{{ asset('assets/front/img/booknow1.png') }}" class="w-75" />
                </a> --}}
                </div>
                <br><br></b>
                </p>
                @endif
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
                @if ($siteDetail->images && count($siteDetail->images) > 0)
                    @foreach ($siteDetail->images as $image)
                        <div class="single-slide-wrapper">
                            <figure>
                                <img src="{{ asset('storage/sites/' . $image) }}" class="w-100 img-fluid"
                                     alt="">
                            </figure>
                        </div>
                    @endforeach
                @else
                    <div class="single-slide-wrapper">
                        <figure>
                            <img src="{{ asset('assets/front/img/(D07).jpg') }}" class="w-100 img-fluid"
                                 alt="">
                        </figure>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-12">
            <div class="site-detail-wrapper">

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
                @if ($uscid != $uscod && $siteclass != 'Amenity' && isset($siteDetail) && isset($rateTier))
                    <p class="card-text">
                        {{-- Site {{ $siteid }} is available for your selected dates.<br> {{ $uscid }} -
                            {{ $uscod }}<br>
                            For a {{ $lengthofStay }} night stay the price is
                            {{ \App\CPU\Helpers::format_currency_usd($workingtotal) }} The average nightly rate
                            is:
                            {{ \App\CPU\Helpers::format_currency_usd($avgnightlyrate) }}<br> --}}
                        {{ $bookingmessage }}
                        <br>
                    </p>

                    <p>
                    </p>
                    <div>
                        <div class="policy-wrapper py-5">
                            <h5 class="text-uppercase">Policies</h5>
                            <div class="txt-wrap mt-4">
                                <h5>CANCELLATIONS / DATE CHANGES / REFUNDS:</h5>
                                <p>
                                    Cancellations 10 Days Or More Prior To Arrival Date Are Subject To A 15%
                                    Cancellation Fee.
                                    There Are No Refunds For Cancellations Within 10 Days Prior To The
                                    Arrival Date. There Are
                                    No Date Changes Allowed Within 10 Days Prior To The Arrival Date. There
                                    Are No Refunds In
                                    The Event Of Forced Closures Due To COVID, Other Diseases, Disasters, Or
                                    Due To Other
                                    Reasons. For Stays That Qualify, Only Rain Checks Will Be Issued In The
                                    Event Of Forced
                                    Closure. There Are No Refunds Or Discounts If An Amenity, Activity, Or
                                    Event Is Not
                                    Available, Closed, Or Canceled.
                                </p>
                                <p>
                                    YOUR ENTRY INTO THE PARK INDICATES YOUR ACCEPTANCE OF KAYUTA LAKE'S
                                    POLICIES, TERMS, AND
                                    CONDITIONS.
                                </p>
                            </div>
                        </div>
                        {{-- @if ($thissiteisavailable)
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
                                <input type="hidden" name="subtotal" value="{{ $workingtotal }}">
                                <input type="hidden" name="rid" value="uc">
                                <input type="hidden" name="events" value="{{ $eventname }}">
                                <input type="checkbox" name="sitelock" checked=""> Site Lock fee
                                {{ \App\CPU\Helpers::format_currency_usd($siteLockFee) }}
                                <div class="btn-wrapper mt-3">
                                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                                </div>
                            </form>
                        @else
                            <div class="btn-wrapper mt-3">
                                <button xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                    type="button" class="btn btn-primary">Add to Cart</button>
                            </div>
                        @endif --}}
                    </div>
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
            </div>
        </div>

    </div>


    {{-- </div> --}}
    </div>

</section>
