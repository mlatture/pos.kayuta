$("#saveCustomer").click(function () {
    var formData = new FormData($("#customerForm")[0]);

    $.ajax({
        url: "postcustomer",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        cache: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            toastr.options.timeOut = 1000;
            toastr.options.onHidden = function () {
                window.location.reload();
            };
            $("#saveCustomer").prop("disabled", true);
            toastr.success("Customer added successfully");
        },
        error: function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function (key, value) {
                    toastr.error(value[0]);
                });
            }
        },
    });
});

$("#submitReservations").click(function () {
    var formDataReservation = new FormData($("#dateRangeForm")[0]);

    $.ajax({
        url: "postinfo",
        type: "POST",
        data: formDataReservation,
        contentType: false,
        processData: false,
        cache: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            toastr.success("Information added successfully");
            $("#dateRangeModal").modal("hide");
            $("#dateRangeForm")[0].reset();
            $("#openDatePicker").datepicker("setDate", null);

            $(".secondpage-modal").hide();
            $(".thirdpage-modal").hide();
            $("#backInfo").hide();
            $("#submitReservations").hide();
            $(".firstpage-modal").show();
            $("#nextInfo").show();
            $("#closeModal").show();
            setTimeout(function () {
                window.location.href = "reservations/payment/" + response.id;
            }, 1000);
        },
        error: function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function (key, value) {
                    toastr.error(value[0]);
                });
            }
        },
    });
});


$("#addToCart").click(function() {
    $.ajax({
        url: deleteAddToCart,
        type: 'DELETE',
        cache: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {

            toastr.info("Add To Cart");
            setTimeout(function () {
                window.location.href = "/admin/reservations";
            }, 1000);
        },
        error: function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function (key, value) {
                    toastr.error(value[0]);
                });
            } else {
                toastr.error('An error occurred while deleting the cart.');
            }
        },
    });
})
function sendPaymentRequest() {
    var formDataPayment = new FormData($("#paymentchoices")[0]);
    var reservationId = $('input[name="id"]').val();
    var paymentType = $("#paymentType").val();
    var amount = $("#xAmount").val();

    if (paymentType === "Terminal") {
        showLoader();
    }

    if (paymentType === "Terminal") {
        console.log("Test");
        $.ajax({
            url:
                "/admin/reservations/payment/" +
                reservationId +
                "/postTerminalPayment",
            type: "POST",
            data: formDataPayment,
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                hideLoader();
                if (response.success) {
                    console.log(response.success);
                    toastr.options.timeOut = 3000;
                    toastr.success("Payment added successfully");
                    setTimeout(function () {
                        window.location.href = "/admin/reservations";
                    }, 1000);
                } else {
                    console.log(response);
                    toastr.error(response.message || "Payment failed");
                }
            },
            error: function (xhr) {
                hideLoader();
                toastr.error("Error initiating terminal transaction");
            },
        });
    } else {
        $.ajax({
            url:
                "/admin/reservations/payment/" + reservationId + "/postpayment",
            type: "POST",
            data: formDataPayment,
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                hideLoader();
                if (response.success) {
                    toastr.options.timeOut = 3000;
                    toastr.success("Payment added successfully");
                    setTimeout(function () {
                        window.location.href = "/admin/reservations";
                    }, 1000);
                } else {
                    toastr.error(response.message || "Payment failed");
                }
            },
            error: function (xhr) {
                hideLoader();
                handleError(xhr);
            },
        });
    }
}

function sendPaymentBalanceRequest() {
    var formDataPayment = new FormData($("#paymentchoices")[0]);
    var cartId = $('input[name="cartid"]').val();
    var amount = $("#xAmount").val();
    var paymentType = $("#paymentType").val();

    if (paymentType === "Terminal") {
        showLoader();
    }

    if (paymentType === "Terminal") {
        $.ajax({
            url: "/admin/reservations/invoice/" + cartId + "/payBalanceCredit",

            type: "POST",
            data: formDataPayment,
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                hideLoader();
                if (response.success) {
                    toastr.options.timeOut = 3000;
                    toastr.success("Payment added successfully");
                    setTimeout(function () {
                        window.location.href = "/admin/reservations";
                    }, 1000);
                } else {
                    console.log(response);
                    toastr.error(response.message || "Terminal payment failed");
                }
            },
            error: function (xhr) {
                hideLoader();
                toastr.error("Error initiating terminal transaction");
            },
        });
    } else {
        $.ajax({
            url: "/admin/reservations/invoice/" + cartId + "/paybalance",
            type: "POST",
            data: formDataPayment,
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            success: function (response) {
                toastr.options.timeOut = 3000;
                toastr.success("Payment added successfully");
                setTimeout(function () {
                    window.location.href = "/admin/reservations";
                }, 1000);
            },
            error: function (xhr) {
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            toastr.error(value[0]);
                        });
                    } else if (xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error("An unexpected error occurred.");
                    }
                } else {
                    toastr.error("An unexpected error occurred.");
                }
            },
        });
    }
}
$("#payBtn").click(sendPaymentRequest);
$("#payBalance").click(sendPaymentBalanceRequest);

function showLoader() {
    $("#loader").show();
}

function hideLoader() {
    $("#loader").hide();
}

function handleError(xhr) {
    if (xhr.responseJSON) {
        if (xhr.responseJSON.errors) {
            $.each(xhr.responseJSON.errors, function (key, value) {
                toastr.error(value[0]);
            });
        } else if (xhr.responseJSON.message) {
            toastr.error(xhr.responseJSON.message);
        } else {
            toastr.error("An unexpected error occurred.");
        }
    } else {
        toastr.error("An unexpected error occurred.");
    }
}
