
<?php $__env->startSection('Contents'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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
				<small>unregistered BPJS</small>
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
									<th>No</th>
									<th>EMPLOYEE_NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>DEPARTMENT</th>
									<th>POSITION</th>
									<th>NOMOR KK</th>
									<th>NIK KTP</th>
									<th>NOMOR BPJS_KES</th>
									<th>NOMOR BPJS_KET</th>
									<th>ACTION</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->employee_name); ?></td>
									<td><?php echo e($dt->dept_code); ?></td>
									<td><?php echo e($dt->position_name); ?></td>
									<td><?php echo e($dt->nomor_kk); ?></td>
									<td><?php echo e($dt->nomor_ktp); ?></td>
									<td>&nbsp;
										<?php echo e($dt->nomor_bpjs_kes); ?>

										<div class="form-group pull-left">
											<div class="checkbox">
												<label>
												<input type="checkbox" <?php if($dt->status_bpjs_kes=='1')echo "checked";?> class="status_bpjs_kes" id="status_bpjs_kes<?php echo e($dt->id_detail); ?>" data-iddetail="<?php echo e($dt->id_detail); ?>" data-statusbpjskes="<?php echo e($dt->status_bpjs_kes); ?>" data-nokk="<?php echo e($dt->nomor_kk); ?>" data-noktp="<?php echo e($dt->nomor_ktp); ?>" data-bpjskes="<?php echo e($dt->nomor_bpjs_kes); ?>" data-bpjsket="<?php echo e($dt->nomor_bpjs_ket); ?>" <?php if($dt->status_bpjs_kes==1)echo "disabled";?>>
												</label>
											</div>
										</div>
									</td>
									<td>&nbsp;
										<?php echo e($dt->nomor_bpjs_ket); ?>

										<div class="form-group pull-left">
											<div class="checkbox">
												<label>
												<input type="checkbox" <?php if($dt->status_bpjs_ket=='1')echo "checked";?> class="status_bpjs_ket" id="status_bpjs_ket<?php echo e($dt->id_detail); ?>" data-iddetail="<?php echo e($dt->id_detail); ?>" data-statusbpjsket="<?php echo e($dt->status_bpjs_ket); ?>" data-nokk="<?php echo e($dt->nomor_kk); ?>" data-noktp="<?php echo e($dt->nomor_ktp); ?>" data-bpjskes="<?php echo e($dt->nomor_bpjs_kes); ?>" data-bpjsket="<?php echo e($dt->nomor_bpjs_ket); ?>" <?php if($dt->status_bpjs_ket==1)echo "disabled";?>>
												</label>
											</div>
										</div>
									</td>
									<td>
										<button type="button" class="btn btn-primary btn-xs edit-modal" data-iddetail="<?php echo e($dt->id_detail); ?>" data-employeename="<?php echo e($dt->employee_name); ?>" data-nokk="<?php echo e($dt->nomor_kk); ?>" data-noktp="<?php echo e($dt->nomor_ktp); ?>" data-bpjskes="<?php echo e($dt->nomor_bpjs_kes); ?>" data-bpjsket="<?php echo e($dt->nomor_bpjs_ket); ?>" data-statusbpjskes="<?php echo e($dt->status_bpjs_kes); ?>" data-statusbpjsket="<?php echo e($dt->status_bpjs_ket); ?>"><i class="fa fa-edit"></i></button>
									</td>
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

    <div class="modal fade" id="modal-edit">
		<div class="modal-dialog" style="width:400px;">
			<div class="modal-content">
				<form>
					
					<?php echo e(csrf_field()); ?>

					<div class="modal-body">
						<div class="box box-primary box-solid" style="border:0px;">
							<div class="box-header">
								<label id="judul"></label>
							</div>
							<div class="box-body">
								<div class="form-group">
									<label>Nomor KK</label>
									<input type="number" id="nomor_kk" class="form-control">
								</div>
								<div class="form-group">
									<label>Nomor KTP</label>
									<input type="number" id="nomor_ktp" class="form-control">
									<input type="hidden" id="id_detail" class="form-control">
								</div>
								<div class="form-group">
									<label>Nomor BPJS Kesehatan</label>
									<input type="number" id="nomor_bpjs_kes" class="form-control">
								</div>
								<div class="form-group">
									<label>Nomor BPJS Ketenagakerjaan</label>
									<input type="number" id="nomor_bpjs_ket" class="form-control">
								</div>
								<div class="form-group">
									<div class="checkbox">
										<label>
										<input type="checkbox" id="status_bpjs_kes">
										Status BPJS Kes
										</label>
									</div>
								</div>
								<div class="form-group">
									<div class="checkbox">
										<label>
										<input type="checkbox" id="status_bpjs_ket">
										Status BPJS TK
										</label>
									</div>
								</div>
							</div>
							<div class="box-footer">
								<button type="button" class="btn btn-success pull-right" id="simpan">Save</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
	<script>
		$(document).on('click', '.edit-modal', function() {
			const checkbox1 = document.getElementById('status_bpjs_kes');
			const checkbox2 = document.getElementById('status_bpjs_ket');

			$('#id_detail').val($(this).data('iddetail'));
			$('#nomor_kk').val($(this).data('nokk'));
			$('#nomor_ktp').val($(this).data('noktp'));
			$('#nomor_bpjs_kes').val($(this).data('bpjskes'));
			$('#nomor_bpjs_ket').val($(this).data('bpjsket'));
			document.getElementById("judul").textContent = $(this).data('employeename');
			check1=$(this).data('statusbpjskes');
			if(check1==1){
				checkbox1.checked = true;
			}else{
				checkbox1.checked = false;
			}
			check2=$(this).data('statusbpjsket');
			if(check2==1){
				checkbox2.checked = true;
			}else{
				checkbox2.checked = false;
			}

			$('#modal-edit').modal('show');
		});
		$(document).on('click', '#simpan', function() {
			var nomor_ktp=$('#nomor_ktp').val();
			var karakterNumber = nomor_ktp.match(/\d/g);
			if(karakterNumber.length!=16){
				alert('Nomor KTP mempunyai '+karakterNumber.length+' digit');
			}else{

				const checkbox1 = document.getElementById('status_bpjs_kes');
				const checkbox2 = document.getElementById('status_bpjs_ket');

				var id_detail=$('#id_detail').val();
				var nomor_kk=$('#nomor_kk').val();
				var nomor_bpjs_kes=$('#nomor_bpjs_kes').val();
				var nomor_bpjs_ket=$('#nomor_bpjs_ket').val();

				const status_bpjs_kes = checkbox1.checked;
				const status_bpjs_ket = checkbox2.checked;

				$.ajaxSetup({
					type:"POST",
					url: "/Admin/EmployeeBPJS",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				
				
				$.ajax({
					data:{id_detail:id_detail,nomor_kk:nomor_kk,nomor_ktp:nomor_ktp,nomor_bpjs_kes:nomor_bpjs_kes,nomor_bpjs_ket:nomor_bpjs_ket,status_bpjs_kes:status_bpjs_kes,status_bpjs_ket:status_bpjs_ket},
					success: function(respond){
						// if(respond!="Success"){
						// 	alert(respond);
						// }else{
						// 	window.location.href="/Admin/EmployeeBPJS";
						// }
						window.location.href="/Admin/EmployeeBPJS";
					}
				})
			}
		});
		$(document).on('click', '.status_bpjs_kes', function() {
			const checkbox2 = document.getElementById('status_bpjs_kes'+id_detail);
			var id_detail=$(this).data('iddetail');
			var status_bpjs_kes=$(this).data('statusbpjskes');
			var nomor_kk=$(this).data('nokk');
			var nomor_ktp=$(this).data('noktp');
			var nomor_bpjs_kes=$(this).data('bpjskes');
			var nomor_bpjs_ket=$(this).data('bpjsket');;
			if(nomor_ktp==''||nomor_bpjs_kes==''){
				alert("Nomor KTP & Nomor BPJS Kesehatan harus diisi terlebih dahulu");
				window.location.href="/Admin/EmployeeBPJS";
			}else{
				$.ajaxSetup({
					type:"POST",
					url: "/Admin/EmployeeBPJS/Kes",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				
				
				$.ajax({
					data:{id_detail:id_detail,status_bpjs_kes:status_bpjs_kes},
					success: function(respond){
						if(respond!="Success"){
							alert(respond);
						}else{
							checkbox2.disabled = true;

						}
					}
				})
			}
			
		});
		$(document).on('click', '.status_bpjs_ket', function() {
			var id_detail=$(this).data('iddetail');
			var status_bpjs_ket=$(this).data('statusbpjsket');
			var nomor_kk=$(this).data('nokk');
			var nomor_ktp=$(this).data('noktp');
			var nomor_bpjs_kes=$(this).data('bpjskes');
			var nomor_bpjs_ket=$(this).data('bpjsket');;
			if(nomor_kk==''||nomor_ktp==''||nomor_bpjs_ket==''){
				alert("Nomor KK, Nomor KTP & Nomor BPJS TK harus diisi terlebih dahulu");
				window.location.href="/Admin/EmployeeBPJS";
			}else{
				$.ajaxSetup({
					type:"POST",
					url: "/Admin/EmployeeBPJS/TK",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				
				
				$.ajax({
					data:{id_detail:id_detail,status_bpjs_ket:status_bpjs_ket},
					success: function(respond){
						if(respond!="Success"){
							alert(respond);
						}else{
							const checkbox1 = document.getElementById('status_bpjs_ket'+id_detail);
							checkbox1.disabled = true;

						}
					}
				})
			}
			
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_employee/employee_bpjs.blade.php ENDPATH**/ ?>