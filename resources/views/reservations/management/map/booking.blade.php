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

    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 700 933">
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
        {{-- 
        <a xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <image href="/buttons/booknow1.png" x="425" y="110" width="80"
                onmousemove="showTooltip(evt, 'Click to Search');" onmouseout="hideTooltip();" />
        </a> --}}

        @foreach ($sites as $currentsite)
            <{!! $currentsite['coordinates'] !!}
                stroke="black"
                opacity="0.8"
                fill="{{ $currentsite['fillcolor'] }}"
                style="cursor: {{ $currentsite['disableLink'] ? 'not-allowed' : 'pointer' }};"
                onmousemove="showTooltip(evt, '{{ $currentsite['sitename'] }} — {{ $currentsite['filltext'] }}');"
                onmouseout="hideTooltip();"
                @if (!$currentsite['disableLink'])
                    onclick="selectSite('{{ $currentsite['id'] }}')"
                @else
                    onclick="event.stopPropagation(); event.preventDefault();"
                @endif
            />
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
