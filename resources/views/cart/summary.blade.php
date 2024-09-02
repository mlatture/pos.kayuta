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
