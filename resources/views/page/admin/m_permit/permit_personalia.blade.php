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
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		vertical-align:middle;
		text-align:left;
		}	
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
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
				Employee Permit
				<small>form ijin</small>
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
							<i class="fa fa-list"></i>
							<h3 class="box-title" id="judul">Legalize Permit</h3>
							<div class="pull-right">
								<!-- 
								<a class="btn btn-app table2">
									<?php //if(isset($qty_permit_new)&&$qty_permit_new>0)echo "<span class='badge bg-yellow'>".$qty_permit_new."</span>";?>
									<i class="fa fa-file-o"></i> New Permit
								</a>
								-->
								<!-- <a class="btn btn-app table3">
									<?php //if(isset($qty_permit_proccess)&&$qty_permit_proccess>0)echo "<span class='badge bg-green'>".$qty_permit_proccess."</span>";?>
									<i class="fa fa-file"></i> Approved
								</a> -->
								<!-- Hide
								<a class="btn btn-app tables">
									<i class="fa fa-check-square-o"></i> Complete
								</a>
								<a class="btn btn-app table4">
									<i class="fa fa-trash"></i> Abort
								</a>
								-->
							</div>
						</div>
						<div class="box-body" style="min-height:200px;overflow-x:scroll;">
							<div id="tabel2">
								<table id="table2" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>DOC.DATE</th>
											<th>FORM</th>
											<th>NIK</th>
											<th>EMPLOYEE NAME</th>
											<th>DEPT</th>
											<th>DATE</th>
											<th>TIME</th>
											<th>KEPERLUAN</th>
											<th>APPROVED</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										@foreach($tb_permit_new as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->doc_date}}</td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td>{{$dt->NIK}}</td>
											<td>{{$dt->employee_name}}</td>
											<td>{{$dt->dept_name}}</td>
											<td><?php echo date('Y-m-d',strtotime($dt->apply_date));?></td>
											<td><?php echo date('H:i',strtotime($dt->start_izin));if($dt->finish_izin!=$dt->start_izin)echo " ~ ".date('H:i',strtotime($dt->finish_izin));?></td>
											<td>
												{{$dt->keperluan}} {{$dt->keluhan}} {{$dt->berobat_ke}}
											</td>
											<td>
												{{$dt->nama_atasan}}
												<div class="pull-right">
													@if (request()->user()->hasRole('root')||request()->user()->hasRole('hr_access'))
														<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->employee_name}} for {{$dt->apply_date}}"><i class="fa fa-trash"></i></button>
													@endif
													<a href="/Permit/Preview/{{$dt->id}}" title="Cetak" type="button" class="btn btn-info btn-xs" target="_balnk"><i class="fa fa-print"></i></a>
												</div>
											</td>
										</tr>
										@endforeach
									<tbody>
								</table>
							</div>
							<div id="tabel3">
								<table id="table3" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>DOC.DATE</th>
											<th>FORM</th>
											<th>NIK</th>
											<th>EMPLOYEE NAME</th>
											<th>DEPT</th>
											<th>DATE</th>
											<th>TIME</th>
											<th>KEPERLUAN</th>
											<th>APPROVED</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										@foreach($tb_permit_proccess as $dt)
										<tr>
											<td>
												<div class="pull-right"><a href="/Permit/Preview/{{$dt->id}}" title="Cetak" type="button" class="btn btn-info btn-xs" target="_balnk"><i class="fa fa-print"></i></a></div>
												<?php $no++;echo $no;?>
											</td>
											<td>{{$dt->doc_date}}</td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td>{{$dt->NIK}}</td>
											<td>{{$dt->employee_name}}</td>
											<td>{{$dt->dept_code}}</td>
											<td><?php echo date('d-M',strtotime($dt->apply_date));?></td>
											<td><?php echo date('H:i',strtotime($dt->start_izin));if($dt->finish_izin!=$dt->start_izin&&$dt->finish_izin!='')echo " ~ ".date('H:i',strtotime($dt->finish_izin));?></td>
											<td>
												{{$dt->keperluan}} {{$dt->keluhan}} {{$dt->berobat_ke}}
											</td>
											<td>
												{{$dt->nama_atasan}}
												<div class="pull-right">
													
													<?php if($dt->status_disetujui=='1'){?>
														<?php if($dt->category=='A'){?><button title="Approve" type="button" class="approve-modal btn btn-success btn-xs" data-approveid="{{$dt->id}}" data-approvename="{{$dt->employee_name}}"><i class="fa fa-check-square-o"></i></button> <?php }else{?>
														<button title="Approve" type="button" class="approve-modals btn btn-success btn-xs" data-sysid="{{$dt->id}}" data-employeename="{{$dt->employee_name}}" data-start="{{$start}}" data-finish="{{$finish}}" data-minutes="{{$dt->minutes}}" data-category="{{$dt->category}}"><i class="fa fa-check-square-o"></i></button>
														<?php }?>
														<button title="Refuse" type="button" class="refuse-modal btn btn-warning btn-xs" data-refuseid="{{$dt->id}}" data-refusename="{{$dt->employee_name}}"><i class="fa fa-close"></i></button>
													<?php }?>
													@if (request()->user()->hasRole('root')||request()->user()->hasRole('hr_access'))
														<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->employee_name}} for {{$dt->apply_date}}"><i class="fa fa-trash"></i></button>
													@endif
												</div>
											</td>
										</tr>
										@endforeach
									<tbody>
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
	<!-- Modal Approve -->
	<div class="modal fade" id="modal-approve">
		<div class="modal-dialog box box-success" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Approve Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Approve : <b id="approvename"></b> ?
					<input type="hidden" id="approveid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success pull-left approve" data-dismiss="modal">Yes, Approve</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- Modal Refuse -->
	<div class="modal fade" id="modal-refuse">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Refuse Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Approve : <b id="refusename"></b> ?
					<input type="hidden" id="refuseid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left refuse" data-dismiss="modal">Yes, Refuse</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-approves">
		<div class="modal-dialog box box-success" style="width:300px;">
			<form action="/Permit/Scurity" method="post">
			{{ csrf_field() }}
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Security Confirmation</h4>
					</div>
					<div class="modal-body" style="height:250px;">
						<input type="hidden" name="akses" value="personalia">
						<input type="hidden" name="sysid" id="sysid">
						<input type="hidden" id="isoma">
						<input type="hidden" id="hoursplan" name="minutes">
						<input type="hidden" id="category" name="category">
						<div class="form-group">
							<label>Employee Name</label>
							<input type="text" name="employee_name" id="employeename" class="form-control" disabled>
						</div>
						<div class="form-group">
							<label>Start</label>
							<input type="datetime-local" name="start_izin" class="form-control" id='start_permit'>
						</div>
						<div class="form-group">
							<label>Finish</label>
							<input type="datetime-local" name="finish_izin" class="form-control" id='finish_permit'>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-success">Simpan</button>
						<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</form>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delname"></b> ?
					<input type="hidden" id="delid">
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

@endsection
@section('Scripts')
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			window.location.href='/Permit/Delete/'+x;
		});
	</script>
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
	<!-- Durasi Alert --->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<!-- Approve -->
	<script type="text/javascript">
		// Approve Data
		$(document).on('click', '.approve-modal', function() {
			$('#approveid').val($(this).data('approveid'));
			$('#approvename').text($(this).data('approvename'));
			$('#modal-approve').modal('show');
		});
		$('.modal-footer').on('click', '.approve', function() {
			var x=$('#approveid').val();
			window.location.href='/Permit/PersonaliaSign/'+x+'/1';
		});
	</script>
	<!-- Refuse -->
	<script type="text/javascript">
		// Refuse Data
		$(document).on('click', '.refuse-modal', function() {
			$('#refuseid').val($(this).data('refuseid'));
			$('#refusename').text($(this).data('refusename'));
			$('#modal-refuse').modal('show');
		});
		$('.modal-footer').on('click', '.refuse', function() {
			var x=$('#refuseid').val();
			window.location.href='/Permit/PersonaliaSign/'+x+'/2';
		});
	</script>
	<!--- on Load -->
	<script>
		$( document ).ready(function() {
			<?php if($qty_permit_proccess>0){?>
				$("#tabel3").show();
				$("#tabels").hide();
			<?php }else{?>
				$("#tabel3").hide();
				$("#tabels").show();
			<?php }?>
			$("#tabel2").hide();
			$("#tabel4").hide();
			$("#judul").text('Approved Permit');
			$(document).on('click', '.table2', function() {
				$("#tabel2").show(1000);
				$("#tabel3").hide(50);
				$("#tabels").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('New Applied Permit');
			});
			$(document).on('click', '.table3', function() {
				$("#tabel3").show(1000);
				$("#tabels").hide(50);
				$("#tabel2").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('Approved Permit');
			});
			$(document).on('click', '.tables', function() {
				$("#tabels").show(1000);
				$("#tabel3").hide(50);
				$("#tabel2").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('Completed Permit');
			});
			$(document).on('click', '.table4', function() {
				$("#tabel4").show(1000);
				$("#tabel3").hide(50);
				$("#tabels").hide(50);
				$("#tabel2").hide(50);
				$("#judul").text('Refused Permit');
			});
		});
	</script>
	<script>
      $(document).ready(function() {
        var table = $('#tables').DataTable({
          'paging'      : true,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 25,
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
		$('body').on("change","#start",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			window.location.href="/Permit/Personalia/"+start+"/"+finish;
		});
		$('body').on("change","#finish",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			window.location.href="/Permit/Personalia/"+start+"/"+finish;
		});
	</script>
	<script type="text/javascript">
		// Approve Data
		$(document).on('click', '.approve-modals', function() {
			$('#sysid').val($(this).data('sysid'));
			$('#employeename').val($(this).data('employeename'));
			$('#start_permit').val($(this).data('start'));
			$('#finish_permit').val($(this).data('finish'));
			$('#hoursplan').val($(this).data('minutes'));
			$('#category').val($(this).data('category'));
			$('#modal-approves').modal('show');

			var Awal=new Date($('#start').val());
			var Akhir=new Date($('#finish').val());
			
			var Thn=Akhir.getFullYear();
			var Bln=Akhir.getMonth()+1;
			var Tgl=Akhir.getDate();
			var Hari = Akhir.getDay();
			
			var Break1=new Date(Thn+'-'+Bln+'-'+Tgl+' 02:00:00');
			if(Hari==5){var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 11:30:00');}
			else{var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 12:00:00');}
			var Break3=new Date(Thn+'-'+Bln+'-'+Tgl+' 18:00:00');

			//Setting Isoma
			if(Awal<Break1 && Akhir>Break1){$('#isoma').val('45');}
			else if(Awal<Break2 && Akhir>Break2){
				if(Hari==5){$('#isoma').val('90');}
				else{$('#isoma').val('45')}
			}
			else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('30');}
			else{$('#isoma').val('0');}

		});
	</script>


@endsection
