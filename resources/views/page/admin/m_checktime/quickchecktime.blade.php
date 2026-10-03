@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
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
				Absensi Manual
				<small>checkin/checkout</small>
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-clock-o"></i> 
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
				<div class="box box-info collapsed-box box-solid">
					<div class="box-header">
						<h3 class="box-title">
							<sub style="font-size: 18px"><i class="fa fa-calendar"></i>&nbsp; <?php echo date('l, d F Y',strtotime($Tgl));?></sub>
						</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body" style="overflow-x:scroll;">
						<table id="table2" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>Date</th>
									<th>Total Employee</th>
									<th>Plan Checkin</th>
									<th>Actual Checkin</th>
									<th>Cuti</th>
									<th>Abcent</th>
									<th>Presence</th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							@foreach($tb_sumpresence as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->tanggal}}</td>
									<td>{{$dt->total_employee}}</td>
									<td>{{$dt->plan}}</td>
									<td>{{$dt->hadir}}</td>
									<td>{{$dt->cuti}}</td>
									<td>{{$dt->absen}}</td>
									<td>
										<?php if($dt->plan>0)echo number_format($dt->hadir/$dt->plan,2)*100;?> %
										<div class="pull-right">
											<a title="Show" href='/EMS/Admin/Checktime/Select/<?php echo date('Y/m/d',strtotime($dt->tanggal));?>'><button type="button" class="btn btn-primary btn-xs">Select</button></a>
										</div>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-danger">
					<div class="box-header">
						<h3 class="box-title">
							<sub style="font-size: 18px">Employee Abcent</sub>
						</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-xs" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-xs" data-widget="remove"><i class="fa fa-times"></i>
							</button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body" style="overflow-x:scroll;">
					<table id="tables" class="table table-bordered table-hover">
						<thead>
						<tr>
							<th style="width:30px;">No</th>
							<th>PIN</th>
							<th>NIK</th>
							<th>Name</th>
							<th>Position</th>
							<th>Group</th>
							<th>Working Time</th>
							<th style="width:60px;">&nbsp;</th>
						</tr>
						</thead>
						<tbody>
						@foreach($tb_sumpresence_now as $dtabsen)
						<?php
							date_default_timezone_set("Asia/Jakarta");
							$Tgl=date('Y-m-d');
							$dtcenter=explode('&',$dtabsen->unplanned_absent);
							$nabsen=substr_count($dtabsen->unplanned_absent,"&");
							$max='0';
							$host= mysqli_connect("192.168.1.4","ems","123456","db_ems");
							for($i=0;$i<=$nabsen;$i++){
							$uid=$dtcenter[$i];
							$qry=mysqli_query($host,"select * from tb_employees left join tb_departments on tb_departments.id=tb_employees.dept_id left join tb_employee_shifts on tb_employees.id=tb_employee_shifts.id_employee where tb_employees.id='$uid' and tb_employees.status='1' and tb_employees.PIN>'0' and tb_employee_shifts.status='1' and ($filter)")or die(mysqli_error($host));
							while($dtunplanned=mysqli_fetch_array($qry)){
								$no=$i+1;
								$id_shift=$dtunplanned['id_shift'];
								$qry3=mysqli_query($host,"select tb_group_shifts.*,tb_groups.cycle from tb_group_shifts left join tb_groups on tb_groups.group=tb_group_shifts.group where tb_group_shifts.id='$id_shift'")or die(mysqli_error($host));
								while($dt3=mysqli_fetch_array($qry3)){
									$id_group=$dt3['group'];
									$shift_code=$dt3['shift_code'];
									$tgl1 = new DateTime($dt3['start_implement']);
									$tgl2 = new DateTime($Tgl);
									$diffdays = $tgl2->diff($tgl1)->days;
									$cycle=$dt3['cycle'];
									$diffcycle=Floor($diffdays/$cycle);
									$modcycle=$diffdays%$cycle;
									$modcycle++;

									$qry4=mysqli_query($host,"select * from tb_cycles where tb_cycles.group='$id_group' and days='$modcycle'")or die(mysqli_error($host));
									while($dt4=mysqli_fetch_array($qry4)){
										$shift=$dt4['shift'];
										$check_in=$dt4['check_in'];
										$check_out=$dt4['check_out'];
										$advance=$dt4['advance'];
										$cross=$dt4['cross'];
									}
									//Return Checkin Range
									$cdatein=date('Y-m-d',strtotime($Tgl));
									if($advance==1){
										$date = date_create($cdatein);
										date_add($date, date_interval_create_from_date_string('-1 days'));
										$cdatein= date_format($date, 'Y-m-d');
									}
									$ncdatein=$cdatein." ".$check_in;
									//Return Checkout Range
									$cdateout=date('Y-m-d',strtotime($Tgl));
									if($cross==1){
										$date = date_create($cdateout);
										date_add($date, date_interval_create_from_date_string('+1 days'));
										$cdateout= date_format($date, 'Y-m-d');
									}
									$ncdateout=$cdateout." ".$check_out;
								}

								$checker=Auth::user()->name;
								echo "<tr>";
								echo "<td>".$no."</td>";
								echo "<td>".$dtunplanned['PIN']."</td>";
								echo "<td>".$dtunplanned['NIK']."</td>";
								echo "<td>".$dtunplanned['employee_name']."</td>";
								echo "<td>".$dtunplanned['dept_name']."</td>";
								echo "<td>".$shift_code.'.'.$shift."</td>";
								echo "<td>".$check_in.' ~ '.$check_out."</td>";
								echo "<td>";
								echo "<div class='box-tools pull-right'> <a href='/EMS/Admin/Checktime/Quick/".$dtunplanned['PIN']."/".$ncdatein."/".$ncdateout."/".$checker."/".$dtunplanned['NIK']."/".$dtunplanned['employee_name']."/Manual'><button type='button' class='btn btn-success btn-xs'><i class='fa fa-check'></i></button></a> ";
								echo "<button type='button' class='btn btn-danger btn-xs resign-modal' data-delid1='".$dtunplanned['PIN']."' data-delid2='".$ncdatein."' data-delid3='".$checker."' data-delname1='".$dtunplanned['employee_name']."' style='padding:1px 6px;'><i class='fa fa-trash'></i></button> </div>";
								echo "</td>";
								echo "</tr>";
							}
							}
						?>
						@endforeach
						</tbody>
						<tfoot>
						<tr>
							<th style="width:30px;">No</th>
							<th>PIN</th>
							<th>NIK</th>
							<th>Name</th>
							<th>Department</th>
							<th>Group</th>
							<th>Working Time</th>
							<th style="width:20px;">&nbsp;</th>
						</tr>
						</tfoot>
					</table>

					</div>
				</div>
			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

    <div class="modal fade" id="modal-resign">
            <div class="modal-dialog box box-danger" style="width:400px;">
                    <div class="modal-content">
                            <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Delete Confirmation</h4>
                            </div>
                            <div class="modal-body">
                                    Click Yes to Delete : <b id="delname1"></b> ?
                                    <input type="text" id="delid1">
									<input type="text" id="delid2">
									<input type="text" id="delid3">
                            </div>
                            <div class="modal-footer">
                                    <button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yes, Delete</button>
                                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                            </div>
                    </div>

                    <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
    </div>

    @if ($message = Session::get('success'))
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			{{$message}}
		</div>
    @endif
	@if ($errors->any())
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				@if($errors->has('date_off'))
					- Date harus diisi<br>
				@endif
				@if($errors->has('category'))
					- Category harus dipilih<br>
				@endif
		</div>
	@endif

@endsection
@section('Scripts')
	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 5,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : false,
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


	</script>	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		// Edit Data
		$(document).on('click', '.edit-modal', function() {
			$('#syside').val($(this).data('sysid'));
                        $('#dateoff').val($(this).data('dateoff'));
                        $('#category').val($(this).data('category'));
                        $('#description').val($(this).data('description'));
                        //alert('masuk');
		});
		// Delete Data
		$(document).on('click', '.resign-modal', function() {
			$('#delid1').val($(this).data('delid1'));
			$('#delid2').val($(this).data('delid2'));
			$('#delid3').val($(this).data('delid3'));
			$('#delname1').text($(this).data('delname1'));
			$('#modal-resign').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid1').val();
			var y=$('#delid2').val();
			var z=$('#delid3').val();
			window.location.href='/EMS/Admin/Checktime/Resign/'+x+'/'+y+'/'+z;
		});
	</script>
@endsection
