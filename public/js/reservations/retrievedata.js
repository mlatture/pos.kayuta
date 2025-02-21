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
    $(document).on("click", "#paginationLinks a", function () {
        let page = $(this).data("page");
        let limit = $("#limitSelector").val();
        let tier = $(this).data("tier") ? $(this).data("tier").split(",") : [];
        let siteId = $(this).data("siteid") || "";
        fetchReservations(page, limit, siteId, tier);
    });

    $("#siteSelectors").change(function () {
        let selectedSiteId = $(this).val() || "";
        let selectedType = $("#type").val() || [];
        fetchReservations(1, 10, selectedSiteId, selectedType);
    });

    $("#type").change(function () {
        let selectedType = $(this).val() || [];
        let selectedSiteId = $("#siteSelectors").val() || "";
        fetchReservations(1, 10, selectedSiteId, selectedType);
    });

    $('input[name="dates"]').daterangepicker(
        {
            opens: "left",
            autoApply: true,
        },
        function (start, end, label) {
            let startDate = start.format("YYYY-MM-DD");
            let endDate = end.format("YYYY-MM-DD");

            if (startDate === "0000-01-01") startDate = "";
            if (endDate === "0000-01-01") endDate = "";

            let selectedType = $("#typeSelectors").val() || [];
            let selectedSiteId = $("#siteSelectors").val() || "";

            fetchReservations(
                1,
                10,
                selectedSiteId,
                selectedType,
                startDate,
                endDate
            );
        }
    );

    let allReservations = [];

    // function fetchReservations(
    //     page = 1,
    //     limit = 10,
    //     siteId = "",
    //     tier = [],
    //     startDate = "",
    //     endDate = ""
    // ) {
    //     $.ajax({
    //         type: "GET",
    //         url: "reservepeople",
    //         data: {
    //             page: page,
    //             limit: limit,
    //             siteId: siteId,
    //             tier: tier,
    //             start_date: startDate,
    //             end_date: endDate,
    //         },
    //         dataType: "json",
    //         cache: false,
    //         success: function (data) {
    //             console.log(data); die();
    //             allReservations = data.data;

    //             displayReservations(allReservations);

    //             let paginationLinks = "";
    //             if (data.prev_page_url) {
    //                 paginationLinks += `<a href="javascript:void(0)" data-page="${
    //                     data.current_page - 1
    //                 }" data-siteid="${siteId}" data-tier="${
    //                     tier ? tier.join(",") : ""
    //                 }">Previous</a> `;
    //             }
    //             paginationLinks += `Page ${data.current_page} of ${data.last_page} `;
    //             if (data.next_page_url) {
    //                 paginationLinks += `<a href="javascript:void(0)" data-page="${
    //                     data.current_page + 1
    //                 }" data-siteid="${siteId}" data-tier="${
    //                     tier ? tier.join(",") : ""
    //                 }">Next</a>`;
    //             }
    //             $("#paginationLinks").html(paginationLinks);
    //         },
    //         error: function (xhr, status, error) {
           
    //         },
    //     });
    // }

    // function displayReservations(reservations) {
    //     let tableBody = $("#reservationTable tbody");
    //     tableBody.empty();

    //     let now = new Date();

    //     $.each(reservations, function (index, item) {
    //         let cidDate = new Date(item.cid);
    //         let codDate = new Date(item.cod);
         
    //         let cid = cidDate.toLocaleDateString("en-US", {
    //             year: "numeric",
    //             month: "long",
    //             day: "numeric",
    //         });

    //         let cod = codDate.toLocaleDateString("en-US", {
    //             year: "numeric",
    //             month: "long",
    //             day: "numeric",
    //         });

    //         let dateStatus = "";
    //         let statusClass = "";

    //         if (cidDate.toDateString() === now.toDateString()) {
    //             dateStatus = "Arrival";
    //             statusClass = "bg-success";
    //         } else if (codDate.toDateString() === now.toDateString()) {
    //             dateStatus = "Departure";
    //             statusClass = "bg-danger";
    //         } else if (cidDate <= now && codDate >= now) {
    //             dateStatus = "Occupied";
    //             statusClass = "bg-info";
    //         } else if (cidDate > now) {
    //             dateStatus = "Pending";
    //             statusClass = "bg-warning text-white";
    //         } else if (codDate < now) {
    //             dateStatus = "Completed";
    //             statusClass = "bg-primary";
    //         }

    //         let siteLock = item.sitelock;
    //         let sitelockClass;  
            
    //         if (siteLock === '20') {
    //             siteLock = "Yes";
    //             sitelockClass = "bg-info text-white";
    //         } else {
    //             siteLock = "No";
    //             sitelockClass = "bg-danger text-white";
    //         }
            

    //         let balances = parseFloat(item.total) - parseFloat(item.payment);
    //         let balanceClass;
    //         if (balances <= 0) {
    //             balance = "Paid";
    //             balanceClass = "bg-success text-white";
    //         } else {
    //             balance = '$' + parseFloat(balances).toFixed(2); 
    //             balanceClass = "bg-danger text-white";
    //         }

    //         let checked_in_date; 
    //         let checked_out_date;
    //         if (item.checkedin === null) {
    //             checked_in_date = `<input type="checkbox" name="cartid" value="${item.cartid}" class="checked_in_date">`;
    //         } else {
    //             checked_in_date = item.checkedin;
    //         }
    
    //         if (item.checkedout === null) {
    //             checked_out_date = `<input type="checkbox" name="cartid" value="${item.cartid}" class="checked_out_date">`;
    //         } else {
    //             checked_out_date = item.checkedout;
    //         }
    
            

    //         tableBody.append(`
            
    //             <tr
    //                 data-bs-toggle="tooltip" 
    //                 data-bs-placement="top" 
    //                 title="Arrival: ${cid}\nDeparture: ${cod}\nSite: ${item.siteid} - ${item.siteclass}\nAddress: ${item.address} \nPhone: ${item.phone}\nCust#: ${item.customernumber}"
    //                 class="reservation-row" data-status="${dateStatus}">
    //                 <td class="text-center">
    //                     ${checked_in_date}
    //                 </td>
    //                 <td  class="text-center"> 
    //                     ${checked_out_date}
    //                 </td>
    //                 <td>${item.fname} ${item.lname}</td>
    //                 <td>${item.siteid}</td>
    //                 <td>${item.siteclass}</td>
    //                 <td> 
    //                     <span class="${ sitelockClass } badge rounded-pill p-2" style="font-size: 12px;">
    //                             ${  siteLock }
    //                         </span>
                    
    //                 </td>
    //                 <td>
    //                    <span class="${balanceClass} badge rounded-pill p-2" style="font-size: 12px;">
    //                         ${balance}
    //                     </span>
    //                 </td>
    //                 <td>
    //                     <span class="${statusClass} badge rounded-pill p-2" style="font-size: 12px;">
    //                         ${dateStatus}
    //                     </span>
    //                 </td>
    //                 <td>
    //                     <div class="btn btn-info actionsbtn" type="button" id="actionsbtn"  data-bs-toggle="modal" data-bs-target="#actionsModal" data-id="${item.cartid}">
    //                         <i class="fa-solid fa-eye"></i>
    //                     </div>
    //                 </td>
    //             </tr>
    //         `);
    //     });
    // }

                        // id="actionsbtn"  data-bs-toggle="modal" data-bs-target="#actionsModal"  data-id="${item.cartid}"


    function fetchReservations() {
    $.ajax({
        url: "reservepeople",
        type: "GET",
        success: function (data) {

            let data_reservations = data.allReservations || [];
            let data_payments = data.payments || [];
            let data_customers = data.customers || [];
            if (!Array.isArray(data_reservations) || data_reservations.length === 0) {
                console.error("No reservations found.");
                return;
            }

            console.log("CID Values:", data_reservations.map(item => item.cid));

            let minDate = null;
            let maxDate = null;
            let tasks = [];
            let tableRows = [];

            data_reservations.forEach((item, index) => {
                if (!item.cid || !item.cod) {
                    console.warn("Skipping reservation due to missing CID or COD:", item);
                    return;
                }

                let startDate = new Date(item.cid);
                let endDate = new Date(item.cod);

                if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                    console.warn("Skipping reservation due to invalid dates:", { cid: item.cid, cod: item.cod });
                    return;
                }

                if (endDate <= startDate) {
                    console.warn("End date is before start date. Swapping dates for reservation:", item.id);
                    [startDate, endDate] = [endDate, startDate];
                }

                if (!minDate || startDate < minDate) minDate = startDate;
                if (!maxDate || endDate > maxDate) maxDate = endDate;

                tasks.push({
                    id: `Reservation-${item.id || "Unknown"}`,
                    name: `${item.fname} ${item.lname} - ${item.siteclass || "Unknown"} - ${item.siteid || "Unknown"}`,
                    start: startDate.toISOString().split("T")[0],
                    end: endDate.toISOString().split("T")[0],
                    progress: Math.floor(Math.random() * 100), 
                    dependencies: "",
                    custom_class: index % 2 === 0 ? "gantt-bar-even" : "gantt-bar-odd"
                });

                // Find payment for the current reservation
                let paymentRecord = data_payments.find(payment => payment.cartid === item.cartid);
                let paymentBalance = paymentRecord ? parseFloat(paymentRecord.payment) || 0 : 0;
                
                let balances = parseFloat(item.total) - paymentBalance;
                let balance;
                let balanceClass;

                if (!isNaN(balances) && balances <= 0) {  
                    balance = "Paid";
                    balanceClass = "bg-success text-white";
                } else if (!isNaN(balances)) {
                    balance = '$' + balances.toFixed(2); 
                    balanceClass = "bg-danger text-white";
                } else {
                    balance = "Invalid Amount"; 
                    balanceClass = "bg-warning text-dark";
                }

                let customerRecord = data_customers.find(customer => String(customer.id) === String(item.customernumber)) || { email: "N/A", phone: "N/A" };
                if (!Array.isArray(data_customers) || data_customers.length === 0) {
                    console.error("data_customers is empty or not an array.");
                }
                               
                    let arrivalDate = item.cid ? new Date(item.cid).toLocaleDateString("en-US", { year: 'numeric', month: 'short', day: 'numeric' }) : "N/A";
                let departureDate = item.cod ? new Date(item.cod).toLocaleDateString("en-US", { year: 'numeric', month: 'short', day: 'numeric' }) : "N/A";


                let checked_in_date; 
                let checked_out_date;
                if (item.checkedin === null) {
                    checked_in_date = `<input type="checkbox" name="cartid" value="${item.cartid}" class="checked_in_date">`;
                } else {
                    checked_in_date = item.checkedin;
                }
        
                if (item.checkedout === null) {
                    checked_out_date = `<input type="checkbox" name="cartid" value="${item.cartid}" class="checked_out_date">`;
                } else {
                    checked_out_date = item.checkedout;
                }

                tableRows.push(`
                    <tr class="collapse-btn" onclick="toggleCollapse('${item.cartid}')" style="cursor: pointer;">
                        <td>${item.siteid || "Unknown"}</td>
                        <td>${item.fname} ${item.lname}</td>
                        <td>${startDate.toISOString().split("T")[0]} - ${endDate.toISOString().split("T")[0]}</td>

                    </tr>
                    <tr id="collapseReservationDetails${item.cartid}" class="collapse">
                        <td colspan="3">
                            <div class="card card-body">
                                <h5 class="mb-3">Reservation Details</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Customer:</strong> ${item.fname} ${item.lname}</p>
                                        <p><strong>Arrival:</strong> ${arrivalDate || "N/A"}</p>
                                        <p><strong>Departure:</strong> ${departureDate || "N/A"}</p>
                                        <p><strong>Site ID:</strong> ${item.siteid || "N/A"}</p>
                                        <p><strong>Rig Type:</strong> ${item.rigtype || "N/A"}</p>
                                        <p><strong>Rig Length:</strong> ${item.riglength || "N/A"}</p>
                                        <p><strong>Class:</strong> ${item.siteclass || "N/A"}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Email:</strong> ${customerRecord.email || "N/A"}</p>
                                        <p><strong>Phone:</strong> ${customerRecord.phone || "N/A"}</p>
                                        <p><strong>Length of Stay:</strong> ${item.nights || "N/A"} nights</p>
                                        <p><strong>Confirmation Number:</strong> ${item.cartid || "N/A"}</p>
                                        <p><strong>Comments:</strong> ${item.comments || "N/A"}</p>
                                        <p><strong>Total:</strong> $${parseFloat(item.total).toFixed(2) || "N/A"}</p>
                                        <p><strong>Balance:</strong> 
                                            <span class="badge ${balanceClass} rounded-pill">${balance}</span>
                                        </p>
                                    </div>
                                </div>
                
                                <hr>
                
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Checked In:</strong> 
                                            ${item.checkedin ? item.checkedin : `<input type="checkbox" name="cartid" value="${item.cartid}" class="checked_in_date" onclick="handleCheckIn(this, '${item.cartid}'); event.stopPropagation();">`}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Checked Out:</strong> 
                                            ${item.checkedout ? item.checkedout : `<input type="checkbox" name="cartid" value="${item.cartid}" class="checked_out_date" onclick="handleCheckOut(this, '${item.cartid}'); event.stopPropagation();">`}
                                        </p>
                                    </div>
                                </div>
                
                                <hr>
                
                                <p><strong>Source:</strong> ${item.source || "Walk-In"}</p>
                
                                <hr> 
                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-2 action-card" style="cursor: pointer; height: 150px;" data-id="${item.cartid}" id="action1">
                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                <h6 class="card-title text-center">
                                                    <i class="fa-solid fa-calendar"></i> Reschedule /
                                                    <i class="fa-solid fa-location-arrow"></i> Relocate
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-2 action-card" style="cursor: pointer; height: 150px;" id="action3" data-id="${item.cartid}">
                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                <h6 class="card-title text-center">
                                                    <i class="fa-solid fa-hand-holding-dollar"></i> Payment
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                `);
                
                
            });

            if (tasks.length === 0) {
                console.error("No valid tasks for Gantt chart.");
                return;
            }

            if (!minDate || !maxDate) {
                console.error("Could not determine valid date range for Gantt chart.");
                return;
            }

            let minDateString = minDate.toISOString().split("T")[0];
            let maxDateString = maxDate.toISOString().split("T")[0];

            console.log("Gantt Start Date:", minDateString);
            console.log("Gantt End Date:", maxDateString);

            document.getElementById("ganttTable").innerHTML = `
                <table class="gantt-table">
                    <thead>
                        <tr>
                            <th>Site ID</th>
                            <th>Customer</th>
                            <th>Dates</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows.join("")}
                    </tbody>
                </table>
            `;

            var gantt = new Gantt("#ganttReservations", tasks, {
                view_mode: "Day",
                language: "en",
                container_height: "auto",
                column_width: 50,
                bar_height: 35,
                bar_corner_radius: 8,
                arrow_curve: 5,
                upper_header_height: 45,
                lower_header_height: 30,
                padding: 15,
                lines: "both",
                snap_at: "1d",
                infinite_padding: false,
                move_dependencies: false,
                readonly: true,
                today_button: true,
                scroll_to: minDateString,
                view_mode_select: true,
                popup: function (task) {
                    return `
                        <div class="gantt-tooltip">
                            <strong>${task.name}</strong><br>
                            <small>Start: ${task.start}</small><br>
                            <small>End: ${task.end}</small><br>
                            <small>Progress: ${task.progress}%</small>
                        </div>`;
                }
            });

            setTimeout(() => {
                let daysBetween = (maxDate - minDate) / (1000 * 60 * 60 * 24);
                let newWidth = Math.max(1200, daysBetween * 50) + "px"; 
                document.querySelector("#ganttReservations svg").style.minWidth = newWidth;
            }, 500);

            console.log("Gantt Chart Rendered Successfully:", gantt);
        },
        error: function (error) {
            console.log("Error:", error);
        }
    });
}

    
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

    

   
    
    
    
    
    

    // function displayReservations(data){
    //     let data = data.allReservations;
    //     console.log(data); die();
    //     var tasks = [{
    //         id: 'Reservations-${}'
    //     }]
    // }
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

    $("#limitSelector").change(function () {
        fetchReservations(1, $(this).val());
    });

    $("#limitSelectorNotReserve").change(function () {
        fetchNotReserve(1, $(this).val());
    });

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

    fetchReservations();
    fetchNotReserve();


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
