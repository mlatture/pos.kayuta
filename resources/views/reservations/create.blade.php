@extends('layouts.admin')

@section('title', 'Create Reservation')
@section('content-header', 'Create Reservation')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('reservations.store') }}" method="post">
                @csrf
                <x-forms.input label="Customer" type="select"
                   :required="true" placeholder="Select Customer"
                   input-name="customer_id" input-id="customer_id" :value="old('customer_id')"
                   :options="$customers->map(fn($customer) => ['value' => $customer->id,'label' => $customer->full_name])->toArray()" />
                <a href="javascript:void(0)" onclick="openCustomerModal()">Add New Customer?</a>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <x-forms.input label="Check in Date" type="date" :required="true" placeholder="Select Check In Date"
                           input-name="cid" input-id="cid" :value="old('cid')"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-forms.input label="Check out Date" type="date" :required="true" placeholder="Select Check Out Date"
                           input-name="cod" input-id="cod" :value="old('cod')"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-forms.input label="Site Class" type="select"
                           :required="true" placeholder="Select Site Class"
                           input-name="siteclass" input-id="siteclass" :value="old('siteclass')"
                           :options="$classes->map(fn($class) => ['value' => str_replace(' ','_',$class->siteclass),'label' => $class->siteclass])->toArray()" />
                    </div>
                </div>
                <div class="row" id="rv_sites_div" >
                    <div class="col-md-6">
                        <x-forms.input label="Rig Length" input-name="riglength" input-id="riglength"
                           :value="old('riglength')" :errors="$errors->get('riglength')" placeholder="Enter Rig Length" type="number" :number-min="3" step="1" />
                    </div>
                    <div class="col-md-6">
                        <x-forms.input label="Hookup" input-name="hookup" input-id="hookup"
                           :value="old('hookup')" :errors="$errors->get('hookup')" placeholder="Select Hookup"
                           type="select" :options="$hookups->map(fn($hookup) => ['value' => $hookup->sitehookup,'label' => $hookup->sitehookup])->toArray()"/>
                    </div>
                </div>
                <button class="btn btn-success btn-block btn-lg" type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="modal fade customer--modal" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Create Customer</h5>
                </div>
                <div class="modal-body">
                        <div class="alert alert-danger d-none"></div>
                        <div class="alert alert-success d-none"></div>
                    <form id="customerForm" method="post" action="{{ route('customers.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" class="form-control"
                                   id="first_name"
                                   placeholder="First Name" required>
                        </div>
                        <input type="hidden" name="is_modal" value="1">
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" class="form-control"
                                   id="last_name"
                                   placeholder="Last Name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control"
                                   id="email"
                                   placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Contact Number</label>
                            <input type="text" name="phone" class="form-control"
                                   id="phone"
                                   placeholder="Contact Number" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" name="address" class="form-control"
                                   id="address"
                                   placeholder="Address" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveCustomer(this)">Submit</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        $(function(){
           $('.select2-input').select2();
        });
        let rvSiteClass = '{{str_replace(' ','_',$classes[0]->siteclass)}}';

        window.onload = function(){
            $("#siteclass").val(rvSiteClass).trigger('change');
        }

        $("#siteclass").on('change',function(e){
            if($(this).val() === rvSiteClass) {
                $("#rv_sites_div").removeClass('d-none');
            }
            else {
                $("#rv_sites_div").addClass('d-none');
            }
        });

        function openCustomerModal(){
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $('input').removeClass('is-invalid');
            $('.error--msg').remove();
            $('.customer--modal').modal('toggle');
        }

        function closeModal(){
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text();
            $('input').removeClass('is-invalid');
            $('.error--msg').remove();
            $('.customer--modal').modal('hide');
        }

        function saveCustomer(input){
            $(input).attr('disabled', true);
            $('input').removeClass('is-invalid');
            $('.error--msg').remove();
            $('.alert-success').addClass('d-none').text('');
            $('.alert-danger').addClass('d-none').text('');
            $.post($('#customerForm').attr('action'), $('#customerForm').serialize()).done(function(res) {
                if(res.status == "success"){
                    var newOption = $('<option>', {
                        value: res.data.id,
                        text: res.data.f_name+' '+res.data.l_name
                    });
                    $('#customer_id').find('option').eq(1).before(newOption);
                    $('#customer_id').val(res.data.id).trigger('change');
                    $('.alert-success').removeClass('d-none').text(res.message);
                    setTimeout(function(){
                        $('.customer--modal').modal('hide');
                        $('.alert-success').addClass('d-none').text('');
                        $('.alert-danger').addClass('d-none').text('');
                    }, 1500);
                }else{
                    $(input).attr('disabled', false);
                    $('.alert-danger').removeClass('d-none').text(res.message)
                }
            })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    $(input).attr('disabled', false);
                    if (jqXHR.status === 422) {
                        $.each(jqXHR.responseJSON.errors, function(k,v){
                           $(`#${k}`).addClass('is-invalid').after(`<span class="error--msg" role="alert"><strong class="text-danger">${v[0]}</strong></span>`);
                        });
                    }
                });
        }

    </script>
@endsection
