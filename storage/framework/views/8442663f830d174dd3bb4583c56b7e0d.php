
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
		#tables th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 tbody tr:hover{
			cursor:pointer;
		}
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table4 tbody tr:hover{
			cursor:default;
		}
		#table5 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Form Meals
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-calendar"></i> 
					<?php 
						date_default_timezone_set("Asia/Jakarta");
						echo date('l, d M Y H:i');
					?>
				</a>
				</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-3">
                <input type="hidden" name="sysid" id="syside">
				<div class="box box-success box-solid">
					<div class="box-header with-border">
						<label>Form Group</label>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class="form-group">
							<label>Employee</label>
							<select id="idemployee" class="form-control selectpicker" data-live-search="true">
								<option value="<?php echo e($id_employee); ?>"><?php echo e($employee_name); ?></option>
								<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($dt2->id); ?>"><?php echo e($dt2->employee_name); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

							</select>
							<div class="pull-right" style="color:#F00;padding-right:10px;" id="info">&nbsp;</div>
						</div>
						<div class="form-group">
							<label>Periode</label>
							<input type="month" class="form-control" id="periode" value="<?php echo e($periode); ?>">
						</div>
					</div>
					<div class="box-footer">
						<div class="form-group pull-right">
							<button type="submit" class="btn btn-success" id="simpan">Select</button>
						</div>

					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<?php if($tb_meal!=''){?>
			<div class="col-xs-9">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<h3 class="box-title">Meal Allowance</h3>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th>Date</th>
									<th>Day</th>
                                    <th style="width:50px;">NIK</th>
									<th>Employee Name</th>
									<th>TL MEAL</th>
									<th>OT MEAL</th>
									<th>NO MEALS</th>

								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $ut_tanggal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php
									$no++;
									$daily_meals=$meal_totals[$dt->tanggal] ?? [];
									$off=$daily_meals['meal_off'] ?? 0;
									$tl=$daily_meals['meal_tl'] ?? 0;
									$ot=$daily_meals['meal_ot'] ?? 0;
								?>
								<tr>
									<td><?php echo date('Y-m-d',strtotime($dt->tanggal));?></td>
									<td><?php echo date('l',strtotime($dt->tanggal));?></td>
									<td><?php echo e($nik); ?></td>
									<td><?php echo e($employee_name); ?></td>
									<td>
										<?php 
											if($tl==0){$status10=' hidden';$status11='';}
											else {$status10='';$status11=' hidden';}
											echo "<div".$status11." id='tl0_".$no."'><i class='fa fa-square-o update' data-varoff='tl0_".$no."' data-varon='tl1_".$no."' data-periode='".$periode."' data-tanggal='".$dt->tanggal."' data-idemployee='".$id_employee."' data-category='meal_tl' data-nilai='0'></i></div>";
											echo "<div".$status10." id='tl1_".$no."'><i class='fa fa-check-square-o update' data-varoff='tl1_".$no."' data-varon='tl0_".$no."' data-periode='".$periode."' data-tanggal='".$dt->tanggal."' data-idemployee='".$id_employee."' data-category='meal_tl' data-nilai='1'></i></div>";
										?>
									</td>
									<td>
										<?php 
											if($ot==0){$status20=' hidden';$status21='';}
											else {$status20='';$status21=' hidden';}
											echo "<div".$status21." id='ot0_".$no."'><i class='fa fa-square-o update' data-varoff='ot0_".$no."' data-varon='ot1_".$no."' data-periode='".$periode."' data-tanggal='".$dt->tanggal."' data-idemployee='".$id_employee."' data-category='meal_ot' data-nilai='0'></i></div>";
											echo "<div".$status20." id='ot1_".$no."'><i class='fa fa-check-square-o update' data-varoff='ot1_".$no."' data-varon='ot0_".$no."' data-periode='".$periode."' data-tanggal='".$dt->tanggal."' data-idemployee='".$id_employee."' data-category='meal_ot' data-nilai='1'></i></div>";
										?>
									</td>
									<td>
										<?php 
											if($off==0){$status30=' hidden';$status31='';}
											else {$status30='';$status31=' hidden';}
											echo "<div".$status31." id='off0_".$no."'><i class='fa fa-square-o update' data-varoff='off0_".$no."' data-varon='off1_".$no."' data-periode='".$periode."' data-tanggal='".$dt->tanggal."' data-idemployee='".$id_employee."' data-category='meal_off' data-nilai='0'></i></div>";
											echo "<div".$status30." id='off1_".$no."'><i class='fa fa-check-square-o update' data-varoff='off1_".$no."' data-varon='off0_".$no."' data-periode='".$periode."' data-tanggal='".$dt->tanggal."' data-idemployee='".$id_employee."' data-category='meal_off' data-nilai='1'></i></div>";
										?>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>

			</div>
			<?php }?>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

    <?php if($message = Session::get('success')): ?>
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			<?php echo e($message); ?>

		</div>
    <?php endif; ?>
	<?php if($errors->any()): ?>
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				<?php if($errors->has('date_off')): ?>
					- Date harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('category')): ?>
					- Category harus dipilih<br>
				<?php endif; ?>
		</div>
	<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 15,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
	</script>
    <script>
      $(document).ready(function() {
        var table = $('#tables').DataTable({
          'paging'      : true,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 45,
          'autoWidth'   : false,
          "lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
          "scrollX"     : true
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
		$('body').on("click","#simpan",function(){
			var periode=document.getElementById('periode').value;
			var idemployee=document.getElementById('idemployee').value*1;
			window.location.href="/MealsMonth/"+periode+"/"+idemployee;
		});

		$(document).on('click', '.update', function() {
			var periode=$(this).data('periode');
			var tanggal=$(this).data('tanggal');
			var idemployee=$(this).data('idemployee');
			var category=$(this).data('category');
			var nilai=$(this).data('nilai');
			var varoff=$(this).data('varoff');
			var varon=$(this).data('varon');
			
			$.ajaxSetup({
				type:"POST",
				url: "/MealsMonth/Update",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{tanggal:tanggal,idemployee:idemployee,category:category,nilai:nilai},
				success: function(respond){
					//window.location.href="/MealsMonth/"+periode+"/"+idemployee;
					document.getElementById(varoff).style.display = "none";
					document.getElementById(varon).style.display = "block";
				}
			})


		});

	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_meal/meal_month.blade.php ENDPATH**/ ?>