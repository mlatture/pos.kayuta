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




        <a xlink:href="#" id="returnBtn" onmousemove="showTooltip(evt, 'Click to Return')" onmouseout="hideTooltip()">

            <rect id="returnBtnBg" x="520" y="20" width="150" height="30" rx="10" ry="10" fill="#1e90ff"
                stroke="#0d6efd" stroke-width="2" style="cursor: pointer;">

            </rect>

            <text x="598" y="36" text-anchor="middle" dominant-baseline="middle" font-size="18" font-family="Verdana"
                fill="white" style="pointer-events: none; font-weight: bold;">
                Return
            </text>
        </a>



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



        @foreach ($sites as $currentsite)
            <{!! $currentsite['coordinates'] !!} data-id="{{ $currentsite['siteid'] }}" stroke="black" opacity="0.8"
                fill="{{ $currentsite['fillcolor'] }}"
                style="cursor: {{ $currentsite['disableLink'] ? 'not-allowed' : 'pointer' }};"
                onmousemove="showTooltip(evt, '{{ $currentsite['sitename'] }} ({{ $currentsite['filltext'] }}).');"
                onmouseout="hideTooltip();"
                @if ($currentsite['disableLink']) onclick="event.preventDefault(); event.stopPropagation();" @endif />
        @endforeach

    </svg>

    @include('reservations.modals.site-details')

@endsection

@push('js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#returnBtn').on('click', function() {
            var data = {!! \Illuminate\Support\Js::from($booking) !!};
            console.log('Test Data Booking', data['cid'], data['cod']);
            const url = `${routes.reservationMgmtHome}?cid=${encodeURIComponent(data['cid'])}&cod=${encodeURIComponent(data['cod'])}`;
            window.location.href = url;

        })

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
                    populateSiteDetails(res, start, end);
                    siteDetailsModal.show();
                })
                .fail((jqXHR, textStatus, errorThrown) => {
                    console.error('Error fetching site details:', textStatus, errorThrown);
                    alert(
                        'An error occurred while fetching site details. Please try again later.'
                    );
                });

        });

        function populateSiteDetails(res, start, end) {
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
            const siteImages = r.media?.images ?? r.media?.gallery ?? [];
            const container = $('#sdImagesContainer');
            container.empty();

            const imageBasePath = '/storage/sites/';
            let slidesHtml = '';

            if (siteImages.length > 0) {
                siteImages.forEach((imgFilename, index) => {
                    const isActive = index === 0 ? 'active' : '';
                    const imgSrc = `${imageBasePath}${imgFilename}`;

                    slidesHtml += `
                        <div class="carousel-item ${isActive}">
                            <img src="${imgSrc}" class="d-block w-100 rounded-top" alt="Site Image ${index + 1}"
                                style="height: 400px; object-fit: cover;">
                        </div>
                    `;
                });
            } else {
                slidesHtml += `
                    <div class="carousel-item active">
                        <img src="/no-image.png" class="d-block w-100 rounded-top" alt="No Image Available"
                            style="height: 400px; object-fit: cover;">
                    </div>
                `;
            }

            container.html(slidesHtml);
            const $carouselElement = $('#siteImagesCarousel');
            if ($carouselElement.length) {
                const nativeCarouselElement = $carouselElement[0];

                const bsCarousel = bootstrap.Carousel.getInstance(nativeCarouselElement);
                if (bsCarousel) {
                    bsCarousel.dispose();
                }

                new bootstrap.Carousel(nativeCarouselElement);
            }



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

            // Policies
            $('#sdMinStay').text(r.policies?.minimum_stay ?? '—');
            const siteLockEnabled = r.policies?.site_lock?.enabled ?? false;
            const siteLockFee = r.policies?.site_lock?.fee ?? 0;
            const lockMessage = r.policies?.site_lock?.message ?? '-';

            $('#siteLockToggle').prop('checked', siteLockEnabled);

            if (siteLockEnabled) {
                $('#sdSiteLockFeeDisplay').text(`Yes (+$${siteLockFee})`).removeClass('bg-secondary').addClass(
                    'bg-success');
            } else {
                $('#sdSiteLockFeeDisplay').text("No").removeClass('bg-success').addClass('bg-secondary');
            }

            $('#sdLockMessage').text(lockMessage);
            const csrfToken = $('meta[name="csrf-token"]').attr('content');


            // Add to Cart button data
            $(document).off('click', '#addToCartSite').on('click', '#addToCartSite', async function() {
                const btn = $(this);
                btn.prop('disabled', true);
                const adults = parseInt($('#occupantsAdults').val()) || 2;
                const children = parseInt($('#occupantsChildren').val()) || 0;
                const siteLockFee = $('#siteLockToggle').is(':checked') ? 'on' : 'off';
                if (adults + children === 0) {
                    alert('Please enter at least one occupant.');
                    return;
                }


                try {
                    //  Create or restore shared cart
                    const {
                        cartId,
                        cartToken
                    } = await createOrRestoreCart1();

                    const payload = {
                        cart_id: parseInt(cartId),
                        token: cartToken,
                        site_id: r.site?.site_id,
                        start_date: start,
                        end_date: end,
                        occupants: {
                            adults,
                            children
                        },
                        add_ons: [],
                        price_quote_id: null,
                        site_lock_fee: siteLockFee
                    };


                    // Add item
                    const itemRes = await $.ajax({
                        url: routes.cartItems,
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(payload),

                    });

                    if (itemRes.ok) {
                        window.location.href = routes.reservationMgmtHome;
                    }
                } catch (err) {
                    console.error('Error adding to cart', err);
                } finally {
                    btn.prop('disabled', false);
                }
            });

            async function createOrRestoreCart1() {
                let cartId, cartToken;

                // Check existing cart first
                const stored = JSON.parse(localStorage.getItem('cartInfo') || '{}');
                const now = new Date();

                if (stored.cart_id && stored.cart_token && new Date(stored.expires_at) > now) {
                    cartId = stored.cart_id;
                    cartToken = stored.cart_token;
                } else {
                    const cartRes = await $.ajax({
                        url: routes.cartAdd,
                        method: 'POST',
                        contentType: 'application/json',
                    });

                    const data = cartRes.data;

                    cartId = data.cart_id;
                    cartToken = data.cart_token;
                    // Compute expiration datetime
                    const expiresAt = new Date();
                    expiresAt.setSeconds(expiresAt.getSeconds() + (cartRes.meta?.ttl_seconds || 1800));

                    // Save to localStorage
                    localStorage.setItem('cartInfo', JSON.stringify({
                        cart_id: cartId,
                        cart_token: cartToken,
                        expires_at: expiresAt.toISOString(),
                    }));
                }

                return {
                    cartId,
                    cartToken
                };
            }

        }
    </script>
@endpush
