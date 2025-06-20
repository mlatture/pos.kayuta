@extends('layouts.admin')

@section('title', 'Seasonal Settings')

@section('content')
    @if (session('success'))
        @php
            $setting = null;
        @endphp
    @endif
    <div class="card shadow border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="bi bi-gear-fill me-2"></i> Seasonal Guest Renewal Settings
            </h4>
        </div>

        <div class="card-body">

            

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-4 d-flex ">
                <button type="button" id="triggerRenewalsBtn" class="btn btn-warning">
                    <i class="bi bi-arrow-repeat me-1"></i> Trigger Seasonal Renewals
                </button>

                <form id="triggerRenewalsForm" action="{{ route('admin.seasonal-renewals.trigger') }}" method="POST"
                    style="display: none;">
                    @csrf
                </form>
            </div>


            <form method="POST" action="{{ route('admin.seasonal-settings.store') }}">
                @csrf


                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <label for="default_rate" class="form-label">
                            Default Rate
                        </label>
                        <input type="number" step="0.01" name="default_rate" id="default_rate" class="form-control"
                            placeholder="e.g. 1200.00" required>
                    </div>

                    <div class="col-md-3">
                        <label for="discount_percentage" class="form-label">
                            Discount (%)
                        </label>
                        <input type="number" step="0.01" name="discount_percentage" id="discount_percentage"
                            class="form-control" placeholder="e.g. 10">
                    </div>

                    <div class="col-md-3">
                        <label for="deposit_amount" class="form-label">
                            Deposit Amount
                        </label>
                        <input type="number" step="0.01" name="deposit_amount" id="deposit_amount" class="form-control"
                            placeholder="e.g. 250.00">
                    </div>

                    <div class="col-md-3">
                        <label for="renewal_deadline" class="form-label">
                            Renewal Deadline
                        </label>
                        <input type="date" name="renewal_deadline" id="renewal_deadline" class="form-control" required>
                    </div>
                </div>

                <hr class="mb-4">

                <!-- TIER SETTINGS -->
                <h5 class="text-muted mb-3">
                    <i class="bi bi-layers-half me-2"></i> Rate Tiers Per Site Type
                </h5>

                <div class="row g-4">
                    @forelse ($rateTiers as $tier)
                        <div class="col-md-3">
                            <label class="form-label">
                                {{ Str::title(str_replace('-', ' ', $tier)) }} Rate
                            </label>
                            <input type="number" step="0.01" name="rate_tiers[{{ $tier }}]" class="form-control"
                                placeholder="e.g. 1350.00">
                        </div>
                    @empty
                        <div class="col-12 text-muted">No rate tiers found in the system.</div>
                    @endforelse
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
    <script>
        document.getElementById('triggerRenewalsBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will generate renewals for all seasonal users.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, generate!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('triggerRenewalsForm').submit();
                }
            });
        });
    </script>
@endpush
