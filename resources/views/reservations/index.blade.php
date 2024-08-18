@extends('layouts.admin')

@section('title', 'Reservation List')
@section('content-header', 'Reservation List')
@section('content-actions')
    @hasPermission(config('constants.role_modules.pos_management.value'))
        <a href="{{ route('cart.index') }}" class="btn btn-success">Open POS</a>
    @endHasPermission
@endsection

@section('content')

    <!-- Commenting Previous Code  -->
    {{-- <div class="card">Log on to codeastro.com for more projects
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-12">
                    <form action="{{ route('orders.index') }}">
                        <div class="row">
                            <div class="col-md-5">
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}" />
                            </div>
                            <div class="col-md-5">
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}" />
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Checked-In Date</th>
                        <th>Checked-Out Date</th>
                        <th>Reerved At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->id }}</td>
                            <td>{{ !empty($reservation->user->f_name) ? $reservation->user->f_name : '' }}
                                {{ !empty($reservation->user->l_name) ? $reservation->user->l_name : '' }}
                            </td>
                            <td>{{ config('settings.currency_symbol') }} {{ $reservation->formattedTotal() }}</td>
                            <td>
                                @if ($reservation->receivedAmount() == 0)
                                    <span class="badge badge-danger">Not Paid</span>
                                @else
                                    <span class="badge badge-success">Paid</span>
                                @endif
                            </td>
                            <td> {{ date('l, F jS Y', strtotime($reservation->cid)) }}
                            </td>
                            <td> {{ date('l, F jS Y', strtotime($reservation->cod)) }}
                            <td> {{ date('l, F jS Y', strtotime($reservation->created_at)) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>Log on to codeastro.com for more projects
                    <tr>
                        <th></th>
                        <th></th>
                        <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                        <th>{{ config('settings.currency_symbol') }}
                            {{ number_format($reservation->receivedAmount(), 2) }}</th>

                    </tr>
                </tfoot>
            </table>
        </div>
    </div> --}}
    <!-- Commenting Previous Code  -->
    <header class="reservation__head bg-dark py-2">
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
    </header>
    <div
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
            <a href="javascript:void(0)" onclick="openReservationSiteModal()" class="border text-decoration-none p-2 text-dark">
                <img src="{{ asset('images/return-ico.svg') }}" alt="" class="me-2" />
                Relocate
            </a>
            <a href="#" class="border text-decoration-none p-2 text-dark">
                <img src="{{ asset('images/drop-ico.svg') }}" alt="" class="me-2" />
                Theme
            </a>
        </div>

            <!-- <a href="#" class="text-decoration-none text-dark"> -->
            <div class="d-flex gap-2 align-items-center border p-1 rounded">
                <form action="{{ route('reservations.index') }}" method="get">
                    <div class='input-group mb-3'>
                        <input type='text' class="form-control daterange" id="customerDate" name="date"
                            value="{!! isset($_GET['date']) ? $_GET['date'] : '' !!}" />
                        <span class="input-group-text">
                            <span class="ti-calendar"></span>
                        </span>
                    </div>
                    {{-- <label class="font-small m-0">
                <img src="{{ asset('images/date-ico.svg') }}" alt="" class="me-2" />
                Date Range
                <input type="text" name="daterange" id="dateRange" class="d-none">
            </label> --}}
                    <button id="submitRange" class="btn btn-info text-white font-small">Submit Dates</button>
                </form>
            </div>

            <!-- Search Field -->
            <!-- <div class="position-relative">
                                <a href="#" class="text-decoration-none text-dark search__toggle"><i class="fa fa-search"></i></a>
                                <div class="position-absolute search__form p-2">
                                    <form class="d-flex align-items-center justify-content-between">
                                        <input type="search" name="reservation" id="reservations" placeholder="Search..." class="border-0 outline-0"/>
                                        <button type="submit" class="bg-transparent border-0">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                                </div> -->
            <!-- Search Field -->
    </div>
    <div class="overflow-auto">
        <table class="table management-table table-striped">
            <thead>
                <tr class="t__head">
                    <td class="bg-dark" rowspan="2">
                        <span class="text-white d-block">Size</span>
                        <select class="form-control js-example-basic-multiple" multiple="multiple"  name="sitename[]" id="sitename">
                            @foreach($allSites as $v)
                            <option value="{{$v->id}}" {{(!empty($_GET['site_names']) && in_array($v->id, explode(',', $_GET['site_names']))) ? 'selected' : ''}} >{{$v->sitename}}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary mt-2 btn--apply" onclick="searchSites()" >Apply</button>
                    </td>
                    <td class="bg-dark" rowspan="2">
                        <span class="text-white d-block">Type</span>
                        <select class="form-control js-example-basic-multiple" multiple="multiple" name="siteclass[]" id="siteclass">
                            @foreach($allSites as $v)
                                <option value="{{$v->id}}" {{(!empty($_GET['site_classes']) && in_array($v->id, explode(',', $_GET['site_classes']))) ? 'selected' : ''}}>{{$v->siteclass}}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary mt-2 btn--apply" onclick="searchSites()">Apply</button>
                    </td>
                    @php
                        $colspan = 0;
                        $recurringMonth = '';
                    @endphp

                    @foreach ($calendar as $key => $dates)
                        @if ($key < 1)
                            @php
                                $recurringMonth = date('M', strtotime($dates));
                            @endphp
                        @endif

                        @if ($recurringMonth == date('M', strtotime($dates)) && count($calendar) - 1 != $key)
                            @php
                                $colspan += 1;
                            @endphp
                        @else
                            @if (count($calendar) - 1 == $key)
                                @php
                                    $colspan += 1;
                                @endphp
                            @endif
                            <td colspan="{{ $colspan }}"
                                class="month bg-dark text-center text-white text-uppercase">
                                {{ $recurringMonth }}
                            </td>
                            @php
                                $colspan = 1;
                                $recurringMonth = date('M', strtotime($dates));
                            @endphp
                        @endif
                    @endforeach

                    {{-- <td colspan="10" class="month bg-dark text-center text-white text-uppercase">
                        month
                    </td> --}}
                    <!-- <td class="curr__month"></td> -->
                </tr>
                <tr>
                    @foreach ($calendar as $dates)
                        <th data-date="{{ $dates }}"  class="custom--dates">{{ date('D', strtotime($dates)) }}
                            <hr class="m-0"> {{ date('d', strtotime($dates)) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($sites as $site)
                    <tr>
                        <td class="bg__sky">
                            <span class="text-white" data-bs-toggle="modal" data-bs-target="#sizeModal">
                                <img src="{{ asset('images/search-ico.svg') }}" alt=" " class="me-2" />
                                {{ $site->sitename }}
                            </span>
                        </td>
                        <td class="bg__sky">
                            <span class="text-white"> {{ $site->siteclass }} </span>
                        </td>
                        {{-- <td colspan="4" class="bg__stripped text-white rounded text-center">
                            Unavailable
                        </td> --}}
                        @if (count($site->reservations))
                            @php
                                $calendarLength = count($calendar);
                                $emptyAllowed = $calendarLength - $site->totalDays;
                                $emptyEntered = 0;
                            @endphp
                            @foreach ($site->reservations as $reservations)
                                @foreach($calendar as $k=>$date)
                                    @if(\Carbon\Carbon::parse($reservations->cid)->diffInDays(\Carbon\Carbon::parse($date),false) == 0)
                                        @php
                                            $reservationColSpan = \Carbon\Carbon::parse($reservations->cid)->diffInDays($reservations->cod);
                                        @endphp
                                        <td colspan="{{$reservationColSpan}}" class="dragabble rounded text-center bg-success text-white"
                                            draggable="true"
                                            data-reservation-id="{{ $reservations->id }}"
                                            data-start-date="{{ $reservations->cid }}"
                                            data-end-date="{{ $reservations->cod }}">
                                            {{ $reservations->user->f_name.' '.$reservations->user->l_name }}
                                        </td>
                                    @else
                                        @if($emptyEntered < $emptyAllowed)
                                            @php
                                            $emptyEntered++;
                                            @endphp
                                            <td></td>
                                        @endif
                                    @endif
                                @endforeach
                            @endforeach
                        @endif
                    </tr>
                @endforeach

                {{-- <tr>
                    <td class="bg__sky">
                        <span class="text-white" data-bs-toggle="modal" data-bs-target="#sizeModal">
                            <img src="{{ asset('images/search-ico.svg') }}" alt=" " class="me-2" />
                            SH-01 The Fishing Hut Cabin
                        </span>
                    </td>
                    <td class="bg__sky">
                        <span class="text-white"> Cabin </span>
                    </td>
                    <td colspan="4" class="bg__stripped text-white rounded text-center">
                        Unavailable
                    </td>
                    <td colspan="12" class="dragabble rounded text-center bg-success text-white" draggable="true">
                        P. Hartman
                    </td>
                </tr>

                <tr>
                    <td class="bg__sky">
                        <span class="text-white" data-bs-toggle="modal" data-bs-target="#sizeModal">
                            <img src="{{ asset('images/search-ico.svg') }}" alt=" " class="me-2" />
                            SH-01 The Bird Nest Cabin
                        </span>
                    </td>
                    <td class="bg__sky">
                        <span class="text-white">Delux Cabin </span>
                    </td>
                    <td colspan="4" class="bg__stripped text-white rounded text-center">
                        Unavailable
                    </td>
                    <td colspan="12" class="dragabble rounded text-center bg-success text-white" draggable="true">
                        Evons
                    </td>
                </tr>

                <tr>
                    <td class="bg__sky">
                        <span class="text-white" data-bs-toggle="modal" data-bs-target="#sizeModal">
                            <img src="{{ asset('images/search-ico.svg') }}" alt=" " class="me-2" />
                            CL-01 A1
                        </span>
                    </td>
                    <td class="bg__sky">
                        <span class="text-white"> Water/Electric 30A </span>
                    </td>
                    <td colspan="4" class="bg__stripped text-white rounded text-center">
                        Unavailable
                    </td>
                    <td colspan="12" class="dragabble rounded text-center bg-success text-white" draggable="true">
                        Gavhart
                    </td>
                </tr>

                <tr>
                    <td class="bg__sky">
                        <span class="text-white" data-bs-toggle="modal" data-bs-target="#sizeModal">
                            <img src="{{ asset('images/search-ico.svg') }}" alt=" " class="me-2" />
                            CL-01 A2
                        </span>
                    </td>
                    <td class="bg__sky">
                        <span class="text-white"> Water/Electric 30A </span>
                    </td>
                    <td colspan="4" class="bg__stripped text-white rounded text-center">
                        Unavailable
                    </td>
                    <td colspan="12" class="dragabble rounded text-center bg-success text-white" draggable="true">
                        Roe
                    </td>
                </tr>

                <tr>
                    <td class="bg__sky">
                        <span class="text-white" data-bs-toggle="modal" data-bs-target="#sizeModal">
                            <img src="{{ asset('images/search-ico.svg') }}" alt=" " class="me-2" />
                            CL-01 A3
                        </span>
                    </td>
                    <td class="bg__sky">
                        <span class="text-white"> Water/Electric 30A </span>
                    </td>
                    <td colspan="4" class="bg__stripped text-white rounded text-center">
                        Unavailable
                    </td>
                    <td colspan="12" class="dragabble rounded text-center bg-success text-white" draggable="true">
                        3. Williams
                    </td>
                </tr> --}}
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sizeModal" tabindex="-1" aria-labelledby="sizeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sizeModalLabel">Modal title</h5>
                    <button type="button" class="btn-close border-0 bg-transparent" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">...</div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="reservationDateModal" tabindex="-1" role="dialog" aria-labelledby="reservationDateModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationDateModalTitle">Arrivals & Departures</h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none"></div>
                    <div class="alert alert-success d-none"></div>
                        <form id="reservationDateForm" method="post" action="{{ route('reservations.update-dates') }}">
                        @csrf
                        <div class="form-group">
                            <label>Select Reservation</label>
                            <select class="form-control reservationId" id="reservationId" name="reservationId">
                                <option value="" disabled selected>Select Reservation</option>
                                @foreach ($allReservations as $v)
                                <option value="{{$v->id}}">{{$v->fname.' '.$v->lname.' - '}}{{$v->siteclass.' - '}}{{$v->siteid}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Check-in Date</label>
                            <input type="date" name="cid" id="cid"  class="form-control res-cid">
                        </div>
                        <div class="form-group">
                            <label>Check-out Date</label>
                            <input type="date" name="cod" id="cod"  class="form-control res-cod">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeReservationModal('reservationDateModal')">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveReservationDates(this)">Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reservationSiteModal" tabindex="-1" role="dialog" aria-labelledby="reservationSiteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationSiteModalTitle">Relocate</h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none"></div>
                    <div class="alert alert-success d-none"></div>
                    <form id="reservationSiteForm" method="post" action="{{ route('reservations.update-sites') }}">
                        @csrf
                        <div class="form-group">
                            <label>Select Reservation</label>
                            <select class="form-control reservationId2" id="reservationId2" name="reservationId2">
                                <option value="" disabled selected>Select Reservation</option>
                                @foreach ($allReservations as $v)
                                    <option value="{{$v->id}}">{{$v->fname.' '.$v->lname.' - '}}{{$v->siteclass.' - '}}{{$v->siteid}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Site ID</label>
                            <select class="form-control siteid res-siteid" id="siteid" name="siteid">
                                <option value="" disabled selected>Select Site Id</option>
                                @foreach ($allCurrentSites as $v)
                                    <option value="{{$v->id}}">{{$v->sitename}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Site Class</label>
                            <select class="form-control siteclass res-siteclass" id="siteclass" name="siteclass">
                                <option value="" disabled selected>Select Site Class</option>
                                @foreach ($allCurrentSites as $v)
                                    <option value="{{$v->id}}">{{$v->siteclass}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeReservationModal('reservationSiteModal')">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveReservationSites(this)">Update</button>
                </div>
            </div>
        </div>
    </div>

    {{-- {{ $reservations->render() }} --}}
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#sitename').select2();
            $('#siteclass').select2();
            $('.reservation-select').select2();
        });

        $('#customerDate').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#customerDate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                'MM/DD/YYYY'));
        });

        $('#customerDate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#customerDate').attr("placeholder", "Select Date");

        $('#customersTable').DataTable({
            // 'responsive': true,
            order: [
                [0, 'asc']
            ],
            "columnDefs": [{
                    "sortable": false,
                    "targets": 1
                },
                {
                    "sortable": false,
                    "targets": 8
                },
                {
                    "sortable": false,
                    "targets": 9
                }
            ]
        });

        function searchSites(){
            $('.btn--apply').attr('disabled', true);
            let siteNames = $('#sitename').val();
            let siteClasses = $('#siteclass').val();
            const currentUrl = window.location.href;
            const url = new URL(currentUrl);
            const searchParams = url.searchParams;
            searchParams.set('site_names', siteNames.length > 0?siteNames:'');
            searchParams.set('site_classes', siteClasses.length > 0?siteClasses:'');
            const getUrl =  `${window.location.pathname}?${searchParams.toString()}`;
            window.location.href = getUrl;

        }

        function openReservationModal(){
            $('.res-cid').val('');
            $('.res-cod').val('');
            $('.reservationId').val('');
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $(':input').removeClass('is-invalid');
            $('.error--msg').remove();
            $(`#reservationDateModal`).modal('toggle');
        }

        function openReservationSiteModal(){
            $('.res-siteid').val('');
            $('.res-siteclass').val('');
            $('.reservationId2').val('');
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $(':input').removeClass('is-invalid');
            $('.error--msg').remove();
            $(`#reservationSiteModal`).modal('toggle');
        }

        function closeReservationModal(modalId){
            $(`#${modalId}`).modal('hide');
        }

        function saveReservationDates(input){
            $(input).attr('disabled', true);
            $(':input').removeClass('is-invalid');
            $('.error--msg').remove();
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $.post($('#reservationDateForm').attr('action'), $('#reservationDateForm').serialize()).done(function(res) {
                if(res.status == "success"){
                    $('.alert-success').removeClass('d-none').text(res.message);
                    setTimeout(function(){
                        $('#reservationDateModal').modal('hide');
                        $(input).attr('disabled', false);
                        $('.alert-success').addClass('d-none').text('');
                        $('.alert-danger').addClass('d-none').text('');
                    }, 1500);
                }else{
                    $(input).attr('disabled', false);
                    $('.alert-danger').removeClass('d-none').text(res.message)
                }
            })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    $(input).attr('disabled', false);
                    if (jqXHR.status === 422) {
                        $.each(jqXHR.responseJSON.errors, function(k,v){
                            $(`#${k}`).addClass('is-invalid').after(`<span class="error--msg" role="alert"><strong class="text-danger">${v[0]}</strong></span>`);
                        });
                    }
                });
        }

        function saveReservationSites(input){
            $(input).attr('disabled', true);
            $(':input').removeClass('is-invalid');
            $('.error--msg').remove();
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $.post($('#reservationSiteForm').attr('action'), $('#reservationSiteForm').serialize()).done(function(res) {
                if(res.status == "success"){
                    $('.alert-success').removeClass('d-none').text(res.message);
                    setTimeout(function(){
                        $('#reservationSiteModal').modal('hide');
                        $(input).attr('disabled', false);
                        $('.alert-success').addClass('d-none').text('');
                        $('.alert-danger').addClass('d-none').text('');
                    }, 1500);
                }else{
                    $(input).attr('disabled', false);
                    $('.alert-danger').removeClass('d-none').text(res.message)
                }
            })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    $(input).attr('disabled', false);
                    if (jqXHR.status === 422) {
                        $.each(jqXHR.responseJSON.errors, function(k,v){
                            $(`#${k}`).addClass('is-invalid').after(`<span class="error--msg" role="alert"><strong class="text-danger">${v[0]}</strong></span>`);
                        });
                    }
                });
        }

        {{--$(document).ready(function() {--}}
        {{--    // Handle drag start event--}}
        {{--    $('.dragabble').on('dragstart', function(event) {--}}
        {{--        var data = {--}}
        {{--            reservationId: $(this).data('reservation-id'),--}}
        {{--            startDate: $(this).data('start-date'),--}}
        {{--            endDate: $(this).data('end-date')--}}
        {{--        };--}}
        {{--        console.log('data', data);--}}
        {{--        event.originalEvent.dataTransfer.setData('text/plain', JSON.stringify(data));--}}
        {{--    });--}}

        {{--    // Allow drop on date cells--}}
        {{--    $('.dragabble').on('dragover', function(event) {--}}
        {{--        event.preventDefault();--}}
        {{--        console.log('event================', event)--}}
        {{--        // var data = JSON.parse(event.originalEvent.dataTransfer.getData('text/plain'));--}}
        {{--        // console.log('data', data)--}}
        {{--    });--}}

        {{--    // Handle drop event--}}
        {{--    $('.dragabble').on('drop', function(event) {--}}
        {{--        event.preventDefault();--}}
        {{--        console.log('eventtttt', event);--}}
        {{--        var data = JSON.parse(event.originalEvent.dataTransfer.getData('text/plain'));--}}
        {{--        var newStartDate = $(this).data('date');--}}

        {{--        // Calculate the number of days for the reservation--}}
        {{--        var oldStartDate = new Date(data.startDate);--}}
        {{--        var oldEndDate = new Date(data.endDate);--}}
        {{--        var numberOfDays = (oldEndDate - oldStartDate) / (1000 * 60 * 60 * 24);--}}

        {{--        // Calculate new end date--}}
        {{--        var newEndDate = new Date(newStartDate);--}}
        {{--        newEndDate.setDate(newEndDate.getDate() + numberOfDays);--}}
        {{--        newEndDate = newEndDate.toISOString().split('T')[0];--}}

        {{--        // Send AJAX request to update the reservation dates--}}
        {{--        $.ajax({--}}
        {{--            url: '{{ route("reservations.updateDates") }}',--}}
        {{--            method: 'POST',--}}
        {{--            data: {--}}
        {{--                _token: '{{ csrf_token() }}',--}}
        {{--                reservationId: data.reservationId,--}}
        {{--                newStartDate: newStartDate,--}}
        {{--                newEndDate: newEndDate--}}
        {{--            },--}}
        {{--            success: function(response) {--}}
        {{--                if (response.success) {--}}
        {{--                    location.reload(); // Reload the page to see the changes--}}
        {{--                } else {--}}
        {{--                    alert('Failed to update reservation.');--}}
        {{--                }--}}
        {{--            },--}}
        {{--            error: function(xhr, status, error) {--}}
        {{--                console.error('Error updating reservation:', error);--}}
        {{--                alert('An error occurred while updating the reservation.');--}}
        {{--            }--}}
        {{--        });--}}
        {{--    });--}}
        {{--});--}}
    </script>
@endpush
