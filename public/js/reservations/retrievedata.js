toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: false,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
};

$(document).ready(function () {
  

   
        

  

    
window.toggleCollapse = function(cartid) {
    let collapseRow = document.getElementById(`collapseReservationDetails${cartid}`);
    let icon = document.getElementById(`icon-${cartid}`);

    if (collapseRow.classList.contains("show")) {
        collapseRow.classList.remove("show");
        icon.classList.remove("fa-chevron-up");
        icon.classList.add("fa-chevron-down");
    } else {
        collapseRow.classList.add("show");
        icon.classList.remove("fa-chevron-down");
        icon.classList.add("fa-chevron-up");
    }
};

    

   
    
    
    
    

    $(document).on('change', '.checked_in_date', function(){
        let cartid = $(this).val();
        let row = $(this).closest('tr');
       
       $.ajax({
            url: "reservations/update_checked_in",
            type: "PUT",
            data: { cartid: cartid },
            dataType: "json",
            cache: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                row.find('td:first').text(response.checked_in_date)
            },
            error: function (error){
                console.log('Error: ', error);
            }
       });
    });

    $(document).on('change', '.checked_out_date', function(){
        let cartid = $(this).val();
        let row = $(this).closest('tr');
       
        $.ajax({
            url: "reservations/update_checked_out",
            type: "PUT",
            data: { 
                cartid: cartid 
            },
            dataType: "json",
            cache: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                row.find('td:nth-child(2)').text(response.checked_out_date)
            },
            error: function (error){
                console.log('Error: ', error);
            }
        })
    });
   

    $("#searchInput").on("input", function () {
        let searchTerm = $(this).val();
        let currentStatus =
            $(".reservation-row:visible").data("status") || "All";
        filterBySearch(searchTerm, currentStatus);
    });

    $(".btn-arrival").on("click", function () {
        filterReservations("Arrival");
    });

    $(".btn-departure").on("click", function () {
        filterReservations("Departure");
    });

    $(".btn-occupied").on("click", function () {
        filterReservations("Occupied");
    });

    $(".btn-pending").on("click", function () {
        filterReservations("Pending");
    });

    $(".btn-completed").on("click", function () {
        filterReservations("Completed");
    });

    $(".btn-all").on("click", function () {
        filterReservations("All");
    });

    function filterReservations(status) {
        if (status === "All") {
            $(".reservation-row").show();
        } else {
            $(".reservation-row").each(function () {
                let rowStatus = $(this).data("status");
                if (rowStatus === status) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    }

    function filterBySearch(searchTerm) {
        $(".reservation-row").each(function () {
            let fullName = $(this).find("td").first().text().toLowerCase();
            let searchTermLower = searchTerm.toLowerCase();

            if (fullName.includes(searchTermLower)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function fetchNotReserve(page = 1, limit = 10) {
        $.ajax({
            type: "GET",
            url: "getnotreserve",
            data: { page: page, limit: limit },
            dataType: "json",
            cache: false,
            success: function (data) {
                let tableBody = $("#notReserveTable tbody");
                tableBody.empty();
                $.each(data.data, function (index, item) {

                    if(item.customernumber === null){
                        return;
                    }
                    
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

                let paginationLinks1 = "";
                if (data.prev_page_url) {
                    paginationLinks1 += `<a href="javascript:void(0)" data-page="${
                        data.current_page - 1
                    }">Previous</a> `;
                }
                paginationLinks1 += `Page ${data.current_page} of ${data.last_page} `;
                if (data.next_page_url) {
                    paginationLinks1 += `<a href="javascript:void(0)" data-page="${
                        data.current_page + 1
                    }">Next</a>`;
                }
                $("#paginationLinks1").html(paginationLinks1);
            },
            error: function (xhr, status, error) {
               
            },
        });
    }

   
    $(document).on("click", "#paginationLinks1 a", function () {
        let page = $(this).data("page");
        let limit = $("#limitSelectorNotReserve").val();
        fetchNotReserve(page, limit);
    });

    $(function () {
        var fromDate, toDate;

        $('#openDatePicker').on('click', function () {
            var datePickerDiv = $("<div></div>").datepicker({
                numberOfMonths: 2,
                minDate: 0,
                onSelect: function (selectedDate) {
                    var option = fromDate ? "maxDate" : "minDate";
                    var date = $.datepicker.parseDate("mm/dd/yy", selectedDate);

                    if (!fromDate) {
                        fromDate = date;
                        $("#fromDate").val(
                            $.datepicker.formatDate("MM d, yy", fromDate)
                        );
                        $(this).datepicker("option", option, date);
                    } else {
                        toDate = date;
                        $("#toDate").val(
                            $.datepicker.formatDate("MM d, yy", toDate)
                        );
                        $(this).dialog("close");
                        $("#dateRangeModal").modal("show");
                        fromDate = toDate = null;

                        datePickerDiv
                            .datepicker("setDate", null)
                            .datepicker("option", "minDate", 0)
                            .datepicker("option", "maxDate", null);
                    }
                },
                beforeShowDay: function (date) {
                    if (fromDate && toDate) {
                        return [true, "ui-state-highlight"];
                    }
                    if (fromDate) {
                        return [
                            true,
                            date >= fromDate ? "ui-state-highlight" : "",
                        ];
                    }
                    return [true, ""];
                },
            });

            datePickerDiv.dialog({
                modal: true,
                title: "Select Date Range",
                width: 600,
                autoOpen: true,
                resizable: false,
                position: { my: "center", at: "center", of: window },
                buttons: {
                    Clear: function () {
                        fromDate = toDate = null;
                        $("#fromDate, #toDate").val("");
                        datePickerDiv
                            .datepicker("setDate", null)
                            .datepicker("option", "minDate", 0)
                            .datepicker("option", "maxDate", null);
                    },
                    Close: function () {
                        $(this).dialog("close");
                    },
                },
            });
        });
    });

    $("#customerSelector").change(function () {
        var selectedOption = $(this).find("option:selected");
        var fname = selectedOption.data("fname");
        var lname = selectedOption.data("lname");
        var email = selectedOption.data("email");
        $("#fname").val(fname);
        $("#lname").val(lname);
        $("#email").val(email);
    });



    function loadAddOns() {
        $.ajax({
            url: "getaddons",
            method: "GET",
            success: function(response) {
                const tbody = $("#addon-table-body");
                tbody.empty();

                if (response.length > 0) {
                    response.forEach((addon) => {
                        const row = `
                <tr>
                    <td>${addon.name}</td>
                    <td>${addon.price}</td>
                </tr>`;
                        tbody.append(row);
                    });
                } else {
                    const noDataRow = `
            <tr>
                <td colspan="2" style="text-align: center;">No addons available</td>
            </tr>`;
                    tbody.append(noDataRow);
                }
            },
            error: function(error) {
                console.error("Error fetching addons:", error);
                const tbody = $("#addon-table-body");
                tbody.empty();
                const errorRow = `
        <tr>
            <td colspan="2" style="text-align: center; color: red;">Failed to load addons</td>
        </tr>`;
                tbody.append(errorRow);
            },
        });
    }

    loadAddOns();
    
});
