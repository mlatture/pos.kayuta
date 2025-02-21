<header class="reservation__head bg-dark  mb-2">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
        <a href="javascript:void(0)" class="text-white text-decoration-none fs-5">
            Point Of Sale
        </a>
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <div class="dropdown" id="registerDropdownContainer">
                <button class="btn btn-dark text-white dropdown-toggle" type="button" id="registerDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Station: {{ session('current_register_name', 'Select Register') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="registerDropdown">
                    @foreach ($registers as $register)
                        <a class="dropdown-item" href="#"
                            onclick="setRegister({{ $register->id }}, '{{ $register->name }}')">{{ $register->name }}</a>
                    @endforeach
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" id="addNewStationRegister">Add new Register</a>
                    <a class="dropdown-item" href="#" id="renameRegister">Rename Register</a>


                    {{-- @if (\App\CPU\Helpers::module_permission_check('admin'))
                       
                    @endif --}}
                </div>
            </div>

            <button class="btn btn-dark text-white new-sale" id="new-sale" type="button">
                <i class="fa-solid fa-cart-arrow-down "></i> New Sale
            </button>
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle text-white" type="button" id="actionsDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <ul class="dropdown-menu" aria-labelledby="actionsDropdown">
                    <li>

                        @hasPermission(config('constants.role_modules.orders.value'))
                            <a class="dropdown-item" href="{{ route('orders.index') }}">
                                <i class="fa-solid fa-up-right-from-square"></i>
                                Process Return
                            </a>
                        @endHasPermission
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="fa-solid fa-cash-register"></i>
                            Open Cash Drawer
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            Paid In/Out
                        </a>
                    </li>
                </ul>
            </div>
            <button class="btn btn-dark text-white">
                <i class="fa-solid fa-hand"></i> Held Orders
            </button>
            <button class="btn btn-dark text-white">
                <i class="fa-solid fa-bars-progress"></i> In Progress
            </button>
            @hasPermission(config('constants.role_modules.orders.value'))
                <a href="{{ route('orders.index') }}" class="btn btn-dark text-white">
                    <i class="nav-icon fas fa-box me-2"></i> History
                </a>
            @endHasPermission
            <a href="#" class="btn btn-dark text-white">
                <img src="{{ asset('images/help-ico.svg') }}" alt="Help Icon" class="me-2" />
                Help
            </a>
        </div>
    </div>
</header>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.setRegister = function(registerId, registerName) {
        $.ajax({
            url: '{{ route('registers.set') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                register_id: registerId,
                register_name: registerName
            },
            success: function() {
                let registerData = JSON.parse(localStorage.getItem("current_register_data")) || [];
                if (!Array.isArray(registerData)) {
                    registerData = [];
                }
                let existingEntry = registerData.find(entry => entry.current_register_staff_id ===
                    {{ auth()->user()->id }});

                if (!existingEntry) {
                    registerData.push({
                        current_register_id: registerId,
                        current_register_staff_id: {{ auth()->user()->id }}
                    });
                    localStorage.setItem("current_register_data", JSON.stringify(registerData));
                }
                $('#registerDropdown').text('Station: ' + registerName);
                $('#myTabContent').show();
            }
        });
    };


    $(document).ready(function() {
        function enforceRegisterSelection() {
            let registerData = JSON.parse(localStorage.getItem("current_register_data"));

            if (!registerData || !Array.isArray(registerData) || registerData.length === 0) {
                $('#registerDropdown').addClass('btn-warning text-dark').text('⚠️ Select Register');
                Swal.fire({
                    title: 'Select a Register',
                    text: 'You must select a register before proceeding.',
                    icon: 'warning',
                    allowOutsideClick: true, // Allow dismissal
                    allowEscapeKey: true,
                    confirmButtonText: 'OK',
                }).then((result) => {
                    console.log('Swal Result:', result);

                    if (result.isConfirmed) { // If user dismisses modal
                        let dropdownElement = document.getElementById("registerDropdownContainer");

                        if (dropdownElement) {
                            Swal.fire({
                                title: 'Choose a Register',
                                html: dropdownElement.innerHTML,
                                showCancelButton: false,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    document.querySelectorAll(".dropdown-item").forEach(
                                        item => {
                                            item.addEventListener("click", function() {
                                              
                                                Swal.close();
                                            });
                                        });
                                }

                            })
                        } else {
                            console.error("Element #registerDropdownContainer not found.");
                        }
                    }
                });


                $('#myTabContent').hide();

            } else {
                $('#registerDropdown').removeClass('btn-warning text-dark').addClass('btn-dark text-white');
                $('#myTabContent').show();
            }
        }

        enforceRegisterSelection();


        $('#addNewStationRegister').click(function() {
            Swal.fire({
                title: 'Register New Station',
                input: 'text',
                inputLabel: 'Enter Register Name',
                showCancelButton: true,
                confirmButtonText: "Save",
                cancelButtonText: "Don't save",
                showLoaderOnConfirm: true,
                preConfirm: (registerName) => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: "{{ route('registers.create') }}",
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]')
                                    .attr("content"),
                            },
                            data: {
                                name: registerName
                            },
                            success: function(response) {
                                resolve(response);
                            },
                            error: function(xhr) {
                                reject(xhr.responseJSON);
                            }
                        });
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && result.value.success) {
                    Swal.fire('Success', 'New register created successfully', 'success');
                    location.reload();
                }
            }).catch((error) => {
                Swal.fire('Error', 'Failed to create new register: ' + error.message, 'error');
            });
        });

        $('#renameRegister').click(function() {
            let currentRegister = $('#registerDropdown').text().replace('Station: ', '').trim();
            let registerData = JSON.parse(localStorage.getItem("current_register_data")) || [];
            let currentRegisterId = null;

            if (Array.isArray(registerData) && registerData.length > 0) {
                currentRegisterId = registerData.find(entry => entry.current_register_staff_id ===
                        {{ auth()->user()->id }})?.current_register_id || registerData[0]
                    .current_register_id;
            }

            console.log("Current Register ID:", currentRegisterId);

            if (currentRegister === "Select Register" || currentRegister === "⚠️ Select Register") {
                Swal.fire({
                    title: 'No Register Selected',
                    text: 'Please select a register before renaming.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Rename Register',
                input: 'text',
                inputLabel: 'Enter New Register Name',
                inputValue: currentRegister,
                showCancelButton: true,
                confirmButtonText: "Save",
                cancelButtonText: "Cancel",
                showLoaderOnConfirm: true,
                preConfirm: (newName) => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: "{{ route('registers.rename') }}",
                            type: "PUT",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]')
                                    .attr("content"),
                            },
                            data: {
                                name: newName,
                                id: currentRegisterId
                            },
                            success: function(response) {
                                resolve(response);
                            },

                            error: function(xhr) {
                                reject(xhr.responseJSON);
                            }
                        });
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && result.value.success) {
                    Swal.fire('Success', 'Register renamed successfully', 'success');
                    location.reload();

                }

            }).catch((error) => {
                Swal.fire('Error', 'Failed to rename register: ' + error.message, 'error');
            });
        });
    });
</script>
