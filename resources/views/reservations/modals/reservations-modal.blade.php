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
                <form id="dateRangeForm">
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="customerSelector">Customer</label>
                                <select name="customerID" id="customerSelector" class="form-control">
                                </select>
                            </div>

                            <input type="hidden" name="fname" id="fname">
                            <input type="hidden" name="lname" id="lname">
                            <input type="hidden" class="form-control" name="email" id="email" placeholder="Enter Email">

                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                            </div>
                        </div>
                      
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveDateRange">Save</button>
            </div>
        </div>
    </div>
</div>
