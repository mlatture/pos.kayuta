<tr 
    data-site-id="{{ $site->id }}"
    data-site-siteid="{{ $site->siteid }}"
    data-site-ratetier="{{ $site->ratetier }}"
    data-site-siteclass="{{ $site->siteclass }}"
    data-site-price="{{ $site->price ?? 0.0 }}"
    data-site-images='@json($site->images ?? [])'
    data-site-seasonal="{{ $site->seasonal ? '1' : '0' }}"
    data-site-available="{{ $site->available ? '1' : '0' }}">
    <td class="sticky-col bg__sky text-center">&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;  {{ $site->siteid }}<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
    <td class="sticky-col bg__sky">{{ $site->ratetier }}</td>

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

                $isRefunded = $reservationFound->payment && $reservationFound->payment->transaction_type === 'REFUND';

                $reservationClass = $isRefunded ? 'bg-danger' : 'bg-success';
            @endphp

            <td colspan="{{ $reservationColSpan }}" style="cursor: pointer"
                class="reservation-details rounded text-center text-white {{ $reservationClass }} {{ $highlightToday }}"
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
