@extends('layouts.admin')

@section('title', 'Z - Out Report')




@section('content-header')

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">

    <style>
        .daterangepicker {
            z-index: 9999 !important;
        }

        .refresh-btn-container {
            display: flex;
            justify-content: flex-end;
            padding: 10px 0;
        }

        .refresh-btn-container button {
            padding: 8px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .refresh-btn-container button:hover {
            background-color: #218838;
        }



        div.dt-top-container {
            display: flex;

            text-align: center;
        }

        div.dt-center-in-div {
            margin: 0 auto;
            display: inline-block;
            text-align: center;
        }

        div.dt-filter-spacer {
            margin: 10px 0;
        }

        td.highlight {
            background-color: #F4F6F9 !important;
        }

        div.dt-left-in-div {
            float: left;
        }

        div.dt-right-in-div {
            float: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .section-title {
            font-size: 18px;
            margin: 20px 0 10px;
        }
    </style>
@endsection

@section('content')
<h1><span id="dynamic-content-header" >POS Z - Out Report</span></h1>

    <div class="row ">
        <div class="" style="background-color: #F4F6F9; box-shadow: none; border: none;">
            <div class="card-body" style="border: none;">
                <div class="row mt-3 d-flex justify-content-center align-items-center">

                    <!-- Date Range -->
                    <div class="col-md-3">
                        <label for="productDate" class="form-label">Date Range:</label>
                        <input type="text" class="form-control daterange" id="z-out-datepicker" autocomplete="off"
                            placeholder="Select Date">
                    </div>

                    <!-- Station -->
                    <div class="col-md-3">
                        <label for="station" class="form-label">Station:</label>
                        <select id="station" class="form-control">

                        </select>
                    </div>

                    <!-- User -->
                    <div class="col-md-3">
                        <label for="user" class="form-label">User:</label>
                        <select id="user" class="form-control">

                        </select>
                    </div>

                    <!-- Cash Begin -->
                    <div class="col-md-3">
                        <label for="cash_begin" class="form-label">Cash Begin:</label>
                        <input type="text" id="cash_begin" class="form-control">
                    </div>

                    <!-- Cash Leave -->
                    <div class="col-md-3">
                        <label for="cash_leave" class="form-label">Cash Leave:</label>
                        <input type="text" id="cash_leave" class="form-control">
                    </div>

                    <!-- Actual Cash Count -->
                    <div class="col-md-3">
                        <label for="actual_cash_count" class="form-label">Actual Cash Count:</label>
                        <input type="text" id="actual_cash_count" class="form-control">
                    </div>

                    <!-- Refresh Button -->
                    <div class="col-md-2 d-flex align-items-center">
                        <button class="btn btn-success mt-3" id="refreshBtn">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <h2 class="section-title">Sales Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Gross Sales</th>
                        <th>Gross Returns</th>
                        <th>Net</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sales</td>
                        <td id="grossSales"> </td>
                        <td>$0.00</td>
                        <td id="netSales"></td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td id="salesTax"></td>
                        <td>$0.00</td>
                        <td id="netTax"></td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td id="totalSales"></td>
                        <td>$0.00</td>
                        <td id="netTotalSales"></td>
                    </tr>
                    <tr>
                        <td>Trans. Count</td>
                        <td id="sales_tran_count"></td>
                        <td>0</td>
                        <td id="net_tran_count"></td>
                    </tr>
                </tbody>
            </table>

            <h2 class="section-title">Sales Activity</h2>
            <table id="salesActivityTable">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Tax</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="section-title">Payment Summary</h2>
            <table id="paymentSummaryTable">
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th>Trans. Count</th>
                        <th>Amount</th>
                    </tr>
                </thead>
               <tbody>

               </tbody>
            </table>

        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h2 class="section-title">Credit Card Listing</h2>
            <table id="creditCardListingTable">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Amount</th>
                    </tr>
                </thead>
             <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="section-title">User Activity</h2>
            <table id="userActivityTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Station</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>12 AM</th>
                        <th>1 AM</th>
                        <th>2 AM</th>
                        <th>3 AM</th>
                        <th>4 AM</th>
                        <th>5 AM</th>
                        <th>6 AM</th>
                        <th>7 AM</th>
                        <th>8 AM</th>
                        <th>9 AM</th>
                        <th>10 AM</th>
                        <th>11 AM</th>
                        <th>12 PM</th>
                        <th>1 PM</th>
                        <th>2 PM</th>
                        <th>3 PM</th>
                        <th>4 PM</th>
                        <th>5 PM</th>
                        <th>6 PM</th>
                        <th>7 PM</th>
                        <th>8 PM</th>
                        <th>9 PM</th>
                        <th>10 PM</th>
                        <th>11 PM</th>
                        <th>12 PM</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>

        <button id="downloadPdfBtn" class="btn btn-primary mt-3">
            Download PDF
        </button>
        
    </div>

    
     
@endsection

@section('js')
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.card').hide();

            $('.filter-card').show();

            $('#z-out-datepicker').daterangepicker({
                singleDatePicker: true,
                autoApply: true,
                autoUpdateInput: true,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MM/DD/YYYY',
                },
                ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                                'month').endOf('month')]
                        },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                opens: 'auto',
            });

            function toggleCards(show = false) {
                if (show) {
                    $('.card').show();
                } else {
                    $('.card').hide();
                }
            }

            $.ajax({
                url: "{{ route('user.name') }}",
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    $('#user').empty();
                    $('#user').append('<option disabled selected>Select a user</option>');

                    response.forEach(res => {
                        let name = res.name.charAt(0).toUpperCase() + res.name.slice(1);
                        let option = `<option value="${res.id}" data-name="${name}">${name}</option>`;
                        $('#user').append(option);
                    });

               
                },
                error: function (xhr, error) {
                    console.error('Error fetching users:', error);
                }
            });
            
          
            function fetchData() {
                    let selectedDate = $('#z-out-datepicker').val();
                    let selectedCustomerId = $('#user').val();
                    let stationId = $('#station').val();
                    let selectedUserName = $('#user option:selected').data('name');
                    let selectedStatName = $('#station option:selected').data('name');
                   
                    if (!selectedCustomerId) {
                        console.warn('User not selected. Hiding data cards.');
                        toggleCards(false);
                        return;
                    }

                    toggleCards(true);


                    $.ajax({
                        url: "{{ route('reports.zOutReport') }}",
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            customer_id: selectedCustomerId,
                            date_range: selectedDate,
                            // station_id: stationId
                        },
                        success: function (response) {
                            console.log(response);
                            // Update Sales Summary
                            $('#grossSales').text('$' + response.gross_sales.toFixed(2));
                            $('#salesTax').text('$' + response.gross_sales_tax.toFixed(2));
                            $('#totalSales').text('$' + (response.gross_sales - response.gross_sales_tax).toFixed(2));
                            $('#sales_tran_count').text(response.transaction_count);

                            $('#netSales').text('$' + response.net_sales.toFixed(2));
                            $('#netTax').text('$' + response.net_tax.toFixed(2));
                            $('#netTotalSales').text('$' + response.net_total_sales.toFixed(2));
                            $('#net_tran_count').text(response.net_transaction_count);

                            // Update Sales Activity
                            let totalAmount = 0, totalTax = 0, grandTotal = 0;
                            let salesActivityBody = response.sales_activity
                                .map(activity => {
                                    const grossSales = parseFloat(activity.gross_sales) || 0;
                                    const totalTaxValue = parseFloat(activity.total_tax) || 0;
                                    const total = grossSales + totalTaxValue;

                                    totalAmount += grossSales;
                                    totalTax += totalTaxValue;
                                    grandTotal += total;

                                    return `
                                        <tr>
                                            <td>${activity.tax_type}</td>
                                            <td>$${grossSales.toFixed(2)}</td>
                                            <td>$${totalTaxValue.toFixed(2)}</td>
                                            <td>$${total.toFixed(2)}</td>
                                        </tr>
                                    `;
                                }).join('');

                            salesActivityBody += `
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td><strong>$${totalAmount.toFixed(2)}</strong></td>
                                    <td><strong>$${totalTax.toFixed(2)}</strong></td>
                                    <td><strong>$${grandTotal.toFixed(2)}</strong></td>
                                </tr>
                            `;

                            $('#salesActivityTable tbody').html(salesActivityBody);

                            // Update Payment Summary
                            let paymentSummaryBody = response.payment_summary
                                .map(summary => {
                                    if (summary.transaction_count !== 0) {
                                        return `
                                            <tr>
                                                <td>${summary.payment_method}</td>
                                                <td>${summary.transaction_count}</td>
                                                <td>$${parseFloat(summary.total_amount).toFixed(2)}</td>
                                            </tr>
                                        `;
                                    }
                                }).join('');

                            $('#paymentSummaryTable tbody').html(paymentSummaryBody);

                            // Update Credit Card Listing
                            let creditCardListingTableBody = response.list
                                .map(card => `
                                    <tr>
                                        <td>${card.method}: ${card.masked_card_number}</td>
                                        <td>${card.name}</td>
                                        <td>$${parseFloat(card.order_amount).toFixed(2)}</td>
                                    </tr>
                                `).join('');

                            $('#creditCardListingTable tbody').html(creditCardListingTableBody);




                            // Update User Activity 
                          let userActivity = response.user_activity;
                            console.log('User Activity:', userActivity);

                            if (!userActivity || Object.keys(userActivity).length === 0) {
                                console.warn('No user activity data available.');
                                $('#userActivityTable tbody').html('<tr><td colspan="28">No data available</td></tr>');
                                return;
                            }

                            let date = userActivity.date || 'N/A';
                            let totalCount = userActivity.total_count || 0;
                            let hourlyCounts = userActivity.hourly_counts || [];
                            let hourlyPaymentCounts = userActivity.hourly_payment_counts || [];
                            let totalPaymentCount = userActivity.total_payment_count || 0;

                            let hourlyCountColumns = '';
                            let hourlyPaymentColumns = '';

                            for (let i = 0; i < 24; i++) {
                                hourlyCountColumns += `<td>${hourlyCounts[i] || 0}</td>`;
                                hourlyPaymentColumns += `<td>${hourlyPaymentCounts[i] || 0}</td>`;
                            }

                            let userActivityBody = `
                                <tr>
                                    <td>${date}</td>
                                    <td>${selectedStatName || 'N/A'}</td>
                                    <td>${selectedUserName || 'N/A'}</td>
                                    <td>${totalCount}</td>
                                    ${hourlyCountColumns}
                                </tr>
                               
                            `;

                            // Append rows to the table body
                            $('#userActivityTable tbody').html(userActivityBody);



                       
                        },
                        error: function (xhr, error) {
                            console.error('Error fetching data:', error);
                        }
                    });
                }
                function handleFilterChange() {
                    fetchData();
                    updateContentHeader();
                }

                $('#z-out-datepicker').on('apply.daterangepicker', handleFilterChange);
                $('#user').on('change', handleFilterChange);
                $('#station').on('change', handleFilterChange);

            function ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function updateContentHeader() {
                let selectedDate = $('#z-out-datepicker').val() || 'N/A';
                let selectedStation = $('#station option:selected').data('name') || 'N/A';
                let selectedUser = $('#user option:selected').data('name') || 'N/A';

                let formattedDate = selectedDate
                    ? moment(selectedDate, 'MM/DD/YYYY').format('dddd, MMMM D, YYYY')
                    : 'N/A';

                let headerContent = `POS Z - Out Report (${formattedDate}) (Station: ${selectedStation}) (User: ${selectedUser})`;
                $('#dynamic-content-header').text(headerContent);
            }

                

            // Fetch Station No: 
            $.ajax({
                url: "{{ route('registers.station_name') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#station').empty();

                    $('#station').append('<option disabled selected> Select a station </option>');


                    for (let res of response) {
                        let statName = ucfirst(res.name);
                        let option = `<option value="${res.id}" data-name="${statName}"> ${statName} </option>`;

                        console.log(res);
                        $('#station').append(option);
                        
                    }

                  
                },
                error: function(xhr, error) {
                    console.error('Error', xhr, error);
                }
            });


            $('#downloadPdfBtn').on('click', function () {
                const selectedUserName = $('#user option:selected').data('name');
                const selectedStatName = $('#station option:selected').data('name');
                const selectedDateRange = $('#z-out-datepicker').val();

                // Gather data from the table
                const reportData = {
                    grossSales: $('#grossSales').text(),
                    netSales: $('#netSales').text(),
                    salesTax: $('#salesTax').text(),
                    netTax: $('#netTax').text(),
                    totalSales: $('#totalSales').text(),
                    netTotalSales: $('#netTotalSales').text(),
                    salesTranCount: $('#sales_tran_count').text(),
                    netTranCount: $('#net_tran_count').text(),
                    salesActivity: [],
                    paymentSummary: [],
                    creditCardListing: [],
                    userActivity: [],

                    selectedDateRange,
                    selectedUserName,
                    selectedStatName
                };

                // Collect Sales Activity Data
                $('#salesActivityTable tbody tr').each(function () {
                    const row = $(this).find('td');
                    reportData.salesActivity.push({
                        account: row.eq(0).text(),
                        amount: row.eq(1).text(),
                        tax: row.eq(2).text(),
                        total: row.eq(3).text(),
                    });
                });

                // Collect Payment Summary Data
                $('#paymentSummaryTable tbody tr').each(function () {
                    const row = $(this).find('td');
                    reportData.paymentSummary.push({
                        method: row.eq(0).text(),
                        transactionCount: row.eq(1).text(),
                        amount: row.eq(2).text(),
                    });
                });

                // Collect Credit Card Listing Data
                $('#creditCardListingTable tbody tr').each(function () {
                    const row = $(this).find('td');
                    reportData.creditCardListing.push({
                        type: row.eq(0).text(),
                        name: row.eq(1).text(),
                        amount: row.eq(2).text(),
                    });
                });

                $('#userActivityTable tbody tr').each(function () {
                    const row = $(this).find('td');
                    const hourlyCounts = [];

                    row.slice(4).each(function () {
                        hourlyCounts.push($(this).text());
                    });

                    reportData.userActivity.push({
                        date: row.eq(0).text(),
                        statName: row.eq(1).text(),
                        userName: row.eq(2).text(),
                        totalCount: row.eq(3).text(),
                        hourlyCounts: hourlyCounts,
                    });
                });


                $.ajax({
                    url: "{{ route('reports.downloadPdf') }}", 
                    type: 'POST',
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: { reportData: reportData },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function (response) {
                        const blob = new Blob([response], { type: 'application/pdf' });

                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'z-out-report.pdf';
                        link.click();
                    },
                    error: function (xhr, status, error) {
                        console.error('Error generating PDF:', error);
                    }
                });
            });

        });
    </script>
@endsection
