<div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filtersModalLabel"><i class="fa fa-filter"></i> Apply Filters</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="managementFilterForm" method="GET" action="{{ url()->current() }}">

                    <div class="mb-3">
                        <label for="siteFilter" class="form-label">Site ID</label>
                        <select id="siteFilter" name="siteid[]" class="form-select w-100" multiple="multiple">
                            @foreach ($sites->pluck('siteid')->unique()->sort() as $siteId)
                                <option value="{{ $siteId }}"
                                    {{ in_array($siteId, request('siteid', [])) ? 'selected' : '' }}>
                                    {{ $siteId }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="rigLength" class="form-label">Rig Length</label>
                        <input type="text" id="rigLength" name="riglength" class="form-control w-100" value="{{ request('riglength', '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="typeFilter" class="form-label">Site Type</label>
                        <select id="typeFilter" name="siteclass[]" class="form-select w-100" multiple="multiple">
                            @foreach ($site_classes->pluck('siteclass')->unique()->sort() as $siteclass)
                                <option value="{{ $siteclass }}">
                                    {{ $siteclass }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tierFilter" class="form-label">Rate Tier</label>
                        <select id="tierFilter" name="ratetier[]" class="form-select w-100" multiple="multiple">
                            @foreach ($rate_tiers->pluck('tier')->unique()->sort() as $rateTier)
                                <option value="{{ $rateTier }}"
                                    {{ in_array($rateTier, request('ratetier', [])) ? 'selected' : '' }}>
                                    {{ $rateTier }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="btn btn-primary applyFilter">Apply</button>
                    <a href="{{ url()->current() }}" class="btn btn-secondary">Clear Filters</a>
                </form>
            </div>
        </div>
    </div>
</div>
