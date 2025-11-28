  {{-- Site Details Modal --}}
  <div class="modal fade" id="siteDetailsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl">
          <div class="modal-content">
              <div class="modal-header bg-success text-white">
                  <h5 class="modal-title" id="sdName">Site Details</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                      aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <div class="row g-4">
                      <div class="col-lg-6">
                          <div class="card shadow-sm h-100">
                              <div class="card-body p-0">
                                  <div id="siteImagesCarousel" class="carousel slide" data-bs-ride="carousel">

                                      <div class="carousel-inner rounded-top" id="sdImagesContainer">
                                      </div>

                                      <button class="carousel-control-prev" type="button"
                                          data-bs-target="#siteImagesCarousel" data-bs-slide="prev">
                                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                          <span class="visually-hidden">Previous</span>
                                      </button>
                                      <button class="carousel-control-next" type="button"
                                          data-bs-target="#siteImagesCarousel" data-bs-slide="next">
                                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                          <span class="visually-hidden">Next</span>
                                      </button>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="col-lg-6">
                          <div class="card shadow-sm h-100">
                              <div class="card-header bg-light">
                                  <h5 class="mb-0 text-success">Site Features & Details</h5>
                              </div>
                              <div class="card-body">
                                  <div class="row g-2">
                                      <div class="col-6">
                                          <p class="mb-1 text-muted small">Site ID</p>
                                          <h4 id="sdSiteId" class="fw-bold">—</h4>
                                      </div>
                                      <div class="col-6">
                                          <p class="mb-1 text-muted small">Class</p>
                                          <h4 id="sdClass" class="fw-bold text-truncate">—</h4>
                                      </div>
                                      <div class="col-6">
                                          <p class="mb-1 text-muted small">Hookup</p>
                                          <h5 id="sdHookup" class="fw-semibold">—</h5>
                                      </div>
                                      <div class="col-6">
                                          <p class="mb-1 text-muted small">Rig Length</p>
                                          <h5 id="sdRig" class="fw-semibold">—</h5>
                                      </div>
                                  </div>
                                  <hr>
                                  <div class="row g-3">
                                      <div class="col-12">
                                          <h6 class="text-secondary">Amenities</h6>
                                          <ul id="sdAmenities" class="list-unstyled d-flex flex-wrap gap-3">
                                              <li class="text-muted small">None Listed</li>
                                          </ul>
                                      </div>
                                      <div class="col-12">
                                          <h6 class="text-secondary">Special Attributes</h6>
                                          <p id="sdAttributes" class="fst-italic text-wrap">—</p>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <hr class="my-4">

                  <div class="row g-2">
                      <div class="col">
                          <div class="card shadow-sm h-100">
                              <div class="card-header bg-info text-white">
                                  <h5 class="mb-0">Pricing Summary</h5>
                              </div>
                              <ul class="list-group list-group-flush">
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      Length of Stay:
                                      <span id="sdStay" class="fw-bold">0</span> nights
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      Avg. Nightly Rate:
                                      <strong class="text-primary">$<span id="sdAvgNight">0</span></strong>
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      Platform Fee (Avg.):
                                      <span>$<span id="sdPlatformFee">0</span></span>
                                  </li>
                                  <li
                                      class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                      <strong>Reservation Total:</strong>
                                      <strong class="text-success fs-5">$<span id="sdTotal">0</span></strong>
                                  </li>
                              </ul>
                          </div>
                      </div>

                      <div class="col">
                          <div class="card shadow-sm h-100">
                              <div class="card-header bg-primary text-white">
                                  <h5 class="mb-0">Occupants</h5>
                              </div>
                              <div class="card-body">
                                  <div class="mb-3">
                                      <label for="occupantsAdults" class="form-label fw-bold">Adults</label>
                                      <div class="input-group">
                                          <span class="input-group-text">🧑</span>
                                          <input type="number" class="form-control" id="occupantsAdults" value="2" >
                                      </div>
                                      <div class="form-text">Default is 2 adults per site.</div>
                                  </div>

                                  <div class="mb-0">
                                      <label for="occupantsChildren" class="form-label fw-bold">Children</label>
                                      <div class="input-group">
                                          <span class="input-group-text">👧</span>
                                          <input type="number" class="form-control" id="occupantsChildren" value="0" >
                                      </div>
                                      <div class="form-text">Ages 17 and under.</div>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="col">
                          <div class="card shadow-sm h-100">
                              <div class="card-header bg-warning text-dark">
                                  <h5 class="mb-0">Booking Policies</h5>
                              </div>
                              <ul class="list-group list-group-flush">
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      Minimum Stay:
                                      <span id="sdMinStay" class="fw-semibold">—</span> night(s)
                                  </li>
                                  <li class="list-group-item">
                                      <div
                                          class="form-check form-switch d-flex justify-content-between align-items-center">
                                          <label class="form-check-label fw-bold" for="siteLockToggle">Site
                                              Lock:</label>
                                          <input class="form-check-input" checked type="checkbox" role="switch"
                                              id="siteLockToggle">
                                      </div>

                                      <div class="mt-2">
                                          <span id="sdSiteLockFeeDisplay" class="badge bg-secondary">Not
                                              Included</span>

                                          <p class="small text-muted mt-1 mb-0" id="sdLockMessage">—</p>
                                      </div>
                                  </li>
                              </ul>
                          </div>
                      </div>


                  </div>

                  <hr class="my-4">

                  <div class="row g-2">
                      <div class="col">
                          <div class="card shadow-sm h-100">
                              <div class="card-header bg-danger text-white">
                                  <h5 id="sdTitleInfo" class="mb-0">Important Information</h5>
                              </div>
                              <div class="card-body" id="infoCardBody">
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-success" id="addToCartSite">Add To Cart</button>
              </div>

          </div>



      </div>
  </div>
  </div>
