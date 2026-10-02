
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
				Contract
				<small>employee</small>
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
						<div class="pull-right">
							<?php if($Judul=='Employee (Active)'||$Judul=='Employee (Permanen)'||$Judul=='Employee (Magang)'||$Judul=='Employee (Kontrak)'){?>
								<a href="/Status/Permanen" class="btn btn-app">
									<i class="fa fa-user"></i> Permanen
								</a>
								<a href="/Status/Kontrak" class="btn btn-app">
									<i class="fa fa-user"></i> Kontrak
								</a>
								<a href="/Status/Magang" class="btn btn-app">
									<i class="fa fa-user"></i> Magang
								</a>
								<a href="/Status/Other" class="btn btn-app">
									<i class="fa fa-user"></i> Others
								</a>
							<?php }elseif($Judul=='Employee (Kontrak 1)'||$Judul=='Employee (Kontrak 2)'||$Judul=='Employee (Pembaharuan)'){?>
								<a href="/Status/Active" class="btn btn-app">
									<i class="fa fa-backward"></i> Back
								</a>
								<a href="/Status/Kontrak" class="btn btn-app">
									<i class="fa fa-user"></i> Kontrak
								</a>
								<a href="/Status/Kontrak/Satu" class="btn btn-app">
									<i class="fa fa-question"></i> Kontrak 1
								</a>
								<a href="/Status/Kontrak/Dua" class="btn btn-app">
									<i class="fa fa-question"></i> Kontrak 2
								</a>
								<a href="/Status/Kontrak/Pembaharuan" class="btn btn-app">
									<i class="fa fa-question"></i> Pembaharuan
								</a>
							<?php }elseif($Judul=='Employee (Others)'||$Judul=='Employee (SAB)'||$Judul=='Employee (PSAB)'||$Judul=='Employee (PKL)'){?>
								<a href="/Status/Active" class="btn btn-app">
									<i class="fa fa-backward"></i> Back
								</a>
								<a href="/Status/Other" class="btn btn-app">
									<i class="fa fa-user"></i> Others
								</a>
								<a href="/Status/SAB" class="btn btn-app">
									<i class="fa fa-user"></i> SAB
								</a>
								<!--
								<a href="/Status/PSAB" class="btn btn-app">
									<i class="fa fa-user"></i> PSAB
								</a>
								<a href="/Status/PKL" class="btn btn-app">
									<i class="fa fa-user"></i> PKL
								</a>
								-->
							<?php }else{?>
								<!-- <a href="/Status" class="btn btn-app"><i class="fa fa-users"></i> Over All</a> -->
								<a href="/Status/Draft" class="btn btn-app">
									<i class="fa fa-question"></i> Draft
								</a>
								<a href="/Status/NonActive/0" class="btn btn-app">
									<i class="fa fa-user-times"></i> Non Active
								</a>
								<a href="/Status/Arsif/0" class="btn btn-app">
									<i class="fa fa-trash"></i> Arsif
								</a>
							<?php }?>
						</div>
					</div>
					<div class="box-body" style="overflow-x:scroll;">
						<table id="table1" class="table table-hover tabel2">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th>No</th>
									<th>ID</th>
									<th>NIK</th>
									<th>Employee Name</th>
									<th>Departmet</th>
									<th>Position</th>
									<th><i class="fa fa-clock-o"></i> Join Date</th>
									<th>Status</th>
									<th>Start</th>
									<th>Finish</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;$idemployee='';?>
								<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php if($idemployee!=$dt->idemployee){?>
									<?php if($dt->finish_contract<=date('Y-m-d')&&$dt->finish_contract!=''&&$dt->contract_name!='Permanen'){?>
										<tr <?php if($dt->finish_contract<=date('Y-m-d')&&$dt->finish_contract!=''&&$dt->contract_name!='Permanen') echo "style='background-color:#FF0;'";?>>
											<td>
												<?php
													if($dt->contract_name==''){
														$PeriodeAwal=$dt->joindate;
														$idemp=$dt->id;
													}
													else{
														$PeriodeAwal = date('Y-m-d', strtotime('+1 days', strtotime($dt->finish_contract)));
														$idemp=$dt->id_employee;
													}
												?>
												<button type="button" class="btn btn-primary btn-xs update-modal" data-action='New' data-idcontract='<?php echo $dt->id;?>' data-idemployee='<?php echo $dt->idemployee;?>' data-nmemployee='<?php echo e($dt->employee_name); ?>' data-joindate='<?php echo e($dt->joindate); ?>' data-tglawal='<?php echo e($PeriodeAwal); ?>' data-statusawal='<?php echo e($dt->contract_name); ?>'><i class="fa fa-edit"></i></button>
												<?php if($dt->contract_name!=''){?>
													<!-- <a style="padding:1px 9px;" href="/Employee/<?php echo e($idemp); ?>/<?php echo e($dt->PIN); ?>" target="_blank" type="button" class="btn btn-info btn-xs"><i class="fa fa-info"></i></a> -->
												<?php }?>
												<?php if($dt->contract_ref): ?>
													<div class="pull-right">
														<form action="/AgreementShow2" method="post">
															<input type="hidden" name="id" value="<?php echo e($dt->id); ?>">
															<?php echo e(csrf_field()); ?>

															<input type="submit" class="btn btn-primary btn-xs" value="Preview">
														</form>
													</div>
												<?php endif; ?>
											<td>
												<?php $no++;echo $no;?>
											</td>
											<td><?php echo e($dt->idemployee); ?></td>
											<td><?php echo e($dt->NIK); ?></td>
											<td title="Periode: <?php echo e($idemployee); ?>"><?php echo e($dt->employee_name); ?> (<?php echo substr($dt->gender,0,1);?>)</td>
											<td title="<?php echo e($dt->dept_name); ?>"><?php echo e($dt->dept_code); ?></td>
											<td><?php echo e($dt->position_name); ?></td>
											<td><?php echo e(date('d-M-Y',strtotime($dt->joindate))); ?></td>
											<td><?php if($Judul!='Draft Contract')echo $dt->contract_name;?></td>
											<td><?php if($Judul!='Draft Contract')echo date('d-M-Y',strtotime($dt->start_contract));?></td>
											<td>
												<?php if($Judul!='Draft Contract'){if($dt->contract_name=='Permanen'||$dt->contract_name=='Draft')echo "&nbsp;";else echo date('d-M-Y',strtotime($dt->finish_contract));}?>
												<?php if($Judul=='Employee (Draft)'){?>
													<div class="pull-right">
														<a href="/Status/Deactive/<?php echo e($dt->idemployee); ?>" type="button" class="btn btn-danger btn-xs"><i class="fa  fa-user-times"></i></a>
													</div>
												<?php }?>
												<?php if($Judul=='Employee (Non Active)'){?>
													<div class="pull-right">
														<a href="/Status/Reactive/<?php echo e($dt->idemployee); ?>" type="button" class="btn btn-primary btn-xs"><i class="fa fa-check"></i></a>
														<?php
															$markup=date('Y-m-d',strtotime('+2 months',strtotime($dt->start_contract)));
															$today=date('Y-m-d');
															if($markup<$today){?>
															<a href="/Status/Delete/<?php echo e($dt->idemployee); ?>" type="button" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
														<?php }?>
													</div>
												<?php }?>
											</td>
										</tr>
									<?php }?>
								<?php }$idemployee=$dt->idemployee?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
						<br><br></br>
						<table id="tables" class="table table-hover tabel2">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th>No</th>
									<th>ID</th>
									<th>NIK</th>
									<th>Employee Name</th>
									<th>Departmet</th>
									<th>Position</th>
									<th><i class="fa fa-clock-o"></i> Join Date</th>
									<th>Status</th>
									<th>Start</th>
									<th>Finish</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;$idemployee='';?>
								<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php if($idemployee!=$dt->idemployee){?>
								<!--
								<tr <?php if($dt->finish_contract<=date('Y-m-d')&&$dt->finish_contract!=''&&$dt->contract_name!='Permanen') echo "style='background-color:#FF0;'";?>>
								-->
								<tr>
									<td>
										<?php
											if($dt->contract_name==''){
												$PeriodeAwal=$dt->joindate;
												$idemp=$dt->id;
											}
											else{
												$PeriodeAwal = date('Y-m-d', strtotime('+1 days', strtotime($dt->finish_contract)));
												$idemp=$dt->id_employee;
											}
										?>
										<button type="button" class="btn btn-primary btn-xs update-modal" data-action='New' data-idcontract='<?php echo $dt->id;?>' data-idemployee='<?php echo $dt->idemployee;?>' data-nmemployee='<?php echo e($dt->employee_name); ?>' data-joindate='<?php echo e($dt->joindate); ?>' data-tglawal='<?php echo e($PeriodeAwal); ?>' data-statusawal='<?php echo e($dt->contract_name); ?>'><i class="fa fa-edit"></i></button>
										<?php if($dt->contract_name!=''){?>
											<a style="padding:1px 9px;" href="/Employee/<?php echo e($idemp); ?>/<?php echo e($dt->PIN); ?>" target="_blank" type="button" class="btn btn-info btn-xs"><i class="fa fa-info"></i></a>
										<?php }?>
										<?php if($dt->contract_ref): ?>
											<div class="pull-right">
												<form action="/AgreementShow2" method="post">
													<input type="hidden" name="id" value="<?php echo e($dt->id); ?>">
													<?php echo e(csrf_field()); ?>

													<input type="submit" class="btn btn-primary btn-xs" value="Preview">
												</form>
											</div>
										<?php endif; ?>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td><?php echo e($dt->idemployee); ?></td>
									<td><?php echo e($dt->NIK); ?></td>
									<td title="Periode: <?php echo e($idemployee); ?>"><?php echo e($dt->employee_name); ?> (<?php echo substr($dt->gender,0,1);?>)</td>
									<td title="<?php echo e($dt->dept_name); ?>"><?php echo e($dt->dept_code); ?></td>
									<td><?php echo e($dt->position_name); ?></td>
									<td><?php echo e(date('d-M-Y',strtotime($dt->joindate))); ?></td>
									<td><?php if($Judul!='Draft Contract')echo $dt->contract_name;?></td>
									<td><?php if($Judul!='Draft Contract')echo date('d-M-Y',strtotime($dt->start_contract));?></td>
									<td>
										<?php if($Judul!='Draft Contract'){if($dt->contract_name=='Permanen'||$dt->contract_name=='Draft')echo "&nbsp;";else echo date('d-M-Y',strtotime($dt->finish_contract));}?>
										<?php if($Judul=='Employee (Draft)'){?>
											<div class="pull-right">
												<a href="/Status/Deactive/<?php echo e($dt->idemployee); ?>" type="button" class="btn btn-danger btn-xs"><i class="fa  fa-user-times"></i></a>
											</div>
										<?php }?>
										<?php if($Judul=='Employee (Non Active)'){?>
											<div class="pull-right">
												<a href="/Status/Reactive/<?php echo e($dt->idemployee); ?>" type="button" class="btn btn-primary btn-xs"><i class="fa fa-check"></i></a>
												<?php
													$markup=date('Y-m-d',strtotime('+2 months',strtotime($dt->start_contract)));
													$today=date('Y-m-d');
													if($markup<$today){?>
													<a href="/Status/Delete/<?php echo e($dt->idemployee); ?>" type="button" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
												<?php }?>
											</div>
										<?php }?>
									</td>
								</tr>
								<?php }$idemployee=$dt->idemployee?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>No</th>
									<th>PIN</th>
									<th>NIK</th>
									<th>Employee Name</th>
									<th>Departmet</th>
									<th>Position</th>
									<th><i class="fa fa-clock-o"></i> Join Date</th>
									<th>Status</th>
									<th>Start</th>
									<th>Finish</th>
								</tr>
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

	<div class="modal fade" id="modal-update">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
			<form action="/Status/New" method="post">
			<input type="hidden" id="idaction" name="action">
			<input type="hidden" id="idcontract" name="id_contract">
			<input type="hidden" id="idemployee" name="id_employee">
			<input type="hidden" id="joindate" name="join_date">
			<input type="hidden" id="statusawal" name="statusawal">

			<?php echo e(csrf_field()); ?>

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Perubahan Status</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Employee Name</label>
						<input type="text" id="nmemployee" class="form-control" disabled>
					</div>
					<div class="form-group">
						<label>Agreement</label>
						<input type="hidden" name="id_agreement" id="idagreement" class="form-control">								
						<input type="text" name="nomor_perjanjian" id="nomorperjanjian" class="form-control">								
					</div>
					<div class="form-group">
						<label>New Status</label>
						<select name="contract_name" id="contractname" class="form-control">
							<option></option>
							<option value="Magang" id="magang">Magang</option>
							<option value="Kontrak" id="kontrak">Kontrak</option>
							<option value="Permanen" id="permanen">Permanen</option>
							<option value="Resign" class="finish" id="resign">Resign</option>
							<option value="Kabur" class="finish" id="kabur">Kabur</option>
							<option value="End Contract" class="finish" id="finish">End Contract</option>
							<option value="PSAB" id="psab">PSAB</option>
							<option value="SAB" id="sab">SAB</option>
							<option value="PKL" id="PKL">PKL</option>
							<option value="Other" id="other">Other</option>
						</select>
					</div>
					<div class="form-group waktu1">
						<label>Start</label>
						<input type="date" name="start_contract" id="startcontract" class="form-control">								
					</div>
					<div class="form-group waktu2">
						<label>Finish</label>
						<input type="date" name="finish_contract" id="finishcontract" class="form-control">								
					</div>

				</div>
				<div class="modal-footer" style="text-align:left;">
					<input type="submit" class="btn btn-primary" value="Update">
					<button type="button" class="btn btn-default pull-right cancelafter" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
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
	<!--  on Load  -->
	<script>
		$( document ).ready(function() {
			$('.waktu1').hide();
			$('.waktu2').hide();
			$(document).on('change', '#contractname', function() {
				//if($(this).val()=='Magang'||$(this).val()=='Kontrak 1'||$(this).val()=='Kontrak 2'||$(this).val()=='PKL'||$(this).val()=='Pembaharuan'){
				if($(this).val()=='Magang'||$(this).val()=='Kontrak'||$(this).val()=='PKL'){
					$('.waktu1').show();
					$('.waktu2').show();
				}else if($(this).val()=='Permanen'||$(this).val()=='PSAB'||$(this).val()=='SAB'||$(this).val()=='Other'){
					$('.waktu1').show();
					$('.waktu2').hide();
				}else{
					$('.waktu1').show();
					$('.waktu2').hide();
				}
			});
		});
	</script>
	<script type="text/javascript">
		$(document).on('click', '.update-modal', function() {
			$('.waktu1').hide();
			$('.waktu2').hide();
			$('#contractname').val(0);
			$('#idaction').val($(this).data('action'));
			$('#idcontract').val($(this).data('idcontract'));
			$('#idemployee').val($(this).data('idemployee'));
			$('#nmemployee').val($(this).data('nmemployee'));
			$('#joindate').val($(this).data('joindate'));
			$('#startcontract').val($(this).data('tglawal'));
			$('#statusawal').val($(this).data('statusawal'));
			$('#idagreement').val('');
			$('#nomorperjanjian').val('');
			if($(this).data('statusawal')==''){
				$('#magang').show();
				$('#kontrak').show();
				$('#permanen').hide();
				$('#sab').show();
				$('#psab').show();
				$('#PKL').show();
				$('#other').show();
				$('.finish').hide();
			}
			else if($(this).data('statusawal')=='Magang'){
				$('#magang').show();
				$('#kontrak').show();
				$('#permanen').hide();
				$('#sab').hide();
				$('#psab').hide();
				$('#PKL').hide();
				$('#other').hide();
				$('.finish').show();
			}
			else if($(this).data('statusawal')=='Kontrak'){
				$('#magang').hide();
				$('#kontrak').show();
				$('#permanen').show();
				$('#sab').hide();
				$('#psab').hide();
				$('#PKL').hide();
				$('#other').hide();
				$('.finish').show();
			}
			else if($(this).data('statusawal')=='Permanen'){
				$('#magang').hide();
				$('#kontrak').hide();
				$('#permanen').hide();
				$('#sab').hide();
				$('#psab').hide();
				$('#PKL').hide();
				$('#other').hide();
				$('#resign').show();
				$('#kabur').show();
				$('#finish').hide();
			}
			else{
				$('#magang').hide();
				$('#kontrak').hide();
				$('#permanen').hide();
				$('#sab').hide();
				$('#psab').hide();
				$('#PKL').hide();
				$('#other').hide();
				$('.finish').show();
			}
			$('#modal-update').modal('show');
			
			var idstatus=$(this).data('idcontract');
			var idemployee=$(this).data('idemployee');
			$.ajaxSetup({
				type:"POST",
				url: "/ContractCheck",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{idstatus:idstatus,idemployee:idemployee},
				success: function(respond){
					$('.waktu1').show();
					$('.waktu2').show();
					str=respond.split('#');
					$('#idagreement').val(str[0]);
					$('#nomorperjanjian').val(str[1]);
					$('#contractname').val(str[2]);
					$('#startcontract').val(str[3]);
					$('#finishcontract').val(str[4]);
				}
			})

		});

	</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_employee/contract(custome).blade.php ENDPATH**/ ?>