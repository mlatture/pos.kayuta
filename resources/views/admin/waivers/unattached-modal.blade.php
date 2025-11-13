{{-- resources/views/admin/waivers/unattached-modal.blade.php --}}

{{-- Filters --}}
<div class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="date" id="w_date_from" class="form-control" placeholder="From">
    </div>
    <div class="col-md-3">
        <input type="date" id="w_date_to" class="form-control" placeholder="To">
    </div>
    <div class="col-md-3">
        <input type="text" id="w_email" class="form-control" placeholder="Email">
    </div>
    <div class="col-md-3">
        <input type="text" id="w_name" class="form-control" placeholder="Name">
    </div>
    <div class="col-12 text-end">
        <button class="btn btn-outline-secondary btn-sm" id="w_filter">Filter</button>
    </div>
</div>

{{-- Bulk actions --}}
<div class="d-flex gap-2 mb-2">
    <button class="btn btn-primary btn-sm" id="w_attach">Attach to this customer</button>
    <button class="btn btn-outline-danger btn-sm" id="w_delete">Delete (soft)</button>
</div>

{{-- Table --}}
<div class="table-responsive">
    <table class="table table-sm table-striped align-middle" id="w_table" style="width:100%">
        <thead>
            <tr>
                <th style="width:28px;"><input type="checkbox" id="w_all"></th>
                <th>Date/Time</th>
                <th>Name</th>
                <th>Email</th>
                <th>Site</th>
                <th>Booking ID</th>
                <th>IP</th>
                <th>Download</th>
                <th>Status</th>
            </tr>
        </thead>
    </table>
</div>
