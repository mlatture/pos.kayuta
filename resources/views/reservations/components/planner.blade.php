<div class="collapse px-5" id="collapsePlanner">
    <div class="card card-body">
        <div class="row">
            <div class="col">
                <label for="dateRange">Date Range</label>
                <input type="text" name="dates" class="form-control">
            </div>

            <div class="col">
                <label for="siteId">Site</label>
                <select name="siteId" id="siteSelectors" class="form-control">

                </select>
            </div>

            <div class="col">
                <label for="">Type</label>
                <select id="type" name="type" class="form-control" multiple="multiple">

                </select>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="">Search</label>
                <input type="search" class="form-control">
            </div>
            <div class="col">
                <label for="">Filter Buttons</label>

                <div class="form-control" style="border: none">

                    <button class="btn btn-success">Arrivals</button>
                    <button class="btn btn-danger">Departures</button>
                    <button class="btn btn-primary">Occupied</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
   $(document).ready(function () {
        $('input[name="dates"]').daterangepicker();
    });


</script>