<?php $__env->startSection('title', "{$formattedTable} Management"); ?>
<?php $__env->startSection('content-header', "{$formattedTable} Management"); ?>
<?php $__env->startSection('content-actions'); ?>
    <?php if(auth()->user()->hasPermission("read_{$table}")): ?>
        <a href="<?php echo e(route('admin.whitelist')); ?>"
           class="btn btn-primary"><i
                class="fas fa-arrow-circle-left"></i> Back to whitelist</a>
        <a href="<?php echo e(route('admin.dynamic-module-create-form-data', $table)); ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New <?php echo e($formattedTable); ?>

        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('plugins/sweetalert2/sweetalert2.min.css')); ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row animated fadeInUp">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive m-t-40 p-3">
                            <?php
                                foreach ($columns as $column) {
                                    if (!isset($dictionaryFields[$column])) {
                                        $dictionaryFields[$column] = [
                                            'field_name' => $column,
                                            'order' => PHP_INT_MAX,
                                            'display_name' => ucfirst(str_replace('_', ' ', $column))
                                        ];
                                    }
                                    if ($column === 'created_at') {
                                        $dictionaryFields[$column]['display_name'] = 'Created';
                                    }
                                    if ($column === 'updated_at') {
                                        $dictionaryFields[$column]['display_name'] = 'Updated';
                                    }
                                }

                                $fieldsArray = array_values($dictionaryFields);
                                usort($fieldsArray, function ($a, $b) {
                                    return ($a['order'] <=> $b['order']);
                                });
                                $orderedKeys = array_map(function ($field) {
                                    return $field['field_name'];
                                }, $fieldsArray);
                                $orderedKeys = array_diff($orderedKeys, ['id']);
                            ?>

                            <table class="table table-hover table-striped border">
                                <thead>
                                <tr>
                                    <th></th>
                                    <?php $__currentLoopData = $orderedKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th><?php echo e($dictionaryFields[$key]['display_name'] ?? $key); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php if(isset($record->id)): ?>
                                                <?php if(auth()->user()->hasPermission("read_{$table}")): ?>
                                                    <a title="Edit <?php echo e($table); ?> record"
                                                       href="<?php echo e(route('admin.dynamic-module-create-form-data', [$table, $record->id])); ?>"
                                                       class="btn btn-sm btn-primary"><i
                                                            class="fas fa-edit"></i></a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <?php $__currentLoopData = $orderedKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td>
                                                <?php echo e($record->{$key} ?? '-'); ?>

                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
    <script src="<?php echo e(asset('plugins/sweetalert2/sweetalert2.min.js')); ?>"></script>
    <script>
        $(document).ready(function () {
            $('.table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
            $(document).on('click', '.btn-delete', function () {
                const $this = $(this);
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to delete this product?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.post($this.data('url'), {
                            _method: 'DELETE',
                            _token: '<?php echo e(csrf_token()); ?>'
                        }, function (res) {
                            $this.closest('tr').fadeOut(500, function () {
                                $(this).remove();
                            });
                        });
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\herd\pos.kayuta\resources\views/dynamic-tables/module/listing.blade.php ENDPATH**/ ?>