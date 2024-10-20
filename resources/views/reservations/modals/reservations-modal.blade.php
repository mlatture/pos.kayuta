<style>
    .checkbox-wrapper-55 input[type="checkbox"] {
        visibility: hidden;
        display: none;
    }

    .checkbox-wrapper-55 *,
    .checkbox-wrapper-55 ::after,
    .checkbox-wrapper-55 ::before {
        box-sizing: border-box;
    }

    .checkbox-wrapper-55 .rocker {
        display: inline-block;
        position: relative;
        /*
      SIZE OF SWITCH
      ==============
      All sizes are in em - therefore
      changing the font-size here
      will change the size of the switch.
      See .rocker-small below as example.
      */
        font-size: 2em;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
        color: #888;
        width: 7em;
        height: 4em;
        overflow: hidden;
        border-bottom: 0.5em solid #eee;
    }

    .checkbox-wrapper-55 .rocker-small {
        font-size: 0.75em;
    }

    .checkbox-wrapper-55 .rocker::before {
        content: "";
        position: absolute;
        top: 0.5em;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #999;
        border: 0.5em solid #eee;
        border-bottom: 0;
    }

    .checkbox-wrapper-55 .switch-left,
    .checkbox-wrapper-55 .switch-right {
        cursor: pointer;
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 2.5em;
        width: 3em;
        transition: 0.2s;
        user-select: none;
    }

    .checkbox-wrapper-55 .switch-left {
        height: 2.4em;
        width: 2.75em;
        left: 0.85em;
        bottom: 0.4em;
        background-color: #ddd;
        transform: rotate(15deg) skewX(15deg);
    }

    .checkbox-wrapper-55 .switch-right {
        right: 0.5em;
        bottom: 0;
        background-color: #bd5757;
        color: #fff;
    }

    .checkbox-wrapper-55 .switch-left::before,
    .checkbox-wrapper-55 .switch-right::before {
        content: "";
        position: absolute;
        width: 0.4em;
        height: 2.45em;
        bottom: -0.45em;
        background-color: #ccc;
        transform: skewY(-65deg);
    }

    .checkbox-wrapper-55 .switch-left::before {
        left: -0.4em;
    }

    .checkbox-wrapper-55 .switch-right::before {
        right: -0.375em;
        background-color: transparent;
        transform: skewY(65deg);
    }

    .checkbox-wrapper-55 input:checked+.switch-left {
        background-color: #0084d0;
        color: #fff;
        bottom: 0px;
        left: 0.5em;
        height: 2.5em;
        width: 3em;
        transform: rotate(0deg) skewX(0deg);
    }

    .checkbox-wrapper-55 input:checked+.switch-left::before {
        background-color: transparent;
        width: 3.0833em;
    }

    .checkbox-wrapper-55 input:checked+.switch-left+.switch-right {
        background-color: #ddd;
        color: #888;
        bottom: 0.4em;
        right: 0.8em;
        height: 2.4em;
        width: 2.75em;
        transform: rotate(-15deg) skewX(-15deg);
    }

    .checkbox-wrapper-55 input:checked+.switch-left+.switch-right::before {
        background-color: #ccc;
    }

    /* Keyboard Users */
    .checkbox-wrapper-55 input:focus+.switch-left {
        color: #333;
    }

    .checkbox-wrapper-55 input:checked:focus+.switch-left {
        color: #fff;
    }

    .checkbox-wrapper-55 input:focus+.switch-left+.switch-right {
        color: #fff;
    }

    .checkbox-wrapper-55 input:checked:focus+.switch-left+.switch-right {
        color: #333;
    }
</style>


<div class="modal fade" id="dateRangeModal" tabindex="-1" role="dialog" aria-labelledby="dateRangeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="dateRangeModalLabel">Create Reservation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-info mb-3" id="backInfo" style="display: none;">Back</button>
                <form id="dateRangeForm">
                    <div class="firstpage-modal">
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fromDate">Check In Date</label>
                                    <input type="text" class="form-control" id="fromDate" name="fromDate"
                                        placeholder="Select Check In Date" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="toDate">Check Out Date</label>
                                    <input type="text" class="form-control" id="toDate" name="toDate"
                                        placeholder="Select Check Out Date" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-3">

                            <div class="col-md">
                                <div class="form-group">
                                    <label for="siteclass">Site Type</label>
                                    <select id="siteclass" class="form-control">
                                        <!-- Options will be loaded dynamically -->
                                    </select>
                                    <input type="hidden" name="siteclass" id="siteclasses">
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-3" id="forRv">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="riglength">Rig Length</label>
                                    <input type="number" class="form-control" id="riglength" name="riglength"
                                        placeholder="Enter Rig Length">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hookup">Hookup</label>
                                    <select id="hookup" class="form-control">
                                    </select>
                                    <input type="hidden" name="hookup" id="hookups">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="secondpage-modal" style="display: none;">

                        <div class="form-row ">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="number_of_guests">Number of Guests</label>
                                    <select id="number_of_guests" class="form-control" name="num_guests">

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="siteId">Site</label>
                                    <select name="siteId" id="siteSelector" class="form-control">

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="siteLock">Site Lock ($20)</label>
                                    <div class="custom-checkbox-container">
                                        <div class="checkbox-wrapper-55">
                                            <label class="rocker rocker-small">
                                                <input type="checkbox" id="siteLock" name="siteLock" checked>
                                                <span class="switch-left">Yes</span>
                                                <span class="switch-right">No</span>
                                            </label>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>


                        <div class="form-row mt-2">
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control reservationEmail" id="reservationEmail"
                                        name="email" placeholder="Enter Email">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Fisrt Name</label>
                                    <input type="text" class="form-control" id="f_name" name="f_name"
                                        placeholder="First Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Last Name</label>
                                    <input type="text" class="form-control" id="l_name" name="l_name"
                                        placeholder="Last Name">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact">Contact Number</label>
                                    <input type="text" class="form-control" id="con_num" name="con_num"
                                        placeholder="Contact Number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        placeholder="Address">
                                </div>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModal"
                    data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" id="backInfo" style="display: none;">Back</button>
                <button type="button" class="btn btn-info" id="nextInfo">Next</button>
                <button type="button" class="btn btn-success" id="submitReservations">Submit</button>
            </div>
        </div>
    </div>
</div>


<script>
    var customerInfo = " {{ route('customer.info') }}";
</script>