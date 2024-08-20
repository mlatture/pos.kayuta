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
   
    function fetchReservations(page = 1, limit = 10) {
        $.ajax({
            type: "GET",
            url: "reservepeople",
            data: { page: page, limit: limit },
            dataType: "json",
            cache: false,
            success: function (data) {
                let tableBody = $('#reservationTable tbody');
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
                            <td>${item.fname} ${item.lname}</td>
                            <td>${item.siteid}</td>
                            <td>${item.siteclass}</td>
                            <td>${cid}</td>
                            <td>${cod}</td>
                            <td>
                                <div class="">
                                    <a href="javascript:void(0)" onclick="openReservationModal(${item.id})" class="m-2">
                                        <i class="fa-solid fa-pen-to-square " style="color: #74C0FC;"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="deleteReservation(${item.id})" class="m-2">
                                        <i class="fa-solid fa-trash" style="color: #ff3d3d;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `);
                });

                let paginationLinks = '';
                if (data.prev_page_url) {
                    paginationLinks += `<a href="javascript:void(0)" data-page="${data.current_page - 1}">Previous</a> `;
                }
                paginationLinks += `Page ${data.current_page} of ${data.last_page} `;
                if (data.next_page_url) {
                    paginationLinks += `<a href="javascript:void(0)" data-page="${data.current_page + 1}">Next</a>`;
                }
                $('#paginationLinks').html(paginationLinks);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching reservations: ', error);
            }
        });
    }

    $('#limitSelector').change(function () {
        fetchReservations(1, $(this).val());
    });

    $(document).on('click', '#paginationLinks a', function () {
        let page = $(this).data('page');
        let limit = $('#limitSelector').val();
        fetchReservations(page, limit);
    });

    fetchReservations();

        $.ajax({
            type: "GET",
            url: "getcustomers",
            dataType: "json",
            cache: false,
            success: function (data) {
                $('#customerSelector').append(`<option value="" disabled selected>Select Customer</option>`);
                $.each(data, function (index, item) {
                    $('#customerSelector').append(`<option  value="${item.id}" data-fname="${item.first_name}" data-lname="${item.last_name}" data-email="${item.email}">${item.first_name} ${item.last_name}</option>`);
                });
            }
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


        // Date Picker
        var selectedFromDate = null;
        var selectedToDate = null;

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            datesSet: function() {
                // Add hover indicator on dates
                $('.fc-daygrid-day').click(function() {
                    var selectedDate = $(this).data('date');
                    var formattedDate = formatDate(new Date(selectedDate));

                    if (selectedFromDate) {
                        $('#toDate').val(formattedDate); 
                        selectedToDate = formattedDate;
                        $('#dateRangeModal').modal('show');
                    } else {
                        $('#fromDate').val(formattedDate); 
                        selectedFromDate = formattedDate;
                    }
                });
            }
        });

        calendar.render();

        function formatDate(date) {
            var options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Intl.DateTimeFormat('en-US', options).format(date);
        }

        $('#customerSelector').change(function(){
            selectedCustomer
        });

        $('#toDate').datepicker({
            dateFormat: 'mm/dd/yyyy',
            autoclose: true,
            todayHighlight: true,
            onSelect: function(selectedDate) {
                selectedToDate = selectedDate;
            }
        });

        $('#fromDate').datepicker({
            dateFormat: 'mm/dd/yyyy',
            autoclose: true,
            todayHighlight: true,
            onSelect: function(selectedDate) {
                var minDate = $('#fromDate').datepicker('getDate');
                $('#toDate').datepicker('option', 'minDate', minDate);
            }
        });



        $('#dateRangeModal').on('hidden.bs.modal', function() {
            $('#fromDate').val('');
            $('#toDate').val('');
            selectedFromDate = null;
            selectedToDate = null;
        });

    });
