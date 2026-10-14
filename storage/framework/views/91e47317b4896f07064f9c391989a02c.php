
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   	<style>
		#tablesx th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#tables th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#tables tbody tr:hover{
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
    </style>
	<?php
		date_default_timezone_set("Asia/Bangkok");
		$Today=date('Y-m-d');
		$AWeek=date('Y-m-d',strtotime('+ 14 days',strtotime($Today)));
		$AMonth=date('Y-m-d',strtotime('+ 1 Months',strtotime($Today)));
	?>

	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				KSK List
				<small>konfirmasi status karyawan</small>
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
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-user"></i>
						<h3 class="box-title" style="padding-bottom:25px;"><?php echo e($Judul); ?></h3>
						<div class="box-tools pull-right">
                            &nbsp;
						</div>
					</div>
					<div class="box-body" style="overflow-x:scroll;">
						<div class="box-header" style="padding-top:0px;padding-left:0px;">
							<div class="box-tools pull-left">
								<input type="month" class="form-control" id="periode" name="periode" value="<?php echo e($periode); ?>">
							</div>
						</div>
						<table id="table2" class="table table-hover tabel2">
							<thead>
								<tr>
									<th>NO</th>
									<th>KSK NO</th>
									<th>DEPARTMENT</th>
									<th>APPROVAL_01</th>
									<th>APPROVAL_02</th>
									<th>APPROVAL_03</th>
									<th>APPROVAL_04</th>
									<th>APPROVAL_05</th>
									<th>QTY_EMPLOYEE</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_ksk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td>
										<?php echo e($dt->no_ksk); ?>

									</td>
									<td><?php echo e($dt->dept_code); ?></td>
									<td><?php if($dt->approval1>0&&$dt->approval1_status==0)echo "<i class='fa fa-square-o'></i> ";if($dt->approval1>0&&$dt->approval1_status==1)echo "<i class='fa fa-check-square-o'></i> ";?> <?php echo e($dt->approvalname1); ?></td>
									<td><?php if($dt->approval2>0&&$dt->approval2_status==0)echo "<i class='fa fa-square-o'></i> ";if($dt->approval2>0&&$dt->approval2_status==1)echo "<i class='fa fa-check-square-o'></i> ";?> <?php echo e($dt->approvalname2); ?></td>
									<td><?php if($dt->approval3>0&&$dt->approval3_status==0)echo "<i class='fa fa-square-o'></i> ";if($dt->approval3>0&&$dt->approval3_status==1)echo "<i class='fa fa-check-square-o'></i> ";?> <?php echo e($dt->approvalname3); ?></td>
									<td><?php if($dt->approval4>0&&$dt->approval4_status==0)echo "<i class='fa fa-square-o'></i> ";if($dt->approval4>0&&$dt->approval4_status==1)echo "<i class='fa fa-check-square-o'></i> ";?> <?php echo e($dt->approvalname4); ?></td>
									<td>
										<?php if($dt->approval5>0&&$dt->approval5_status==0)echo "<i class='fa fa-square-o'></i> ";if($dt->approval5>0&&$dt->approval5_status==1)echo "<i class='fa fa-check-square-o'></i> ";?> <?php echo e($dt->approvalname5); ?>

									</td>
									<td>
										<?php
											$host = mysqli_connect("192.168.1.4","ems","123456","db_ems");
											$idksk=$dt->id;
											$qryt=mysqli_query($host,"select * from tb_ksk_detail where id_ksk='$idksk'")or die(mysqli_error($host));
											$qty_total=mysqli_num_rows($qryt);
										?>
										<?php echo e($qty_total); ?>

										<div class="pull-right">
											<a href="/Employee/KSK/Detail/<?php echo e($dt->id); ?>/<?php echo e($periode); ?>" type="button" class="btn btn-primary btn-xs"><i class="fa  fa-folder-o"></i></a>
											<a href="/Employee/KSK/Print/<?php echo e($dt->id); ?>" type="button" class="btn btn-info btn-xs" target="_blank"><i class="fa  fa-print"></i></a>
										</div>
										<?php echo e($dt->approvalname6); ?>

									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
							<tfoot>

							</tfoot>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->
	</div>

    <?php if($message = Session::get('success')): ?>
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			<?php echo e($message); ?>

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
			"pageLength"  : 50,
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
		$(function () {
			$('#table4').DataTable({
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
          "pageLength"  : 10,
          'autoWidth'   : false,
		  "order": [[ 10, 'asc' ]],
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
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script>
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/Employees/KSK/"+periode;
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_employee/ksk_approval.blade.php ENDPATH**/ ?>