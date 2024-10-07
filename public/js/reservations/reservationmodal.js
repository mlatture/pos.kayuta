$(document).ready(function () {
    var currentPage = 1;
    $(".secondpage-modal").hide();
    $(".thirdpage-modal").hide();
    $("#backInfo").hide();
    $("#submitReservations").hide();
    $("#nextInfo").on("click", function () {
        var fromDate = $("#fromDate").val();
        var toDate = $("#toDate").val();
        var fname = $("#fname").val();
        var lname = $("#lname").val();
        var email = $("#email").val();
        var siteclass = $("#siteclass").val();
        var riglength = $("#riglength").val();
        var hookup = $("#hookup").val();

        sessionStorage.setItem("fromDate", fromDate);
        sessionStorage.setItem("toDate", toDate);
        sessionStorage.setItem("fname", fname);
        sessionStorage.setItem("lname", lname);
        sessionStorage.setItem("email", email);
        sessionStorage.setItem("siteclass", siteclass);
        sessionStorage.setItem("riglength", riglength);
        sessionStorage.setItem("hookup", hookup);

        $(".firstpage-modal").fadeOut(400, function () {
            $(".secondpage-modal").fadeIn(400, function () {
                $("#backInfo").show();
                // $('.thirdpage-modal').hide();
                $("#nextInfo").hide();
                $("#submitReservations").show();
            });
        });
    });

    $("#backInfo").on("click", function () {
        $(".secondpage-modal").fadeOut(400, function () {
            $("#backInfo").hide();
            $(".firstpage-modal").fadeIn(400, function () {
                $("#submitReservations").hide();
                $("#nextInfo").show();
                $("#closeModal").show();
            });
        });
    });

    function loadSites() {
        var selectedSiteClass = $("#siteclass option:selected").data(
            "siteclass"
        );
        console.log(selectedSiteClass);
        $.ajax({
            type: "GET",
            url: "getsite",
            dataType: "json",
            cache: false,
            data: {
                siteclass: selectedSiteClass,
            },
            success: function (data) {
                $("#siteSelector").empty();
                $("#siteSelector").append(
                    '<option value="" disabled selected>Select a Site</option>'
                );
                $.each(data, function (index, item) {
                    if (
                        $('#siteSelector option[value="' + item.id + '"]')
                            .length == 0
                    ) {
                        $("#siteSelector").append(
                            `<option value="${item.siteid}">${item.siteid} - ${item.sitename}</option>`
                        );
                    }
                });
            },
        });
    }

    function number_of_guests() {
        var selectedSiteClass = $("#siteclass option:selected").data(
            "siteclass"
        );

        var maxGuests = 0;
        if (selectedSiteClass === "RV Sites") {
            maxGuests = 6;
        } else if (selectedSiteClass === "Cabin") {
            maxGuests = 5;
        } else if (selectedSiteClass === "Tiny Homes") {
            maxGuests = 2;
        } else {
            $("#number_of_guests")
                .empty()
                .append(
                    '<option value="" disabled selected>No Maximum Guests </option>'
                )
                .prop("disabled", true);
            return;
        }

        var guestOptions = "";
        for (var i = 1; i <= maxGuests; i++) {
            guestOptions += `<option value="${i}">${i}</option>`;
        }

        var $guestDropdown = $("#number_of_guests");

        $guestDropdown.empty().append(guestOptions).prop("disabled", false);
    }

    $("#siteclass").on("change", function () {
        loadSites();
        number_of_guests();
    });

    $("#customerSelector").on("select2:select", function (e) {
        var data = e.params.data;

        $("#lname").val(data.lname);
        $("#fname").val(data.fname);
        $("#custnum").val(data.customernumber);
        $("#email").val(data.email);
    });

    function loadCustomers() {
        $.ajax({
            type: "GET",
            url: "getcustomers",
            dataType: "json",
            cache: false,
            success: function (data) {
                $("#customerSelector").empty();
                $("#customerSelector").append(
                    '<option value="" disabled selected>Select a Customer</option>'
                );

                $.each(data, function (index, item) {
                    $("#customerSelector").append(
                        `<option value="${item.id}" data-fname="${item.first_name}" data-cust="${item.id}" data-lname="${item.last_name}" data-email="${item.email}">${item.first_name} ${item.last_name}</option>`
                    );
                });
            },
        });
    }

    $("#customerSelector").change(function () {
        var selectedOption = $(this).find("option:selected");

        var fname = selectedOption.data("fname");
        var lname = selectedOption.data("lname");
        var email = selectedOption.data("email");
        var custnum = selectedOption.data("cust");

        $("#fname").val(fname);
        $("#lname").val(lname);
        $("#email").val(email);
        $("#custnum").val(custnum);
    });

    function loadSiteClasses() {
        $.ajax({
            type: "GET",
            url: "getsiteclasses",
            dataType: "json",
            cache: false,
            success: function (data) {
                $("#siteclass").empty();

                $("#siteclass").append(
                    '<option value="" disabled selected>Select a Site Class</option>'
                );

                $.each(data, function (index, item) {
                    if (
                        $('#siteclass option[value="' + item.id + '"]')
                            .length == 0
                    ) {
                        $("#siteclass").append(
                            `<option value="${item.id}" data-siteclass='${item.siteclass}'>${item.siteclass}</option>`
                        );
                    }
                });
            },
        });
    }

    $("#siteclass").on("change", function () {
        var selectedOption = $(this).find("option:selected");
        var siteclass = selectedOption.data("siteclass");
        $("#siteclasses").val(siteclass);
    });

    function loadSiteHookups() {
        $.ajax({
            type: "GET",
            url: "getsitehookups",
            dataType: "json",
            cache: false,
            success: function (data) {
                $("#hookup").empty();
                $("#hookup").append(
                    '<option value="" disabled selected>Select a Site Hookup</option>'
                );
                $.each(data, function (index, item) {
                    if (
                        $('#hookup option[value="' + item.id + '"]').length == 0
                    ) {
                        $("#hookup").append(
                            `<option value="${item.id}" data-sitehookup='${item.sitehookup}'>${item.sitehookup}</option>`
                        );
                    }
                });
            },
        });
    }

    $("#hookup").on("change", function () {
        var selectedOption = $(this).find("option:selected");
        var siteclass = selectedOption.data("sitehookup");
        $("#hookups").val(siteclass);
    });

    $("#siteclass").on("change", function () {
        var selectedValue = $(this).val();
        if ($(this).val() != "1") {
            $("#forRv").hide();
        } else {
            $("#forRv").show();
        }
    });

    $("#siteclass").trigger("change");

    loadCustomers();
    loadSiteClasses();
    loadSiteHookups();
    loadSites();
});

$(document).on("click", ".actionsbtn", function () {
    const id = $(this).data("id");

    $("#actionsModal .modal-body").empty();

    $("#actionsModal .modal-body").append(`
        <div class="row justify-content-center">
            <div class="col-8">
                <div class="card mb-2 action-card" style="cursor: pointer; height: 150px;" data-id="${id}" id="action1">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <h6 class="card-title text-center">
                            <i class="fa-solid fa-calendar"></i>
                            Reschedule /
                              <i class="fa-solid fa-location-arrow"></i>
                            Relocate
                        </h6>
                    </div>
                </div>
            </div>
        
            <div class="col-4">
                <div class="card mb-2 action-card" style="cursor: pointer; height: 150px;" id="action3" data-id="${id}">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <h6 class="card-title text-center">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                            Payment
                         </h6>
                    </div>
                </div>
            </div>
        </div>


    `);
});

$(document).on("click", "#action1", function () {
    const id = $(this).data("id");
    url = "reservations/relocate/" + id;
    window.location = url;
});
