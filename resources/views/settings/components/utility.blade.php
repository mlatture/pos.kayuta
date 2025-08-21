<div class="tab-pane fade" id="utility" role="tabpanel" aria-labelledby="utility-tab">


    <div class="d-grid gap-3 mb-4" style="grid-template-columns: repeat(3, 1fr);">
        {{-- Electric Meter Settings --}}
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-bolt"></i> Electric Meter Rate
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <form method="POST" action="{{ route('admin.electric-meter-rate.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <label for="electric_meter_rate" class="form-label">Electric Meter Rate (per kWh)</label>
                        <input type="number" step="0.01" min="0" max="10"
                            class="form-control @error('electric_meter_rate') is-invalid @enderror"
                            id="electric_meter_rate" name="electric_meter_rate"
                            value="{{ old('electric_meter_rate', $settings['electric_meter_rate'] ?? '') }}"
                            placeholder="Example: 0.12">

                        <small class="form-text text-muted">Please enter a valid rate (e.g., 0.12 per kWh).</small>

                        @error('electric_meter_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-primary float-end">Save Settings</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>


@push('js')
    <script>
        $(document).ready(function() {
            $('#maintenance_mode').on('change', function() {
                const isEnabled = $(this).is(':checked') ? 1 : 0;
                const url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        maintenance_mode: isEnabled,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Maintenance mode updated');
                            toastr.options = {
                                "positionClass": "toast-bottom-left",
                                "timeOut": 3000,
                            };
                            toastr.success('Maintenance mode has been updated.');
                        } else {
                            alert('Failed to update setting');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush
