<div class="tab-pane fade" id="search" role="tabpanel" aria-labelledby="search-tab">
    <form method="POST" action="{{ route('admin.search-settings.update') }}" enctype enctype="multipart/form-data">
        @csrf

        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-solid fa-map"></i> Grid View
            </div>
            <div class="card-body">
                <p><strong>
                        If Grid View is off, the Map View will be the default</strong></p>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="gridViewToggle" name="grid_view"
                    {{ $settings2['is_grid_view'] == '1' ? 'checked' : '' }}>
                    
                <label class="form-check-label" for="gridViewToggle">Search Results in Grid View</label>
                </div>

            </div>
        </div>

        <button type="submit" class="btn btn-primary float-end">Save Settings</button>
    </form>
</div>
