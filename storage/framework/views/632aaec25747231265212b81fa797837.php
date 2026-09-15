
<?php $__env->startSection('Contents'); ?>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Outside Assignment<small>Add New Assignment</small></h1>
		</section>

		<section class="content">
		<div class="row">
            <form role="form" action="<?php echo e(url('/outside')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Header Information</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="book_id">Book ID</label>
                                <input type="number" class="form-control" name="book_id" value="<?php echo e(old('book_id')); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" value="<?php echo e(old('tanggal', date('Y-m-d'))); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="nopol">No Polisi (License Plate)</label>
                                <input type="text" class="form-control" name="nopol" value="<?php echo e(old('nopol')); ?>" required placeholder="e.g. B 1234 ABC">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary btn-block">Submit Assignment</button>
                            <a href="<?php echo e(url('/outside')); ?>" class="btn btn-default btn-block">Cancel</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Select Employees</h3>
                        </div>
                        <div class="box-body" style="max-height: 600px; overflow-y: auto;">
                            <table id="empTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:30px;"><input type="checkbox" id="checkAll"></th>
                                        <th>NIK</th>
                                        <th>Name</th>
                                        <th>Dept</th>
                                        <th>Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><input type="checkbox" name="employees[]" value="<?php echo e($emp->id_employee); ?>" class="emp-checkbox"></td>
                                        <td><?php echo e($emp->NIK); ?></td>
                                        <td><?php echo e($emp->nama_karyawan); ?></td>
                                        <td><?php echo e($emp->department); ?></td>
                                        <td><?php echo e($emp->jabatan); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
		</div>
		</section>
  	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
<script>
    $(document).ready(function() {
        var table = $('#empTable').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false
        });

        $('#checkAll').on('click', function() {
            var rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        $('form').on('submit', function(e) {
            var form = this;
            // Iterate over all checkboxes in the table (including those not in DOM if paging was on, but also handles filtered rows better)
            table.$('input.emp-checkbox:checked').each(function() {
                if(!$.contains(document, this)) {
                    $(form).append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'employees[]')
                            .val($(this).val())
                    );
                }
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\outside\create.blade.php ENDPATH**/ ?>