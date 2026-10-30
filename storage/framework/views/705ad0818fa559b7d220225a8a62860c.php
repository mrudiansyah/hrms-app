
<?php $__env->startSection('Contents'); ?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>">	
						</div>
						<div class="pull-right">
                            <a href="<?php echo e($site); ?>/RecapPDF/<?php echo e($periode); ?>/<?php echo e($tipe); ?>" class="btn btn-primary btn-md" target="_blank"><i class="fa fa-print"></i> &nbsp;Preview</a>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered">
							<thead>
                                <tr>
                                    <th rowspan="2" style="width:120px;border-bottom:3px double #000;">DEPARTMENT</th>
                                    <th rowspan="2" style="width:120px;border-bottom:3px double #000;">SEGMENT</th>
                                    <th rowspan="2" style="width:120px;border-bottom:3px double #000;">CC_CODE</th>
                                    <th rowspan="2" style="border-bottom:3px double #000;">TOTAL<br><small style="font-size:8px;border-bottom:3px double #000;">EMPLOYEE</small></th>
                                    <th colspan="3">SALARY(gross)</th>
                                    <th rowspan="2" style="border-bottom:3px double #000;">BACKPAY</th>
                                    <th rowspan="2" style="border-bottom:3px double #000;">TOTAL</th>
                                    <th rowspan="2" style="width:1px;border-style: none;">&nbsp;</th>
                                    <th colspan="2">ALLOWANCE</th>
                                    <th rowspan="2" style="border-bottom:3px double #000;">TOTAL</th>
                                    <th rowspan="2" style="width:1px;border-style: none;">&nbsp;</th>
                                    <th rowspan="2" style="border-bottom:3px double #000;">TOTAL SALARY<br><small>(gross)&</small><br>ALLOWANCE</th>
                                    <th rowspan="2" style="width:1px;border-style: none;">&nbsp;</th>
                                </tr>
                                <tr>
                                    <th style="border-bottom:3px double #000;">DIRECT</th>
                                    <th style="border-bottom:3px double #000;">INDIRECT</th>
                                    <th style="border-bottom:3px double #000;">OPEX<br><small style="font-size:10px;">operating expenditure</small></th>
                                    <th style="border-bottom:3px double #000;">SHIFT</th>
                                    <th style="border-bottom:3px double #000;">TRANSPORT</th>
                                </tr>
							</thead>
							<tbody>
                                <?php $a=0;$b=0;$c=0;$d=0;$e=0;$f=0;$g=0;$h=0;$i=0;$j=0;$k=0;$l=0;$m=0;$n=0;$o=0;$p=0;$q=0;?>
                                <?php $__currentLoopData = $tb_rekap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="text-align:left;"><?php echo e($dt->department); ?></td>  
                                    <td style="text-align:left;"><?php echo e($dt->segment); ?></td>  
                                    <td style="text-align:left;"><?php echo e($dt->cc_code); ?></td>  
                                    <td style="text-align:center;"><?php echo e($dt->jml_employee); ?><?php $a=$a+$dt->jml_employee;?></td>            
                                    <td><?php if($dt->category=='DIRECT'){echo  number_format($dt->total_salary,0);$b=$b+$dt->total_salary;}?></td>            
                                    <td><?php if($dt->category=='INDIRECT'){echo  number_format($dt->total_salary,0);$c=$c+$dt->total_salary;}?></td>            
                                    <td><?php if($dt->category=='OPEX'){echo  number_format($dt->total_salary,0);$d=$d+$dt->total_salary;}?></td>            
                                    <td><?php echo number_format($dt->total_rapel,0);$q=$q+$dt->total_rapel;?></td>            
                                    <td><?php echo number_format($dt->total_salary,0);$e=$e+$dt->total_salary;?></td>            
                                    <td style="width:1px;border-style: none;">&nbsp;</td>
                                    <td><?php echo number_format($dt->total_shift,0);$f=$f+$dt->total_shift;?></td>            
                                    <td><?php echo number_format($dt->total_transport,0);$g=$g+$dt->total_transport;?></td>            
                                    <td><?php $allowance=$dt->total_shift+$dt->total_transport;echo number_format($allowance,0);$h=$h+$allowance;?></td>            
                                    <td style="width:1px;border-style: none;">&nbsp;</td>
                                    <td><?php $salary_allowance=$dt->total_salary+$allowance+$dt->total_rapel;echo number_format($salary_allowance,0);$i=$i+$salary_allowance;?></td>            
                                    <td style="width:1px;border-style: none;">&nbsp;</td>
                                </tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
                            <tfoot>
                                <tr>
                                    <td style="border-top:3px double #000;">&nbsp;</td>  
                                    <td style="border-top:3px double #000;">&nbsp;</td>  
                                    <td style="border-top:3px double #000;">&nbsp;</td>  
                                    <td style="text-align:center;border-top:3px double #000;"><?php echo e($a); ?></td>            
                                    <td style="border-top:3px double #000;"><?php echo number_format($b,0);?></td>            
                                    <td style="border-top:3px double #000;"><?php echo number_format($c,0);?></td>            
                                    <td style="border-top:3px double #000;"><?php echo number_format($d,0);?></td>            
                                    <td style="border-top:3px double #000;"><?php echo number_format($q,0);?></td>            
                                    <td style="border-top:3px double #000;"><?php echo number_format($e,0);?></td>            
                                    <td style="width:1px;border-style: none;">&nbsp;</td>
                                    <td style="border-top:3px double #000;"><?php echo number_format($f,0);?></td>            
                                    <td style="border-top:3px double #000;"><?php echo number_format($g,0);?></td>            
                                    <td style="border-top:3px double #000;"><?php echo number_format($h,0);?></td>            
                                    <td style="width:1px;border-style: none;">&nbsp;</td>
                                    <td style="border-top:3px double #000;"><?php echo number_format($i,0);?></td>            
                                    <td style="width:1px;border-style: none;">&nbsp;</td>
                                </tr>
                            </tfoot>
						</table>
                        <?php $__currentLoopData = $tb_approval; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <table border="1" cellspacing="0" cellpadding="5" width="900" align="center">
                                <tr>
                                    <td colspan="4" style="text-align:center;">Approved by</td>
                                    <td style="text-align:center;">Verified by</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;vertical-align:middle;height:100px;" class="relative">
                                        <?php if($dt2->id_approver_1==$id_employee&&$dt2->status_1==0): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="1" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="1" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_1==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_1); ?>

                                        <?php elseif($dt2->status_1==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_1); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        <?php if($dt2->id_approver_2==$id_employee&&$dt2->status_2==0&&$dt2->status_1==1): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="2" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="2" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_2==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_2); ?>

                                        <?php elseif($dt2->status_2==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_2); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        <?php if($dt2->id_approver_3==$id_employee&&$dt2->status_3==0&&$dt2->status_2==1): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="3" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="3" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_3==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_3); ?>

                                        <?php elseif($dt2->status_3==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_3); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        <?php if($dt2->id_approver_4==$id_employee&&$dt2->status_4==0&&$dt2->status_3==1): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_4==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_4); ?>

                                        <?php elseif($dt2->status_4==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_4); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        <?php if($dt2->id_approver_5==$id_employee&&$dt2->status_5==0&&$dt2->status_3==1): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="5" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="5" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_5==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_5); ?>

                                        <?php elseif($dt2->status_5==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_5); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_1); ?></th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_2); ?></th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_3); ?></th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_4); ?></th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_5); ?></th>
                                </tr>
                                <tr>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_1); ?></td>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_2); ?></td>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_3); ?></td>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_4); ?></td>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_5); ?></td>
                                </tr>
                            </table>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
					</div>
					<!-- /.box-body -->


                </div>

			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
<script>
    $('body').on("change","#periode",function(){
        //var department=document.getElementById('idDepartment').value;
        var periode=document.getElementById('periode').value;
        if(periode=='') var periode=0;
        var tipe="<?php echo $tipe;?>";
        window.location.href="<?php echo e($site); ?>/Recap/"+periode+"/"+tipe;
    });
    $(document).on('click', '.simpan', function() {
			var periode=$(this).data('periode');
			var category=$(this).data('category');
			var tipe=$(this).data('tipe');
            var kolom=$(this).data('kolom');
			var status=$(this).data('status');

			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/RecapSave",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{periode:periode,category:category,tipe:tipe,kolom:kolom,status:status},
				success: function(respond){
					location.reload();
					// if(respond=='Sukses'){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
				}
			})
		});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll_recap', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_rekap.blade.php ENDPATH**/ ?>