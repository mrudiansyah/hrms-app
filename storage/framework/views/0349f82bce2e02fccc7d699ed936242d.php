
<?php $__env->startSection('Contents'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                FPPK
                <small>Form Permintaan Pengadaan Karyawan</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">FPPK</li>
            </ol>
        </section>

        <section class="content">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <!-- Filter Box -->
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Data FPPK</h3>
                        </div>
                        <div class="box-body">
                            <form action="<?php echo e(url('/FPPK')); ?>" method="GET" class="form-inline"
                                style="display: inline-block;">
                                <div class="form-group">
                                    <label for="start_date">Start Date: </label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="<?php echo e($start_date ?? ''); ?>">
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label for="end_date">End Date: </label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="<?php echo e($end_date ?? ''); ?>">
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label for="department">Department: </label>
                                    <select name="department" id="department" class="form-control">
                                        <option value="">-- All Department --</option>
                                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($dept); ?>" <?php echo e(request('department') == $dept ? 'selected' : ''); ?>>
                                                <?php echo e($dept); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label for="status">Status: </label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">-- All Status --</option>
                                        <option value="Baru" <?php echo e(request('status') == 'Baru' ? 'selected' : ''); ?>>Baru</option>
                                        <option value="Penggantian" <?php echo e(request('status') == 'Penggantian' ? 'selected' : ''); ?>>Penggantian</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
                                    <i class="fa fa-search"></i> Filter
                                </button>
                                <div class="pull-right">
                                    <a href="<?php echo e(url('/FPPK/create')); ?>" class="btn btn-success" style="margin-left: 5px;">
                                        <i class="fa fa-plus"></i> New FPPK
                                    </a>
                                </div>
                            </form>
                            <form action="<?php echo e(url('/FPPK/export')); ?>" method="GET" style="display: inline-block;">
                                <input type="hidden" name="start_date" value="<?php echo e($start_date ?? ''); ?>">
                                <input type="hidden" name="end_date" value="<?php echo e($end_date ?? ''); ?>">
                                <input type="hidden" name="department" value="<?php echo e(request('department')); ?>">
                                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                            </form>
                        </div>
                    </div>

                    <!-- Data Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-list"></i> Form Permintaan Pengadaan Karyawan (FPPK)</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="fppk-table" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>RegNumber</th>
                                            <th>Department</th>
                                            <th>Position</th>
                                            <th>Need</th>
                                            <th>Education</th>
                                            <th>Category</th>
                                            <th>Approval</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $fppkList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td><?php echo e($item->registration_number ?? '-'); ?></td>
                                                <td><?php echo e($item->department ?? '-'); ?></td>
                                                <td><?php echo e($item->position_job ?? '-'); ?></td>
                                                <td><?php echo e(number_format($item->employee_number_needed, 0)); ?></td>
                                                <td><?php echo e($item->min_education ?? '-'); ?></td>
                                                <td>
                                                    <?php if($item->application_status == 'Baru'): ?>
                                                        New
                                                    <?php elseif($item->application_status == 'Penggantian'): ?>
                                                        Replacement
                                                    <?php else: ?>
                                                        <span class="label label-default"><?php echo e($item->application_status); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $approvalStatus = 'Draft';
                                                        if ($item->legalized_by && $item->legalized_date) {
                                                            $approvalStatus = 'Legalized';
                                                        } elseif ($item->approved_by && $item->approved_date) {
                                                            $approvalStatus = 'Approved';
                                                        } elseif ($item->validated_by && $item->validated_date) {
                                                            $approvalStatus = 'Validated';
                                                        } elseif ($item->checked_by && $item->checked_date) {
                                                            $approvalStatus = 'Checked';
                                                        } elseif ($item->supported_by && $item->supported_date) {
                                                            $approvalStatus = 'Supported';
                                                        } elseif ($item->submitted_by && $item->submitted_date) {
                                                            $approvalStatus = 'Submitted';
                                                        }
                                                    ?>
                                                    <?php if($approvalStatus == 'Legalized'): ?>
                                                        <span class="label label-success"><?php echo e($approvalStatus); ?></span>
                                                    <?php elseif($approvalStatus == 'Approved'): ?>
                                                        <span class="label label-primary"><?php echo e($approvalStatus); ?></span>
                                                    <?php elseif($approvalStatus == 'Validated'): ?>
                                                        <span class="label label-info"><?php echo e($approvalStatus); ?></span>
                                                    <?php elseif($approvalStatus == 'Draft'): ?>
                                                        <span class="label label-default"><?php echo e($approvalStatus); ?></span>
                                                    <?php else: ?>
                                                        <span class="label label-warning"><?php echo e($approvalStatus); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="<?php echo e(url('/FPPK/show/' . $item->id)); ?>"
                                                            class="btn btn-xs btn-info" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo e(url('/FPPK/edit/' . $item->id)); ?>"
                                                            class="btn btn-xs btn-warning" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-xs btn-primary" title="Approval"
                                                            onclick="showApprovalModal(<?php echo e($item->id); ?>)">
                                                            <i class="fa fa-check-circle"></i>
                                                        </button>
                                                        <a href="<?php echo e(url('/FPPK/print/' . $item->id)); ?>"
                                                            class="btn btn-xs btn-default" title="Print" target="_blank">
                                                            <i class="fa fa-print"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-xs btn-danger" title="Delete"
                                                            onclick="deleteFppk(<?php echo e($item->id); ?>)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
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
        </section>
    </div>

    <!-- Modal Approval -->
    <div class="modal fade" id="modal-approval" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Approval FPPK</h4>
                </div>
                <form id="approval-form" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body">
                        <input type="hidden" name="fppk_id" id="approval_fppk_id">
                        <div class="form-group">
                            <label>Approval Stage</label>
                            <select name="approval_stage" id="approval_stage" class="form-control" required>
                                <option value="">-- Select Stage --</option>
                                <option value="submitted">Submitted</option>
                                <option value="supported">Supported</option>
                                <option value="checked">Checked</option>
                                <option value="validated">Validated</option>
                                <option value="approved">Approved</option>
                                <option value="legalized">Legalized</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Approver Name</label>
                            <input type="text" name="approver_name" id="approver_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Approval Date</label>
                            <input type="date" name="approval_date" id="approval_date" class="form-control"
                                value="<?php echo e(date('Y-m-d')); ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Approval</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
    <script>
        $(document).ready(function () {
            $('#fppk-table').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'print'
                ]
            });
        });

        function showApprovalModal(id) {
            $('#approval_fppk_id').val(id);
            $('#modal-approval').modal('show');
        }

        $('#approval-form').on('submit', function (e) {
            e.preventDefault();
            var id = $('#approval_fppk_id').val();
            var formData = $(this).serialize();

            $.ajax({
                url: '/FPPK/approve/' + id,
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        $('#modal-approval').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr) {
                    alert('Terjadi kesalahan, silakan coba lagi.');
                }
            });
        });

        function deleteFppk(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data FPPK ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
                $.ajax({
                    url: '/FPPK/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        alert('Terjadi kesalahan, silakan coba lagi.');
                    }
                });
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\recruitment\fppk.blade.php ENDPATH**/ ?>