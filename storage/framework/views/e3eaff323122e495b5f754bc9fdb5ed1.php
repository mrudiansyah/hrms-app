
<?php $__env->startSection('Contents'); ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary" style="background:#FFF;">
                <div class="box-header">
                    <h3 class="box-title"><?php echo e($status == 0 ? 'New Resignation' : 'Paid Resignation'); ?></h3>
                    <div class="pull-right">
                        <button type="button" class="btn btn-success btn-md" data-toggle="modal" data-target="#modal-add-new"><i class="fa fa-plus"></i> Add New</button>
                        <a href="<?php echo e($site); ?>/Resignation/0" class="btn btn-info btn-md">New Resignation</a>
                        <a href="<?php echo e($site); ?>/Resignation/1" class="btn btn-primary btn-md">Paid Resignation</a>
                    </div>
                </div>
                <div class="box-body" style="overflow: auto;">
                    <table id="tables" class="table table-hover table-bordered">
                        <thead>
                            <tr style="background-color: #f4f4f4;">
                                <th style="width:30px; text-align: center;">No</th>
                                <th style="text-align: center;">NIK</th>
                                <th>Employee Name</th>
                                <th style="text-align: center;">Dept</th>
                                <th style="text-align: center;">Date Permanent</th>
                                <th style="text-align: center;">Cutoff Date</th>
                                <th style="text-align: center;">Tenure</th>
                                <th style="text-align: right;">Base Salary</th>
                                <th style="text-align: right;">Amount</th>
                                <th style="text-align: right;">PPh21</th>
                                <th style="text-align: right;">Paid Amount</th>
                                <?php if($status == 0): ?>
                                <th style="text-align: center;">Action</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=0; $t_amount=0; $t_pph21=0; $t_paid=0;?>
                            <?php $__currentLoopData = $tb_resignation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $t_amount += $dt->amount;
                                    $t_pph21 += $dt->pph21;
                                    $t_paid += $dt->amount_paid;
                                ?>
                                <tr>
                                    <td align="center"><?php $no++; echo $no;?></td>
                                    <td align="center"><?php echo e($dt->nik); ?></td>
                                    <td><?php echo e($dt->employee_name); ?></td>
                                    <td align="center"><?php echo e($dt->department); ?></td>
                                    <td align="center"><?php echo e($dt->date_permanent); ?></td>
                                    <td align="center"><?php echo e($dt->date_cutoff); ?></td>
                                    <td align="center"><?php echo e($dt->tahun); ?> th <?php echo e($dt->bulan); ?> bln</td>
                                    <td align="right"><?php echo e(number_format($dt->total_salary,0)); ?></td>
                                    <td align="right"><?php echo e(number_format($dt->amount,0)); ?></td>
                                    <td align="right"><?php echo e(number_format($dt->pph21,0)); ?></td>
                                    <td align="right"><?php echo e(number_format($dt->amount_paid,0)); ?></td>
                                    <?php if($status == 0): ?>
                                    <td align="center">
                                        <a href="<?php echo e($site); ?>/Resignation/Distribute/<?php echo e($dt->id); ?>" class="btn btn-xs btn-success" onclick="return confirm('Distribute this item?')"><i class="fa fa-send"></i> Distribute</a>
                                        <a href="<?php echo e($site); ?>/Resignation/Delete/<?php echo e($dt->id); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this item?')"><i class="fa fa-trash"></i></a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f4f4f4; font-weight: bold;">
                                <td colspan="8" align="right">TOTAL</td>
                                <td align="right"><?php echo e(number_format($t_amount,0)); ?></td>
                                <td align="right"><?php echo e(number_format($t_pph21,0)); ?></td>
                                <td align="right"><?php echo e(number_format($t_paid,0)); ?></td>
                                <?php if($status == 0): ?>
                                <td></td>
                                <?php endif; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Add New -->
<div class="modal fade" id="modal-add-new" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Resignation</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cutoff Date</label>
                            <input type="date" id="modal-cutoff" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-md-offset-4">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" id="modal-search" class="form-control" placeholder="Search name or NIK...">
                        </div>
                    </div>
                </div>
                <div style="overflow: auto; max-height: 400px;">
                    <table class="table table-bordered table-hover" id="modal-table">
                        <thead>
                            <tr style="background-color: #f4f4f4;">
                                <th style="width:30px; text-align: center;">No</th>
                                <th style="text-align: center;">NIK</th>
                                <th>Name</th>
                                <th style="text-align: center;">Permanent Date</th>
                                <th style="text-align: center;">Tenure</th>
                                <th style="text-align: right;">Salary</th>
                                <th style="text-align: right;">Amount</th>
                                <th style="width:30px; text-align: center;"><input type="checkbox" id="modal-check-all"></th>
                            </tr>
                        </thead>
                        <tbody id="modal-employee-list">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-modal-capture"><i class="fa fa-save"></i> Capture</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
<script>
    var severanceRules = [];
    var employees = [];

    $(document).ready(function() {
        $('#modal-add-new').on('show.bs.modal', function() {
            fetchEmployees();
        });

        $('#modal-cutoff').on('change', function() {
            calculateAll();
        });

        $('#modal-search').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#modal-employee-list tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('#modal-check-all').on('change', function() {
            $('.modal-row-checkbox').prop('checked', $(this).prop('checked'));
        });

        $('#btn-modal-capture').on('click', function() {
            captureData();
        });
    });

    function fetchEmployees() {
        var cutoff = $('#modal-cutoff').val() || '0';
        $.ajax({
            url: '<?php echo e($site); ?>/Resignation/PermanentList/' + cutoff,
            type: 'GET',
            success: function(data) {
                console.log('Data received:', data);
                employees = data.tb_resignation;
                severanceRules = data.tb_severance;
                renderEmployees();
            },
            error: function(xhr) {
                alert('Error fetching employees: ' + xhr.responseText);
                console.error(xhr);
            }
        });
    }

    function renderEmployees() {
        var html = '';
        var cutoff = $('#modal-cutoff').val();
        $.each(employees, function(i, emp) {
            var calc = calculateResignation(emp, cutoff);
            var salary = Math.round(emp.total_salary);
            var amount = Math.round(calc.amount);
            html += '<tr>' +
                '<td align="center">' + (i + 1) + '</td>' +
                '<td align="center">' + (emp.NIK || emp.nik || '') + '</td>' +
                '<td>' + emp.employee_name + '</td>' +
                '<td align="center" class="date-permanent">' + emp.date_permanent + '</td>' +
                '<td align="center" class="tenure-cell">' + calc.years + ' th ' + calc.months + ' bln</td>' +
                '<td align="right" class="salary-cell">' + formatNumber(salary) + '</td>' +
                '<td align="right" class="amount-cell">' + formatNumber(amount) + '</td>' +
                '<td align="center">' +
                    '<input type="checkbox" class="modal-row-checkbox" data-id-employee="' + emp.id + '" data-salary="' + salary + '" data-amount="' + amount + '">' +
                '</td>' +
            '</tr>';
        });
        $('#modal-employee-list').html(html);
    }

    function calculateAll() {
        var cutoff = $('#modal-cutoff').val();
        $('#modal-employee-list tr').each(function(i) {
            var emp = employees[i];
            var calc = calculateResignation(emp, cutoff);
            var amount = Math.round(calc.amount);
            $(this).find('.tenure-cell').text(calc.years + ' th ' + calc.months + ' bln');
            $(this).find('.amount-cell').text(formatNumber(amount));
            $(this).find('.modal-row-checkbox').data('amount', amount);
        });
    }

    function calculateResignation(emp, cutoffDate) {
        var d1 = new Date(emp.date_permanent);
        var d2 = new Date(cutoffDate);
        
        var years = d2.getFullYear() - d1.getFullYear();
        var months = d2.getMonth() - d1.getMonth();
        if (months < 0) {
            years--;
            months += 12;
        }
        if (d1 > d2) { years = 0; months = 0; }

        var amount = 0;
        $.each(severanceRules, function(i, rule) {
            if (years >= rule.batas_bawah && (rule.batas_atas == null || years < rule.batas_atas)) {
                amount = emp.total_salary * (rule.prosentase / 100) * rule.multiple;
                return false; // break
            }
        });

        return { years: years, months: months, amount: amount };
    }

    function captureData() {
        var data = [];
        $('.modal-row-checkbox:checked').each(function() {
            var row = $(this).closest('tr');
            data.push({
                id_employee: $(this).data('id-employee'),
                date_permanent: row.find('.date-permanent').text().trim(),
                total_salary: $(this).data('salary'),
                amount: $(this).data('amount'),
                cutoff: $('#modal-cutoff').val()
            });
        });

        if (data.length == 0) {
            alert('Please select at least one employee.');
            return;
        }

        if(!confirm('Capture ' + data.length + ' items?')) return;

        $.ajax({
            url: '<?php echo e($site); ?>/Resignation/Capture',
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                items: data
            },
            success: function(response) {
                if(response.status == 'success'){
                    alert('Success');
                    location.reload();
                } else {
                    alert('Error');
                }
            }
        });
    }

    function formatNumber(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    $('body').on("change","#periode",function(){
        var periode = document.getElementById('periode').value;
        if(periode == '') periode = 0;
        window.location.href = "<?php echo e($site); ?>/Resignation/" + periode;
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll_payments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/payroll/resignation_list.blade.php ENDPATH**/ ?>