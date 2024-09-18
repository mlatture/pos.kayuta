<div class="col-12 mb-3">
    <div class="btn-group btn-group-sm btn-block btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-outline-dark btn-payment active ">
            <input type="radio" name="payment_method" class="paymentMethod" id="paymentMethodCash" value="Cash"
                autocomplete="off" checked />
            Cash
        </label>
        <label class="btn btn-outline-dark btn-payment ">
            <input type="radio" name="payment_method" class="paymentMethod" id="paymentMethodGiftCard" value="GiftCard"
                autocomplete="off" />
            Gift Card
        </label>
        <label class="btn btn-outline-dark btn-payment ">
            <input type="radio" name="payment_method" class="paymentMethod" id="paymentMethodCreditCard"
                value="CreditCard" autocomplete="off" />
            Credit Card
        </label>
    </div>

    <div class="row mt-3">
        <div class="col">
            <label for="orderAmountInput" id="orderLabel">Enter Order Amount:</label>
            <input type="text" id="orderAmountInput" class="form-control" placeholder="Enter amount">
        </div>

    </div>

    <div class="row mt-3">
        <div class="col" id="expire" hidden>
            <label for="" id="orderLabel">Enter Expiration Date:</label>
            <input type="text" id="cardExpiry" class="form-control" placeholder="MM/YY" maxlength="5">
        </div>

    </div>
    
   

    <div class="row mt-3">
        <div class="col">
            <button type="submit" class="btn btn-success" id="submitOrderButton" style="width: 100%">Submit
                Order</button>
        </div>
    </div>
</div>