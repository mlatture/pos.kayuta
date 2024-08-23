<div class="modal fade" id="dateRangeModal" tabindex="-1" role="dialog" aria-labelledby="dateRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="dateRangeModalLabel">Create Reservation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-info" id="backInfo">Back</button>
                <form id="dateRangeForm">
                    <div class="firstpage-modal">
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fromDate">Check In Date</label>
                                    <input type="text" class="form-control" id="fromDate" name="fromDate" placeholder="Select Check In Date" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="toDate">Check Out Date</label>
                                    <input type="text" class="form-control" id="toDate" name="toDate" placeholder="Select Check Out Date" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col">
                                <div class="form-group">
                                    <label for="customerSelector">Customer</label>
                                    <select name="customerID" id="customerSelector" class="form-control">
                                    </select>
                                </div>
    
                                <input type="hidden" name="lname" id="lname">
                                <input type="hidden" name="fname" id="fname">
                                <input type="hidden" class="form-control" name="email" id="email" placeholder="Enter Email">
    
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="siteclass">Site Type</label>
                                    <select  id="siteclass" class="form-control">
                                    </select>
                                    <input type="text" name="siteclass" id="siteclasses" >
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-3" id="forRv">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="riglength">Rig Length</label>
                                    <input type="number" class="form-control" id="riglength" 
                                    name="riglength" placeholder="Enter Rig Length">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hookup">Hookup</label>
                                    <select  id="hookup" class="form-control">
                                    </select>
                                    <input type="text" name="hookup" id="hookups">
                                </div>
                            </div>
                        </div>
                        
                    </div>
                   
                    <div class="secondpage-modal">
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="adults">Number of Adults</label>
                                    <input type="number" class="form-control" id="adults" 
                                    name="adults" placeholder="Enter Number of Adults">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="under">Number of Children Ages 5 and under</label>
                                    <input type="number" class="form-control" id="under" 
                                    name="under" placeholder="Enter Number of Childrens">
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="teen">Number of Children Ages 6 to 17</label>
                                    <input type="number" class="form-control" id="teen" 
                                    name="teen" placeholder="Enter Number of Childrens">
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pets">Number of Pets</label>
                                    <input type="number" class="form-control" id="pets" 
                                    name="pets" placeholder="Enter Number of Pets">
                                </div>
                            </div>
                        </div>
                        {{-- <div class="form-row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="petdes">Pet Description(s)</label>
                                    <input type="text" class="form-control" id="petdes" 
                                    name="petdes" placeholder="Enter Pet Description">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicles">Number of Vehicles</label>
                                    <input type="number" class="form-control" id="vehicles" 
                                    name="vehicles" placeholder="Enter Number of Vehicles">
                                </div>
                            </div>
                        </div> --}}
                        {{-- <div class="form-row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="trailers">Number of Trailers</label>
                                    <input type="text" class="form-control" id="trailers" 
                                    name="trailers" placeholder="Enter Number of Trailers">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicles">Number of Vehicles</label>
                                    <input type="number" class="form-control" id="vehicles" 
                                    name="vehicles" placeholder="Enter Number of Vehicles">
                                </div>
                            </div>
                        </div> --}}
                        <div class="form-row mb-3">
                            <div class="col">
                                <div class="form-group">
                                    <label for="siteId">Site</label>
                                    <select name="siteId" id="siteSelector" class="form-control">
                                    </select>
                                </div>
                            </div>
                          
                        </div>
                    </div>
                    {{-- @include('reservations.modals.thirdpagemodal') --}}
                </form>

                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModal" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" id="nextInfo">Next</button>
               
                <button type="button" class="btn btn-success" id="submitReservations">Submit</button>
            </div>
        </div>
    </div>
</div>
