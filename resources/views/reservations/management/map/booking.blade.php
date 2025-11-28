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

    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" id="siteBtns" xmlns:xlink="http://www.w3.org/1999/xlink"
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
        {{-- 
        <a xlink:href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <image href="/buttons/booknow1.png" x="425" y="110" width="80"
                onmousemove="showTooltip(evt, 'Click to Search');" onmouseout="hideTooltip();" />
        </a> --}}

        @foreach ($sites as $currentsite)
            <{!! $currentsite['coordinates'] !!} data-id="{{ $currentsite['siteid'] }}" stroke="black" opacity="0.8"
                fill="{{ $currentsite['fillcolor'] }}"
                style="cursor: {{ $currentsite['disableLink'] ? 'not-allowed' : 'pointer' }};"
                onmousemove="showTooltip(evt, '{{ $currentsite['sitename'] }} ({{ $currentsite['filltext'] }}).');"
                onmouseout="hideTooltip();"
                @if ($currentsite['disableLink']) onclick="event.preventDefault(); event.stopPropagation();" @endif />
        @endforeach

    </svg>

    @include('reservations.modals.site-details');

@endsection

@push('js')
    <script>
        function showTooltip(evt, text) {
            let tooltip = document.getElementById("tooltip");
            tooltip.innerHTML = text;
            tooltip.style.color = "white";
            tooltip.style.display = "inline-block";
            tooltip.style.left = evt.pageX + 10 + 'px';
            tooltip.style.top = evt.pageY + 10 + 'px';
        }



        function hideTooltip() {
            document.getElementById("tooltip").style.display = "none";
        }

        const siteDetailsModal = new bootstrap.Modal('#siteDetailsModal', {
            keyboard: true
        });


        $('#siteBtns').on('click', '[data-id]', function() {
            const siteId = $(this).data('id');
            const start = '{{ request()->query('start_date') }}';
            const end = '{{ request()->query('end_date') }}';

            populateSiteDetails({}); // Clear previous data

            // Load site details view AJAX
            $.get(routes.viewSiteDetails, {
                    site_id: siteId,
                    uscid: start,
                    uscod: end
                })
                .done(res => {
                    populateSiteDetails(res);
                    siteDetailsModal.show();
                })
                .fail((jqXHR, textStatus, errorThrown) => {
                    console.error('Error fetching site details:', textStatus, errorThrown);
                    alert(
                        'An error occurred while fetching site details. Please try again later.'
                    );
                });

        });

        function populateSiteDetails(res) {
            const r = res.response ?? {}; // safety fallback

            // Load Important Information
            $.get(routes.information)
                .done(infoRes => {
                    const infos = infoRes.information || [];
                    const $infoCardBody = $('#infoCardBody'); 

                    $infoCardBody.empty();

                    if (infos.length > 0) {
                        $('#sdTitleInfo').text('Important Information');

                        infos.forEach(info => {
                            if (info.title && info.description) {
                                $infoCardBody.append(`
                                <div class="mb-3">
                                    <h6 class="text-dark mb-0 fw-bold">${info.title}</h6>
                                    <p class="small text-muted mb-0">${info.description}</p>
                                </div>
                            `);
                            }
                        });

                    } else {
                        // Handle case where no information is available
                        $('#sdTitleInfo').text('Information Not Available');
                        $infoCardBody.append(
                            '<p class="text-muted fst-italic">No important information currently listed for this site.</p>'
                            );
                    }
                });

            // Site
            $('#sdName').text(r.site?.name ?? '');
            $('#sdSiteId').text(r.site?.site_id ?? '—');
            $('#sdClass').text(r.site?.class?.replace(/_/g, ' ') ?? '');
            $('#sdHookup').text(r.site?.hookup ?? '');

            // Image
            const img = r.media?.images?.[0] ?? r.media?.gallery?.[0] ?? null;
            $('#sdImage').attr('src', img ? `shared_storage/${img}` : '/no-image.png');

            // Attributes
            $('#sdAttributes').text(r.site?.attributes ?? '');

            // Amenities
            $('#sdAmenities').empty();
            if ((r.site?.amenities || []).length > 0) {
                (r.site.amenities).forEach(a => {
                    $('#sdAmenities').append(
                        `<li><span class="badge badge-pill badge-primary text-white">${a.replaceAll('_', ' ')}</span></li>`
                    );
                });
            } else {
                $('#sdAmenities').append('<li class="text-muted small">None Listed</li>');
            }
            // Rig
            if (r.constraints?.rig_length) {
                $('#sdRig').text(`${r.constraints.rig_length.min}ft – ${r.constraints.rig_length.max}ft`);
            }

            // Pricing
            $('#sdAvgNight').text(r.pricing?.average_nightly ?? '0');
            $('#sdTotal').text(r.pricing?.total ?? '0');
            $('#sdStay').text(r.pricing?.range?.length_of_stay ?? '0');
            $('#sdPlatformFee').text(r.pricing?.platform_fee?.average_nightly ?? '0');

            // Policies
            $('#sdMinStay').text(r.policies?.minimum_stay ?? '—');
            if (r.policies?.site_lock?.enabled) {
                $('#sdSiteLock').text(`Yes (+$${r.policies.site_lock.fee})`);
            } else {
                $('#sdSiteLock').text("No");
            }
            $('#sdLockMessage').text(r.policies?.site_lock?.message ?? '');
        }
    </script>
@endpush
