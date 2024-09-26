@extends('layouts.admin')

@section('title', 'Re-Schedule')
@section('content-header', 'Re-Schedule')

@section('content')
<div class="card">
    <div class="card-body">
        <div id="calendar"></div> 
    </div>
</div>
@endsection

@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/evo-calendar@1.1.3/evo-calendar/css/evo-calendar.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/evo-calendar@1.1.3/evo-calendar/js/evo-calendar.min.js"></script>

<style>
    
    .occupied-day .event-indicator {
        background-color: red !important;
    }
</style>

<script>
$(document).ready(function() {
   

    var eventDates = [];

    $.ajax({
        url: "{{ route('reservations.unavailable-dates') }}",
        method: 'GET',
        success: function(data) {
            data.unavailable_dates.forEach(function(dateRange) {
                var startDate = moment(dateRange.cid);
                var endDate = moment(dateRange.cod);

                while (startDate <= endDate) {
                    eventDates.push({
                        id: 'occupied-' + startDate.format('YYYY-MM-DD'),
                        name: 'Occupied',
                        date: startDate.format('YYYY-MM-DD'),
                        type: 'occupied'
                    });
                    startDate.add(1, 'days');
                }
            });

         
            $('#calendar').evoCalendar({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                eventListToggler: false,
                sidebarToggler: false,
                calendarEvents: eventDates,
                theme: 'Royal Navy'
            });

          
            $('.event-indicators').each(function() {
                var indicators = $(this).find('.event-indicator');
                indicators.each(function() {
                    if ($(this).attr('style').includes('occupied')) {
                        $(this).addClass('occupied-day');
                    }
                });
            });
        },
        error: function(error) {
            console.error('Error fetching occupied dates:', error);
        }
    });
});
</script>
@endsection
