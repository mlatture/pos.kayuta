@foreach ($sites->sortBy('siteclass') as $site)
        @include('reservations.components._site_rows', ['site' => $site, 'calendar' => $calendar])
@endforeach
