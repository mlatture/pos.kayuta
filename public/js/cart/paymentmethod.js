$(document).ready(function () {
    $(".paymentMethod").on("click", function () {
        let label = $("#orderLabel");
        let input = $("#orderAmountInput");
        if ($(this).val() === "GiftCard") {
            label.text("Enter Gift Card Number: ");
            input.attr("placeholder", "Enter Gift Card Number");
        } else if ($(this).val() === "CreditCard") {
            label.text("Enter Card Number: ");
            input.attr("placeholder", "Enter Card Number");
        } else {
            label.text("Enter Order Amount: ");
            input.attr("placeholder", "Enter amount");
        }

        $(".btn-payment").removeClass("active");
        $(this).closest("label").addClass("active");
    });

    $("#cardExpiry").on('input', function(e) {
        let input = e.target.value;
    
      
        input = input.replace(/[^\d\/]/g, '');
    
        if (input.length > 2 && input[2] !== '/') {
            input = input.slice(0, 2) + '/' + input.slice(2);
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
            proceedWithOrder(customer_id, amount, change);
        } else if(paymentMethod === "GiftCard"){
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
                                proceedWithOrder(customer_id, totalAmount, 0);
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
        }else if(paymentMethod === "CreditCard"){
            let expiry = $("#cardExpiry").val();
            let formattedExp = expiry.replace("/", ""); 

            let cardDetails = {
                ccnum: $("#orderAmountInput").val(),
                exp: formattedExp,
              
                amount: totalAmount,
            }
            $.ajax({
                url: processCreditCard,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: cardDetails,
                success: function (response) {
                    if (response.message === "Payment Approved") {
                        Swal.fire({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                        });
                        proceedWithOrder(customer_id, totalAmount, 0);
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
                        text: response.message || "Failed to process the request.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                }
            });
            
        }
    });

    function proceedWithOrder(customer_id, order_amount, change) {
        Swal.fire({
            title:
                change > 0
                    ? "Change is: $" +
                      change.toFixed(2) +
                      ". Do you want to proceed?"
                    : "Card successfully processed!",
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
                    document.getElementById("offcanvasOrder")
                );
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
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
   
});
