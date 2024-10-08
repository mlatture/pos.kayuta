$(document).ready(function () {
    $(".paymentMethod").on("click", function () {
        let label = $("#orderLabel");
        let input = $("#orderAmountInput");
        if ($(this).val() === "GiftCard") {
            label.text("Enter Gift Card Number: ");
            input.attr("placeholder", "Enter Gift Card Number");
            $("#expire").attr("hidden", true);
            input.attr("readonly", false);
        } else if ($(this).val() === "CreditCard") {
            label.hide();
            input.attr("placeholder", "Start Terminal Transaction");
            input.attr("readonly", true);
        } else {
            label.text("Enter Order Amount: ");
            input.attr("placeholder", "Enter amount");
            $("#expire").attr("hidden", true);
            input.attr("readonly", false);
        }

        $(".btn-payment").removeClass("active");
        $(this).closest("label").addClass("active");
    });

    $("#cardExpiry").on("input", function (e) {
        let input = e.target.value;

        input = input.replace(/[^\d\/]/g, "");

        if (input.length > 2 && input[2] !== "/") {
            input = input.slice(0, 2) + "/" + input.slice(2);
        }

        if (input.length > 5) {
            input = input.slice(0, 5);
        }

        e.target.value = input;
    });

    $("#submitOrderButton").on("click", function () {
        let totalAmount = parseFloat(
            $("#total-amount").val().replace(/,/g, "")
        );

        let amount = parseFloat($("#orderAmountInput").val().replace(/,/g, ""));

        let customer_id = $("#customer_id").val();
        let paymentMethod = $('input[name="payment_method"]:checked').val();

        let change = 0;

        if (paymentMethod === "Cash") {
            if (amount > totalAmount) {
                change = amount - totalAmount;
            }
            proceedWithOrder(
                customer_id,
                amount,
                change,
                paymentMethod,
                0,
                0,
                totalAmount
            );
        } else if (paymentMethod === "GiftCard") {
            let giftCardNumber = $("#orderAmountInput").val();

            $.ajax({
                url: processGiftCard,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: {
                    gift_card_number: giftCardNumber,
                },
                success: function (response) {
                    let giftCardAmount = response.amount;

                    if (giftCardAmount >= totalAmount) {
                        let remainingBalance = giftCardAmount - totalAmount;

                        $.ajax({
                            url: updateGiftCardBalance,
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": $(
                                    'meta[name="csrf-token"]'
                                ).attr("content"),
                            },
                            data: {
                                gift_card_number: giftCardNumber,
                                remaining_balance: remainingBalance,
                            },
                            success: function () {
                                proceedWithOrder(
                                    customer_id,
                                    amount,
                                    change,
                                    paymentMethod,
                                    0,
                                    0,
                                    totalAmount,
                                    giftCardNumber
                                );

                                // proceedWithOrder(customer_id, totalAmount, 0, paymentMethod, giftCardNumber);
                            },
                            error: function () {
                                Swal.fire({
                                    title: "Error",
                                    text: "Failed to update the gift card balance.",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                });
                            },
                        });
                    } else {
                        Swal.fire({
                            title: "Gift card Balance is insufficient",
                            text:
                                "The gift card balance is $" +
                                giftCardAmount.toFixed(2) +
                                ". It must cover the total amount.",
                            icon: "warning",
                            confirmButtonText: "OK",
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Error",
                        text: "Failed to process the gift card.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
            // }else if(paymentMethod === "CreditCard"){
            //     let expiry = $("#cardExpiry").val();
            //     let formattedExp = expiry.replace("/", "");
            //     let x_ref_num = $("#x_ref_num").val();
            //     let cardDetails = {
            //         ccnum: $("#orderAmountInput").val(),
            //         exp: formattedExp,
            //         amount: totalAmount,

            //     }

            //     let cardNum = $("#orderAmountInput").val();

            //     $.ajax({
            //         url: processCreditCard,
            //         type: 'POST',
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         data: cardDetails,
            //         success: function (response) {
            //             if (response.message === "Payment Approved") {
            //                 Swal.fire({
            //                     title: "Success",
            //                     text: response.message,
            //                     icon: "success",
            //                     confirmButtonText: "OK",
            //                 });

            //                 proceedWithOrder(customer_id, totalAmount, 0, paymentMethod, cardNum, response.transaction_data.xRefNum);
            //             } else if (response.message === "Payment Declined") {
            //                 Swal.fire({
            //                     title: "Error",
            //                     text: response.error || response.message,
            //                     icon: "error",
            //                     confirmButtonText: "OK",
            //                 });
            //             } else {
            //                 Swal.fire({
            //                     title: "Error",
            //                     text: "Unexpected response from server.",
            //                     icon: "error",
            //                     confirmButtonText: "OK",
            //                 });
            //             }
            //         },
            //         error: function (xhr, status, error) {
            //             let response;
            //             try {
            //                 response = JSON.parse(xhr.responseText);
            //             } catch (e) {
            //                 response = { message: "An unknown error occurred" };
            //             }

            //             Swal.fire({
            //                 title: "Error",
            //                 text: response.message || "Failed to process the request.",
            //                 icon: "error",
            //                 confirmButtonText: "OK",
            //             });
            //         }
            //     });
        } else if (paymentMethod === "CreditCard") {
            $.ajax({
                url: processTerminal,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: {
                    amount: totalAmount,
                },
                success: function (response) {
                    if (response.message === "Payment Approved") {
                        Swal.fire({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                        });

                        proceedWithOrder(
                            customer_id,
                            totalAmount,
                            0,
                            paymentMethod,
                            response.success.xMaskedCardNumber,
                            response.success.xRefNum
                        );
                    } else if (response.message === "Payment Declined") {
                        Swal.fire({
                            title: "Error",
                            text: response.error || response.message,
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Unexpected response from server.",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                        console.log(response.success);
                    }
                },
                error: function (xhr, status, error) {
                    let response;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (e) {
                        response = { message: "An unknown error occurred" };
                    }

                    Swal.fire({
                        title: "Error",
                        text:
                            response.message ||
                            "Failed to process the request.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
        }
    });

    var offcanvasOrder = new bootstrap.Offcanvas(document.getElementById('offcanvasOrder'), {
        backdrop: false, 
        keyboard: false   
    });
    

    function proceedWithOrder(
        customer_id,
        order_amount,
        change,
        paymentMethod,
        number,
        x_ref_num,
        totalAmount
    ) {
        Swal.fire({
            title:
                change >= 0
                    ? "Change is: $" +
                      change.toFixed(2) +
                      ". Do you want to proceed?"
                    : "Successfully processed!",
            showCancelButton: true,
            confirmButtonText: "Save",
            cancelButtonText: `Don't save`,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    $.ajax({
                        url: cartOrderStoreUrl,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        data: {
                            amount: order_amount,
                            customer_id: customer_id,
                            payment_method: paymentMethod,
                            acc_number: number,
                            x_ref_num: x_ref_num,
                        },
                        success: function (response) {
                         
                            resolve(response);

                        },
                        error: function (reject) {
                            resolve(reject);
                        },
                    });
                });
            },
        }).then((result) => {
          
            if (result.isConfirmed && result.value) {
                var offcanvas = bootstrap.Offcanvas.getInstance(
                    document.getElementById("offcanvasOrder"), {
                        backdrop: false,
                    }
                );

                if (order_amount >= totalAmount) {
                    offcanvas.hide();

                    $("#selected-product tbody").empty();
                    $("#card-summary").empty();

                    $.toast({
                        heading: result.value[0] || "Success",
                        text: result.value[1] || "Order placed successfully!",
                        position: "top-right",
                        loaderBg: "#00c263",
                        icon: "success",
                        hideAfter: 3000,
                        stack: 6,
                    });

                    setTimeout(function () {
                        window.location.reload();
                    }, 3000);
                } else {
                    $.toast({
                        heading: result.value[0] || "Success",
                        text:
                            result.value[1] ||
                            "Partial Payment only",
                        position: "top-right",
                        loaderBg: "#00c263",
                        icon: "success",
                        hideAfter: 3000,
                        stack: 6,
                    });

                    
                    let orderId = result.value.order_id;

                    $('#submitOrderButton').hide();
                    $('#updateOrderButton').attr('hidden', false);
                    $('#offcanvasOrder').on('hide.bs.offcanvas', function (e) {
                        e.preventDefault(); 
                    });

                    $('#updateOrderButton').on('click', function (e) {
                        e.preventDefault();

                        $.ajax({
                            url: cartOrderUpdateUrl,
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                    "content"
                                ),
                            },
                            data: {
                                order_id: orderId,
                                amount: order_amount,
                                payment_method: paymentMethod
                            },
                            success: function (response) {
                                console.log(response);
                                $.toast({
                                    heading: result.value[0] || "Success",
                                    text: result.value[1] || "Order updated successfully!",
                                    position: "top-right",
                                    loaderBg: "#00c263",
                                    icon: "success",
                                    hideAfter: 3000,
                                    stack: 6,
                                });

                                window.location.reload();
                            },

                        });
                
                    

                    });
                }


            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
});

