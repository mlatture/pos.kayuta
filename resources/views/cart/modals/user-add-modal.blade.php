
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addUserModalLabel">Add User</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.input label="First name" input-id="f_name" input-name="f_name" :value="old('f_name')" :errors="$errors->get('f_name')" placeholder="Enter First Name" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input label="Last name" input-id="l_name" input-name="l_name" :value="old('l_name')" :errors="$errors->get('l_name')" placeholder="Enter Last Name" :required="true" />
                        </div>
                    </div>

                    <x-forms.input label="Email" input-id="email" input-name="email" :value="old('email')" :errors="$errors->get('email')" placeholder="Enter Email" :required="true" type="email" />

                    <x-forms.input label="Password" input-id="password" input-name="password" :value="old('password')" :errors="$errors->get('password')" placeholder="Enter Password" :required="true" type="password" />
                    <button class="btn btn-success btn-block btn-lg" type="submit">Submit</button>
                </form>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div> --}}
        </div>
    </div>
</div>