@extends('layouts.admin')

@section('title', 'Seasonal Settings')

@section('content')


    <div class="card shadow border-0 bg-white rounded-4 overflow-hidden">
        <div class="card-header bg-gradient text-dark d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #00b09b, #96c93d);">

            <h4 class="mb-0 d-flex align-items-center">
                <i class="bi bi-gear-fill me-2"></i> Seasonal Guest Renewal Settings
            </h4>


        </div>

        {{-- Seasonal Add Ons Modal --}}
        @include('admin.seasonal.modal.add-ons')

        <div class="card-body px-4 py-3" style="max-height: 80vh; overflow-y: auto;">
            @include('admin.seasonal.component.nav-seasonal')

            <div class="tab-content mt-3">
                <div class="tab-pane fade {{ request('tab', 'overview') == 'overview' ? 'show active' : '' }}" id="overview">
                    @include('admin.seasonal.component.seasonal-guest-renewals')
                </div>

                <div class="tab-pane fade {{ request('tab', 'form') == 'form' ? 'show active' : '' }}" id="form">
                    @include('admin.seasonal.component.upload-document')
                </div>

                <div class="tab-pane fade {{ request('tab', 'rate') == 'rate' ? 'show active' : '' }}" id="rate">
                    @include('admin.seasonal.component.seasonal-rates')
                </div>




                <div class="tab-pane fade {{ request('tab', 'addons') == 'addons' ? 'show active' : '' }}" id="addons">
                    @include('admin.seasonal.component.seasonal-add-ons')
                </div>
            </div>


        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).on('click', '#clearAndReloadBtn', function() {
            const renewalCount = {{ $currentYearRenewalsCount }};
            let htmlMessage = '';

            if (renewalCount === 0) {
                htmlMessage += `<p>No renewal records found for the current year.</p>`;
            } else {
                htmlMessage +=
                    `<p>There are currently <strong>${renewalCount}</strong> renewal records for this year.</p>`;
            }

            Swal.fire({
                title: 'Are you sure?',
                html: `
            <p>This will reset the entire renewal process for the new season.</p>
            ${htmlMessage}
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, reset it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.post('{{ route('seasonal.reload') }}', {
                        _token: '{{ csrf_token() }}'
                    })
                    .done(function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.reload();
                        });
                    })
                    .fail(function(err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: err.responseJSON?.message || 'Something went wrong.',
                            confirmButtonColor: '#dc3545'
                        });
                    });
            });
        });

        $(document).on('click', '#sendEmailBtn', function() {
            const $btn = $(this);
            const url = $btn.data('url');

            $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Sending...');

            $.post(url, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                $.toast({
                    heading: 'Success',
                    text: res.message,
                    icon: 'success',
                    position: 'bottom-left',
                    hideAfter: 3000,
                });

                $btn.prop('disabled', false).html('<i class="bi bi-envelope"></i> Send Emails');
            }).fail(function(err) {
                alert(err.responseJSON?.message || 'Something went wrong.');
                $btn.prop('disabled', false).html('<i class="bi bi-envelope"></i> Send Emails');
            });
        });

        $(document).on('click', '.btn-delete', function() {
            $this = $(this);

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this data?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No',
                reverseButtons: true
            }).then((result) => {
                console.log('result:', result);

                if (result.value) {
                    $.post($this.data('url'), {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        $this.closest('li').fadeOut(500, function() {
                            $(this).remove();
                        });
                        $.toast({
                            heading: 'Success',
                            text: res.message,
                            icon: 'success',
                            position: 'bottom-left',
                            hideAfter: 3000,
                            stack: 3
                        });

                    })
                }
            });
        });
    </script>
@endpush
