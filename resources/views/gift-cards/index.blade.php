@extends('layouts.admin')

@section('title', 'Gift Card Management')
@section('content-header', 'Gift Card Management')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_gift_cards.value'))
        <a href="{{ route('gift-cards.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Gift Card</a>
    @endHasPermission
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <style>
        div.dt-top-container {
            display: flex;

            text-align: center;
        }

        div.dt-center-in-div {
            margin: 0 auto;
            display: inline-block;
            text-align: center;
        }

        div.dt-filter-spacer {
            margin: 10px 0;
        }

        td.highlight {
            background-color: #F4F6F9 !important;
        }

        div.dt-left-in-div {
            float: left;
        }

        div.dt-right-in-div {
            float: right;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Actions</th>
                            <th>ID</th>
                            <th>User Email</th>
                            <th>Barcode</th>
                            <th>Amount</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Modified By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($giftCards as $key => $gift)
                            <tr>
                                <td>
                                    @hasPermission(config('constants.role_modules.edit_gift_cards.value'))
                                        <a href="{{ route('gift-cards.edit', $gift) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endHasPermission
                                    @hasPermission(config('constants.role_modules.delete_gift_cards.value'))
                                        <button class="btn btn-danger btn-sm btn-delete" 
                                                data-url="{{ route('gift-cards.destroy', $gift) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endHasPermission
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $gift->user_email ?? 'N/A' }}</td>
                                <td>{{ $gift->barcode ?? 'N/A' }}</td>
                                <td>{{ number_format($gift->amount ?? 0, 2) }}</td>
                                <td>{{ $gift->expire_date ? date('Y, M d', strtotime($gift->expire_date)) : 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $gift->status ? 'badge-success' : 'badge-danger' }}">
                                        {{ $gift->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $gift->created_at ? $gift->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td>{{ $gift->modified_by ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No Gift Cards Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $giftCards->links() }}
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                responsive: true,
                dom: '<"dt-top-container"<"dt-left-in-div"f><"dt-center-in-div"l><"dt-right-in-div"B>>rt<ip>',
                buttons: [
                    'colvis', 
                    'copy', 
                    {
                        extend: 'csv',
                    },
                    {
                        extend: 'excel',
                    },
                    {
                        extend: 'pdf',
                    },
               
                    'print'],
                language: {
                    search: 'Search: ',
                    lengthMenu: 'Show _MENU_ entries',
                },
                pageLength: 10
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
                    text: "Do you really want to delete this card?",
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
                            $this.closest('tr').fadeOut(500, function() {
                                $(this).remove();
                            })
                        })
                    }
                })
            })
            
        });
    </script>
@endsection
