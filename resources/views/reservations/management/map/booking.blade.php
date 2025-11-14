@extends('layouts.admin')

@section('title', 'Reservation Management — Map')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/front/css/campground.css') }}">
    <style>
        #tooltip {
            background: green;
            border: 1px solid black;
            border-radius: 5px;
            padding: 5px;
        }
    </style>
@endpush

@section('content')
    <div id="tooltip" style="position: absolute; display: none;"></div>

    <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
         xmlns:xlink="http://www.w3.org/1999/xlink"
         viewBox="0 0 700 933">
        <image width="100%" height="933" xlink:href="{{ asset('storage/map/mapforwebsite.jpg') }}"></image>

        <text x="440" y="180" font-family="Verdana" font-size="15" fill="black">Click on a Green or Orange site</text>
        <text x="460" y="200" font-family="Verdana" font-size="15" fill="black">to see pricing or to book</text>

        <text x="505" y="232" font-family="Verdana" font-size="10" fill="black">Green sites are available</text>
        <ellipse cx="490" cy="228" rx="11" ry="6" fill="#66FF66" />

        <text x="505" y="250" font-family="Verdana" font-size="10" fill="black">Orange sites are available but do</text>
        <text x="505" y="260" font-family="Verdana" font-size="10" fill="black">not have the selected amenities</text>
        <ellipse cx="490" cy="252" rx="11" ry="6" fill="orange" />

        <text x="505" y="280" font-family="Verdana" font-size="10" fill="black">Red sites are not available.</text>
        <ellipse cx="490" cy="276" rx="11" ry="6" fill="red" />

        <text x="505" y="300" font-family="Verdana" font-size="10" fill="black">Blue sites have a booking in</text>
        <text x="505" y="310" font-family="Verdana" font-size="10" fill="black">progress. Check back later.</text>
        <ellipse cx="490" cy="300" rx="11" ry="6" fill="blue" />

        <a xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <image href="/buttons/booknow1.png" x="425" y="110" width="80"
                   onmousemove="showTooltip(evt, 'Click to Search');"
                   onmouseout="hideTooltip();" />
        </a>

        @foreach ($sites as $currentsite)
            @php

                $csite = trim($currentsite['siteid']);
                $chookup = $currentsite['hookup'] ?? '';
                $csiteclass = $currentsite['class'] ?? '';
                $crigtype = $currentsite['rigtypes'] ?? '';
                $booking = Session::get('booking');
                $fillcolor = '#66FF66'; // default green
                $filltext = '';
                $allowbooking = true;
                $lengthdetails = '';





                // Rig type validation
                if ($currentsite['status'] === 'JustShowMap') {
                    $type = 'JustShowMap';
                } elseif ($csiteclass === 'Amenity') {
                    $type = 'Amenity';
                } else {
                    $type = 'Normal';
                }

                if ($type === 'Normal') {
                    if (!empty($hookup) && $chookup !== $hookup && !in_array($csiteclass, ['Boat_Slips', 'Cabin'])) {
                        $fillcolor = 'orange';
                        $filltext .= "This site does not have your selected hookup. ($hookup)";
                    }

                    if ($currentsite['maxlength'] > 2) {
                        if (str_contains($csiteclass, 'RV_Sites')) {
                            $lengthdetails .= "($chookup Max Length is {$currentsite['maxlength']} feet)";
                        }


                    }

                    if (!empty($riglength)) {
                        if ($riglength < $currentsite['minlength'] || $riglength > $currentsite['maxlength']) {
                            $allowbooking = false;
                            $fillcolor = 'red';
                        } else {
                            $filltext .= 'Your rig will fit. ';
                        }
                    }

                    if (!str_contains($csiteclass, $siteclass)) {
                        if (!in_array($csiteclass, ['Boat_Slips', 'Cabin'])) {
                            $filltext .= 'This is not the type of site you are looking for. ';
                        }
                        $allowbooking = false;
                        $fillcolor = 'red';
                    }

                    if ($currentsite['incart'] !== 'Available' && $currentsite['reserved'] === 'Available') {
                        $fillcolor = 'blue';
                        if (!in_array($currentsite['cartid'], $cartIds)) {
                            $filltext .= 'This site is locked in another cart - check back later. ';
                            $allowbooking = false;
                        }
                    } elseif (trim($currentsite['reserved']) !== 'Available') {
                        $allowbooking = false;
                        $fillcolor = 'red';
                        $filltext = 'This site is already reserved.';
                    }
                }
            @endphp

            @if ($type !== 'Normal')
                <a xlink:href="{{ route('front.site-details', ['siteid' => $csite]) }}">
                    <{!! $currentsite['coordinates'] !!}
                        stroke="{{ $type === 'Amenity' ? 'black' : 'blue' }}"
                    stroke-opacity="1.0" opacity="0.7" fill="white"
                    onmousemove="showTooltip(evt, '{{ $currentsite['sitename'] }}');"
                    onmouseout="hideTooltip();" />
                </a>
            @else
                @if ($allowbooking)
                    <a xlink:href="{{ route('front.site-details', ['siteid' => $csite]) }}">
                        @endif

                        @php



                            $isAvailable = $currentsite['available'];
                          $isAvailableOnline = $currentsite['availableonline'];
                          $isSeasonal = $currentsite['seasonal'];
                          $isUnavailable = !$isAvailable || !$isAvailableOnline;




                                  $selectedClass = strtolower(trim($booking['siteclass'])); // from user input
                                  $csiteclassArray = array_map('strtolower', array_map('trim', explode(',', $csiteclass)));

                           $rigLength = (int) $booking['riglength']; // user input
                                  $minLength = (int) $currentsite['minlength'];
                                  $maxLength = (int) $currentsite['maxlength'];
                                          $isThisAnRvSite = in_array('rv_sites', $csiteclassArray);


                                  // normalize class names to avoid case mismatch
                                  $classMatch = in_array($selectedClass, $csiteclassArray);


                          // Optional: cleaner way to cast to boolean
                          $seasonAvailable = ($isSeasonal == '1');

                          // Fill color logic
                                  $fillcolor = ($seasonAvailable || $isUnavailable || !$classMatch) ? 'red' : ($fillcolor ?? 'green');


                             // Tooltip logic
                                  if (!$classMatch) {
                                      $filltext =  'This is not the type of site you are looking for.';
                                      $disableLink = true;
                                  } elseif ($isSeasonal || trim($currentsite['reserved']) !== 'Available') {
                                       if(!$classMatch)
                                    {
                                                    $filltext =  'This is not the type of site you are looking for.';

                                    }
                                    else
                                    {
                                             $filltext = 'This site is reserved';

                                    }
                                      $disableLink = true;

                                  } elseif ($isUnavailable) {
                                      $filltext = 'This site is not available';
                                      $disableLink = true;

                                  } else {
                                  if($fillcolor == 'blue')
                                  {
                                          $filltext = 'This site is in another customer’s cart.';
                                  }
                                  else{
                                              $filltext = 'This site is available. Click to review';

                                  }
                                      $disableLink = false;

                                  }

                                   if ($isThisAnRvSite && ($rigLength < $minLength || $rigLength > $maxLength)) {
                                      $rigTooBigOrSmall = true;
                                      $fillcolor = 'red';
                                      $filltext = 'Your rig will not fit.';
                                      $disableLink = true;
                                  }

                                  if($isThisAnRvSite && !empty($hookup) && $chookup !== $hookup)
                                  {
                                    if($isSeasonal)
                                  {

                                    if(!$classMatch)
                                    {
                                                    $filltext =  'This is not the type of site you are looking for.';

                                    }
                                    else
                                    {
                                             $filltext = 'This site is reserved';

                                    }
                                  }
                                  else
                                  {

                                          if(!$classMatch)
                                          {
                                          $filltext =  'This is not the type of site you are looking for.';
                                          $fillcolor = 'red';
                                          $disableLink = true;

                                          }else
                                          {
                                                $disableLink = false;

                                            $filltext = 'This site does not have the requested hookup';
                                          }


                                  }        }

                          $latestSeason = \DB::table('camping_seasons')
                              ->orderByDesc('id')
                              ->first();

                          $openingDay = \Carbon\Carbon::parse($latestSeason?->opening_day);
                          $closingDay = \Carbon\Carbon::parse($latestSeason?->closing_day);

                          $checkIn = \Carbon\Carbon::parse($booking['cid']);
                          $checkOut = \Carbon\Carbon::parse($booking['cod']);


                          if ($checkIn->lt($openingDay) || $checkOut->gt($closingDay)) {
                              $fillcolor = 'red';
                              $filltext = 'We are not open on those dates.';
                              $disableLink = true;
                          }
                              // Interaction disabling
                        @endphp






                        <{!! $currentsite['coordinates'] !!}
                            stroke="black"
                        stroke-opacity="1.0"
                        opacity="0.8"
                        fill="{{ $fillcolor }}"
                        style="cursor: {{ isset($disableLink) && $disableLink ? 'not-allowed' : 'pointer' }};"
                        onmousemove="showTooltip(evt, '{{ $currentsite['sitename'] }} {{ $lengthdetails }}<br>{{ $filltext }}');"
                        onmouseout="hideTooltip();"
                        @if(!$disableLink)
                            onclick="selectSite('{{ $currentsite['id'] }}')"
                        @else
                            onclick="event.stopPropagation(); event.preventDefault();" {{-- disables click without blocking hover --}}
                        @endif
                        />




                        @if ($allowbooking)
                    </a>
                @endif
            @endif
        @endforeach
    </svg>

@endsection

@push('js')
    <script>
        function showTooltip(evt, text) {
            let tooltip = document.getElementById("tooltip");
            tooltip.innerHTML = text;
            tooltip.style.display = "inline-block";
            tooltip.style.left = evt.pageX + 10 + 'px';
            tooltip.style.top = evt.pageY + 10 + 'px';
        }

        function hideTooltip() {
            document.getElementById("tooltip").style.display = "none";
        }
    </script>
@endpush
