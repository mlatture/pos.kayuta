
<div class="modal fade" id="addRegisterModal" tabindex="-1" aria-labelledby="addRegisterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addRegisterModalLabel">Add User</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <x-forms.input label="Last name" input-id="l_name" input-name="l_name" :value="old('l_name')" :errors="$errors->get('l_name')" placeholder="Enter Last Name" :required="true" />

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