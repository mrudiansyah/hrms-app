
<?php $__env->startSection('Contents'); ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary" style="background:#FFF;">
                <div class="box-header">
                    <h3 class="box-title">Pesangon (Severance Pay)</h3>
                    <div class="pull-right" style="margin-left: 10px; display: flex; align-items: center;">
                        <label for="cutoff" style="margin-right: 5px;">Cutoff: </label>
                        <input type="date" id="cutoff" class="form-control" style="width: 150px;" value="<?php echo e($cutoff); ?>">
                        &nbsp;&nbsp;
                        <button type="button" id="btn-capture" class="btn btn-success btn-md" style="margin-right: 10px;"><i class="fa fa-save"></i> Capture</button>
                    </div>
                </div>
                <div class="box-body" style="overflow: auto;">
                    <table id="tables" class="table table-hover table-bordered">
                        <thead>
                            <tr style="background-color: #f4f4f4;">
                                <th style="width:30px; text-align: center;">No</th>
                                <th style="text-align: center;">NIK</th>
                                <th>Employee Name</th>
                                <th style="text-align: center;">Dept Code</th>
                                <th style="text-align: center;">Date Permanent</th>
                                <th style="text-align: right;">Total Salary</th>
                                <th style="text-align: center;">Year</th>
                                <th style="text-align: center;">Month</th>
                                <th style="text-align: right;">Pesangon</th>
                                <th style="width:30px; text-align: center;"><input type="checkbox" id="check-all"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=0;?>
                            <?php $__currentLoopData = $tb_pesangon; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $d1 = new DateTime($dt->date_permanent);
                                    $d2 = new DateTime($cutoff);
                                    $diff = $d1->diff($d2);
                                    $years = $diff->y;
                                    $months = $diff->m;
                                    
                                    if ($d1 > $d2) {
                                        $years = 0;
                                        $months = 0;
                                    }

                                    $amount_tahun=$years*$dt->total_salary;
                                    $amount_bulan = $months/12*$dt->total_salary;
                                    $amount_total = $amount_tahun+$amount_bulan;
                                ?>
                                <tr>
                                    <td align="center"><?php $no++; echo $no;?></td>
                                    <td align="center"><?php echo e($dt->NIK); ?></td>
                                    <td><?php echo e($dt->employee_name); ?></td>
                                    <td align="center"><?php echo e($dt->dept_code); ?></td>
                                    <td align="center" class="date-permanent"><?php echo e($dt->date_permanent); ?></td>
                                    <td align="right"><?php echo e(number_format($dt->total_salary,0)); ?></td>
                                    <td align="center"><?php echo e($years); ?></td>
                                    <td align="center"><?php echo e($months); ?></td>
                                    <td align="right"><?php echo e(number_format($amount_total,0)); ?></td>
                                    <td align="center">
                                        <input type="checkbox" class="row-checkbox" data-id-employee="<?php echo e($dt->id); ?>" data-salary="<?php echo e($dt->total_salary); ?>" data-amount="<?php echo e($amount_total); ?>">
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
<script>
    $(document).ready(function() {

        $('#btn-capture').on('click', function() {
            var data = [];
            $('.row-checkbox:checked').each(function() {
                var row = $(this).closest('tr');
                data.push({
                    id_employee: $(this).data('id-employee'),
                    date_permanent: row.find('.date-permanent').text().trim(),
                    total_salary: $(this).data('salary'),
                    amount: $(this).data('amount'),
                    cutoff: $('#cutoff').val()
                });
            });

            if (data.length == 0) {
                alert('Please select at least one row.');
                return;
            }

            if(!confirm('Are you sure you want to capture ' + data.length + ' items?')) return;

            $.ajax({
                url: '<?php echo e($site); ?>/Pesangon/Capture',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    items: data
                },
                success: function(response) {
                    if(response.status == 'success'){
                        alert('Success Capture Data');
                        location.reload();
                    }else{
                        alert('Error: ' + response.status);
                    }
                },
                error: function(xhr){
                    alert('Error capturing data: ' + xhr.responseText);
                }
            });
        });

        $('#check-all').on("change", function() {
            $('.row-checkbox').prop('checked', $(this).prop('checked'));
        });
    });

    $('body').on("change","#cutoff",function(){
        var cutoff = document.getElementById('cutoff').value;
        if(cutoff == '') cutoff = 0;
        window.location.href = "<?php echo e($site); ?>/Pesangon/" + cutoff;
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll_payments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/payroll/permanent_list.blade.php ENDPATH**/ ?>