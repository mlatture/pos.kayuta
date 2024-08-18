@extends('layouts.admin')

@section('title', 'Reservation List')
@section('content-header', 'Reservation List')
@section('content-actions')
@hasPermission(config('constants.role_modules.pos_management.value'))
<a href="{{ route('cart.index') }}" class="btn btn-success">Open POS</a>
@endHasPermission
@endsection

<style>
    .fc-event-main{
        border:2px solid green !important;
        background: green;
    }
  
    .fc-scrollgrid{
        color: white;
        height: 60vh !important;
    }
    thead{
        background: #212529;
    }
    .fc-col-header-cell-cushion {
        text-decoration: none !important;
        color: white;
    }
    .fc-daygrid-day-number{
        text-decoration: none;
        color: #212529;
    }

    .fc-event-time{
        display: none;
    }


    .resource-site1 {
        background-color: rgba(255, 99, 132, 0.2); /* Color for site1 */
    }

    .resource-site2 {
        background-color: rgba(54, 162, 235, 0.2); /* Color for site2 */
    }

</style>


@section('content')


{{-- <header class="reservation__head bg-dark py-2">
    <div
        class="d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between px-md-3 px-2">
        <div class="d-flex align-items-center gap-3">
            <a href="#" class="text-white text-decoration-none">
                <img src="{{ asset('images/grid-ico.svg') }}" alt="" class="me-2" />
                Planner
            </a>
            <a href="#" class="text-white text-decoration-none">
                <img src="{{ asset('images/pin-ico.svg') }}" alt="" class="me-2" />
                Map
            </a>
            <a href="#" class="text-white text-decoration-none">
                <img src="{{ asset('images/flash-ico.svg') }}" alt="" class="me-2" />
                Quick Add
            </a>
        </div>
        <div>
            <a href="#" class="text-white text-decoration-none">
                <img src="{{ asset('images/help-ico.svg') }}" alt="" class="me-2" />
                Help
            </a>
        </div>
    </div>
</header> --}}
{{-- <div
    class="table-actions d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between pe-2 pt-md-3 pt-2">
    <div class="d-flex align-items-center action__links">
        <a href="{{route('reservations.create')}}" target="_blank" class="border text-decoration-none p-2 text-dark">
            <img src="{{ asset('images/add-ico.svg') }}" alt="" class="me-2" />
            Available
        </a>
        <a href="#" class="border text-decoration-none p-2 text-dark">
            <img src="{{ asset('images/search-ico.svg') }}" alt="" class="me-2" />
            Search
        </a>
        <a href="javascript:void(0)" onclick="openReservationModal()" class="border text-decoration-none p-2 text-dark">
            <img src="{{ asset('images/track-ico.svg') }}" alt="" class="me-2" />
            Arrivals & Departures
        </a>
        <a href="javascript:void(0)" onclick="openReservationSiteModal()"
            class="border text-decoration-none p-2 text-dark">
            <img src="{{ asset('images/return-ico.svg') }}" alt="" class="me-2" />
            Relocate
        </a>
        <a href="#" class="border text-decoration-none p-2 text-dark">
            <img src="{{ asset('images/drop-ico.svg') }}" alt="" class="me-2" />
            Theme
        </a>
    </div>

    <div class="d-flex gap-2 align-items-center border p-1 rounded">
        <form action="{{ route('reservations.index') }}" method="get">
            <div class='input-group mb-3'>
                <input type='text' class="form-control daterange" id="customerDate" name="date"
                    value="{!! isset($_GET['date']) ? $_GET['date'] : '' !!}" />
                <span class="input-group-text">
                    <span class="ti-calendar"></span>
                </span>
            </div>
          
            <button id="submitRange" class="btn btn-info text-white font-small">Submit Dates</button>
        </form>
    </div>

   
</div> --}}
<div class="overflow-auto">
 
    <div id='calendar' ></div>

</div>

@extends('reservations.modals.modals')


@endsection

@push('js')
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = $('#calendar')[0];

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek', 
            editable: true,
            droppable: true,
            events: function(info, successCallback, failureCallback) {
                $.ajax({
                    url: 'reservepeople',
                    method: 'GET',
                    success: function(response) {
                        successCallback(response.events);
                    },
                    error: function() {
                        failureCallback();
                    }
                });
            },
            eventClassNames: function(arg) {
                return ['custom-event-' + arg.event.extendedProps.siteclass];
            },
            eventContent: function(arg) {
                return {
                    html: `<b>${arg.event.title}</b><br>${arg.event.extendedProps.siteclass}`
                };
            },
            eventDrop: function(info) {
                var reservationId = info.event.id;
                var resource = info.event.extendedProps.resource;
                var startDate = info.event.start.toISOString();
                var endDate = info.event.end.toISOString();

                $.ajax({
                    url: 'reservations/update/' + reservationId,
                    method: 'POST',
                    cache: false,
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Reservation updated successfully');
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred');
                    }
                });
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridDay,timeGridWeek,dayGridMonth,listWeek'
            }
           
        });

        calendar.render();
    });
</script>
@endpush
