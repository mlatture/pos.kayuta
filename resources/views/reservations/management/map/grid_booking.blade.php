@extends('layouts.admin')

@section('title', 'Reservation Management — Check Availability')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/front/css/amenities.css') }}">
@endpush
@section('content')
    <style>
        .hotel-title {
            background-color: #efc368; /* Theme color */
            padding: 10px;
            text-align: center;
            color: #fff;
            font-size: 2em;
        }

        .hotel-listing {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .hotel-card {
            display: flex;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: 0.3s ease;
            max-width: 100%;
        }

        .hotel-card:hover {
            transform: scale(1.02);
        }

        .hotel-image img {
            width: 300px;
            height: 100%;
            object-fit: cover;
        }

        .hotel-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            width: 100%;
        }

        .hotel-name {
            font-size: 1.6em;
            font-weight: bold;
            margin: 0 0 10px;
            color: #333;
        }

        .reservation-dates, .rating {
            font-size: 1em;
            color: #666;
            margin: 5px 0;
        }

        .rating {
            color: #FFD700;
        }

        .btn-book {
            background-color: #efc368; /* Theme color */
            color: #000; /* Black font color */
            padding: 10px 15px;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            width: 100%;
            max-width: 150px;
            transition: background-color 0.3s;
        }

        .btn-book:hover {
            background-color: #d9aa58; /* Darker shade of theme color */
        }

        @media (max-width: 768px) {
            .hotel-card {
                flex-direction: column;
            }
            .hotel-image img {
                width: 100%;
                height: 200px;
            }
        }
    </style>
    @php
        $booking    =   Session::get('booking');
    @endphp
    {{-- page header starts here --}}
    <section class="page-header" style="background-image: url('{{ asset('assets/front/img/theme-page-bg.webp') }}')">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-heading-wrapper text-center">
                        <h2>Grid View</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- page header ends here --}}

    <section>
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="text-wrapper">
                        <div class="not-the-left-column">
                            <div class="site-details">
                                Click below to book
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="page-heading-wrapper text-center">
                <h2 class="hotel-title">Site Listings</h2>
            </div>
            <div class="hotel-listing">
{{--@dd($sites[0])--}}
                @foreach ($sites as $currentsite)
                    @php
                        // Variables from your original function
                        $siteId = $currentsite['siteid'];
                        $cid = $booking['cid']; // Example: current date
                        $availability = $currentsite['availability']; // e.g. "A,A,N,A,A,A"
                        $sitehookup = $currentsite['hookup'];
                        $hookup = $booking['hookup'] ?? '';

                        // Generate the availability status as an associative array
                        $days = explode(",", $availability);
                        $availabilityStatus = [];
                        foreach ($days as $index => $day) {
                            // Use the index to create a corresponding date key
                            $dateKey = date('Y-m-d', strtotime("+$index days", strtotime('2024-09-01'))); // Adjust the base date accordingly
                            $availabilityStatus[$dateKey] = $day; // 'A' or 'N'
                        }

                        // Retrieve the flat rate and tier details
                        $rateTier = DB::table('rate_tiers')->where('tier', $currentsite->hookup)->first();
                        $flatRate = null;

                        if ($rateTier && $rateTier->useflatrate == 1) {
                            $flatRate = $rateTier->flatrate;
                        }

                        // Prepare availability dates
                        $datesString = $currentsite['availability_dates']; // e.g. "2024-09-01,2024-09-02,..."
                        $availabilityDates = explode(',', $datesString); // Convert to array

                        // Filter availability dates based on the $stay variable
                        //if ($stay === 'weekend') {
                            // Filter for weekends (Saturday and Sunday)
                            //$availabilityDates = array_filter($availabilityDates, function ($date) {
                                //return date('N', strtotime($date)) >= 6; // 6: Saturday, 7: Sunday
                          //  });
                        //} elseif ($stay === 'week') {
                            // Filter for weekdays (Monday to Friday)
                            //$availabilityDates = array_filter($availabilityDates, function ($date) {
                           //     return date('N', strtotime($date)) < 6; // 1: Monday to Friday
                          //  });
                        //}
                    @endphp
                    <div class="hotel-card">

                    <div class="hotel-image">
                    <img
    src="{{ isset($currentsite->images[0]) ? asset('shared_storage/sites/' . $currentsite->images[0]) : 'https://images.trvl-media.com/lodging/52000000/51290000/51282600/51282532/1093ec84.jpg?impolicy=resizecrop&rw=598&ra=fit' }}"
    alt="Hotel Image"
    class="w-100 img-fluid"
    style="height: 100%; width: 100%; object-fit: cover;">

                    </div>


                        <div class="hotel-details">
                            <div class="hotel-name">Site: {{ $currentsite->sitename ?? '-' }}</div>
{{--                            <div class="rating">Rating: ★★★★☆ (4.5)</div>--}}
{{--                            <div class="reservation-dates">Maximum Guests: {{ $currentsite->maxlength  ?? 0 }}</div>--}}

                            <div class="reservation-dates">Hookup Type: {{ !empty($currentsite->hookup) ? $currentsite->hookup : 'N/A' }}</div>
                            <div class="reservation-dates">
                                Available For:
                                {{
                                    implode(', ', array_map(function($class) {
                                        return str_replace(['RV_Sites', 'Tent_Sites'], ['RVs', 'Tents'], $class);
                                    }, explode(',', $currentsite->class)))
                                }}.
                            </div>

                            <div class="reservation-dates">Max Length: {{ !empty($currentsite->maxlength) ? $currentsite->maxlength . ' feet.' : 'N/A' }}</div>
                            <div class="reservation-dates">Minimum Stay: {{ !empty($rateTier->minimumstay) ? $rateTier->minimumstay : 'N/A' }} Nights</div>
                            <div class="reservation-dates">Details: {!! $currentsite->attributes !!} {{ ucwords(implode(', ', str_replace('_', ' ', $currentsite->amenities))) }}</div>
{{--                            <div class="reservation-dates">Availability Range: {{$currentsite->reserved}}</div>--}}
{{--                            @if(empty($currentsite->cartid))--}}
{{--                                <div class="reservation-dates">{{'This site does not have your selected hookup. ( ' . $currentsite->hookup . ' ) Your rig will fit.'}}</div>--}}
{{--                            @endif--}}
                            @if(!empty($eventname))
                                <div class="reservation-dates">{{ "This booking is during " . $eventname . ". Sites booked during these events are subject to a surcharge of " . \App\CPU\Helpers::format_currency_usd($extracharge) . "." }}</div>
                            @endif

{{--                            <div class="reservation-dates"> Dates :--}}
{{--                                <select class="mt-3 form-control siteclass-active">--}}
{{--                                    <option value="">Select Availability Date</option>--}}
{{--                                    @foreach ($availabilityDates as $day)--}}
{{--                                        @php--}}
{{--                                            // Check if the date exists in the availability status array--}}
{{--                                            $status = isset($availabilityStatus[$day]) ? $availabilityStatus[$day] : 'N';--}}
{{--                                            $humanReadableDate = date('F j, Y', strtotime($day)); // Format the date--}}
{{--                                            $availabilityText = ($status === 'A') ? 'Available' : 'Not Available';--}}
{{--                                        @endphp--}}
{{--                                        <option value="{{ $day }}">--}}
{{--                                            {{ $humanReadableDate }} - {{ $availabilityText }}--}}
{{--                                        </option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}

{{--                            @if($flatRate != null)--}}
{{--                                <div class="reservation-dates">Flat Rate: ${{ $flatRate }}</div>--}}
{{--                            @endif--}}

                            @php
                                // Call to your function to determine the style for booking
                                $style = '';
                                if ($hookup != "") {
                                    $style = ($hookup != $sitehookup) ? "background-color: #efc368;" : "background-color: #66FF66;";
                                } else {
                                    $style = "background-color: #66FF66;";
                                }
                            @endphp
                            <a href="{{ route('front.site-details', ['siteid' => $siteId]) }}" class="btn-book" style="{{ $style }}">SHOW DETAILS</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('js')
@endpush
