{{-- <div class="modal fade" id="sizeModal" tabindex="-1" aria-labelledby="sizeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sizeModalLabel">Modal title</h5>
                <button type="button" class="btn-close border-0 bg-transparent" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">...</div>
        </div>
    </div>
</div>


<div class="modal fade" id="reservationDateModal" tabindex="-1" role="dialog"
    aria-labelledby="reservationDateModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservationDateModalTitle">Arrivals & Departures</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none"></div>
                <div class="alert alert-success d-none"></div>
                <form id="reservationDateForm" method="post" action="{{ route('reservations.updateDates') }}">
                    @csrf
                    <div class="form-group">
                        <label>Select Reservation</label>
                        <select class="form-control reservationId" id="reservationId" name="reservationId">
                            <option value="" disabled selected>Select Reservation</option>
                            @foreach ($allReservations as $v)
                            <option value="{{$v->id}}">{{$v->fname.' '.$v->lname.' - '}}{{$v->siteclass.' -
                                '}}{{$v->siteid}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Check-in Date</label>
                        <input type="date" name="cid" id="cid" class="form-control res-cid">
                    </div>
                    <div class="form-group">
                        <label>Check-out Date</label>
                        <input type="date" name="cod" id="cod" class="form-control res-cod">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                    onclick="closeReservationModal('reservationDateModal')">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveReservationDates(this)">Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reservationSiteModal" tabindex="-1" role="dialog"
    aria-labelledby="reservationSiteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservationSiteModalTitle">Relocate</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none"></div>
                <div class="alert alert-success d-none"></div>
                <form id="reservationSiteForm" method="post" action="{{ route('reservations.update-sites') }}">
                    @csrf
                    <div class="form-group">
                        <label>Select Reservation</label>
                        <select class="form-control reservationId2" id="reservationId2" name="reservationId2">
                            <option value="" disabled selected>Select Reservation</option>
                            @foreach ($allReservations as $v)
                            <option value="{{$v->id}}">{{$v->fname.' '.$v->lname.' - '}}{{$v->siteclass.' -
                                '}}{{$v->siteid}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Site ID</label>
                        <select class="form-control siteid res-siteid" id="siteid" name="siteid">
                            <option value="" disabled selected>Select Site Id</option>
                            @foreach ($allCurrentSites as $v)
                            <option value="{{$v->id}}">{{$v->sitename}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Site Class</label>
                        <select class="form-control siteclass res-siteclass" id="siteclass" name="siteclass">
                            <option value="" disabled selected>Select Site Class</option>
                            @foreach ($allCurrentSites as $v)
                            <option value="{{$v->id}}">{{$v->siteclass}}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                    onclick="closeReservationModal('reservationSiteModal')">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveReservationSites(this)">Update</button>
            </div>
        </div>
    </div>
</div> --}}
