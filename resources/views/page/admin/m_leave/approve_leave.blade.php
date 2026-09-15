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
				Employee Leave & SKDs
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
							<h3 class="box-title" id="judul" style="padding-bottom:25px;">New Applied Leave</h3>
							<div class="pull-right">
								<a class="btn btn-app table2">
									<?php if(isset($qty_leave_new)&&$qty_leave_new>0)echo "<span class='badge bg-yellow'>".$qty_leave_new."</span>";?>
									<i class="fa fa-file-o"></i> New Leave
								</a>
								@if (request()->user()->hasRole('leave_legalize_dirhr'))
								<a class="btn btn-app" href="/Leave/Legalizes_dirhr/0/0/Annual">
									<?php if(isset($qty_leave_proccess)&&$qty_leave_proccess>0)echo "<span class='badge bg-yellow'>".$qty_leave_proccess."</span>";?>
									<i class="fa fa-check-square"></i> Legalize HR
								</a>
								@endif
								<a class="btn btn-app table3">
									<i class="fa fa-check-square-o"></i> Approved
								</a>
								<a class="btn btn-app table4">
									<i class="fa fa-trash"></i> Abort
								</a>
							</div>
						</div>
						<div class="box-body" style="min-height:200px;overflow-x:scroll;">
							<div class="pull-right">
							<ul>
								@if($code_qty>0)
									<b>ACTIVE CODE</b>:
									@foreach($list_code as $li)
										<li><label class="label label-warning">{{$li->leader_code}}</label> {{$li->employee_name}}</li>
									@endforeach
								@endif
							</ul>
							</div>
							<div id="tabel2">
								<table id="table2" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>APPLIED</th>
											<th>CATEGORY</th>
											<th>NIK</th>
											<th>EMPLOYEE</th>
											<th>LEAVE</th>
											<th>WORKING</th>
											<th>REASON/CONTACT</th>
											<th>APPROVE1</th>
											<th>APPROVE2</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										@foreach($tb_leave_new as $dt)
										<?php
											$batas='+'.$limit_day.' days';
											if($limit_day==0){
												$tgl=date('Y-m-d',strtotime($dt->start_leave)).' 23:59:59';
												$date_limit=date('Y-m-d H:i:s',strtotime($tgl));
											}else{
												$date_limit= date('Y-m-d H:i:s',strtotime($batas,strtotime($dt->start_leave)));
											} 
											$now=date('Y-m-d H;i:s');
											if($date_limit<$now&&$limit_approval>0)$status_limit=1;
											else $status_limit=0;
											if($dt->exception==1)$status_limit=0;
										?>
										<tr>
											<td>
                                                <?php $no++;echo $no;?>
                                                <div class="pull-right">
                                                    <a href="/Leave/Employee/{{$dt->id_leave}}" target="_blank"><button title="Preview" type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
                                                </div>
                                            </td>
											<td>{{$dt->doc_date}}</td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td title="{{$dt->id_leave}}">{{$dt->NIK}}</td>
											<td>{{$dt->employee_name}}</td>
											<td><?php echo date('d-M-Y',strtotime($dt->start_leave));if($dt->start_leave!=$dt->finish_leave)echo "<br>".date('d-M-Y',strtotime($dt->finish_leave));?></td>
											<td><?php echo date('d-M-Y',strtotime($dt->start_working));?></td>
											<td>
												{{$dt->reason}} {{$dt->remark}}
												<div class="pull-right">
                                                    @if($dt->overlap_doc!="")
													<a href="/Download/{{$dt->overlap_doc}}"><i class="fa fa-files-o"></i> Overlapping</a>
                                                    @endif
											</div>
											</td>
											<td>
												<?php if($dt->status_approved=='1'){?>
													<small><i class="fa fa-check-square-o" style="color:#00F;" title="Done"></i></small>
												<?php }else if($dt->status_approved=='0'){?>
													<small><i class="fa fa-square-o" style="color:#00F;" title="Need Approve"></i></small>	
												<?php }else{?>
													<small><i class="fas fa-cross-square-o" style="color:#F00;" title="Done"></i></small>
													<?php } ?>
												{{$dt->leader_name}}<br>{{$dt->date_approved}}
												<?php if($dt->status_approved=='0'&&$dt->approved==$id_employee){?>
													<div class="pull-right">
														<button title="Approve" type="button" class="approve-modal btn btn-success btn-xs" data-approveid="{{$dt->id}}" data-approvename="{{$dt->employee_name}}" data-approved="1"><i class="fa fa-check-square-o"></i></button>
														<button title="Refuse" type="button" class="refuse-modal btn btn-danger btn-xs" data-refuseid="{{$dt->id}}" data-refusename="{{$dt->employee_name}}" data-approved="1"><i class="fa fa-close"></i></button>
													</div>
												<?php }?>
											</td>
											<td>
												{{$dt->leader_name2}}<br>{{$dt->date_approved2}}
												<?php if($dt->status_approved=='1'&&$dt->status_approved2=='0'&&$dt->approved2==$id_employee){?>
													<div class="pull-right">
														@if($status_limit==0)
															<button title="Approve" type="button" class="approve-modal btn btn-success btn-xs" data-approveid="{{$dt->id}}" data-approvename="{{$dt->employee_name}}" data-approved="2"><i class="fa fa-check-square-o"></i></button>
															<button title="Refuse" type="button" class="refuse-modal btn btn-danger btn-xs" data-refuseid="{{$dt->id}}" data-refusename="{{$dt->employee_name}}" data-approved="1"><i class="fa fa-close"></i></button>
														@else	
														<i class="btn btn-danger btn-xs" title="Please Inform HR to open">OverDue</i>
														@endif
													</div>
												<?php }?>
											</td>
										</tr>
										@endforeach
									<tbody>
								</table>
							</div>
							<div id="tabel3">
								<div class="row" style="padding-bottom:10px;">
									<div class="col-lg-2 col-xs-6">
										From: 
										<input type="date" class="form-control" id="start" name="start" value="{{$start}}"> 
									</div>
									<div class="col-lg-2 col-xs-6">
										To:
										<input type="date" class="form-control" id="finish" name="finish" value="{{$finish}}">
									</div>
								</div>
								<table id="tables" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>APPLIED</th>
											<th>CATEGORY</th>
											<th>NIK</th>
											<th>EMPLOYEE</th>
											<th>LEAVE</th>
											<th>WORKING</th>
											<th>REASON/CONTACT</th>
											<th>APPROVE1</th>
											<th>APPROVE2</th>
											<th>STATUS</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										@foreach($tb_leave_approve as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->doc_date}}</td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td>{{$dt->NIK}}</td>
											<td>{{$dt->employee_name}}</td>
											<td>{{$dt->start_leave}}<?php if($dt->start_leave!=$dt->finish_leave)echo " ~ ".$dt->finish_leave;?></td>
											<td>{{$dt->start_working}}</td>
											<td>
												{{$dt->reason}} {{$dt->remark}}
											</td>
											<td>
												<?php if($dt->status_approved=='0'){?>
													<small><i class="fa fa-square-o" style="color:#00F;" title="Need Approve"></i></small>	
												<?php }else{?>
													<small><i class="fa fa-check-square-o" style="color:#00F;" title="Done"></i></small>
												<?php }?>
												{{$dt->leader_name}}<br>{{$dt->date_approved}}
											</td>
											<td>
												<?php if($dt->approved2!='0'&&$dt->status_approved2=='0'){?>
													<small><i class="fa fa-square-o" style="color:#00F;" title="Need Approve"></i></small>	
												<?php }?>
												<?php if($dt->approved2!='0'&&$dt->status_approved2=='1'){?>
													<small><i class="fa fa-check-square-o" style="color:#00F;" title="Done"></i></small>	
												<?php }?>
												{{$dt->leader_name2}}<br>{{$dt->date_approved2}}
											</td>
											<td>
												&nbsp;
												<a href="/Leave/Employee/{{$dt->id_leave}}" target="_blank"><button title="Preview" type="button" class="btn btn-primary btn-xs pull-right"><i class="fa fa-print"></i></button></a>
												<?php
												if($nama==$dt->leader_name){
													$host = mysqli_connect("192.168.1.4","ems","123456","db_ems");
													$qry=mysqli_query($host,"select * from tb_leave_code where id_employee='$dt->id_employee'")or die(mysqli_error($host));
													$qty=mysqli_num_rows($qry);
													if($qty==0)echo "<button id='lock' title='Lock' type='button' class='btn btn-success btn-xs pull-left' data-idemployee='".$dt->id_employee."'><i class='fa fa-unlock'></i></button>";
													else echo "<button id='unlock' title='Un Lock' type='button' class='btn btn-danger btn-xs pull-left' data-idemployee='".$dt->id_employee."'><i class='fa fa-lock'></i></button>";													
												}
												?>
											</td>
										</tr>
										@endforeach
									<tbody>
								</table>
							</div>
							<div id="tabel4">
								<table id="table4" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>APPLIED</th>
											<th>CATEGORY</th>
											<th>NIK</th>
											<th>EMPLOYEE</th>
											<th>LEAVE</th>
											<th>WORKING</th>
											<th>REASON/CONTACT</th>
											<th>APPROVE1</th>
											<th>APPROVE2</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										@foreach($tb_leave_refuse as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->doc_date}}</td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td>{{$dt->NIK}}</td>
											<td>{{$dt->employee_name}}</td>
											<td>{{$dt->start_leave}}<?php if($dt->start_leave!=$dt->finish_leave)echo " ~ ".$dt->finish_leave;?></td>
											<td>{{$dt->start_working}}</td>
											<td>
												{{$dt->reason}} {{$dt->remark}}
											</td>
											<td>
												{{$dt->leader_name}}
											</td>
											<td>
												{{$dt->leader_name2}}
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
					<input type="hidden" id="approveindex">
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
					<input type="hidden" id="approveindex">
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


    @if ($message = Session::get('success'))
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			{{$message}}
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
			'searching'   : false,
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
	<script type="text/javascript">
		// Approve Data
		$(document).on('click', '.approve-modal', function() {
			$('#approveid').val($(this).data('approveid'));
			$('#approveindex').val($(this).data('approved'));
			$('#approvename').text($(this).data('approvename'));
			$('#modal-approve').modal('show');
		});
		$('.modal-footer').on('click', '.approve', function() {
			var x=$('#approveid').val();
			var y=$('#approveindex').val();
			window.location.href='/Leave/Approve/'+x+'/1/'+y;
		});
	</script>
	<script type="text/javascript">
		// Refuse Data
		$(document).on('click', '.refuse-modal', function() {
			$('#refuseid').val($(this).data('refuseid'));
			$('#approveindex').val($(this).data('approved'));
			$('#refusename').text($(this).data('refusename'));
			$('#modal-refuse').modal('show');
		});
		$('.modal-footer').on('click', '.refuse', function() {
			var x=$('#refuseid').val();
			var y=$('#approveindex').val();
			window.location.href='/Leave/Approve/'+x+'/2/'+y;
		});
	</script>
	<script>
		$( document ).ready(function() {
			$("#tabel2").show();
			$("#tabel3").hide();
			$("#tabel4").hide();
			$("#judul").text('New Applied');
			$(document).on('click', '.table2', function() {
				$("#tabel2").show(1000);
				$("#tabel3").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('New Applied');
			});
			$(document).on('click', '.table3', function() {
				$("#tabel3").show(1000);
				$("#tabel2").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('Approved');
			});
			$(document).on('click', '.table4', function() {
				$("#tabel4").show(1000);
				$("#tabel3").hide(50);
				$("#tabel2").hide(50);
				$("#judul").text('Refused');
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
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/Leave/Approve/"+periode;
		});
	</script>
	<script>
		$('body').on("change","#start",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			window.location.href="/Leave/Approves/"+start+"/"+finish;
		});
		$('body').on("change","#finish",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			window.location.href="/Leave/Approves/"+start+"/"+finish;
		});
	</script>
	<script>
		
		$('body').on("click","#lock",function(){
			$.ajaxSetup({
				type:"POST",
				url: "/Leave/Lock",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var idemployee=$(this).data('idemployee');
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			$.ajax({
				data:{id_employee:idemployee},
				success: function(respond){
					//alert(respond);
					if(respond==1){
						window.location.href="/Leave/Approves/"+start+"/"+finish;
					}
				}
			})
		})
		$('body').on("click","#unlock",function(){
			$.ajaxSetup({
				type:"POST",
				url: "/Leave/unLock",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var idemployee=$(this).data('idemployee');
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			$.ajax({
				data:{id_employee:idemployee},
				success: function(respond){
					//alert(idemployee);
					if(respond==1){
						window.location.href="/Leave/Approves/"+start+"/"+finish;
					}
				}
			})
		})
	</script>


@endsection
