<tr data-site-id="{{ $site->id }}" data-site-siteid="{{ $site->siteid }}" data-site-ratetier="{{ $site->ratetier }}"
    data-site-siteclass="{{ $site->siteclass }}" data-site-price="{{ $site->price ?? 0.0 }}"
    data-site-images='@json($site->images ?? [])' data-site-seasonal="{{ $site->seasonal }}">
    <td class="sticky-col bg__sky text-center">&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
        {{ $site->siteid }}<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>

    <td class="sticky-col bg__sky">{{ str_replace('_', ' ', $site->siteclass) }}</td>
    @php
        $calendarCount = count($calendar);
        $i = 0;
    @endphp

    @while ($i < $calendarCount)
        @php
            $currentDate = $calendar[$i];
            $highlightToday = $currentDate == now()->format('Y-m-d') ? 'border border-warning' : '';

            $reservationFound = null;
            foreach ($site->reservations as $reservation) {
                $resStart = \Carbon\Carbon::parse($reservation->cid)->format('Y-m-d');
                $resEnd = \Carbon\Carbon::parse($reservation->cod)->format('Y-m-d');

                if ($currentDate >= $resStart && $currentDate < $resEnd) {
                    $reservationFound = $reservation;
                    break;
                }
            }
        @endphp

        @if ($reservationFound)
            @php
                $resStart = \Carbon\Carbon::parse($reservationFound->cid);
                $resEnd = \Carbon\Carbon::parse($reservationFound->cod);
                $reservationColSpan = $resStart->diffInDays($resEnd);
                $i += $reservationColSpan;

                $today = \Carbon\Carbon::today()->format('Y-m-d');

                $outline =
                    isset($reservation->sitelock) &&
                    (int) $reservation->sitelock === 20 &&
                    $today >= $reservation->cid &&
                    $today < $reservation->cod
                        ? '2px solid black'
                        : '2px solid white';

                $textColor = 'white';

                $isCancelled = $reservationFound->cancelled ?? false;

                $matchingPayment = $reservationFound->payments
                    ->where('cartid', $reservationFound->cartid)
                    ->sum('amount');

                $fullyPaid = $matchingPayment >= ($reservationFound->total ?? 0);

                $source = strtolower($reservationFound->source ?? '');

                if ($isCancelled) {
                    $bgColor = 'red';
                    $textColor = 'white';
                } elseif (in_array($source, ['booking.com', 'airbnb'])) {
                    if ($fullyPaid) {
                        $bgColor = 'purple';
                    } else {
                        $bgColor = 'yellow';
                        $textColor = 'black';
                    }
                } else {
                    if ($fullyPaid) {
                        $bgColor = 'green';
                    } else {
                        $bgColor = 'orange';
                        $textColor = 'black';
                    }
                }
            @endphp

            <td colspan="{{ $reservationColSpan }}"
                style="cursor: pointer; background-color: {{ $bgColor }}; border: {{ $outline }}; color: {{ $textColor }}"
                class="reservation-details text-center {{ $highlightToday }}"
                data-reservation-id="{{ $reservationFound->id }}" data-start-date="{{ $reservationFound->cid }}"
                data-end-date="{{ $reservationFound->cod }}">
                {{ $reservationFound->user->f_name ?? 'Guest' }}
            </td>
        @else
            @php
                $availableColSpan = 0;
                $today = \Carbon\Carbon::today();

                if (\Carbon\Carbon::parse($calendar[$i])->lt($today)) {
                    $i++;
                    continue;
                }

                while (
                    $i + $availableColSpan < $calendarCount &&
                    \Carbon\Carbon::parse($calendar[$i + $availableColSpan])->gte($today) &&
                    !$site->reservations->some(function ($r) use ($calendar, $i, $availableColSpan) {
                        $checkDate = $calendar[$i + $availableColSpan];
                        $resStart = \Carbon\Carbon::parse($r->cid)->format('Y-m-d');
                        $resEnd = \Carbon\Carbon::parse($r->cod)->format('Y-m-d');
                        return $checkDate >= $resStart && $checkDate < $resEnd;
                    })
                ) {
                    $availableColSpan++;
                }

                $i += $availableColSpan;
            @endphp

            @if ($availableColSpan > 0)
                <td colspan="{{ $availableColSpan }}" class="text-center bg-info text-white {{ $highlightToday }}"
                    style="opacity: 50%">
                    Available
                </td>
            @endif
        @endif
    @endwhile
</tr>
