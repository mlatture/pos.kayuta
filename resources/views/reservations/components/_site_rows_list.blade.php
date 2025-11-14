@foreach ($sites->sortBy('ratetier') as $site)
    @foreach ($site_classes->sortBy('siteclass') as $topSiteclass)
        @include('reservations.components._site_rows', ['site' => $site, 'calendar' => $calendar, 'topSiteclass' => $topSiteclass])
    @endforeach
@endforeach
