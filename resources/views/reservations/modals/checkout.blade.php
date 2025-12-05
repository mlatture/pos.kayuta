  {{-- Checkout Modal --}}
  <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Checkout</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body">

                  <!-- Dynamic Cart Items -->
                  <div id="cartItemsList" class="mb-3"><!-- dynamically filled --></div>

                  <div class="row g-3">
                      <div class="col-md-6">
                          <!-- Customer Information -->
                          <div class="border rounded p-3 mb-3">
                              <h6 class="mb-3">Customer Information</h6>

                              {{--  Hidden ID --}}
                              <input type="hidden" name="" id="custId">

                              <div class="row g-3">
                                  <div class="col-md-6">
                                      <label class="form-label">First Name</label>
                                      <input type="text" class="form-control" id="custFname"
                                          placeholder="First Name">
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Last Name</label>
                                      <input type="text" class="form-control" id="custLname" placeholder="Last Name">
                                  </div>

                                  <div class="col-md-6">
                                      <label class="form-label">Email</label>
                                      <input type="email" class="form-control" id="custEmail"
                                          placeholder="Email Address">
                                  </div>
                                  <div class="col-md-6">
                                      <label class="form-label">Phone</label>
                                      <input type="text" class="form-control" id="custPhone"
                                          placeholder="Phone Number">
                                  </div>

                                  <div class="col-12">
                                      <label class="form-label">Street Address</label>
                                      <input type="text" class="form-control" id="custStreet"
                                          placeholder="Street Address">
                                  </div>

                                  <div class="col-md-4">
                                      <label class="form-label">City</label>
                                      <input type="text" class="form-control" id="custCity" placeholder="City">
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label">State</label>
                                      <input type="text" class="form-control" id="custState" placeholder="State">
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label">ZIP</label>
                                      <input type="text" class="form-control" id="custZip" placeholder="ZIP Code">
                                  </div>
                              </div>
                          </div>



                          <!-- Coupon Code Section -->
                          <div class="border rounded p-3">
                              <div class="mb-3">
                                  <label class="form-label">Coupon code</label>
                                  <div class="input-group">
                                      <input type="text" class="form-control" id="couponCode"
                                          placeholder="Enter code">
                                      <button class="btn btn-outline-secondary" id="btnApplyCoupon">Apply</button>
                                  </div>
                                  <div class="form-text">Same validation rules as book site.</div>
                              </div>

                              <!-- Instant Discount Section -->
                              <div class="mb-3">
                                  <label class="form-label fw-bold">Apply Instant Discount</label>
                                  <div class="d-flex align-items-center mb-1">
                                      <!-- Radio buttons -->
                                      <div class="form-check me-2">
                                          <input class="form-check-input" type="radio" name="discountType"
                                              id="percentDiscount" value="percent" checked>
                                          <label class="form-check-label" for="percentDiscount">%</label>
                                      </div>
                                      <div class="form-check me-2">
                                          <input class="form-check-input" type="radio" name="discountType"
                                              id="dollarDiscount" value="dollar">
                                          <label class="form-check-label" for="dollarDiscount">$</label>
                                      </div>

                                      <!-- Discount value input -->
                                      <input type="number" class="form-control form-control-sm" id="discountValue"
                                          placeholder="0.00" min="0" max="999.99" step="0.01"
                                          style="width: 80px;">

                                      <!-- Apply button -->
                                      <button class="btn btn-outline-primary btn-sm ms-2"
                                          id="btnApplyDiscount">Apply</button>
                                  </div>

                                  <!-- Description input -->
                                  <input type="text" class="form-control form-control-sm" id="discountDescription"
                                      placeholder="Description (will show on receipt)">
                              </div>

                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="border rounded p-3 mb-3" id="totalsBox">
                              <div class="d-flex justify-content-between"><span>Subtotal</span><strong
                                      id="tSubtotal">—</strong></div>
                              <div class="d-flex justify-content-between"><span>Site Lock Fee</span><strong
                                      id="tSiteLock">—</strong></div>
                              <div class="d-flex justify-content-between"><span>Discounts</span><strong
                                      id="tDiscounts">—</strong></div>
                              <div class="d-flex justify-content-between"><span>Tax</span><strong
                                      id="tTax">—</strong></div>
                              <hr>
                              <div class="d-flex justify-content-between fs-5"><span>Total</span><strong
                                      id="tTotal">—</strong></div>
                          </div>

                          <div class="border rounded p-3">
                              <div class="d-grid gap-2 d-md-flex">
                                  <button class="btn btn-outline-dark" data-method="cash">Cash</button>
                                  <button class="btn btn-outline-dark" data-method="ach">ACH</button>
                                  <button class="btn btn-outline-dark" data-method="gift_card">Gift Card</button>
                                  <button class="btn btn-outline-dark" data-method="card">Credit Card</button>
                              </div>

                              <div class="mt-3" id="paymentInputs"><!-- dynamically injected --></div>
                          </div>
                      </div>


                  </div>

                  <hr>


              </div>

              <div class="modal-footer">
                  <button class="btn btn-outline-secondary" id="btnCheckCancel"
                      data-bs-dismiss="modal">Cancel</button>
                  <button class="btn btn-success" id="btnPlaceOrder">Place Order</button>
              </div>
          </div>
      </div>
  </div>
