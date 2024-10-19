$(document).ready(function () {
    $(".paymentMethod").on("click", function () {
        let label = $("#orderLabel");
        let input = $("#orderAmountInput");
        if ($(this).val() === "GiftCard") {
            $("#gift-card-section").removeAttr("hidden");
            label.show().text("Payment Amount: ");
            $("#giftcardno").removeAttr("readonly").focus();
            input.attr("placeholder", "Payment Amount").removeAttr("readonly");
            $("#expire").attr("hidden", true);
        } else if ($(this).val() === "CreditCard") {
            $("#gift-card-section").attr("hidden", true);
            label.show().text("Payment Amount: ");
            input.attr("placeholder", "Payment Amount").removeAttr("readonly");
        } else {
            label.show().text("Payment Amount: ");
            input.attr("placeholder", "Payment Amount").removeAttr("readonly");
            $("#gift-card-section").attr("hidden", true);
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

    var offcanvasOrder = new bootstrap.Offcanvas(
        document.getElementById("offcanvasOrder"),
        {
            backdrop: false,
            keyboard: false,
        }
    );
    $("#submitOrderButton").on("click", function () {
        processPayment();
    });
    
    $("#updateOrderButton").on("click", function (e) {
        e.preventDefault();
        processPayment(true); 
    });
    
    $("#email_invoice").on("input", function () {
        let cust_email = $(this).val();
        $("#cust_email").val(cust_email);
    })
    function processPayment(isPartial = false) {
        let totalAmount = parseFloat($("#total-amount").val().replace(/,/g, ""));
        let amount = parseFloat($("#orderAmountInput").val().replace(/,/g, ""));
        let customer_id = $("#customer_id").val();
        let paymentMethod = $('input[name="payment_method"]:checked').val();
        let customer_email = $("#cust_email").val();
       
        let orderId = $("#order_id").val();
    
        let change = 0;
        if (paymentMethod === "Cash") {
            if (amount > totalAmount) {
                change = amount - totalAmount;
            }
            handlePayment(customer_id, amount, change, paymentMethod, 0, 0, totalAmount, isPartial, orderId, customer_email);
        } else if (paymentMethod === "GiftCard") {
            let giftCardNumber = $("#giftcardno").val();
            processGiftCardPayment(giftCardNumber, customer_id, amount, totalAmount, isPartial, orderId, customer_email);
        } else if (paymentMethod === "CreditCard") {
            processCreditCardPayment(customer_id, amount, totalAmount, isPartial, orderId, customer_email);
        }
    }
    
    function handlePayment(customer_id, amount, change, paymentMethod, number, x_ref_num, totalAmount, isPartial, orderId, customer_email) {

        let remainingBalance = parseFloat($("#remainingBalance").text()) || totalAmount;
        let firstRemainingBalance = remainingBalance - amount;
        $("#remainingBalance").text(firstRemainingBalance.toFixed(2));
    
        Swal.fire({
            title: change >= 0 ? `Change is: $${change.toFixed(2)}. Do you want to proceed?` : `Partial payment made! Remaining balance is: $${firstRemainingBalance.toFixed(2)}`,
            showCancelButton: true,
            confirmButtonText: "Save",
            cancelButtonText: `Don't save`,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    $.ajax({
                        url: isPartial ? cartOrderUpdateUrl : cartOrderStoreUrl, 
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: {
                            order_id: orderId, 
                            amount: amount,
                            customer_id: customer_id ?? 0,
                            payment_method: paymentMethod,
                            acc_number: number,
                            x_ref_num: x_ref_num,
                        },
                        success: function (response) {
                            resolve(response);
                                      
                            if (!isPartial) { 
                               
                                orderId = response.order_id;  
                                $("#order_id").val(orderId); 
                                $("#orderAmountInput").val('');
                                $("#giftcardno").val('');
                                $('input[name="payment_method"]:checked').prop('checked', false);
                            } else {
                                $("#orderAmountInput").val('');
                                $("#giftcardno").val('');
                                $('input[name="payment_method"]:checked').prop('checked', false);
                                orderId = response.OrderItem.order_id;  
                                $("#order_id").val(orderId);  
                            }
                    
                          
                            console.log(response) 
                        },
                        error: function (reject) {
                            resolve(reject);
                        },
                    });
                });
            },
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                let response = result.value;
           
                finalizeOrder(amount, totalAmount, response, firstRemainingBalance, customer_email, orderId);
    
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
    
    function processGiftCardPayment(giftCardNumber, customer_id, amount, totalAmount, isPartial, orderId, customer_email) {
        $.ajax({
            url: processGiftCard,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: { gift_card_number: giftCardNumber },
            success: function (response) {
                let giftCardAmount = response.amount;
                if (giftCardAmount >= amount) {
                    let remainingBalance = giftCardAmount - amount;
                    $.ajax({
                        url: updateGiftCardBalance,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: { gift_card_number: giftCardNumber, remaining_balance: remainingBalance },
                        success: function () {
                            handlePayment(customer_id, amount, 0, "GiftCard", 0, 0, totalAmount, isPartial, orderId);
                        },
                        error: function () {
                            Swal.fire("Error", "Failed to update the gift card balance.", "error");
                        },
                    });
                } else {
                    Swal.fire("Gift card Balance is insufficient", `The gift card balance is $${giftCardAmount.toFixed(2)}.`, "warning");
                }
            },
            error: function () {
                Swal.fire("Error", "Failed to process the gift card.", "error");
            },
        });
    }
    
    function processCreditCardPayment(customer_id, amount, totalAmount, isPartial, orderId, customer_email) {
        $.ajax({
            url: processTerminal,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: { amount: amount },
            success: function (response) {
                if (response.message === "Payment Approved") {
                    // Swal.fire("Success", response.message, "success");
                    handlePayment(customer_id, amount, 0, "CreditCard", response.success.xMaskedCardNumber, response.success.xRefNum, totalAmount, isPartial, orderId);
                } else {
                    Swal.fire("Error", response.error || response.message, "error");
                }
            },
            error: function (xhr) {
                Swal.fire("Error", "Failed to process the request.", "error");
            },
        });
    }
    
    function finalizeOrder(order_amount, totalAmount, response, firstRemainingBalance, customer_email, orderId) {
       console.log(customer_email);
        var offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById("offcanvasOrder"), { backdrop: false });
        $('#offcanvasOrder').on('hide.bs.offcanvas', function (e) {
            e.preventDefault(); 
        });

        $("#submitOrderButton").hide();
        $("#updateOrderButton").attr("hidden", false);
    
        if (order_amount >= totalAmount || response.totalpayAmount.amount >= response.OrderItem.price) {
         
            offcanvas.hide();
            $("#selected-product tbody").empty();
            $("#card-summary").empty();
            $.toast({
                heading: response[0] || "Success",
                text: response[1] || "Order placed successfully!",
                position: "top-right",
                loaderBg: "#00c263",
                icon: "success",
                hideAfter: 3000,
                stack: 6,
            });

            sendInvoiceEmail(customer_email,orderId)
            // setTimeout(function () {
            //     clearInputFields(true);
            //     window.location.reload();
            // }, 3000);
        } else {
            $.toast({
                heading: response[0] || "Success",
                text: `Partial Payment only. Remaining balance: $${firstRemainingBalance.toFixed(2)}`,
                position: "top-right",
                loaderBg: "#00c263",
                icon: "success",
                hideAfter: 3000,
                stack: 6,
            });
            $("#submitOrderButton").hide();
            $("#updateOrderButton").attr("hidden", false);
        }
    }
    
    function clearInputFields(isFullPayment = false) {
        $("#orderAmountInput").val('');
        $("#giftcardno").val('');
        $('input[name="payment_method"]:checked').prop('checked', false);
    }

    function sendInvoiceEmail(customer_email, orderId)
    {
        $.ajax({
            url: sentInvoiceEmail,
            type: 'POST',
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                email: customer_email,
                order_id: orderId
            },
            sunccess: function (response) {
                console.log(response.message);
            },
            error: function (xhr) {
                console.log(xhr);
            }
        })
    }
  
   
});
