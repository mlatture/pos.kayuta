@extends('layouts.admin')
@section('title', 'Reservation List')
@section('content-header')
    <div class="d-flex align-items-center gap-3 flex-nowrap ">

        <!-- LEFT: Buttons -->
        <div class="d-flex align-items-center gap-3 flex-shrink-0">
            <a class="btn btn-primary" href="{{ route('flow-reservation.step1') }}">
                Check Availability
            </a>

            <a href="#" class="btn btn-primary d-none" id="btnCreateReservation">
                Add To Cart
            </a>
        </div>

        <!-- CENTER: Legend -->
        <div class="flex-grow-1 d-flex justify-content-center">
            <div class="reservation-legend d-flex align-items-center gap-3 p-2 bg-light rounded shadow-sm"
                style="white-space: nowrap;">
                <!-- Fully Paid -->
                <div class="d-flex align-items-center gap-2 legend-item">
                    <span class="legend-indicator paid-full">
                        <i class="fa fa-check"></i>
                    </span>
                    <span class="legend-label">Fully Paid</span>
                </div>

                <!-- Partial Payment -->
                <div class="d-flex align-items-center gap-2 legend-item">
                    <span class="legend-indicator paid-partial">
                        <i class="fa fa-exclamation"></i>
                    </span>
                    <span class="legend-label">Partial Payment</span>
                </div>

                <!-- Site Lock -->
                <div class="d-flex align-items-center gap-2 legend-item">
                    <span class="legend-indicator site-lock"></span>
                    <span class="legend-label">Site Lock</span>
                </div>

                <!-- Not Site Lock -->
                <div class="d-flex align-items-center gap-2 legend-item">
                    <span class="legend-indicator site-unlock"></span>
                    <span class="legend-label">Not Site Lock</span>
                </div>

                <!-- Checked In -->
                <div class="d-flex align-items-center gap-2 legend-item">
                    <span class="legend-indicator checked-in">
                        <i class="fa fa-user-check"></i>
                    </span>
                    <span class="legend-label">Checked In</span>
                </div>
            </div>
        </div>



    </div>
@endsection
@section('content-actions')
    <!-- RIGHT: Date Picker -->
    <div class="d-flex flex-column align-items-end flex-shrink-0">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary" id="prev30">&larr;</button>
            <input type="date" id="startDatePicker" value="{{ $filters['startDate'] }}" class="form-control w-auto">
            <button class="btn btn-outline-secondary" id="next30">&rarr;</button>
        </div>

        <small class="text-muted mt-1">
            Select a start date to view the next 30 days.
        </small>
    </div>
@endsection
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        table.management-table {
            border-collapse: separate;
            border-spacing: 6px;
        }

        .management-table th,
        .management-table td {
            white-space: nowrap;
        }

        .sticky-col {
            position: sticky;
            left: 0;
            z-index: 2;
            background: #343a40 !important;
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sticky-col+.sticky-col {
            left: 50px;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .site-card {
            border: 2px solid #ddd;
            cursor: pointer;
            transition: 0.3s;
        }

        .site-card:hover {
            border: 2px solid #007bff;
        }

        .site-card.selected {
            border: 2px solid green;
            background-color: #f0fff0;
        }

        #siteFilter,
        #typeFilter {
            width: 100%;
            margin-top: 5px;
            padding: 5px 10px;
            border-radius: 0.25rem;
            background-color: #f8f9fa;
        }

        .select2-container {
            width: 100% !important;
        }


        /* Container styling */
        .reservation-legend {
            background-color: #ffffff !important;
            border: 1px solid #e9ecef;
            width: fit-content;

            margin-left: auto;
            margin-right: auto;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 50px;

            white-space: nowrap;
            flex-shrink: 0;
        }

        .legend-item {
            padding: 4px 8px;
            transition: transform 0.2s ease;
        }

        .legend-item:hover {
            transform: translateY(-1px);
        }

        /* Base indicator shape */
        .legend-indicator {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 11px;
            flex-shrink: 0;
        }

        .legend-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #495057;
        }

        .paid-full {
            background-color: #28a745;
            color: white;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
        }

        .paid-partial {
            background-color: #fd7e14;
            color: white;
            box-shadow: 0 2px 4px rgba(253, 126, 20, 0.2);
        }

        .site-lock {
            background-color: #fff;
            border: 2px solid #dc3545;
            position: relative;
        }

        .site-lock::after {
            content: '';
            width: 6px;
            height: 6px;
            background: #dc3545;
            border-radius: 50%;
        }

        .site-unlock {
            background-color: #fff;
            border: 2px solid #198754;
        }

        .checked-in {
            background-color: #f8f9fa;
            border: 2px solid #212529;
            color: #212529;
        }
    </style>

@endsection
@section('content')
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow: auto;">
                        <table class="table management-table table-striped">
                            <thead>
                                <tr class="t__head sticky-top bg-dark">
                                    <th class="sticky-col sticky-top bg-dark text-white text-center" rowspan="3"
                                        style="width: 120px;">
                                        <button type="button" class="btn btn-sm btn-light mb-2" data-bs-toggle="modal"
                                            data-bs-target="#filtersModal">
                                            <i class="fa fa-filter"></i> Filters
                                        </button>
                                        <div class="d-flex flex-column justify-content-between align-items-center">
                                        </div>
                                    </th>


                                    @php
                                        $recurringMonth = '';
                                        $colspan = 0;
                                    @endphp
                                    @foreach ($calendar as $key => $day)
                                        @php
                                            $currentMonth = date('M Y', strtotime($day['date']));
                                        @endphp

                                        @if ($key === 0)
                                            @php $recurringMonth = $currentMonth; @endphp
                                        @endif

                                        @if ($recurringMonth == $currentMonth)
                                            @php $colspan++; @endphp
                                        @else
                                            <td colspan="{{ $colspan }}"
                                                class="month sticky-top bg-dark text-center text-white text-uppercase">
                                                {{ $recurringMonth }}
                                            </td>
                                            @php
                                                $colspan = 1;
                                                $recurringMonth = $currentMonth;
                                            @endphp
                                        @endif

                                        @if ($loop->last)
                                            <td colspan="{{ $colspan }}"
                                                class="month sticky-top bg-dark text-center text-white text-uppercase">
                                                {{ $recurringMonth }}
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($calendar as $day)
                                        @php
                                            $hasEvent = !empty($day['event']);
                                            $dateObj = strtotime($day['date']);

                                        @endphp

                                        <th data-date="{{ $day['date'] }}"
                                            class="sticky-top custom--dates {{ $hasEvent ? 'border border-danger' : '' }}"
                                            @if ($hasEvent) data-bs-toggle="tooltip" 
                                    data-bs-placement="bottom" 
                                    title="{{ $day['event']['title'] }} requires a {{ $day['event']['nights'] }} night(s) minimum stay." @endif>

                                            {{ date('D', $dateObj) }}
                                            <hr class="m-0">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>
                                                    {{ date('d', $dateObj) }}
                                                    @if ($hasEvent)
                                                        <small class="text-danger fw-bold "
                                                            style="font-size: 0.7rem; cursor: pointer;">
                                                            {{ $day['event']['nights'] }}N ⓘ
                                                        </small>
                                                    @endif
                                                </span>

                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="siteTableBody">
                                @foreach ($sites as $site)
                                    @include('reservations.components._site_rows', [
                                        'site' => $site,
                                        'calendar' => $calendar,
                                    ])
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('reservations.modals.details')
@include('reservations.modals.filter-col')

@include('reservations.script')
