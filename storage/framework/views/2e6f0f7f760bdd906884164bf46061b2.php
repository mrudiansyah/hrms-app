
<?php $__env->startSection('Contents'); ?>
	<?php 
		$awal=date('Y-m-d',strtotime('-31 days',strtotime($periode.'-01')));
		$text_awal=' 25 '.date('F',strtotime($awal));
		$text_akhir=' - 24 '.date('F Y',strtotime($periode.'-01'));
		$text_periode='PERIODE'.$text_awal.$text_akhir;
		$periode_text=strtoupper(date('F Y',strtotime($periode.'-01')));

	?>

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
						<?php
							$categories = ['PSAB_5Days', 'PSAB_6Days'];
							$grand_total_all = 0;
						?>

						<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php
								$filtered_data = $tb_salary_summary->where('kategori', $category);
								$sub_total = 0;
								$no = 0;
							?>
							
							<?php if($filtered_data->count() > 0): ?>
								<h4><?php echo e(str_replace('_', ' ', $category)); ?></h4>
								<table class="table table-bordered table-striped" style="margin-bottom: 30px;">
									<thead>
										<tr>
											<th width="40">NO</th>
											<th>NIK</th>
											<th>EMPLOYEE_NAME</th>
											<th>KATEGORI</th>
											<th>DAYS</th>
											<th>SALARY/DAY</th>
											<th>RAPEL</th>
											<th>TOTAL_AMOUNT</th>
										</tr>
									</thead>
									<tbody>
										<?php $__currentLoopData = $filtered_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<tr>
												<td><?php echo e(++$no); ?></td>
												<td><?php echo e($dt->NIK ?? ''); ?></td>
												<td><?php echo e($dt->employee_name ?? ''); ?></td>
												<td><?php echo e($dt->kategori ?? ''); ?></td>
												<td><?php echo e($dt->days ?? '0'); ?></td>
												<td style="text-align:right;"><?php echo e(isset($dt->salary_perday) ? number_format($dt->salary_perday, 0) : '0'); ?></td>
												<td style="text-align:right;"><?php echo e(isset($dt->rapel) ? number_format($dt->rapel, 0) : '0'); ?></td>
												<td style="text-align:right;"><?php echo e(isset($dt->total_amount) ? number_format($dt->total_amount, 0) : '0'); ?></td>
											</tr>
											<?php $sub_total += ($dt->total_amount ?? 0); ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</tbody>
									<tfoot>
										<tr style="background: #f9f9f9; font-weight: bold;">
											<th colspan="7" style="text-align:right;">SUB TOTAL <?php echo e(str_replace('_', ' ', $category)); ?></th>
											<th style="text-align:right;"><?php echo e(number_format($sub_total, 0)); ?></th>
										</tr>
									</tfoot>
								</table>
								<?php $grand_total_all += $sub_total; ?>
							<?php endif; ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

						<div class="row">
							<div class="col-md-12">
								<div class="well" style="text-align:right; font-weight:bold; font-size:1.2em; background: #eee;">
									GRAND TOTAL: <?php echo e(number_format($grand_total_all, 0)); ?>

								</div>
							</div>
						</div>
                        <?php $__currentLoopData = $tb_approval; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <table border="1" cellspacing="0" cellpadding="5" width="1200" align="center">
                                <tr>
                                    <td style="text-align:center;">Prepared by</td>
                                    <td style="text-align:center;">Checked by</td>
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
                                        <?php if($dt2->id_approver_5==$id_employee&&$dt2->status_5==0&&$dt2->status_4==1): ?>
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
                                    <td style="text-align:center;vertical-align:middle;">
                                        <?php if($dt2->id_approver_6==$id_employee&&$dt2->status_6==0&&$dt2->status_5==1): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_6==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_6); ?>

                                        <?php elseif($dt2->status_6==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_6); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        <?php if($dt2->id_approver_7==$id_employee&&$dt2->status_7==0&&$dt2->status_6==1): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_7==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_7); ?>

                                        <?php elseif($dt2->status_7==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_7); ?>

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
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_6); ?></th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_7); ?></th>
                                </tr>
                                <tr>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_1); ?></td>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_2); ?></td>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_3); ?></td>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_4); ?></td>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_5); ?></td>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_6); ?></td>
                                    <td style="text-align:center;"><?php echo e($dt2->pos_7); ?></td>
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

<?php echo $__env->make('layouts/payroll_recap', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/payroll/salary_rekap_psab.blade.php ENDPATH**/ ?>