<div class="modal fade" id="addOnsModal" tabindex="-1" aria-labelledby="addOnsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addOnForm">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOnsModalLabel">Add-On Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addonName" class="form-label">Add-On Name</label>
                        <input type="text" class="form-control" id="addonName" name="name"
                            placeholder="e.g. Winter Storage" required>
                    </div>

                    <div class="mb-3">
                        <label for="addonPrice" class="form-label">Price ($)</label>
                        <input type="number" class="form-control" id="addonPrice" name="price" placeholder="e.g. 250"
                            min="0" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="maxAllowed" class="form-label">Max Allowed</label>
                        <input type="number" class="form-control" id="maxAllowed" name="max_allowed"
                            placeholder="e.g. 1" value="1" min="1">
                    </div>

                    <div class="form-check mb-3">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" class="form-check-input" id="activeAddon" name="active" value="1"
                            checked>
                        <label class="form-check-label" for="activeAddon">
                            Active
                        </label>
                    </div>

                    <div class="alert alert-info small">
                        Sample: Winter Storage $250, Extra Adult $150, Extra Child $100.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Add-On</button>
                </div>
            </div>
        </form>
    </div>
</div>


@push('js')
    <script>
        // Seasonal Add on Create
        $(document).ready(function() {
            $('#addOnForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('seasonal.addons.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            $('#addOnsModal').modal('hide');
                            $.toast({
                                heading: 'Success',
                                text: res.message || 'Add-on saved successfully!',
                                icon: 'success',
                                position: 'bottom-left',
                                hideAfter: 3000
                            });

                        }
                    },
                    error: function(err) {
                        console.error(err);
                        alert(err.responseJSON?.message ||
                            'There was a problem saving the add-on.');
                    }
                });

            })
        })
    </script>
@endpush
