@extends('front.layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/front/css/amenities.css') }}">
@endpush
@section('content')
    <style>
        .hotel-title {
            background-color: #efc368; /* Theme color */
            padding: 10px;
            text-align: center;
            color: #fff;
            font-size: 2em;
        }

        .hotel-listing {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .hotel-card {
            display: flex;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: 0.3s ease;
            max-width: 100%;
        }

        .hotel-card:hover {
            transform: scale(1.02);
        }

        .hotel-image img {
            width: 300px;
            height: 100%;
            object-fit: cover;
        }

        .hotel-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            width: 100%;
        }

        .hotel-name {
            font-size: 1.6em;
            font-weight: bold;
            margin: 0 0 10px;
            color: #333;
        }

        .reservation-dates, .rating {
            font-size: 1em;
            color: #666;
            margin: 5px 0;
        }

        .rating {
            color: #FFD700;
        }

        .btn-book {
            background-color: #efc368; /* Theme color */
            color: #000; /* Black font color */
            padding: 10px 15px;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            width: 100%;
            max-width: 150px;
            transition: background-color 0.3s;
        }

        .btn-book:hover {
            background-color: #d9aa58; /* Darker shade of theme color */
        }

        @media (max-width: 768px) {
            .hotel-card {
                flex-direction: column;
            }
            .hotel-image img {
                width: 100%;
                height: 200px;
            }
        }
    </style>
@php
    $booking    =   Session::get('booking');
@endphp
{{-- page header starts here --}}
<section class="page-header" style="background-image: url('{{ asset('assets/front/img/theme-page-bg.webp') }}')">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-heading-wrapper text-center">
                    <h2>Flexible Dates</h2>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- page header ends here --}}

    <section>
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="text-wrapper">
                        <div class="not-the-left-column">
                            <div class="site-details">
                                Click below to book
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="page-heading-wrapper text-center">
                <h2 class="hotel-title">Site Listings</h2>
            </div>
            <div class="hotel-listing">
                @foreach ($sites as $currentsite)
                    @php
                        // Variables from your original function
                        $siteId = $currentsite['siteid'];
                        $cid = $booking['cid']; // Example: current date
                        $availability = $currentsite['availability']; // e.g. "A,A,N,A,A,A"
                        $sitehookup = $currentsite['hookup'];
                        $hookup = $booking['hookup'] ?? '';

                        // Generate the availability status as an associative array
                        $days = explode(",", $availability);
                        $availabilityStatus = [];
                        foreach ($days as $index => $day) {
                            // Use the index to create a corresponding date key
                            $dateKey = date('Y-m-d', strtotime("+$index days", strtotime('2024-09-01'))); // Adjust the base date accordingly
                            $availabilityStatus[$dateKey] = $day; // 'A' or 'N'
                        }

                        // Retrieve the flat rate and tier details
                        $rateTier = DB::table('rate_tiers')->where('tier', $currentsite->hookup)->first();
                        $flatRate = null;

                        if ($rateTier && $rateTier->useflatrate == 1) {
                            $flatRate = $rateTier->flatrate;
                        }

                        // Prepare availability dates
                        $datesString = $currentsite['availability_dates']; // e.g. "2024-09-01,2024-09-02,..."
                        $availabilityDates = explode(',', $datesString); // Convert to array

                        // Filter availability dates based on the $stay variable
                        if ($stay === 'weekend') {
                            // Filter for weekends (Saturday and Sunday)
                            $availabilityDates = array_filter($availabilityDates, function ($date) {
                                return date('N', strtotime($date)) >= 6; // 6: Saturday, 7: Sunday
                            });
                        } elseif ($stay === 'week') {
                            // Filter for weekdays (Monday to Friday)
                            $availabilityDates = array_filter($availabilityDates, function ($date) {
                                return date('N', strtotime($date)) < 6; // 1: Monday to Friday
                            });
                        }
                    @endphp


                    <div class="hotel-card" data-flat-rate="{{ $flatRate }}">
                    <div class="hotel-image" style="height: 300px; overflow: hidden;">
    @if ($currentsite->images && count($currentsite->images) > 0)
        <!-- Show the first image if available -->
        <img src="{{ asset('shared_storage/sites/' . $currentsite->images[0]) }}" alt="Hotel Image" class="w-100 img-fluid" style="height: 100%; width: 100%; object-fit: cover;">
        @else
        <!-- Show default image if no images are available -->
        <img src="https://images.trvl-media.com/lodging/52000000/51290000/51282600/51282532/1093ec84.jpg?impolicy=resizecrop&rw=598&ra=fit" alt="Default Hotel Image" class="w-100 img-fluid" style="height: 100%; width: 100%; object-fit: cover;">
    @endif
</div>


    <div class="hotel-details">
        <div class="hotel-name">Site: {{ $currentsite->sitename ?? '-' }}</div>

              <!-- Range Type Display -->
    <div class="reservation-dates" id="range-type" style="text-align:right;color:black" class="mt-3">
        <strong></strong><span id="range-type-text">
        @php
    // Checking $stay variable and displaying corresponding range text
    if ($stay == 'weekend') {
        echo "Weekend: Friday to Sunday";
    } elseif ($stay == 'week') {
        echo "Week: Monday to Friday";
    } elseif ($stay == 'week_weekend') {
        echo "Week + Weekend: Monday to Sunday";
    } elseif ($stay == 'month') {
        // Iterate over the $months array to convert each month to a human-readable format
        $months = ["2025-02", "2025-04", "2025-06"]; // Your received $months array

        $monthNames = [];
        foreach ($months as $month) {
            // Create DateTime object from year-month string and format it as the full month name
            $monthName = (new DateTime($month . '-01'))->format('F'); // 'F' returns the full month name (e.g., "February")
            $monthNames[] = $monthName;
        }

        // Output the months in human-readable format, separated by commas
        echo "Month: " . implode(', ', $monthNames);
    } else {
        echo "Select a range to view";
    }
@endphp

        </span>
    </div>


{{--        <div class="rating">Rating: ★★★★★ (5)</div>--}}

        <!-- <div class="reservation-dates">Maximum Guests: {{ $currentsite->maxlength ?? 0 }}</div>
        <div class="reservation-dates">Minimum Guests: {{ $currentsite->minlength ?? 0 }}</div> -->
        <div class="reservation-dates">Hookup Type: {{ $currentsite->hookup }}</div>
        <!-- <div class="reservation-dates">Available For: {{$currentsite->class}}</div> -->
        @if (!str_contains($currentsite->class, 'Cabin'))
            <div class="reservation-dates">
                Maximum Rig Length: {{ !empty($currentsite->maxlength) ? $currentsite->maxlength . ' feet.' : 'N/A' }}
            </div>
        @endif



        <div class="reservation-dates">
            <strong>Attributes:</strong> {!! $currentsite->attributes !!}
        </div>

        <div class="reservation-dates">
            <strong>Amenities:</strong>
            {{ is_array($currentsite->amenities) ? ucwords(implode(', ', str_replace('_', ' ', $currentsite->amenities))) : 'N/A' }}
        </div>


        <div class="reservation-dates">
                <?php
                // Assume $currentsite is defined and available
                $availableRanges = explode(', ', $currentsite['available_ranges']); // Convert string to array
                ?>

            <select class="mt-3 form-control siteclass-active site-select" data-siteid="{{ $siteId }}">
                <option value="">Select Availability Date</option>
                    <?php foreach ($availableRanges as $range): ?>
                    <?php
                    // Split the range into start and end dates
                    [$startDate, $endDate] = explode(' to ', $range);
                    $startFormatted = (new DateTime($startDate))->format('F j, Y'); // Example: February 7, 2025
                    $endFormatted = (new DateTime($endDate))->format('F j, Y');
                    ?>
                <option value="<?= htmlspecialchars($range) ?>">
                        <?= $startFormatted ?> to <?= $endFormatted ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        @php
            // Determine the style for booking
            $style = '';
            if ($hookup != "") {
                $style = ($hookup != $sitehookup) ? "background-color: #efc368;" : "background-color: #66FF66;";
            } else {
                $style = "background-color: #66FF66;";
            }
        @endphp

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll(".site-select").forEach(select => {
                    select.addEventListener("change", function () {
                        let siteId = this.getAttribute("data-siteid");
                        let selectedValue = this.value;

                        if (selectedValue) {
                            // Redirect to the site details page when an option is selected
                            window.location.href = "{{ route('front.site-details', ['siteid' => '__SITE_ID__']) }}".replace("__SITE_ID__", siteId);
                        }
                    });
                });
            });
        </script>

    </div>
</div>

                @endforeach
            </div>





        </div>
    </section>
@endsection

<script>



document.addEventListener('DOMContentLoaded', function () {
    $(document).ready(function () {
        $(".siteclass-active").change(function () {
            let selectedDateRange = $(this).val();
            let siteId = "{{ $siteId }}"; // Ensure $siteId is passed to the view

            if (selectedDateRange) {
                $.ajax({
                    url: "{{ route('front.booking.session.store') }}", // Laravel route
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        site_id: siteId,
                        date_range: selectedDateRange
                    },
                    success: function (response) {
                        if (response.success) {
                            $(".total-cost").html("Total Cost: $" + response.totalCost);
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    });


    // Event listener for when the user selects a date range
    document.querySelectorAll('.siteclass-active').forEach(function(selectElement) {
        selectElement.addEventListener('change', function() {
            // Get the selected date range
            const selectedRange = this.value;

            if (selectedRange) {
                // Split the selected range into start and end dates
                const [startDate, endDate] = selectedRange.split(' to ');

                // Convert dates to Date objects for comparison
                const start = new Date(startDate);
                const end = new Date(endDate);

                // Calculate the number of days in the selected range
                const timeDifference = end - start;
                const numberOfDays = timeDifference / (1000 * 3600 * 24) + 1; // Add 1 to include both start and end dates

                // Get the flat rate value (this should be available as a data attribute or inside a variable)
                const flatRate = parseFloat(this.closest('.hotel-card').querySelector('.flat-rate').textContent.replace('$', ''));

                // Calculate the total cost
                const totalCost = flatRate * numberOfDays;

                // Display the total cost in a place where you want it (e.g., next to the booking button)
                const priceElement = this.closest('.hotel-card').querySelector('.total-cost');
                if (priceElement) {
                    priceElement.textContent = `Total Cost: $${totalCost.toFixed(2)}`;
                }
            }
        });
    });
});

    </script>
@push('js')
@endpush
