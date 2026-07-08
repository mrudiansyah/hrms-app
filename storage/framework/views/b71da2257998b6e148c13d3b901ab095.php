
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
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Employee
				<small>list</small>
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
						<h3 class="box-title">Data Tables</h3>
						<div class="box-tools pull-right">
							<a href='/Admin/Employee'><button type="button" class="btn btn-success btn-md"><i class="fa fa-user"></i> &nbsp;Employee List</button></a>
						</div>
					</div>
					<div class="box-body">
						<div style="padding:20px;overflow-x: scroll;">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th>No</th>
									<th>ID</th>
									<th>PIN</th>
									<th>EMPLOYEE_NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>DEPARTMENT</th>
									<th>POSITION</th>
									<th>STATUS</th>
									<th>JOIN_DATE</th>
									<th>START_CONTRACT</th>
									<th>FINISH_CONTRACT</th>
									<th>GENDER</th>
									<th>LINE</th>
									<th>TAX</th>
									<th>BIRTH_CITY</th>
									<th>DATE_BIRTH</th>
									<th>KABUPATEN</th>
									<!--
									<th>ADDRESS KTP</th>
									<th>DOMICILES</th>
									-->
									<th>TELEPON</th>
									<th>BLOOD</th>
									<th>RELIGION</th>
									<th>EDUCATION</th>
									<th>PROGRAM</th>
									<th>MOTHER</th>
									<th>KTP</th>
									<th>NPWP</th>
									<th>BANK_ACCOUNT</th>
									<th>KK</th>
									<th>BPJS_KES</th>
									<th>BPJS_KET</th>
									<th>EMERGENCY</th>
									<th>RELATION</th>
									<th>CONTACT</th>
									<!--
									<th>ADDRESS</th>
									-->
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td style="text-align:left;padding-left:0px;">
										<a title="Show" href='/Admin/Employee/Update/<?php echo $dt->id;?>'><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-folder-open-o"></i></button></a>
									</td>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->id); ?></td>
									<td><?php echo e($dt->badgenumber); ?></td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->employee_name); ?></td>
									<td><?php echo e($dt->dept_code); ?></td>
									<td><?php echo e($dt->position_name); ?></td>
									<td><?php echo e($dt->contract_name); ?></td>
									<td><?php echo e($dt->join_date); ?></td>
									<td><?php echo e($dt->start_contract); ?></td>
									<td><?php echo e($dt->finish_contract); ?></td>
									<td><?php echo e($dt->gender); ?></td>
									<td><?php echo e($dt->line); ?></td>
									<td><?php echo e($dt->kode_status); ?></td>
									<td><?php echo e($dt->tempat_lahir); ?></td>
									<td><?php echo e($dt->tanggal_lahir); ?></td>
									<td><?php echo e($dt->kabupaten); ?></td>
									<!--
									<td><?php echo e($dt->detail); ?> <?php echo e($dt->kelurahan); ?> <?php echo e($dt->kecamatan); ?> <?php echo e($dt->kabupaten); ?> <?php echo e($dt->provinsi); ?></td>
									<td><?php echo e($dt->dom_detail); ?> <?php echo e($dt->dom_kelurahan); ?> <?php echo e($dt->dom_kecamatan); ?> <?php echo e($dt->dom_kabupaten); ?> <?php echo e($dt->dom_provinsi); ?></td>
									-->
									<td><?php echo e($dt->nomor_telepon); ?></td>
									<td><?php echo e($dt->golongan_darah); ?></td>
									<td><?php echo e($dt->agama); ?></td>
									<td><?php echo e($dt->top_education); ?></td>
									<td><?php echo e($dt->prodi); ?></td>
									<td><?php echo e($dt->ibu_kandung); ?></td>
									<td><?php echo e($dt->nomor_ktp); ?></td>
									<td><?php echo e($dt->nomor_npwp); ?></td>
									<td><?php echo e($dt->nomor_rekening); ?></td>
									<td><?php echo e($dt->nomor_kk); ?></td>
									<td><?php echo e($dt->nomor_bpjs_kes); ?></td>
									<td><?php echo e($dt->nomor_bpjs_ket); ?></td>
									<td><?php echo e($dt->nama_keluarga); ?></td>
									<td><?php echo e($dt->hubungan); ?></td>
									<td><?php echo e($dt->nomor_kontak); ?></td>
									<!--
									<td><?php echo e($dt->detail_kontak); ?> <?php echo e($dt->kelurahan_kontak); ?> <?php echo e($dt->kecamatan_kontak); ?> <?php echo e($dt->kabupaten_kontak); ?> <?php echo e($dt->provinsi_kontak); ?></td>
									-->
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
						</div>
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
			"pageLength"  : 10,
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
				"pageLength"  : 10,
				'autoWidth'   : false,
				"pagingType": "full",
				"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]]
        //"iDisplayLength": 50
				//dom: 'Bfrtip',buttons: ['print']
			});
		
			new $.fn.dataTable.Buttons( table, {
				buttons: ['copy', 'excel', 'print']
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_employee/employee_psab.blade.php ENDPATH**/ ?>