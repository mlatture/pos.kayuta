<?php $__env->startSection('title', 'Whitelist Management'); ?>
<?php $__env->startSection('content-header', 'Whitelist Management'); ?>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <button id="addButton" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Table</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive m-t-40 p-3">
                            <table class="table table-hover table-striped border">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Table Name <i class="fa fa-sort" aria-hidden="true"></i></th>
                                    <th>Can Read?</th>
                                    <th>Can Update?</th>
                                    <th>Can Delete?</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $whitelists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $whitelist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($whitelist->id); ?></td>
                                        <td><?php echo e($whitelist->table_name); ?></td>
                                        <?php
                                            $allowRead = auth()->user()->hasPermission("read_{$whitelist->table_name}");
                                            $allowUpdate = auth()->user()->hasPermission("update_{$whitelist->table_name}");
                                            $allowDelete = auth()->user()->hasPermission("delete_{$whitelist->table_name}");
                                        ?>
                                        <td><span class="badge <?php echo e($allowRead ? 'badge-success' : 'badge-danger'); ?>">
                                                    <?php echo e($allowRead ? 'Yes' : 'No'); ?>

                                                </span>
                                        </td>
                                        <td><span class="badge <?php echo e($allowUpdate ? 'badge-success' : 'badge-danger'); ?>">
                                                    <?php echo e($allowUpdate ? 'Yes' : 'No'); ?>

                                                </span>
                                        </td>
                                        <td><span class="badge <?php echo e($allowRead ? 'badge-success' : 'badge-danger'); ?>">
                                                    <?php echo e($allowDelete ? 'Yes' : 'No'); ?>

                                                </span>
                                        </td>
                                        <td>
                                            <?php if(auth()->user()->hasPermission("read_{$whitelist->table_name}")): ?>
                                                <a title="View <?php echo e($whitelist->table_name); ?> data"
                                                   href="<?php echo e(route('admin.dynamic-module-records', $whitelist->table_name)); ?>"
                                                   class="btn btn-primary"><i
                                                        class="fas fa-eye"></i></a>
                                            <?php endif; ?>
                                            <?php if(auth()->user()->hasPermission("update_{$whitelist->table_name}")): ?>
                                                <a title="Edit <?php echo e($whitelist->table_name); ?> table"
                                                   href="<?php echo e(route('admin.edit-table', $whitelist->table_name)); ?>"
                                                   class="btn btn-primary"><i
                                                        class="fas fa-edit"></i></a>
                                            <?php endif; ?>
                                            <?php if(auth()->user()->hasPermission("delete_{$whitelist->table_name}")): ?>
                                                <a title="Delete <?php echo e($whitelist->table_name); ?> table" href="#"
                                                   class="btn btn-danger delete-table"
                                                   data-url="<?php echo e(route('admin.delete-table', $whitelist->table_name)); ?>">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(function () {
            $('.table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $(document).on('click', '.delete-table', function (event) {
                event.preventDefault();
                const url = $(this).data('url');

                Swal.fire({
                    title: "Are you sure?",
                    text: "All the related settings will be deleted!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'delete',
                            headers: {
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            },
                            success: function (data) {
                                $('#deleteModal').modal('hide');
                                toastr.success(data.message);
                                setTimeout(() => location.reload(), 2000);
                            },
                            error: function (xhr) {
                                toastr.error(xhr.responseJSON.message);
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#addButton').on('click', function(e) {
                let remainingTables = <?php echo $remainingTablesJson; ?>;
                let inputOptions = {};
                for (let key in remainingTables) {
                    if (remainingTables.hasOwnProperty(key)) {
                        inputOptions[key] = remainingTables[key];  // Key-Value pairs
                    }
                }
                e.preventDefault();
                Swal.fire({
                    title: 'Add New Table',
                    input: 'select',
                    inputOptions: inputOptions,
                    inputPlaceholder: 'Select any table',
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Cancel',
                    preConfirm: (value) => {
                        if (!value) {
                            Swal.showValidationMessage(`Please select an option`);
                        }
                        return value;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?php echo e(route('admin.add-table')); ?>',
                            method: 'POST',
                            data: {
                                _token: '<?php echo e(csrf_token()); ?>',
                                selected_option: result.value
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 2000);
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.message);
                            }
                        });
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\herd\pos.kayuta\resources\views/dynamic-tables/whitelist/index.blade.php ENDPATH**/ ?>