<tr data-site-id="{{ $site->id }}" data-site-siteid="{{ $site->siteid }}" data-site-ratetier="{{ $site->ratetier }}"
    data-site-siteclass="{{ $site->siteclass }}" data-site-price="{{ $site->price ?? 0.0 }}"
    data-site-images='@json($site->images ?? [])' data-site-seasonal="{{ $site->seasonal }}"
    data-site-min-rig-length="{{ $site->minlength }}" data-site-max-rig-length="{{ $site->maxlength }}"
    data-site-hookup="{{ $site->hookup ?? 'N/A' }}">

    <td class="sticky-col bg__sky text-center">
        <div class="d-flex align-items center justify-content-center" style="font-size: 1rem">
            {{ $site->siteid }}
            <span class="ms-2 w-auto site-info-icon" style="cursor: pointer;" data-bs-toggle="tooltip"
                data-bs-placement="right" title=""
                data-site-data="{{ json_encode([
                    'Class' => str_replace('_', ' ', $site->siteclass),
                    'Rate Tier' => $site->ratetier,
                    'Rig Length' =>
                        str_replace('_', ' ', $site->siteclass) == 'RV Sites' ||
                        str_replace('_', ' ', $site->siteclass) == 'RV Sites,Tent Sites'
                            ? ($site->minlength ?? 0) . '-' . ($site->maxlength ?? 35)
                            : null,
                    'Hookup' =>
                        str_replace('_', ' ', $site->siteclass) == 'RV Sites' ||
                        str_replace('_', ' ', $site->siteclass) == 'RV Sites,Tent Sites'
                            ? $site->hookup ?? 'N/A'
                            : null,
                ]) }}">
                ⓘ
            </span>
        </div>
    </td>

    @php
        $calendarCount = count($calendar);
        $i = 0;
    @endphp

    @while ($i < $calendarCount)
        @php
            $date = $calendar[$i];
            $highlightToday = $date === now()->format('Y-m-d') ? 'border border-warning' : '';
            $availability_value = $site->availability[$date] ?? null;

            $reservation = is_object($availability_value) ? $availability_value : null;

            $isOccupiedButNotStart = $availability_value === true;
        @endphp

        @if ($reservation)
            @php
                $today = \Carbon\Carbon::today();
                $resStart = \Carbon\Carbon::parse($reservation->cid);
                $resEnd = \Carbon\Carbon::parse($reservation->cod);

                // Cap colspan to remaining days
                $reservationColSpan = min($resStart->diffInDays($resEnd), $calendarCount - $i);

                $isCancelled = $reservation->cancelled ?? false;
                $matchingPayment = $reservation->payments->where('cartid', $reservation->cartid)->sum('amount');
                $fullyPaid = $matchingPayment >= ($reservation->total ?? 0) ?? $reservation->status === 'Paid';
                $source = strtolower($reservation->source ?? '');
                $createdBy = strtolower($reservation->createdby ?? '');
                $siteLock = intval($reservation->sitelock) > 0;

                $noSiteLock = $reservation->sitelock === 0 || is_null($reservation->sitelock);

                if ($isCancelled) {
                    $bgColor = 'red';
                    $textColor = 'white';
                    // } elseif (in_array($source, ['booking.com', 'airbnb'])) {
                    //     $bgColor = $fullyPaid ? 'purple' : 'yellow';
                    //     $textColor = $fullyPaid ? 'white' : 'black';
                }
                if ($reservation->balance_due == 0) {
                    // No balance
                    $bgColor = '#58D68D';
                    $textColor = 'white';
                } else {
                    // Has balance (positive or negative)
                    $bgColor = '#FFAE42';
                    $textColor = 'black';
                }

                $borderColor = $siteLock ? 'red' : 'green';

                $hasCheckedIn = $reservation->checkedin !== null ? 'black' : $borderColor;
                $borderColor = $hasCheckedIn;

                // dd([$borderColor, $reservation->checkedin, $reservation]);

                // $hasStarted = $today->greaterThanOrEqualTo($resStart);

                // if ($hasStarted && $borderColor === 'black') {
                //     $borderColor = 'blue';
                // }

            @endphp
            <td colspan="{{ $reservationColSpan }}" class="reservation-details text-center {{ $highlightToday }}"
                style="cursor:pointer; background-color: {{ $bgColor }}; color: {{ $textColor }}; border: 4px solid {{ $borderColor }}; "
                data-reservation-id="{{ $reservation->id }}" data-start-date="{{ $reservation->cid }}"
                data-end-date="{{ $reservation->cod }}" data-cart-id="{{ $reservation->cartid }}">
                {{ strtoupper($reservation->lname ?? 'Guest') }}
            </td>
            @php $i += $reservationColSpan; @endphp
        @elseif ($isOccupiedButNotStart)
            @php $i++; @endphp
        @else
            @php $i++; @endphp
            <td class="text-center text-dark {{ $highlightToday }}" style="opacity: 50%">
                Available
            </td>
        @endif
    @endwhile
</tr>
