
<?php $__env->startSection('Contents'); ?>
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
   <!-- Contents -->
   <style>
        tr:hover {
          background-color: #DCDCDC;
		  cursor:pointer;
        }
   </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Performance Rank
				<div class="pull-right">
					<div class="col-xs-3" style="padding:3px;">
						<input type="number" id="periode" class="form-control kelompok" value="<?php echo e($periode); ?>">
					</div>
					<div class="col-xs-4" style="padding:3px;">
						<select class="form-control kelompok" id="dept">
							<option value="<?php echo e($dept); ?>"><?php echo e($dept); ?></option>
							<?php $__currentLoopData = $tb_department; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php if($dt->dept_code!=$dept): ?>
									<option value="<?php echo e($dt->dept_code); ?>"><?php echo e($dt->dept_code); ?></option>
								<?php endif; ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php if(request()->user()->hasRole('performance')): ?>
							<option value="0">All Dept</option>
							<?php endif; ?>
						</select>
					</div>
					<div class="col-xs-5" style="padding:3px;">
						<select class="form-control kelompok" id="level">
							<option value="<?php echo e($nm_level); ?>"><?php echo e($nm_level); ?></option>
							<?php $__currentLoopData = $tb_position; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<option value="<?php echo e($dt->nama_level); ?>"><?php echo e($dt->nama_level); ?></option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<option value="0">All Level</option>
						</select>
					</div>
				</div>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-header" style="padding:3px;">
					<div class="pull-right">
						<!--
						<?php if($nm_level!="All Level"): ?>
							<button class="btn btn-md btn-warning" id="resetRank"><i class="fa fa-refresh"></i> Reset Rank by this group</button>
						<?php endif; ?>
						-->
					</div>
				</div>

				<div class="box-body">
				<table id="tables" class="table table-bordered">
					<thead>
						<tr>
							<th>No</th>
							<th>Rank</th>
							<th>NIK</th>
							<th>Nama Karyawan</th>
							<th>Dept</th>
							<th>Jabatan</th>
							<th>Penilai</th>
							<th style="width:100px;">Masa_Kerja</th>
							<th>Nilai</th>
							<th>Grade</th>
							<th>Detail</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=0;$r1=0;$r2=0;$r3=0;$r4=0;$r5=0;?>
						<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php
								$T1 = !empty($dt->triwulan1) ? $dt->triwulan1 : '0';
								$T2 = !empty($dt->triwulan2) ? $dt->triwulan2 : '0';
								$T3 = !empty($dt->triwulan3) ? $dt->triwulan3 : '0';
								$T4 = !empty($dt->triwulan4) ? $dt->triwulan4 : '0';
							?>
							<?php if($dt->dept_code!='BOD'): ?>
							<tr>
								<td>
									<?php $no++;echo $no;?>
								</td>
								<td>
									<?php if($dt->average>0): ?>
									<?php 
										$warna='';
										$equivalen='';
										if($dt->ranked=='1'){$warna="bg-green";$r1++;$equivalen='A+';}
										else if($dt->ranked=='2'){$warna="bg-primary";$r2++;$equivalen='A';}
										else if($dt->ranked=='3'){$warna="bg-gray";$r3++;$equivalen='B+';}
										else if($dt->ranked=='4'){$warna="bg-yellow";$r4++;$equivalen='B';}
										else {$warna="bg-red";$r5++;$equivalen='C';}
									?>
									<div class="pull-left">
										<i class='label <?php echo e($warna); ?>'>
											<b class="edit-modal" data-idperformance="<?php echo e($dt->idperformance); ?>"  data-ranked="<?php echo e($dt->ranked); ?>" data-judul="<?php echo e($dt->employee_name); ?>">Rank <?php if($dt->ranked=='')echo "0";else echo $dt->ranked.' &equiv; '.$equivalen;?></b>
										</i>
									</div>
									<?php endif; ?>
									<div class="pull-right">
									<?php
										//echo " = ".$equivalen;
										// if($no<=$level[1]){echo "1";}
										// else if($no<=$level[2]){echo "2";}
										// else if($no<=$level[3]){echo "3";}
										// else if($no<=$level[4]){echo "4";}
										// else {echo "5";}
									?>
									</div>
								</td>
								<td><?php echo e($dt->NIK); ?></td>
								<td><?php echo e($dt->employee_name); ?></td>
								<td><?php echo e($dt->dept_code); ?></td>
								<td><?php echo e($dt->position_name); ?></td>
								<td><?php echo e($dt->leader_name); ?></td>
								<td style="text-align:right;">
                                    <?php $thn=floor($dt->masa_kerja_member/12);$bln=$dt->masa_kerja_member%12;?>
                                    <?php if($thn>0)echo $thn.' Tahun';if($thn>0&&$bln>0)echo " ";if($bln>0)echo $bln.' Bulan';?>
                                </td>
								<td style="text-align:center;color:blue;">
									<?php echo e($dt->average); ?>

								</td>
								<td>
									<?php
										if($dt->grade=='D')$warna='bg-red';
										else if($dt->grade=='C')$warna='bg-yellow';
										else if($dt->grade=='B+')$warna='bg-aqua';
										else if($dt->grade=='A')$warna='bg-primary';
										else if($dt->grade=='A+')$warna='bg-green';
										else $warna='bg-gray';
										echo "<label class='label ".$warna."'>".$dt->grade."</label>";
									?>
								</td>
								<td>
									<div class="pull-left">
										<?php if($dt->grade>0): ?>
											<a href="/PerformancePreview/<?php echo e($dt->idperformance); ?>" target="_blank"><button class="btn btn-xs btn-primary"><i class="fa fa-print"></i></button></a>
										<?php endif; ?>
									</div>
							</td>
							</tr>
							<?php endif; ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
				</table>
				<input type="hidden" id="jumlah" value="<?php echo e($no); ?>">
				<?php if($dept!='All Dept'&&$nm_level!=0&&$no>0): ?>
					<b>Summary:</b><br>
					<?php
						$p1=number_format($r1/$no*100,0);
						$p2=number_format($r2/$no*100,0);
						$p3=number_format($r3/$no*100,0);
						$p4=number_format($r4/$no*100,0);
						$p5=number_format($r5/$no*100,0);
						$s1=number_format($no*0.1,2);
						$s2=number_format($no*0.2,2);
						$s3=number_format($no*0.4,2);
						$s4=number_format($no*0.2,2);
						$s5=number_format($no*0.1,2);
					?>
					<ul>
						<li>Rank 1 : <?php echo e($p1); ?>% <?php echo e($r1); ?> (Std: 10% <?php echo e($s1); ?>)</li>
						<li>Rank 2 : <?php echo e($p2); ?>% <?php echo e($r2); ?> (Std: 20% <?php echo e($s2); ?>)</li>
						<li>Rank 3 : <?php echo e($p3); ?>% <?php echo e($r3); ?> (Std: 40% <?php echo e($s3); ?>)</li>
						<li>Rank 4 : <?php echo e($p4); ?>% <?php echo e($r4); ?> (Std: 20% <?php echo e($s4); ?>)</li>
						<li>Rank 5 : <?php echo e($p5); ?>% <?php echo e($r5); ?> (Std: 10% <?php echo e($s5); ?>)</li>
					</ul>
				<?php endif; ?>

			</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->

			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->


<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>
    <div class="modal fade" id="modal-edit">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<form>
					<input type="hidden" id="idperformance">
					<?php echo e(csrf_field()); ?>

					<div class="modal-body">
						<div class="box box-info box-solid" style="border:0px;">
							<div class="box-header" style="padding:5px 10px;font-size:20px;">
								<label id="judul"></label>
								<div class="pull-right">
									<button type="button" class="btn btn-warning pull-right" id="simpan">Save</button>&nbsp;
									<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;
								</div>
							</div>
							<div class="box-body" style="padding:10px 0px;">
								<div class="form-group">
									<label>Ranked</label>
									<input type="number" min="1" max="5" class="form-control" id="ranked">
								</div>
							</div>
						</div>
					</div>
				</form>		
			</div>
		</div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<!-- page script Tabel-->
	<script>
		$('body').on("change",".kelompok",function(){
			var periode=document.getElementById('periode').value;
			var dept=document.getElementById('dept').value;
			var level=document.getElementById('level').value;
			window.location.href="/Performance/"+periode+"/"+dept+"/"+level;
		});

	</script>
	<script>
	$(function () {
		$('#table1').DataTable({
		'paging'      : true,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		//"pageLength"  : 25,
		'autoWidth'   : false
		})
		$('#table2').DataTable({
		'paging'      : false,
		'lengthChange': false,
		'searching'   : false,
		'ordering'    : false,
		'info'        : false,
		'autoWidth'   : false
		})
	})
	</script>
	<!-- page script alert-->
	<script>
		$(document).ready(function() {
		  var table = $('#tables').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : true,
			"lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
		  });
		
		  new $.fn.dataTable.Buttons( table, {
			//buttons: ['copy', 'excel', 'print']
			  buttons: [
				  { extend: 'copyHtml5', footer: true },
				  { extend: 'excelHtml5', footer: true },
				  { extend: 'print', footer: true }
			  ]
  
		  } );
		
		  table.buttons( 0, null ).container().prependTo(
			table.table().container()
		  );
		} );
  
  
	</script>
	<script>
		$(document).on('click', '.edit-modal', function() {
			var judul=$(this).data('judul');
			$('#idperformance').val($(this).data('idperformance'));
			$('#ranked').val($(this).data('ranked'));
			document.getElementById("judul").textContent = judul;
			$('#modal-edit').modal('show');
		});
		$('.box-header').on('click', '#simpan', function() {
			var idperformance=$('#idperformance').val();
			var ranked=$('#ranked').val();
			$.ajaxSetup({
				type:"POST",
				url: "/Performance/RankUpdate",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{idperformance:idperformance,ranked:ranked},
				success: function(respond){
					if(respond=="Sukses"){
						location.reload();
					}else{
						alert(respond);
						//alert('Gagal Update');
					}
				}
			})
		});
		$('.box-header').on('click', '#resetRank', function() {
			var periode=document.getElementById('periode').value;
			var dept=document.getElementById('dept').value;
			var level=document.getElementById('level').value;
			//alert(periode);
			$.ajaxSetup({
				type:"POST",
				url: "/Performance/RankReset",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{periode:periode,dept:dept,level:level},
				success: function(respond){
					if(respond=="Sukses"){
						location.reload();
					}else{
						alert(respond);
						//alert('Gagal Update');
					}
				}
			})
		});
		
		$(document).ready(function() {
			var jumlah=document.getElementById('jumlah').value;
			if(jumlah>0){
				$('#resetRank').show();
			}else{
				$('#resetRank').hide();
			}
		});

	</script>
  <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/performance/performance_all.blade.php ENDPATH**/ ?>