<div class="border rounded-3 mt-3 p-4 show-sm">
    <button type="button"
        class="btn btn-sm btn-outline-primary d-flex justify-content-end float-right"
        data-bs-toggle="modal" data-bs-target="#addOnsModal">Add Ons</button>

    <h6 class="mt-3">🏕️ Existing Seasonal Add Ons</h6>
    <ul class="list-group">
        @forelse ($seasonalAddOns as $addon)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $addon->seasonal_add_on_name }}</strong> -
                    ${{ $addon->seasonal_add_on_price }}

                </div>
                <div class="d-flex gap-2">

                    <button class="btn btn-sm btn-outline-danger btn-delete"
                        data-url="{{ route('seasonal.addon.destroy', $addon->id) }}">Delete</button>
                </div>
            </li>

        @empty
            <li class="list-group-item">No addon added yet.</li>
        @endforelse
    </ul>
</div>
