<div class="row">
    <div class="col">Sub Total:</div>
    <div class="col text-right">$ {{ number_format($subtotal, 2) }}</div>
</div>
<div class="row">
    <div class="col">Discount:</div>
    <div class="col text-right">$ {{ number_format($totalDiscount, 2) }}</div>
</div>
<div class="row">
    <div class="col">Tax:</div>
    <div class="col text-right">$ {{ number_format($totalTax, 2) }}</div>
</div>
<div class="row">
    <div class="col">Gift Card Discount:</div>
    <div class="col text-right show-gift-discount">$ 0</div>
</div>
<div class="row">
    <div class="col">Total:</div>
    <input type="hidden" class="total-amount" value="{{ number_format($subtotal - $totalDiscount + $totalTax, 2) }}"
        id="total-amount">
    <input type="hidden" class="subtotal-amount" value="{{ number_format($subtotal - $totalDiscount + $totalTax, 2) }}"
        id="subtotal-amount">

    <input type="hidden" name="gift_card_id" id="gift_card_id">
    <input type="hidden" name="gift_card_discount" id="gift_card_discount">

    <div class="col text-right show-total-amount">$
        {{ number_format($subtotal - $totalDiscount + $totalTax, 2) }}</div>
</div>
<!-- Off-canvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasOrder" aria-labelledby="offcanvasOrderLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasOrderLabel">Order Summary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="row">
            <div class="col">Sub Total:</div>
            <div class="col text-right" id="offcanvasSubtotal"></div>
        </div>
        <div class="row">
            <div class="col">Discount:</div>
            <div class="col text-right"></div>
        </div>
        <div class="row">
            <div class="col">Tax:</div>
            <div class="col text-right"></div>
        </div>
        <div class="row">
            <div class="col">Gift Card Discount:</div>
            <div class="col text-right" id="offcanvasGiftDiscount">$ 0.00</div>
        </div>
        <div class="row">
            <div class="col">Total:</div>
            <div class="col text-right" id="offcanvasTotalAmount">
               
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <label for="orderAmountInput">Enter Order Amount:</label>
                <input type="text" id="orderAmountInput" class="form-control" placeholder="Enter amount">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col text-right">
                <button type="submit" class="btn btn-primary" id="submitOrderButton">Submit Order</button>
            </div>
        </div>
    </div>
</div>