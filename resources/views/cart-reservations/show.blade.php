@extends('layouts.admin')

@section('title', 'Cart Reservation Details')
@section('content-header', 'Cart Reservation Details')

@section('content')
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Reservation ID:</strong> {{ $cartReservation->cartid ?? 'N/A' }}</p>
                        <p><strong>Site Id:</strong> {{ $cartReservation->siteid ?? 'N/A' }}</p>
                        <p><strong>Site Name:</strong> {{ $cartReservation->sitename ?? 'N/A' }}</p>
                        <p><strong>Check-in Date:</strong> {{ $cartReservation->checkin ?? 'N/A' }}</p>
                        <p><strong>Check-out Date:</strong> {{ $cartReservation->checkout ?? 'N/A' }}</p>
                        <p><strong>Guests:</strong> {{ $cartReservation->guests ?? 'N/A' }}</p>
                        <p><strong>Price:</strong> ${{ number_format($cartReservation->price, 2) ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>User Name:</strong> {{ $cartReservation->user->f_name ?? 'N/A' }} {{ $cartReservation->user->l_name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $cartReservation->user->email ?? 'N/A' }}</p>
                        <p><strong>Status:</strong> {{ $cartReservation->status ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <h4 class="mt-4">Actions</h4>
                <div class="mt-2">

                    <a href="{{ route('reservations.payment.index', $cartReservation->cartid) }}"
                        class="btn btn-primary"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-danger btn-delete"
                        data-url="{{ route('cart-reservation.destroy', $cartReservation->id) }}"><i
                            class="fas fa-trash"></i></a>
                </div>
                
                <div class="mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>


    <script>
           
           $(document).on('click', '.btn-delete', function() {
                let $this = $(this);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to delete this cart reservation?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.post($this.data('url'), {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        }, function(res) {
                            window.location.href = "{{ route('reservations.reservation-in-cart') }}";
                        });
                    }
                });
            });
    </script>
@endsection
