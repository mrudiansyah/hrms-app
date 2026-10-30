

<?php $__env->startSection('Contents'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Overtime Report <small>Detail</small></h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(url('/Dashboard')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Overtime Report</li>
            </ol>
        </section>

        <section class="content">
            <style>
                .overtime-filter {
                    border-top: 3px solid #0f766e;
                }

                .overtime-filter .box-header {
                    padding: 14px 18px;
                }

                .overtime-filter .box-title {
                    font-size: 16px;
                    font-weight: 600;
                }

                .overtime-filter .box-title i {
                    margin-right: 7px;
                    color: #0f766e;
                }

                .overtime-filter .box-body {
                    padding: 18px;
                    background: #f7fafb;
                }

                .overtime-filter-grid {
                    display: grid;
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                    gap: 16px;
                }

                .overtime-filter-field {
                    min-width: 0;
                }

                .overtime-filter-field label {
                    display: block;
                    margin-bottom: 6px;
                    color: #34495e;
                    font-size: 12px;
                    font-weight: 600;
                }

                .overtime-filter-field .form-control {
                    width: 100%;
                    height: 38px;
                    border-color: #d6e0e4;
                    border-radius: 4px;
                    box-shadow: none;
                    transition: border-color .15s ease, box-shadow .15s ease;
                }

                .overtime-filter-field .form-control:focus {
                    border-color: #0f766e;
                    box-shadow: 0 0 0 2px rgba(15, 118, 110, .12);
                }

                .overtime-filter-actions {
                    grid-column: 1 / -1;
                    display: flex;
                    justify-content: flex-end;
                    gap: 8px;
                    padding-top: 14px;
                    border-top: 1px solid #e3eaed;
                }

                .overtime-filter-actions .btn {
                    min-width: 106px;
                    height: 36px;
                    border-radius: 4px;
                }

                .overtime-filter-actions .btn-apply {
                    border-color: #0f766e;
                    background: #0f766e;
                    color: #fff;
                }

                .overtime-filter-actions .btn-apply:hover,
                .overtime-filter-actions .btn-apply:focus {
                    border-color: #115e59;
                    background: #115e59;
                    color: #fff;
                }

                @media (max-width: 991px) {
                    .overtime-filter-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                    }
                }

                @media (max-width: 540px) {
                    .overtime-filter .box-body {
                        padding: 14px;
                    }

                    .overtime-filter-grid {
                        grid-template-columns: minmax(0, 1fr);
                        gap: 12px;
                    }

                    .overtime-filter-actions {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                    }

                    .overtime-filter-actions .btn {
                        width: 100%;
                    }
                }
            </style>

            <div class="box box-default overtime-filter">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-sliders"></i> Report Filters</h3>
                </div>
                <div class="box-body">
                    <form action="<?php echo e(url('/Overtimes/Report')); ?>" method="GET">
                        <div class="overtime-filter-grid">
                            <div class="overtime-filter-field">
                            <label for="periode">OT Period</label>
                            <select name="periode" id="periode" class="form-control">
                                <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $periodOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($periodOption); ?>" <?php echo e($period == $periodOption ? 'selected' : ''); ?>><?php echo e($periodOption); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <?php if($departments->count() > 1): ?>
                            <div class="overtime-filter-field">
                                <label for="dept_id">Department</label>
                                <select name="dept_id" id="dept_id" class="form-control">
                                    <option value="">All authorized departments</option>
                                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($department->dept_id); ?>" <?php echo e((string) $departmentFilter === (string) $department->dept_id ? 'selected' : ''); ?>>
                                            <?php echo e($department->dept_code); ?> - <?php echo e($department->dept_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="overtime-filter-field">
                            <label for="category_ot">Category</label>
                            <select name="category_ot" id="category_ot" class="form-control">
                                <option value="">All categories</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($categoryOption); ?>" <?php echo e($category === $categoryOption ? 'selected' : ''); ?>><?php echo e($categoryOption); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="overtime-filter-field">
                            <label for="detail_category">Detail</label>
                            <select name="detail_category" id="detail_category" class="form-control">
                                <option value="">All details</option>
                                <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detailOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($detailOption); ?>" <?php echo e($detailCategory === $detailOption ? 'selected' : ''); ?>><?php echo e($detailOption); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="overtime-filter-field">
                            <label for="employee">Employee</label>
                            <select name="employee" id="employee" class="form-control">
                                <option value="">All employees</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employeeOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($employeeOption->NIK); ?>" <?php echo e($employee === (string) $employeeOption->NIK ? 'selected' : ''); ?>>
                                        <?php echo e($employeeOption->NIK); ?> - <?php echo e($employeeOption->employee_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="overtime-filter-field">
                            <label for="sort_by">Sort by</label>
                            <select name="sort_by" id="sort_by" class="form-control">
                                <?php $__currentLoopData = $sortOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sortKey => $sortLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sortKey); ?>" <?php echo e($sortBy === $sortKey ? 'selected' : ''); ?>><?php echo e($sortLabel); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="overtime-filter-field">
                            <label for="direction">Order</label>
                            <select name="direction" id="direction" class="form-control">
                                <option value="desc" <?php echo e($sortDirection === 'desc' ? 'selected' : ''); ?>>Descending</option>
                                <option value="asc" <?php echo e($sortDirection === 'asc' ? 'selected' : ''); ?>>Ascending</option>
                            </select>
                        </div>

                            <div class="overtime-filter-actions">
                                <a href="<?php echo e(url('/Overtimes/Report')); ?>" class="btn btn-default">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                                <button type="submit" class="btn btn-apply">
                                    <i class="fa fa-filter"></i> Apply filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list"></i> Overtime Details</h3>
                    <div class="box-tools">
                        <span class="label label-default"><?php echo e(number_format($rows->total())); ?> records</span>
                    </div>
                </div>
                <div class="box-body">
                    <?php if($departments->isEmpty()): ?>
                        <div class="alert alert-warning">No department access is assigned to your account.</div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table id="overtime-report-table" class="table table-bordered table-striped table-hover table-condensed">
                            <thead>
                                <tr>
                                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th><?php echo e(ucwords(str_replace('_', ' ', $column))); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td><?php echo e($column === 'ammount' && !$canSeeAmountDetails ? '-' : data_get($row, $column, '-')); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="<?php echo e(count($columns)); ?>" class="text-center">No overtime data found for these filters.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th colspan="9">Total</th>
                                    <th><?php echo e(number_format((float) $totalHours, 2)); ?></th>
                                    <th><?php echo e(number_format((float) $totalAmount, 0)); ?></th>
                                    <th colspan="<?php echo e(count($columns) - 11); ?>"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php echo e($rows->links()); ?>

                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
    <script>
        $(function () {
            $('#overtime-report-table').DataTable({
                paging: false,
                searching: false,
                ordering: false,
                info: false,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'excel', 'print']
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\admin\m_overtime\report.blade.php ENDPATH**/ ?>