@extends('layouts.admin')

@section('title', 'Customer Management')
@section('content-header', 'Customer Management')
@section('content-actions')
    @hasPermission(config('constants.role_modules.create_customers.value'))
        <a href="{{ route('customers.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Customer</a>
    @endHasPermission
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
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
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- Edit Seasonal Rates Modal -->
                    <div class="modal fade" id="seasonalModal" tabindex="-1">
                        <div class="modal-dialog">
                            <form id="seasonalForm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Seasonal Rates</h5>

                                        <div id="seasonalDiscounts" class="ms-3"></div>



                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <select name="seasonal[]" id="seasonalSelect" multiple class="form-select select2"
                                            style="width: 100%">
                                            @foreach (\App\Models\SeasonalRate::where('active', true)->get() as $rate)
                                                <option value="{{ $rate->id }}">{{ $rate->rate_name }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="user_id" id="selectedUserId">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>


                    <div class="row">
                        <div class="table-responsive m-t-40 p-0">

                            <table class="display nowrap table table-hover table-striped border p-0 customersTable"  cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Seasonal</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- {{ $customers->links() }} --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('customers.index') }}",
                    data: function(d) {
                        d.only_seasonal = $('#onlySeasonalFilter').is(':checked') ? 1 : 0;
                    }
                },


                columns: [{
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'full_name',
                        name: 'name'
                    },
                    
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'street_address',
                        name: 'street_address'
                    },
                    {
                        data: 'seasonal_names',
                        name: 'seasonal_names'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                ],
                responsive: true,
                dom: '<"dt-top-container d-flex justify-content-between align-items-center mb-3"' +
                    '<"dt-left-in-div"f>' +
                    '<"dt-center-in-div custom-filter-checkbox">' +
                    '<"dt-right-in-div"B>' +
                    '>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
                buttons: ['colvis', 'copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    search: 'Search: ',
                    lengthMenu: 'Show _MENU_ entries'
                },
                pageLength: 10
            });

            // Tooltip
            $('.table').on('draw.dt', function () {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    new bootstrap.Tooltip(this);
                });
            });


            $('.custom-filter-checkbox').html(`
                <label class="form-check-label mb-0">
                    <input type="checkbox" id="onlySeasonalFilter" class="form-check-input me-2">
                    Only Seasonal
                </label>
            `);
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const onlySeasonalChecked = $('#onlySeasonalFilter').is(':checked');
                if (!onlySeasonalChecked) return true;

                const seasonalCell = document.createElement("div");
                seasonalCell.innerHTML = data[6] || '';
                const plainText = seasonalCell.textContent || seasonalCell.innerText || '';

                return plainText.trim().toLowerCase() !== 'none';
            });

            $('#onlySeasonalFilter').on('change', function() {
                $('.table').DataTable().ajax.reload();
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
                    text: "Do you really want to delete this customer?",
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
            });

            let selectedUserId = null;
            let updateSeasonalUrl = '{{ route('customers.updateSeasonal', ['user' => '__USER_ID__']) }}';

            $(document).on('click', '.edit-seasonal', function() {
                selectedUserId = $(this).data('id');
                const selected = $(this).data('selected').toString().split(',');
                $('#seasonalSelect').val(selected).trigger('change');

                $('#seasonalDiscounts').html('');

                let discounts = $(this).data('discounts');

                // Ensure JSON string is parsed if needed
                if (typeof discounts === 'string') {
                    try {
                        discounts = JSON.parse(discounts);
                    } catch (e) {
                        discounts = [];
                    }
                }

                if (discounts && discounts.length > 0) {
                    discounts.forEach(d => {
                        let type = (d.type && d.type.value) ? d.type.value : d.type;
                        let symbol = '';

                        if (type === 'percentage') symbol = '%';
                        else if (type === 'dollar') symbol = '$';

                        $('#seasonalDiscounts').append(
                            `<span class="badge bg-info text-dark mx-1">${type}: ${symbol}${d.value}</span>`
                        );
                    });
                } else {
                    $('#seasonalDiscounts').append('<span class="text-muted">No discounts</span>');
                }

                $('#seasonalModal').modal('show');
            });


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });


            $('#seasonalForm').submit(function(e) {
                e.preventDefault();

                const finalUrl = updateSeasonalUrl.replace('__USER_ID__', selectedUserId);

                $.post(finalUrl, $(this).serialize(), function(res) {
                    if (res.success) {
                        $('#seasonalModal').modal('hide');

                        $.toast({
                            heading: 'Success',
                            text: res.message,
                            icon: 'success',
                            position: 'bottom-left',
                            hideAfter: 3000,
                            stack: 3
                        });

                        $('.table').DataTable().ajax.reload(null, false);
                    }
                });
            });

            $('#seasonalModal').on('shown.bs.modal', function() {
                $('#seasonalSelect').select2({
                    dropdownParent: $('#seasonalModal'),
                    width: '100%',
                    placeholder: "Select seasonal rates"
                });
            });


        })
    </script>
@endsection
