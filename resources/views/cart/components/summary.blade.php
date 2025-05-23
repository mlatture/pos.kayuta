<div class="card p-3" id="card-summary" hidden >
    <div class="card-body">
        <div class="row">
            <div class="col">Sub Total:</div>
            <div class="col text-right">$ {{ number_format($subtotal, 2) }}</div>
        </div>
        <div class="row">
            <div class="col">Discount:</div>
            <div class="col text-right" id="total-discount">$ {{ number_format($totalDiscount, 2) }}</div>
        </div>
        <div class="row">
            <div class="col">Tax:</div>
            <div class="col text-right" id="tax-amount">$ {{ number_format($totalTax, 2) }}</div>
        </div>
       
        <div class="row border-top mt-2">
            <div class="col">Total:</div>
            <input type="hidden" class="total-amount"
                value="{{ number_format($subtotal - $totalDiscount + $totalTax, 2) }}" id="total-amount">
            <input type="hidden" class="subtotal-amount"
                value="{{ number_format($subtotal - $totalDiscount + $totalTax, 2) }}" id="subtotal-amount">

            <input type="hidden" name="gift_card_id" id="gift_card_id">
            <input type="hidden" name="gift_card_discount" id="gift_card_discount">

            <div class="col text-right show-total-amount">$
                {{ number_format($subtotal - $totalDiscount + $totalTax, 2) }}
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" data-bs-keyboard="false" tabindex="-1" id="offcanvasOrder"  aria-labelledby="offcanvasOrderLabel">
    <div class="offcanvas-header border-bottom">
        <div class="header-title">
            <h3 class="weight-600 font-16">Order Summary</h3>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div id="orderSummaryCardTwo" class="col-12 font-14 mb-3">
            <div class="border rounded pt-3 px-3">
                <div class="row">
                    <div class="col-6">
                        <p>Subtotal</p>
                    </div>
                    <div class="col-6">
                        <span>$</span>
                        <p class="float-right" id="offcanvasSubtotal"></p>
                    </div>

                    <div class="col-6" id="discount-section">
                        <p>Discount</p>
                    </div>
                    <div class="col-6" id="discount-section1">
                        <span>$</span>
                        <p class="float-right" id="offcanvasDiscount"></p>
                    </div>

                    <div class="col-6">
                        <p>Tax</p>
                    </div>
                    <div class="col-6">
                        <span>$</span>
                        <p class="float-right" id="offcanvasTax"></p>
                    </div>

                    {{-- <div class="col-sm-12 border-top">
                        <div class="row pt-2">
                            <div class="col-6">
                                <p><b>Total</b></p>
                            </div>
                            <div class="col-6">
                                <span>$</span>
                                <p class="float-right" id="offcanvasTotalAmount"></p>
                            </div>
                        </div>
                    </div> --}}
                   
                    
                    <div class="col-sm-12 border-top">
                        <div class="row pt-2">
                            <div class="col-6">
                                <p><b>Total</b></p>
                            </div>
                            <div class="col-6">
                                <span>$</span>
                                <p class="float-right" id="displayTotalAmount"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 border-top">
                        <div class="row pt-2">
                            <div class="col-6">
                                <p><b>Remaining</b></p>
                            </div>
                            <div class="col-6">
                                <span>$</span>
                                <p class="float-right" id="remainingBalance"></p>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="order_id" name="order_id" value="{{ old('order_id', $order_id ?? '') }}">

                 
                </div>
            </div>
        </div>
        @include('cart.components.paymentmethod')
    </div>
</div>
<script>

</script>

