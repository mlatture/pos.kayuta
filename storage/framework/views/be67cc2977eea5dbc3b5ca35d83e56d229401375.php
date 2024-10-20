<div class="col-12 mb-3">
    <div class="btn-group btn-group-sm btn-block btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-outline-dark btn-payment active ">
            <input type="radio" name="payment_method" class="paymentMethod" id="paymentMethodCash" value="Cash"
                autocomplete="off" checked />
            <i class="bi bi-cash"></i> Cash
        </label>
        <label class="btn btn-outline-secondary btn-payment flex-fill">
            <input type="radio" name="payment_method" class="paymentMethod" id="paymentMethodGiftCard"
                value="GiftCard" autocomplete="off" />
            <i class="bi bi-credit-card-2-front"></i> Gift Card / RFID
        </label>
        <label class="btn btn-outline-secondary btn-payment flex-fill">
            <input type="radio" name="payment_method" class="paymentMethod" id="paymentMethodCreditCard"
                value="CreditCard" autocomplete="off" />
            <i class="bi bi-credit-card"></i> Credit Card
        </label>
    </div>

    <div class="row mt-4" id="gift-card-section" hidden>
        <label for="giftcardno">Gift Card / RFID Number:</label>
        <div class="input-group">
            <input type="text" id="giftcardno" class="form-control shadow-sm" placeholder="Enter Gift Card No.">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary shadow-sm" id="lookupGiftCardButton" type="button">
                    <i class="bi bi-search"></i> Lookup
                </button>
            </div>
        </div>
        <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <label class="mb-0">Balance: </label>
                <p class="form-control-plaintext text-primary fw-bold mb-0"> $<span id="giftCardBalanceText">0.00</span></p>
            </div>
        </div>
        
    </div>

    <div class="row mt-3">
        <div class="col" id="expire" hidden>
            <label for="" id="orderLabel">Enter Expiration Date:</label>
            <input type="text" id="cardExpiry" class="form-control" placeholder="MM/YY" maxlength="5">
        </div>
    </div>

   
   
    <!-- Process Button -->
    <div class="row mt-5">
        <div class="col">
            <button type="submit" class="btn btn-success btn-lg btn-block shadow-sm" id="submitOrderButton">
                <i class="bi bi-check-circle"></i> Process
            </button>
            <button type="submit" class="btn btn-success btn-lg btn-block shadow-sm" id="updateOrderButton" hidden>
                <i class="bi bi-arrow-repeat"></i> Process
            </button>
        </div>
    </div>
</div>

<script>
   

    $("#giftcardno").on("keypress", function(e) {
        if (e.which === 13) {
            lookupGiftCard();
        }
    });

    $("#lookupGiftCardButton").on("click", function () {
        lookupGiftCard();
    });

    function lookupGiftCard() {
        let giftCardNumber = $("#giftcardno").val();
        let totalAmount = parseFloat($("#total-amount").val().replace(/,/g, ""));
        
        if (!giftCardNumber) {
            Swal.fire("Error", "Please enter a gift card number", "error");
            return;
        }

        $.ajax({
            url: processGiftCard,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: { gift_card_number: giftCardNumber },
            success: function (response) {
                let giftCardBalance = response.amount;
                $("#giftCardBalanceText").text(giftCardBalance.toFixed(2));

                let amountToSpend = 0;
                if (giftCardBalance > totalAmount) {
                    amountToSpend = totalAmount;
                } else {
                    amountToSpend = giftCardBalance;
                }

                $("#orderAmountInput").val(amountToSpend.toFixed(2));
            },
            error: function () {
                Swal.fire("Error", "Failed to lookup the gift card.", "error");
            },
        });
    }
</script>
<?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views/cart/components/paymentmethod.blade.php ENDPATH**/ ?>