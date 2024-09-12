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


$(document).ready(function () {
   
  
    $(document).on('click', '#paginationLinks a', function () {
        let page = $(this).data('page');
        let limit = $('#limitSelector').val();
        let tier = $(this).data('tier') ? $(this).data('tier').split(',') : [];
        let siteId = $(this).data('siteid') || ''; 
        fetchReservations(page, limit, siteId, tier);
    });
    
    $("#siteSelectors").change(function() {
        let selectedSiteId = $(this).val() || '';
        let selectedType = $("#type").val() || []; 
        fetchReservations(1, 10, selectedSiteId, selectedType);
    });
    
    $("#type").change(function() {
        let selectedType = $(this).val() || []; 
        let selectedSiteId = $("#siteSelectors").val() || ''; 
        fetchReservations(1, 10, selectedSiteId, selectedType);
    });
    
 
    $('input[name="dates"]').daterangepicker({
        opens: 'left',
        autoApply: true,
    }, function (start, end, label) {
        let startDate = start.format('YYYY-MM-DD');
        let endDate = end.format('YYYY-MM-DD'); 
        
        if(startDate === '0000-01-01') startDate = '';
        if(endDate === '0000-01-01') endDate = '';
      
        let selectedType = $("#typeSelectors").val() || []; 
        let selectedSiteId = $("#siteSelectors").val() || ''; 
        
        fetchReservations(1, 10, selectedSiteId, selectedType, startDate, endDate);
    });
    
    let allReservations = [];
   
    
    function fetchReservations(page = 1, limit = 10, siteId = '', tier = [], startDate = '', endDate = '') {
        $.ajax({
            type: "GET",
            url: "reservepeople",
            data: { 
                page: page, 
                limit: limit, 
                siteId: siteId, 
                tier: tier,
                start_date: startDate,
                end_date: endDate
            },
            dataType: "json",
            cache: false,
            success: function (data) {
                allReservations = data.data; 
                
                displayReservations(allReservations); 
                
                let paginationLinks = '';
                if (data.prev_page_url) {
                    paginationLinks += `<a href="javascript:void(0)" data-page="${data.current_page - 1}" data-siteid="${siteId}" data-tier="${tier ? tier.join(',') : ''}">Previous</a> `;
                }
                paginationLinks += `Page ${data.current_page} of ${data.last_page} `;
                if (data.next_page_url) {
                    paginationLinks += `<a href="javascript:void(0)" data-page="${data.current_page + 1}" data-siteid="${siteId}" data-tier="${tier ? tier.join(',') : ''}">Next</a>`;
                }
                $('#paginationLinks').html(paginationLinks);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching reservations: ', error);
            }
        });
    }

   
    function displayReservations(reservations) {
        let tableBody = $('#reservationTable tbody');
        tableBody.empty();
    
        let now = new Date();
    
        $.each(reservations, function (index, item) {
            let cidDate = new Date(item.cid);
            let codDate = new Date(item.cod);
    
            let cid = cidDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
    
            let cod = codDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
    
            let dateStatus = '';
            let statusClass = '';
    
            if (cidDate.toDateString() === now.toDateString()) {
                dateStatus = 'Arrival';
                statusClass = 'bg-success';
            } else if (codDate.toDateString() === now.toDateString()) {
                dateStatus = 'Departure';
                statusClass = 'bg-danger';
            } else if (cidDate <= now && codDate >= now) {
                dateStatus = 'Occupied';
                statusClass = 'bg-info';
            } else if (cidDate > now) {
                dateStatus = 'Pending';
                statusClass = 'bg-warning text-white';
            } else if (codDate < now) {
                dateStatus = 'Completed';
                statusClass = 'bg-primary';
            }
    
            tableBody.append(`
                <tr
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Arrival: ${cid}\nDeparture: ${cod}\nSite: ${item.siteid} - ${item.siteclass}\nAddress: ${item.address} \nPhone: ${item.phone}\nCust#: ${item.customernumber}"
                    class="reservation-row" data-status="${dateStatus}">
                    <td>${item.fname} ${item.lname}</td>
                    <td>${item.siteid}</td>
                    <td>${item.siteclass}</td>
                    <td>
                        <span class="${statusClass} badge rounded-pill p-2" style="font-size: 12px;">
                            ${dateStatus}
                        </span>
                    </td>
                    <td>
                        <div class="btn btn-info" type="button" id="viewInvoice" data-id="${item.cartid}">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                    </td>
                </tr>
            `);
        });
    }
    
    $('#searchInput').on('input', function(){
        let searchTerm = $(this).val();
        let currentStatus = $('.reservation-row:visible').data('status') || 'All';
        filterBySearch(searchTerm, currentStatus);
    })

    $('.btn-arrival').on('click', function() {
        filterReservations('Arrival');
    });
    
    $('.btn-departure').on('click', function() {
        filterReservations('Departure');
    });
    
    $('.btn-occupied').on('click', function() {
        filterReservations('Occupied');
    });
    
    $('.btn-pending').on('click', function() {
        filterReservations('Pending');
    });
    
    $('.btn-completed').on('click', function() {
        filterReservations('Completed');
    });
    
    $('.btn-all').on('click', function() {
        filterReservations('All');
    });
    
    function filterReservations(status) {
        if (status === 'All') {
            $('.reservation-row').show();
        } else {
            $('.reservation-row').each(function() {
                let rowStatus = $(this).data('status');
                if (rowStatus === status) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

            
      
    }

    function filterBySearch(searchTerm) {
        $('.reservation-row').each(function() {
            let fullName = $(this).find('td').first().text().toLowerCase();
            let searchTermLower = searchTerm.toLowerCase();

            if(fullName.includes(searchTermLower)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    
    
    
// Fetch NotReserve Data
    function fetchNotReserve(page = 1, limit = 10) {
        $.ajax({
            type: "GET",
            url: "getnotreserve",
            data: { page: page, limit: limit },
            dataType: "json",
            cache: false,
            success: function (data) {
                let tableBody = $('#notReserveTable tbody');
                tableBody.empty();

                $.each(data.data, function (index, item) {
                    let cid = new Date(item.cid).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long', 
                        day: 'numeric'  
                    });

                    let cod = new Date(item.cod).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                  
                    tableBody.append(`
                        <tr>
                            <td>${item.first_name} ${item.last_name}</td>
                        
                         
                            <td>
                                <div class="">
                                    
                                    <div class="btn btn-info" data-id="${item.id}" type="submit" id="paymentbtn">
                                        <i class="fa-solid fa-hand-holding-dollar"></i>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    `);
                });

                let paginationLinks1 = '';
                if (data.prev_page_url) {
                    paginationLinks1 += `<a href="javascript:void(0)" data-page="${data.current_page - 1}">Previous</a> `;
                }
                paginationLinks1 += `Page ${data.current_page} of ${data.last_page} `;
                if (data.next_page_url) {
                    paginationLinks1 += `<a href="javascript:void(0)" data-page="${data.current_page + 1}">Next</a>`;
                }
                $('#paginationLinks1').html(paginationLinks1);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching not reserved: ', error);
            }
        });
    }

   
    
    $('#limitSelector').change(function () {
        fetchReservations(1, $(this).val());
    });
    
    $('#limitSelectorNotReserve').change(function(){
        fetchNotReserve(1, $(this).val());
    });
    
   
   
    
    $(document).on('click', '#paginationLinks1 a', function () {
        let page = $(this).data('page');
        let limit = $('#limitSelectorNotReserve').val();
        fetchNotReserve(page, limit);
    });

  
    $(function() {
        var fromDate, toDate;
    
        $("#openDatePicker").click(function() {
            var datePickerDiv = $("<div></div>").datepicker({
                numberOfMonths: 2,
                minDate: 0,
                onSelect: function(selectedDate) {
                    var option = fromDate ? "maxDate" : "minDate";
                    var date = $.datepicker.parseDate("mm/dd/yy", selectedDate);
    
                    if (!fromDate) {
                        fromDate = date;
                        $("#fromDate").val($.datepicker.formatDate("MM d, yy", fromDate));
                        $(this).datepicker("option", option, date);
                    } else {
                        toDate = date;
                        $("#toDate").val($.datepicker.formatDate("MM d, yy", toDate));
                        $(this).dialog("close");
                        $('#dateRangeModal').modal('show');
                        fromDate = toDate = null;
                       
                        datePickerDiv.datepicker("setDate", null)
                            .datepicker("option", "minDate", 0)
                            .datepicker("option", "maxDate", null);
                    }
                },
                beforeShowDay: function(date) {
                    if (fromDate && toDate) {
                        return [true, "ui-state-highlight"];
                    }
                    if (fromDate) {
                        return [true, date >= fromDate ? "ui-state-highlight" : ""];
                    }
                    return [true, ""];
                }
            });
    
            datePickerDiv.dialog({
                modal: true,
                title: "Select Date Range",
                width: 600,
                autoOpen: true,
                resizable: false, 
                position: { my: "center", at: "center", of: window },
                buttons: {
                    "Clear": function() {
                        fromDate = toDate = null;
                        $("#fromDate, #toDate").val("");
                        datePickerDiv.datepicker("setDate", null)
                            .datepicker("option", "minDate", 0)
                            .datepicker("option", "maxDate", null);
                    },
                    "Close": function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
    });
    

      

        $('#customerSelector').change(function(){
            var selectedOption = $(this).find('option:selected');
            var fname = selectedOption.data('fname');
            var lname = selectedOption.data('lname');
            var email = selectedOption.data('email');
            $('#fname').val(fname);
            $('#lname').val(lname);
            $('#email').val(email)
        });



        fetchReservations();
        fetchNotReserve();  
   
    });
