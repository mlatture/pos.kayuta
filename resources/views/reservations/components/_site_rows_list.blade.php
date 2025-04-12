@foreach ($sites as $site)
    @include('reservations.components._site_rows', ['site' => $site, 'calendar' => $calendar])
@endforeach
