@extends('layouts.admin')

@section('title', 'Relocate & Re-Schedule')
@section('content-header', 'Relocate & Re-Schedule')
@php
    use Carbon\Carbon;
@endphp
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Relocate</h5>
                        </div>
                        <div class="card-body">
                            @if ($reservation)
                                <div class="row">
                                    <div class="col">
                                        <label for="">Name</label>
                                        <input type="text" class="form-control"
                                            value="{{ $reservation->fname }} {{ $reservation->lname }}" readonly>
                                    </div>
                                    <div class="col">
                                        <label for="">Site Class</label>
                                        <input type="text" class="form-control" value="{{ $reservation->siteclass }}"
                                            readonly>
                                    </div>
                                    <div class="col">
                                        <label for="">Site ID</label>
                                        <input type="text" class="form-control" value="{{ $reservation->siteid }}"
                                            readonly>
                                    </div>
                                </div>
                            @else
                                <p>No reservation found for this Cart ID.</p>
                            @endif

                            @if ($siteclasses)
                                <div class="row mt-3">
                                    <label for="">Select New Sites</label>

                                    <div class="col">
                                        <select name="siteclass" class="form-control" id="siteclass">
                                            <option value="" disabled selected>Select Site Class</option>
                                            @foreach ($siteclasses as $siteclass)
                                                <option value="{{ $siteclass->siteclass }}"
                                                    data-siteclass="{{ $siteclass->siteclass }}">
                                                    {{ $siteclass->siteclass }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if ($hookups)
                                        <div class="col" id="parent" hidden>

                                            <select class="form-control" id="hookup">
                                                <option value="" disabled selected>Select Site Hookup</option>

                                                @foreach ($hookups as $hookup)
                                                    <option value="{{ $hookup->sitehookup }}"
                                                        data-sitehookup="{{ $hookup->sitehookup }}">
                                                        {{ $hookup->sitehookup }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    @endif
                                </div>
                            @else
                                <p>No site classes found.</p>
                            @endif

                            <div class="row mt-3">

                                @if ($sites)
                                    <div class="col">


                                        <select name="siteid" class="form-control" id="siteid">
                                            <option value="" disabled selected>Select Site ID</option>

                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Re-Schedule</h5>
                        </div>
                        <div class="card-body">
                            @if ($reservation)
                                <div class="row">
                                    <div class="col">
                                        <label for="dateRangePicker">Check In and Check Out Date</label>
                                        <input type="text" id="dateRangePicker" class="form-control">
                                    </div>
                                </div>
                            @else
                                <p>No reservation found for this Cart ID.</p>
                            @endif

                            <div class="row mt-3" hidden>
                                <div class="col">
                                    <label for="checkInDate">Check In Date</label>
                                    <input type="text" class="form-control" id="checkInDate" value="{{ $cid }}"
                                        readonly>
                                </div>
                                <div class="col">
                                    <label for="checkOutDate">Check Out Date</label>
                                    <input type="text" class="form-control" id="checkOutDate"
                                        value="{{ $cod }}" readonly>
                                </div>
                                <input type="text" value="{{ $reservation->siteid }}" id="site_id">

                            </div>



                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5>Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <p><strong>Paid Amount:</strong> $<span>{{ $paidAmount->payment }}</span></p>
                                </div>
                                <div class="col-6">
                                    <p><strong>Number of Nights:</strong> <span id="no_nights"></span></p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <p><strong>Total Amount:</strong> $<span id="total_amount">0</span></p>
                                </div>
                                <div class="col-6">
                                    <p><strong>Balance:</strong> $<span id="balance"></span></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-right">
                                    <button id="updatePricing" class="btn btn-primary" hidden>Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@section('js')

    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">
      $(document).ready(function() {
            $('#dateRangePicker').daterangepicker({
                locale: {
                    format: 'MMM D, YYYY'
                },
                startDate: moment("{{ $cid }}"),
                endDate: moment("{{ $cod }}")
            }).on('apply.daterangepicker', function(ev, picker) {
                $('#checkInDate').val(picker.startDate);
                $('#checkOutDate').val(picker.endDate);
                loadRelocateSite();
                loadApiPricing(); 
            });

            $("#siteclass").change(function() {
                var siteclass = $(this).val();
                console.log(siteclass);
                $("#updatePricing").prop("hidden", false);
                if(siteclass === 'RV Sites') {
                    $("#parent").removeAttr("hidden");
                } else {
                    $("#parent").prop("hidden", true);

                }
                loadRelocateSite();
            });

            $("#hookup").change(loadRelocateSite);

            $("#siteid").change(function() {
                $('#site_id').val($(this).find('option:selected').data('selected_siteid'));
                loadApiPricing();
            });

            $("#updatePricing").click(function() {
                const site_id = $('#site_id').val();
                const cid = $('#checkInDate').val();
                const cod = $('#checkOutDate').val();
                const selectedHookup = $("#hookup option:selected").data("sitehookup");
                const selectedSiteClass = $("#siteclass option:selected").data("siteclass");
                const totalAmount = $('#total_amount').text();
                const cartid = "{{ $reservation->cartid }}";
                if (!site_id || !cid || !cod || !selectedSiteClass) return;

                $.ajax({
                    url: '{{ route('update.pricing') }}',
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        site_id: site_id,
                        cid: cid,
                        cod: cod,
                        siteclass: selectedSiteClass,
                        hookup: selectedHookup,
                        total_amount: totalAmount,
                        cartid: cartid
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.href = "{{ route('reservations.index') }}";
                            }, 2000);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    },
                    error: function(xhr) {
                        console.error("Error updating pricing:", xhr.responseText);
                    }
                });
            });

            function loadRelocateSite() {
                const cid = $('#checkInDate').val();
                const cod = $('#checkOutDate').val();
                const selectedHookup = $("#hookup option:selected").data("sitehookup");
                const selectedSiteClass = $("#siteclass option:selected").data("siteclass");

                if (!cid || !cod || !selectedSiteClass) return;

                $.ajax({
                    url: '{{ route('filter.sites') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        siteclass: selectedSiteClass,
                        cid: cid,
                        cod: cod,
                        hookup: selectedHookup
                    },
                    success: function(response) {
                        const siteidSelect = $('#siteid');
                        siteidSelect.empty().append(
                            '<option value="" disabled selected>Select Site ID</option>'
                        );

                        $.each(response, function(index, item) {
                            siteidSelect.append(
                                `<option value="${item.siteid}" data-selected_siteid="${item.siteid}">${item.siteid}</option>`
                            );
                        });

                        if (siteidSelect.val()) {
                            loadApiPricing();
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching sites:", xhr.responseText);
                    }
                });
            }

            function loadApiPricing() {
                const site_id = $('#site_id').val();
                const cid = $('#checkInDate').val();
                const cod = $('#checkOutDate').val();

                if (!site_id || !cid || !cod) return;

                $.ajax({
                    url: `${webdavinci_api}/api/get_pricing`,
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        start_date: cid,
                        end_date: cod,
                        site_id: site_id
                    }),
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        "X-API-KEY": `${webdavinci_api_key}`
                    },
                    success: function(api_data) {
                        $('#no_nights').text(api_data.number_of_nights);
                        $('#total_amount').text(api_data.total_price);
                        const paidAmount = parseFloat("{{ $paidAmount->payment }}");
                        const totalAmount = parseFloat(api_data.total_price);
                        const balance = totalAmount - paidAmount;
                        $('#balance').text(balance.toFixed(2));
                    },
                    error: function(xhr) {
                        console.error("Error loading pricing:", xhr.responseText);
                    }
                });
            }

            // Initial call to load pricing if values are already set
            loadApiPricing();
        });
    </script>



@endsection
