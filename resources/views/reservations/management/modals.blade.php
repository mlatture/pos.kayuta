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



                          <label class="form-label">Coupon code</label>
                          <div class="input-group">
                              <input type="text" class="form-control" id="couponCode" placeholder="Enter code">
                              {{-- <button class="btn btn-outline-secondary" id="btnApplyCoupon">Apply</button> --}}
                          </div>
                          <div class="form-text">Same validation rules as book site.</div>
                      </div>
                      <div class="col-md-6">
                          <div class="border rounded p-3" id="totalsBox">
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
                      </div>
                  </div>

                  <hr>

                  <div class="d-grid gap-2 d-md-flex">
                      <button class="btn btn-outline-dark" data-method="cash">Cash</button>
                      <button class="btn btn-outline-dark" data-method="ach">ACH</button>
                      <button class="btn btn-outline-dark" data-method="gift_card">Gift Card</button>
                      <button class="btn btn-outline-dark" data-method="credit_card">Credit Card</button>
                  </div>

                  <div class="mt-3" id="paymentInputs"><!-- dynamically injected --></div>
              </div>

              <div class="modal-footer">
                  <button class="btn btn-outline-secondary" id="btnCheckCancel"
                      data-bs-dismiss="modal">Cancel</button>
                  <button class="btn btn-success" id="btnPlaceOrder">Place Order</button>
              </div>
          </div>
      </div>
  </div>
