@extends('layouts.admin')

@section('title', 'Reservation List')
@section('content-header')
    <h4 class="mb-0 ">Reservation List</h4>

    <div class="d-flex flex-wrap align-items-center gap-2">


        <div class="d-flex align-items-center mt-3">

            <small for="seasonalFilter" class="text-muted small me-2" style="font-size: 1rem; white-space: nowrap;">
                Seasonal Filter
            </small>

            <select id="seasonalFilter" name="seasonalFilter" class="form-select form-select-lg w-auto">
                <option value="short" {{ request('seasonalFilter', 'short') === 'short' ? 'selected' : '' }}>
                    Short Term
                </option>
                <option value="seasonal" {{ request('seasonalFilter') === 'seasonal' ? 'selected' : '' }}>
                    Seasonal
                </option>
                <option value="all" {{ request('seasonalFilter') === 'all' ? 'selected' : '' }}>
                    Show All
                </option>
            </select>
        </div>

        <a class="btn btn-sm btn-primary"
            href="{{ route('admin.reservation_mgmt.index', ['admin' => auth()->user()->id]) }}">
            Check Availability
        </a>

        <a href="" class="btn btn-sm btn-primary d-none" id="btnCreateReservation">
            Create Reservation
        </a>
    </div>


@endsection
@section('content-actions')
    <div class="d-flex justify-content-end mb-3">
        <div class="text-end">
            <div class="d-flex align-items-center justify-content-end">
                <button class="btn btn-outline-secondary me-4" id="prev30">&larr;</button>

                <input type="date" id="startDatePicker" value="{{ $filters['startDate'] }}" class="form-control w-auto">

                <button class="btn btn-outline-secondary ms-4" id="next30">&rarr;</button>
            </div>

            <small class="text-muted d-block mt-1 me-2">
                Select a start date to view the next 30 days.
            </small>
        </div>
    </div>


@endsection
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

@section('content')
    <style>
        body {
            overflow: hidden !important;
        }


        table.management-table {
            border-collapse: separate;
            border-spacing: 6px;
        }

        .table-actions {
            margin-bottom: 10px;
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
            justify-content: center;
            padding: 10px 20px;
            border-radius: 50px;
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



    <div class="container-fluid py-3">
        <div class="card shadow-sm p-2" style="max-height: 75vh; overflow: auto;">
            <div
                class="reservation-legend mb-4 d-flex gap-3 flex-wrap p-3 bg-light rounded shadow-sm d-flex justify-content-center align-items-center">
                <div class="d-flex align-items-center gap-2 legend-item">
                    <span class="legend-indicator paid-full">
                        <i class="fa fa-check"></i>
                    </span>
                    <span class="legend-label">Fully Paid</span>
                </div>

                <div class="d-flex align-items-center gap-2 legend-item">
                    <span class="legend-indicator paid-partial">
                        <i class="fa fa-exclamation"></i>
                    </span>
                    <span class="legend-label">Partial Payment</span>
                </div>

                <div class="d-flex align-items-center gap-2 legend-item">
                    <span class="legend-indicator site-lock"></span>
                    <span class="legend-label">Site Lock</span>
                </div>

                <div class="d-flex align-items-center gap-2 legend-item">
                    <span class="legend-indicator site-unlock"></span>
                    <span class="legend-label">Not Site Lock</span>
                </div>

                <div class="d-flex align-items-center gap-2 legend-item">
                    <span class="legend-indicator checked-in">
                        <i class="fa fa-user-check"></i>
                    </span>
                    <span class="legend-label">Checked In</span>
                </div>
            </div>

            <div class="table-responsive mb-3" style="overflow: auto;">
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

@endsection
@include('reservations.modals.details')
@include('reservations.modals.filter-col')

@include('reservations.script')
