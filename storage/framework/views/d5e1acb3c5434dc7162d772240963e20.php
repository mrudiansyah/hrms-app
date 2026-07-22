
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
							<a href='/Status/KSK/Create/<?php echo e($periode); ?>'><button type="button" class="btn btn-default btn-xs"><i class="fa fa-backward"></i> &nbsp;BACK</button></a>
							<a href='/Status/KSK/Performance/<?php echo e($id_ksk); ?>/<?php echo e($periode); ?>'><button type="button" class="btn btn-info btn-xs"><i class="fa fa-star-half-o"></i> &nbsp;PERFORMANCE</button></a>
							<?php if($id_ksk>0): ?>
								<a href='/Employee/KSK/Print/<?php echo e($id_ksk); ?>' target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> &nbsp;PRINT</button></a>
							<?php endif; ?>
						</div>
					</div>
					<div class="box-body" style="overflow-x:scroll;">
						<table id="tables" class="table table-hover tabel2">
							<thead>
								<tr>
									<th>NO.</th>
									<th>NIK</th>
									<th>NAME</th>
									<th>JOIN DATE</th>
									<th>DEPT</th>
									<th>START</th>
									<th>END</th>
									<th>DURATION</th>
									<th>APPROVAL</th>
									<th>LEGALIZE</th>
									<th>JUDGE</th>
									<th>REASON</th>
									<th>MONTH</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_ksk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php $bulan=$dt->months%12;$tahun=($dt->months-$bulan)/12;?>
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->employee_name); ?></td>
									<td><?php echo e($dt->join_date); ?></td>
									<td><?php echo e($dt->dept_code); ?></td>
									<td><?php echo e($dt->first_contract); ?></td>
									<td><?php echo e($dt->finish_contract); ?></td>
									<td><?php if($tahun>0)echo $tahun.' Tahun ';if($bulan>0)echo $bulan.' Bulan';?></td>
									<td>
										<?php
										$host = mysqli_connect("192.168.1.4","ems","123456","db_ems");
										$id_ksk_detail=$dt->id;
										$approval1=$dt->approval1;
										$approval2=$dt->approval2;
										$approval3=$dt->approval3;
										$approval4=$dt->approval4;
										$approval5=$dt->approval5;
										$approval6=$dt->approval6;
										if($approval1!='')$approve1="<span class='badge bg-gray' title='".$dt->approvalname1."'>1</span>";else $approve1="";
										if($approval2!='')$approve2="<span class='badge bg-gray' title='".$dt->approvalname2."'>2</span>";else $approve2="";
										if($approval3!='')$approve3="<span class='badge bg-gray' title='".$dt->approvalname3."'>3</span>";else $approve3="";
										if($approval4!='')$approve4="<span class='badge bg-gray' title='".$dt->approvalname4."'>4</span>";else $approve4="";
										if($approval5!='')$approve5="<span class='badge bg-gray' title='".$dt->approvalname5."'>5</span>";else $approve5="";
										if($approval6!='')$approve6="<span class='badge bg-gray' title='".$dt->approvalname6."'>6</span>";else $approve6="";
										$status=0;
										$judge1='';
										$judge2='';
										$judge3='';
										$judge4='';
										$judge5='';
										$judge6='';
										$qry1=mysqli_query($host,"select * from tb_ksk_detail_status where id_ksk_detail='$id_ksk_detail' and id_employee='$approval1'")or die(mysqli_error($host));
										while($dt1=mysqli_fetch_array($qry1)){
											if($dt1['judge']=='PERMANENCY')$approve1="<span class='badge bg-green'>1</span>";
											elseif($dt1['judge']=='EXTEND')$approve1="<span class='badge bg-yellow'>1</span>";
											elseif($dt1['judge']=='NOT EXTEND')$approve1="<span class='badge bg-red'>1</span>";
											$judge1=$dt1['judge'];
										}
										$qry2=mysqli_query($host,"select * from tb_ksk_detail_status where id_ksk_detail='$id_ksk_detail' and id_employee='$approval2'")or die(mysqli_error($host));
										while($dt2=mysqli_fetch_array($qry2)){
											if($dt2['judge']=='PERMANENCY')$approve2="<span class='badge bg-green'>2</span>";
											elseif($dt2['judge']=='EXTEND')$approve2="<span class='badge bg-yellow'>2</span>";
											elseif($dt2['judge']=='NOT EXTEND')$approve2="<span class='badge bg-red'>2</span>";
											$judge2=$dt2['judge'];
										}
										$qry3=mysqli_query($host,"select * from tb_ksk_detail_status where id_ksk_detail='$id_ksk_detail' and id_employee='$approval3'")or die(mysqli_error($host));
										while($dt3=mysqli_fetch_array($qry3)){
											if($dt3['judge']=='PERMANENCY')$approve3="<span class='badge bg-green'>3</span>";
											elseif($dt3['judge']=='EXTEND')$approve3="<span class='badge bg-yellow'>3</span>";
											elseif($dt3['judge']=='NOT EXTEND')$approve3="<span class='badge bg-red'>3</span>";
											$judge3=$dt3['judge'];
										}
										$qry4=mysqli_query($host,"select * from tb_ksk_detail_status where id_ksk_detail='$id_ksk_detail' and id_employee='$approval4'")or die(mysqli_error($host));
										while($dt4=mysqli_fetch_array($qry4)){
											if($dt4['judge']=='PERMANENCY')$approve4="<span class='badge bg-green'>4</span>";
											elseif($dt4['judge']=='EXTEND')$approve4="<span class='badge bg-yellow'>4</span>";
											elseif($dt4['judge']=='NOT EXTEND')$approve4="<span class='badge bg-red'>4</span>";
											$judge4=$dt4['judge'];
										}
										$qry5=mysqli_query($host,"select * from tb_ksk_detail_status where id_ksk_detail='$id_ksk_detail' and id_employee='$approval5'")or die(mysqli_error($host));
										while($dt5=mysqli_fetch_array($qry5)){
											if($dt5['judge']=='PERMANENCY')$approve5="<span class='badge bg-green'>5</span>";
											elseif($dt5['judge']=='EXTEND')$approve5="<span class='badge bg-yellow'>5</span>";
											elseif($dt5['judge']=='NOT EXTEND')$approve5="<span class='badge bg-red'>5</span>";
											$judge5=$dt5['judge'];
										}
										$qry6=mysqli_query($host,"select * from tb_ksk_detail_status where id_ksk_detail='$id_ksk_detail' and id_employee='$approval6'")or die(mysqli_error($host));
										while($dt6=mysqli_fetch_array($qry6)){
											if($dt6['judge']=='PERMANENCY')$approve6="<span class='badge bg-green'>6</span>";
											elseif($dt6['judge']=='EXTEND')$approve6="<span class='badge bg-yellow'>6</span>";
											elseif($dt6['judge']=='NOT EXTEND')$approve6="<span class='badge bg-red'>6</span>";
											$judge6=$dt6['judge'];
										}
										if($approval1==$id_employee){
											$status=1;
										}else if($approval2==$id_employee&&$judge1!=''){
											$status=1;
										}else if($approval3==$id_employee&&$judge2!=''){
											$status=1;
										}else if($approval4==$id_employee&&$judge3!=''){
											$status=1;
										}else if($approval5==$id_employee&&$judge4!=''){
											$status=1;
										}else if($approval6==$id_employee&&$judge5!=''){
											$status=1;
										}else if($approval6==$id_employee&&$judge5!=''){
											$status=1;
										}
										echo $approve1.$approve2.$approve3.$approve4.$approve5.$approve6;
										?>
									</td>
									<td>
										<?php
										$host = mysqli_connect("192.168.1.4","ems","123456","db_ems");
										$id_ksk_detail=$dt->id;
										$legalize1=$dt->legalize1;
										$legalize2=$dt->legalize2;
										$legalize3=$dt->legalize3;
										$legalize4=$dt->legalize4;
										if($legalize1!='')$legaliz1="<span class='badge bg-gray' title='".$dt->legalizename1."'>1</span>";else $legaliz1="";
										if($legalize2!='')$legaliz2="<span class='badge bg-gray' title='".$dt->legalizename2."'>2</span>";else $legaliz2="";
										if($legalize3!='')$legaliz3="<span class='badge bg-gray' title='".$dt->legalizename3."'>3</span>";else $legaliz3="";
										if($legalize4!='')$legaliz4="<span class='badge bg-gray' title='".$dt->legalizename4."'>4</span>";else $legaliz4="";
										$status=0;
										$judge7='';
										$judge8='';
										$judge9='';
										$judge10='';
										$qry7=mysqli_query($host,"select * from tb_ksk_detail_status where id_ksk_detail='$id_ksk_detail' and id_employee='$legalize1'")or die(mysqli_error($host));
										while($dt7=mysqli_fetch_array($qry7)){
											if($dt7['judge']=='PERMANENCY')$legaliz1="<span class='badge bg-green'>1</span>";
											elseif($dt7['judge']=='EXTEND')$legaliz1="<span class='badge bg-yellow'>1</span>";
											elseif($dt7['judge']=='NOT EXTEND')$legaliz1="<span class='badge bg-red'>1</span>";
											$judge7=$dt7['judge'];
										}
										$qry8=mysqli_query($host,"select * from tb_ksk_detail_status where id_ksk_detail='$id_ksk_detail' and id_employee='$legalize2'")or die(mysqli_error($host));
										while($dt8=mysqli_fetch_array($qry8)){
											if($dt8['judge']=='PERMANENCY')$legaliz2="<span class='badge bg-green'>2</span>";
											elseif($dt8['judge']=='EXTEND')$legaliz2="<span class='badge bg-yellow'>2</span>";
											elseif($dt8['judge']=='NOT EXTEND')$legaliz2="<span class='badge bg-red'>2</span>";
											$judge8=$dt8['judge'];
										}
										$qry9=mysqli_query($host,"select * from tb_ksk_detail_status where id_ksk_detail='$id_ksk_detail' and id_employee='$legalize3'")or die(mysqli_error($host));
										while($dt9=mysqli_fetch_array($qry9)){
											if($dt9['judge']=='PERMANENCY')$legaliz3="<span class='badge bg-green'>3</span>";
											elseif($dt9['judge']=='EXTEND')$legaliz3="<span class='badge bg-yellow'>3</span>";
											elseif($dt9['judge']=='NOT EXTEND')$legaliz3="<span class='badge bg-red'>3</span>";
											$judge9=$dt9['judge'];
										}
										$qry10=mysqli_query($host,"select * from tb_ksk_detail_status where id_ksk_detail='$id_ksk_detail' and id_employee='$legalize4'")or die(mysqli_error($host));
										while($dt10=mysqli_fetch_array($qry10)){
											if($dt10['judge']=='PERMANENCY')$legaliz4="<span class='badge bg-green'>4</span>";
											elseif($dt10['judge']=='EXTEND')$legaliz4="<span class='badge bg-yellow'>4</span>";
											elseif($dt10['judge']=='NOT EXTEND')$legaliz4="<span class='badge bg-red'>4</span>";
											$judge10=$dt10['judge'];
										}
										if($legalize1==$id_employee&&$dt->approval_status==1){
											$status=1;
										}else if($legalize2==$id_employee&&$judge7!=''){
											$status=1;
										}else if($legalize3==$id_employee&&$judge8!=''){
											$status=1;
										}else if($legalize4==$id_employee&&$judge9!=''){
											$status=1;
										}
										echo $legaliz1.$legaliz2.$legaliz3.$legaliz4;
										?>
									</td>
									<td><?php echo e($dt->judge); ?>

									</td>
									<td><?php echo e($dt->reason); ?></td>
									<td>
										<?php echo e($dt->next_contract); ?>

										<div class="pull-right">
											<?php if($dt->judge==''){?>
												<button type="button" class="btn btn-warning btn-xs refresh-modal" data-idkskdetail='<?php echo e($dt->id); ?>'><i class="fa fa-refresh"></i></button>
												<button type="button" class="btn btn-primary btn-xs update-modal" data-idksk='<?php echo e($dt->id); ?>' data-warningletter='<?php echo e($dt->warning_letter); ?>' data-sick='<?php echo e($dt->sick); ?>' data-permit='<?php echo e($dt->permit); ?>' data-alpa='<?php echo e($dt->alpa); ?>' data-late='<?php echo e($dt->late); ?>' data-minutes='<?php echo e($dt->minutes); ?>' data-performance='<?php echo e($dt->performance); ?>'><i class="fa fa-edit"></i></button>
												<?php }?>
										</div>
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

	<div class="modal fade" id="modal-update">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
			<form action="/Status/KSK/Update" method="post">
			<input type="hidden" id="idksk" name="idksk">
			<?php echo e(csrf_field()); ?>

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Perubahan Status</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-ld-6 col-md-6 col-xs-12">
							<div class="form-group">
								<label>Warning Letter</label>
								<input type="text" class="form-control" id="warning_letter" name="warning_letter">
							</div>
							<div class="form-group">
								<label>Sick</label>
								<input type="number" class="form-control" id="sick" name="sick">
							</div>
							<div class="form-group">
								<label>Permit</label>
								<input type="number" class="form-control" id="permit" name="permit">
							</div>
							<div class="form-group">
								<label>Alpa</label>
								<input type="number" class="form-control" id="alpa" name="alpa">
							</div>
						</div>
						<div class="col-ld-6 col-md-6 col-xs-12">
							<div class="form-group">
								<label>Late (times)</label>
								<input type="number" class="form-control" id="late" name="late">
							</div>
							<div class="form-group">
								<label>Late (minutes)</label>
								<input type="number" class="form-control" id="minutes" name="minutes">
							</div>
							<div class="form-group">
								<label>Performance</label>
								<select name="performance" id="performance" class="form-control">
									<option value="">&nbsp;</option>
									<option value="A+">A+</option>
									<option value="A">A</option>
									<option value="B">B</option>
									<option value="C">C</option>
									<option value="D">D</option>
									<option value="E">E</option>
								</select>
							</div>
						</div>
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
	<script type="text/javascript">
		$(document).on('click', '.update-modal', function() {
			$('#idksk').val($(this).data('idksk'));
			$('#warning_letter').val($(this).data('warningletter'));
			$('#sick').val($(this).data('sick'));
			$('#permit').val($(this).data('permit'));
			$('#alpa').val($(this).data('alpa'));
			$('#late').val($(this).data('late'));
			$('#minutes').val($(this).data('minutes'));
			$('#performance').val($(this).data('performance'));
			$('#modal-update').modal('show');
		});
		$(document).on('click', '.refresh-modal', function() {
			var x=$(this).data('idkskdetail');

			$.ajaxSetup({
				type:"POST",
				url: "/Status/KSKDetail/Refresh",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{id:x},
				success: function(respond){
					if(respond=='Sukses'){
						location.reload();
					}else{
						alert(respond);
					}
				}
			})
		});
	</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_employee/ksk_detail.blade.php ENDPATH**/ ?>