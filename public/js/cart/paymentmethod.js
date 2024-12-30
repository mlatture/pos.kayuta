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
    });
    function processPayment(isPartial = false) {
        let totalAmount = parseFloat(
            $("#total-amount").val().replace(/,/g, "")
        );
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
            handlePayment(
                customer_id,
                amount,
                change,
                paymentMethod,
                0,
                0,
                totalAmount,
                isPartial,
                orderId,
                customer_email
            );
        } else if (paymentMethod === "GiftCard") {
            let giftCardNumber = $("#giftcardno").val();
            processGiftCardPayment(giftCardNumber, customer_id, amount, totalAmount, isPartial, orderId, customer_email);

        } else if (paymentMethod === "CreditCard") {
            processCreditCardPayment(customer_id, amount, totalAmount, isPartial, orderId, customer_email);

        }
    }

    function handlePayment(
        customer_id,
        amount,
        change,
        paymentMethod,
        number,
        x_ref_num,
        totalAmount,
        isPartial,
        orderId,
        customer_email
    ) {
        let remainingBalance =
            parseFloat($("#remainingBalance").text()) || totalAmount;
        let firstRemainingBalance = remainingBalance - amount;
        $("#remainingBalance").text(firstRemainingBalance.toFixed(2));

        Swal.fire({
            title:
                change >= 0
                    ? `Change is: $${change.toFixed(
                          2
                      )}. Do you want to proceed?`
                    : `Partial payment made! Remaining balance is: $${firstRemainingBalance.toFixed(
                          2
                      )}`,
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
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
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
                                $("#orderAmountInput").val("");
                                $("#giftcardno").val("");
                                $('input[name="payment_method"]:checked').prop(
                                    "checked",
                                    false
                                );
                            } else {
                                $("#orderAmountInput").val("");
                                $("#giftcardno").val("");
                                $('input[name="payment_method"]:checked').prop(
                                    "checked",
                                    false
                                );
                                orderId = response.OrderItem.order_id;
                                $("#order_id").val(orderId);
                            }
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

                finalizeOrder(
                    amount,
                    totalAmount,
                    response,
                    firstRemainingBalance,
                    customer_email,
                    orderId
                );
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }

    function processGiftCardPayment(
        giftCardNumber,
        customer_id,
        amount,
        totalAmount,
        isPartial,
        orderId,
        customer_email
    ) {
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
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        data: {
                            gift_card_number: giftCardNumber,
                            remaining_balance: remainingBalance,
                        },
                        success: function () {
                            handlePayment(
                                customer_id,
                                amount,
                                0,
                                "GiftCard",
                                0,
                                0,
                                totalAmount,
                                isPartial,
                                orderId
                            );
                        },
                        error: function () {
                            Swal.fire(
                                "Error",
                                "Failed to update the gift card balance.",
                                "error"
                            );
                        },
                    });
                } else {
                    Swal.fire(
                        "Gift card Balance is insufficient",
                        `The gift card balance is $${giftCardAmount.toFixed(
                            2
                        )}.`,
                        "warning"
                    );
                }
            },
            error: function () {
                Swal.fire("Error", "Failed to process the gift card.", "error");
            },
        });
    }

    function processCreditCardPayment(
        customer_id,
        amount,
        totalAmount,
        isPartial,
        orderId,
        customer_email
    ) {
        $.ajax({
            url: 'https://localemv.com:8887',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded',
            data: $.param({
                xKey: encodeURIComponent(cardknoxApiKey),
                xCommand: 'cc:Sale',
                xAmount: encodeURIComponent(amount),
                xAllowDuplicate: encodeURIComponent('TRUE'),
            }),
            success: function (response) {
                let jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;
                console.log(jsonResponse);
                const xResult = jsonResponse.xResult;

                if(xResult === 'A') {
                    handlePayment(
                        customer_id,
                        jsonResponse.xAuthAmount,
                        0,
                        jsonResponse.xCardType,
                        jsonResponse.xMaskedCardNumber,
                        jsonResponse.RefNum,
                        totalAmount,
                        isPartial,
                        orderId,
                        customer_email
                    )
                }else if(jsonResponse.xError === 'NaN is not a valid integer'){
                    Swal.fire(
                        "Error",
                        `${amount} is not a valid integer`,
                        "error"
                    );
                }else{
                    Swal.fire(
                        "Canceled",
                        "The transaction was canceled.",
                        "warning"
                    );
                
                }
            },
            error: function (xhr, error) {
                console.error('Error', xhr, error);
            }
        })
        // $.ajax({
        //     url: processTerminal,
        //     type: "POST",
        //     headers: {
        //         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        //     },
        //     data: { amount: amount },
        //     success: function (response) {
        //         if (response.message === "Payment Approved") {
        //             handlePayment(
        //                 customer_id,
        //                 amount,
        //                 0,
        //                 "CreditCard",
        //                 response.success.xMaskedCardNumber,
        //                 response.success.xRefNum,
        //                 totalAmount,
        //                 isPartial,
        //                 orderId,
        //                 customer_email
        //             );

        //             console.log(orderId, 'Test')

                   
        //         } else {
        //             Swal.fire(
        //                 "Error",
        //                 response.error || response.message,
        //                 "error"
        //             );
        //         }
        //     },
        //     error: function (xhr) {
        //         Swal.fire("Error", "Failed to process the request.", "error");
        //     },
        // });
    }


    function finalizeOrder(
        order_amount,
        totalAmount,
        response,
        firstRemainingBalance,
        customer_email,
        orderId
    ) {
        var offcanvas = bootstrap.Offcanvas.getInstance(
            document.getElementById("offcanvasOrder"),
            { backdrop: false }
        );
        $("#offcanvasOrder").on("hide.bs.offcanvas", function (e) {
            e.preventDefault();
        });

        $("#submitOrderButton").hide();
        $("#updateOrderButton").attr("hidden", false);

        if (
            order_amount >= totalAmount ||
            response.totalpayAmount.amount >= response.OrderItem.price
        ) {
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

            $.ajax({
                url: 'cart/get-product-for-receipt',
                type: "GET",
                data: { order_id: orderId },
                success: function (orders_response) {
                    receiptPrint(
                        order_amount,
                        totalAmount,
                        orderId,
                        customer_email,
                        response,
                        orders_response,
                    );
                }
            });

            if(customer_email && customer_email.trim() !== ''){
                sendInvoiceEmail(customer_email, orderId);
            }
            setTimeout(function () {
                clearInputFields(true);

                window.location.reload();
            }, 3000);
        } else {
            $.toast({
                heading: response[0] || "Success",
                text: `Partial Payment only. Remaining balance: $${firstRemainingBalance.toFixed(
                    2
                )}`,
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

    function receiptPrint(
        order_amount,
        totalAmount,
        orderId,
        customer_email,
        response,
        orders_response
    ) {
        let receiptDetails = `
        <div style="font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ccc; margin-top: 20px;">
            <h2>Order Receipt</h2>
            <p><strong>Order ID:</strong> ${orderId}</p>
            <p><strong>Customer Email:</strong> ${customer_email}</p>
            <p><strong>Date:</strong> ${new Date().toLocaleString()}</p>
            <hr>
            <h3>Product Details:</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 8px;">Product</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Quantity</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Price</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Tax</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Discount</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Total</th>
                    </tr>
                </thead>
                <tbody>
        `;
    
        if (Array.isArray(  orders_response.products) &&   orders_response.products.length > 0) {
            orders_response.products.forEach((item) => {
                let totalPrice =
                    item.quantity * item.price + item.tax - item.discount;
                receiptDetails += `
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">${item.product_name}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">${item.quantity}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">$${parseFloat(item.price).toFixed(2)}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">$${parseFloat(item.tax).toFixed(2)}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">$${parseFloat(item.discount).toFixed(2)}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">$${totalPrice.toFixed(2)}</td>
                </tr>
                `;
            });
        } else {
            receiptDetails += `
            <tr>
                <td colspan="6" style="border: 1px solid #ddd; padding: 8px; text-align: center;">No items found.</td>
            </tr>
            `;
        }
    
        receiptDetails += `
                </tbody>
            </table>
            <hr>
            <p><strong>Total Amount:</strong> $${totalAmount.toFixed(2)}</p>
            <p><strong>Paid Amount:</strong> $${order_amount.toFixed(2)}</p>
            <p><strong>Change:</strong> $${Math.abs(totalAmount - order_amount).toFixed(2)}</p>
        </div>
        `;
    
        var printWindow = window.open('', '', 'height=600,width=800'); 
        printWindow.document.write('<html><head><title>Receipt</title></head><body>');
        printWindow.document.write(receiptDetails); 
        printWindow.document.write('</body></html>');
        printWindow.document.close(); 
        printWindow.print(); 
    }
    

    function clearInputFields(isFullPayment = false) {
        $("#orderAmountInput").val("");
        $("#giftcardno").val("");
        $('input[name="payment_method"]:checked').prop("checked", false);
    }

    function sendInvoiceEmail(customer_email, orderId) {
        if (!customer_email || !customer_email.includes("@")) {
            console.error("Invalid customer email:", customer_email);
            return;
        }
    
        $.ajax({
            url: sentInvoiceEmail,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                email: customer_email,
                order_id: orderId,
            },
            success: function (response) {
                console.log(response.message);
            },
            error: function (xhr) {
                Swal.fire(
                    "Error",
                    "Failed to send the invoice email. Please verify the email address.",
                    "error"
                );
                console.error(xhr);
            },
        });
    }
    
    $('#customer_id').on('change', function(){
        let customer_email = $(this).find('option:selected').data('email');
    
        console.log(customer_email);

        $('#cust_email').val(customer_email);
        $('#email_invoice').val(customer_email);
    });
});


