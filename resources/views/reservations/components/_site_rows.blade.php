<tr data-site-id="{{ $site->id }}" 
    data-site-siteid="{{ $site->siteid }}" 
    data-site-ratetier="{{ $site->ratetier }}"
    data-site-siteclass="{{ $site->siteclass }}" 
    data-site-price="{{ $site->price ?? 0.0 }}"
    data-site-images='@json($site->images ?? [])' 
    data-site-seasonal="{{ $site->seasonal }}">

    <td class="sticky-col bg__sky text-center">{{ $site->siteid }}</td>
    <td class="sticky-col bg__sky">{{ str_replace('_', ' ', $site->siteclass) }}</td>
    <td class="sticky-col bg__sky">{{ $site->ratetier }} </td>
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
                $resStart = \Carbon\Carbon::parse($reservation->cid);
                $resEnd = \Carbon\Carbon::parse($reservation->cod);

                // Cap colspan to remaining days
                $reservationColSpan = min($resStart->diffInDays($resEnd), $calendarCount - $i);
                
                $isCancelled = $reservation->cancelled ?? false;
                $matchingPayment = $reservation->payments->where('cartid', $reservation->cartid)->sum('amount');
                $fullyPaid = $matchingPayment >= ($reservation->total ?? 0);
                $source = strtolower($reservation->source ?? '');

                if ($isCancelled) {
                    $bgColor = 'red';
                    $textColor = 'white';
                } elseif (in_array($source, ['booking.com', 'airbnb'])) {
                    $bgColor = $fullyPaid ? 'purple' : 'yellow';
                    $textColor = $fullyPaid ? 'white' : 'black';
                } else {
                    $bgColor = $fullyPaid ? 'green' : 'orange';
                    $textColor = $fullyPaid ? 'white' : 'black';
                }
            @endphp

            <td colspan="{{ $reservationColSpan }}" class="reservation-details text-center {{ $highlightToday }}"
                style="cursor:pointer; background-color: {{ $bgColor }}; color: {{ $textColor }}; border: 2px solid white;"
                data-reservation-id="{{ $reservation->id }}" data-start-date="{{ $reservation->cid }}"
                data-end-date="{{ $reservation->cod }}">
                {{ $reservation->fname ?? 'Guest' }}
            </td>
            @php $i += $reservationColSpan; @endphp
        @elseif ($isOccupiedButNotStart)
            @php $i++; @endphp
        @else
            @php $i++; @endphp
            <td class="text-center bg-info text-white {{ $highlightToday }}" style="opacity: 50%">
                Available
            </td>
        @endif
    @endwhile
</tr>
