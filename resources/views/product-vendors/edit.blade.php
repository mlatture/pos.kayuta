@extends('layouts.admin')

@section('title', 'Edit Product Vendor')
@section('content-header', 'Edit Product Vendor')

@section('content')

    <div class="card">
        <div class="card-body">
            <!-- Log on to codeastro.com for more projects -->
            <form action="{{ route('product-vendors.update',$productVendor) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <x-forms.input label="Name" :required="true" input-name="name" input-id="name"
                               placeholder="Enter name" :errors="$errors->get('name')" :value="old('name', $productVendor->name)" />

                <x-forms.input label="Address 1" :required="true" input-name="address_1" input-id="address_1"
                               placeholder="Enter address 1" :errors="$errors->get('address_1')" :value="old('address_1', $productVendor->address_1)" />

                <x-forms.input label="Address 2" :required="false" input-name="address_2" input-id="address_2"
                               placeholder="Enter address 2" :errors="$errors->get('address_2')" :value="old('address_2', $productVendor->address_2)" />

                <x-forms.input label="City" :required="true" input-name="city" input-id="city"
                               placeholder="Enter city" :errors="$errors->get('city')" :value="old('city', $productVendor->city)" />

                <x-forms.input label="State" :required="true" input-name="state" input-id="state"
                               placeholder="Enter State" :errors="$errors->get('state')" :value="old('state', $productVendor->state)" />

                <x-forms.input label="Zip" :required="true" input-name="zip" input-id="zip"
                               placeholder="Enter zip" :errors="$errors->get('zip')" :value="old('zip', $productVendor->zip)" />

                <x-forms.input label="Country" :required="true" input-name="country" input-id="country"
                               placeholder="Enter country" :errors="$errors->get('country')" :value="old('country', $productVendor->country)" />

                <x-forms.input label="Contact Name" :required="true" input-name="contact_name" input-id="contact_name"
                               placeholder="Enter contact name" :errors="$errors->get('contact_name')" :value="old('contact_name', $productVendor->contact_name)" />

                <x-forms.input label="Email" type="email" :required="true" input-name="email" input-id="email"
                               placeholder="Enter email" :errors="$errors->get('email')" :value="old('email', $productVendor->email)" />

                <x-forms.input label="Work Phone" type="tel" :required="false" input-name="work_phone" input-id="work_phone"
                               placeholder="Enter work phone" :errors="$errors->get('work_phone')" :value="old('work_phone', $productVendor->work_phone)" />

                <x-forms.input label="Mobile Phone" type="tel" :required="false" input-name="mobile_phone" input-id="mobile_phone"
                               placeholder="Enter mobile phone" :errors="$errors->get('mobile_phone')" :value="old('mobile_phone', $productVendor->mobile_phone)" />

                <x-forms.input label="Fax" :required="false" input-name="fax" input-id="fax"
                               placeholder="Enter fax" :errors="$errors->get('fax')" :value="old('fax', $productVendor->fax)" />

                <x-forms.input label="Notes" :required="false" input-name="notes" input-id="notes" type="textarea"
                               placeholder="Enter Notes" :errors="$errors->get('notes')" :value="old('notes', $productVendor->notes)" />

                <button class="btn btn-success btn-block btn-lg" type="submit">Submit</button>
            </form><!-- Log on to codeastro.com for more projects -->
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
