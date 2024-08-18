@extends('layouts.admin')

@section('title', 'Gift Card Management')
@section('content-header', 'Gift Card Management')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_gift_cards.value'))
    <a href="{{route('gift-cards.create')}}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Gift Card</a>
    @endHasPermission
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
        <table class="table table-bordered table-hover table-responsive">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>User Email</th>
                    <th>Barcode</th>
                    <th>Discount Type</th>
                    <th>Discount</th>
                    <th>Start Date</th>
                    <th>Expiry Date</th>
                    <th>Minimum Purchase</th>
                    <th>Maximum Discount</th>
                    <th>Limit</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($giftCards as $key => $gift)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>{{$gift->title ?? ''}}</td>
                        <td>{{$gift->user_email ?? ''}}</td>
                        <td>{{$gift->barcode ?? ''}}</td>
                        <td>{{$gift->discount_type ? ucwords(str_replace('_', '', $gift->discount_type)) : 'N/A'}}</td>
                        <td>{{$gift->discount ?? 'N/A'}}</td>
                        <td>{{date('Y-m-d', strtotime($gift->start_date))}}</td>
                        <td>{{date('Y-m-d', strtotime($gift->expire_date))}}</td>
                        <td>{{$gift->min_purchase ?? 0}}</td>
                        <td>{{$gift->max_discount ?? 0}}</td>
                        <td>{{$gift->limit ?? 0}}</td>
                        <td>{{$gift->status ? 'Active' : 'Inactive'}}</td>
                        <td>{{$gift->created_at}}</td>
                        <td>
                            @hasPermission(config('constants.role_modules.edit_gift_cards.value'))
                            <a href="{{ route('gift-cards.edit', $gift) }}" class="btn btn-primary"><i
                                    class="fas fa-edit"></i></a>
                            @endHasPermission
                            @hasPermission(config('constants.role_modules.delete_gift_cards.value'))
                            <button class="btn btn-danger btn-delete" data-url="{{route('gift-cards.destroy', $gift)}}"><i
                                    class="fas fa-trash"></i></button>
                            @endHasPermission
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $giftCards->render() }}
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.btn-delete', function () {
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
                    text: "Do you really want to delete this Gift Card?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.post($this.data('url'), {_method: 'DELETE', _token: '{{csrf_token()}}'}, function (res) {
                            $this.closest('tr').fadeOut(500, function () {
                                $(this).remove();
                            })
                        })
                    }
                })
            })
        })
    </script>
@endsection
