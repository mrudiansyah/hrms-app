@extends('layouts/admin')
@section('Contents')
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
						<h3 class="box-title" style="padding-bottom:25px;">{{$Judul}}</h3>
						<div class="box-tools pull-right">
							<?php if($leader_status=='1'){?>
								<a href='/Status/KSK/Create/{{$periode}}'><button type="button" class="btn btn-primary btn-xs">FORM KSK &nbsp;<i class="fa fa-forward"></i></button></a>
							<?php }else echo "Waiting Leader's Data . . ."?>
						</div>
					</div>
					<div class="box-body" style="overflow-x:scroll;">
						<div class="box-header" style="padding-left:0px;padding-bottom:30px;">
							<div class="box-tools pull-left">
								Select Periode<input type="month" class="form-control" id="periode" name="periode" value="{{$periode}}">
							</div>
						</div>
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
									<th>Finish</th>
									<th>Direct Leader</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;$idemployee='';date_default_timezone_set("Asia/Jakarta");$sekarang=date('Y-m-d');?>
								@foreach($tb_employee as $dt)
								<?php if($idemployee!=$dt->idemployee){?>
								<tr style="<?php if($dt->finish_contract<=$sekarang)echo "background:yellow;";?>">
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
										<!-- 
										<button type="button" class="btn btn-primary btn-xs updatekontrak-modal" data-action='New' data-idcontract='<?php echo $dt->id;?>' data-idemployee='<?php echo $dt->idemployee;?>' data-nmemployee='{{$dt->employee_name}}' data-joindate='{{$dt->joindate}}' data-tglawal='{{$PeriodeAwal}}' data-statusawal='{{$dt->contract_name}}'><i class="fa fa-edit"></i></button>
										-->
										<?php if($dt->contract_name!=''){?>
											<a style="padding:1px 9px;" href="/Employee/{{$idemp}}/{{$dt->PIN}}" target="_blank" type="button" class="btn btn-info btn-xs"><i class="fa fa-info"></i></a>
										<?php }?>
									</td>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td>{{$dt->idemployee}}</td>
									<td>{{$dt->NIK}}</td>
									<td title="Periode: {{$idemployee}}">{{$dt->employee_name}} (<?php echo substr($dt->gender,0,1);?>)</td>
									<td title="{{$dt->dept_name}}">{{$dt->dept_code}}</td>
									<td>{{$dt->position_name}}</td>
									<td>{{$dt->joindate}}</td>
									<td><?php if($Judul!='Draft Contract')echo $dt->contract_name;?></td>
									<td><?php if($Judul!='Draft Contract'){if($dt->contract_name=='Permanen'||$dt->contract_name=='Draft')echo "&nbsp;";else echo $dt->finish_contract;}?></td>
									<td>
										<?php if($dt->leader1==''){?>
											<div class="pull-right">
												<button type="button" class="btn btn-primary btn-xs update-modal" data-idemployee='{{$dt->idemployee}}' data-nmemployee='{{$dt->employee_name}}'><i class="fa fa-edit"></i></button>
											</div>
										<?php }elseif($dt->leader2==''){?>
											{{$dt->leader1}}
											<div class="pull-right">
												<button type="button" class="btn btn-primary btn-xs update-modal" data-idemployee='{{$dt->id_leader1}}' data-nmemployee='{{$dt->leader1}}'><i class="fa fa-edit"></i></button>
											</div>
										<?php }elseif($dt->leader3==''){?>
											{{$dt->leader2}}
											<div class="pull-right">
												<button type="button" class="btn btn-primary btn-xs update-modal" data-idemployee='{{$dt->id_leader2}}' data-nmemployee='{{$dt->leader2}}'><i class="fa fa-edit"></i></button>
											</div>
										<?php }else{
											//if($dt->leader3=='KOMSANT MOONPUGDEE')echo $dt->leader2; 
											//else echo $dt->leader3;
											echo $dt->leader1;
										}?>
									</td>
								</tr>
								<?php }$idemployee=$dt->idemployee?>
								@endforeach
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
									<th>Finish</th>
									<th>Direct Leader</th>
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
	
	<div class="modal fade" id="modal-updatekontrak">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
			<form action="/Status/New" method="post">
			<input type="hidden" id="idaction" name="action">
			<input type="hidden" id="idcontract" name="id_contract">
			<input type="text" id="idemployee" name="id_employee">
			<input type="hidden" id="joindate" name="join_date">
			<input type="hidden" id="statusawal" name="statusawal">

			{{ csrf_field() }}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Perubahan Status</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Plan Start</label>
						<input type="text" id="nmemployee" class="form-control" disabled>
					</div>
					<div class="form-group">
						<label>New Status</label>
						<select name="contract_name" id="contractname" class="form-control">
							<option></option>
							<option value="Magang" id="magang">Magang</option>
							<option value="Kontrak 1" id="kontraksatu">Kontrak 1</option>
							<option value="Kontrak 2" id="kontrakdua">Kontrak 2</option>
							<option value="Pembaharuan" id="pembaharuan">Pembaharuan</option>
							<option value="Permanen" id="permanen">Permanen</option>
							<option value="Resign" class="finish">Resign</option>
							<option value="Kabur" class="finish">Kabur</option>
							<option value="End Contract" class="finish">End Contract</option>
							<option value="NASKA" id="naska">NASKA</option>
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


	<div class="modal fade" id="modal-update">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
			<form action="/Status/Leader" method="post">
			<input type="hidden" id="idemployee" name="id_employee">

			{{ csrf_field() }}
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
							<label>Direct Leader</label>
							<select name="id_leader" class="form-control selectpicker" data-live-search="true">
								<option value=""></option>
								@foreach($tb_leader as $dt)
									<option value="{{$dt->id}}">{{$dt->employee_name}}</option>
								@endforeach

							</select>
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
				if($(this).val()=='Magang'||$(this).val()=='Kontrak 1'||$(this).val()=='Kontrak 2'||$(this).val()=='Pembaharuan'){
					$('.waktu1').show();
					$('.waktu2').show();
				}else if($(this).val()=='Permanen'||$(this).val()=='NASKA'||$(this).val()=='PSAB'||$(this).val()=='SAB'||$(this).val()=='Other'){
					$('.waktu1').show();
					$('.waktu2').hide();
				}else{
					$('.waktu1').hide();
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
			if($(this).data('statusawal')==''){
				$('#magang').show();
				$('#kontraksatu').show();
				$('#kontrakdua').hide();
				$('#pembaharuan').hide();
				$('#permanen').hide();
				$('.finish').hide();
			}
			else if($(this).data('statusawal')=='Magang'){
				$('#magang').hide();
				$('#kontraksatu').show();
				$('#kontrakdua').show();
				$('#pembaharuan').hide();
				$('#permanen').hide();
				$('.finish').show();
			}
			else if($(this).data('statusawal')=='Kontrak 1'){
				$('#magang').hide();
				$('#kontraksatu').hide();
				$('#kontrakdua').show();
				$('#pembaharuan').hide();
				$('#permanen').hide();
				$('.finish').show();
			}
			else if($(this).data('statusawal')=='Kontrak 2'){
				$('#magang').hide();
				$('#kontraksatu').hide();
				$('#kontrakdua').hide();
				$('#pembaharuan').show();
				$('#permanen').show();
				$('.finish').show();
			}
			else if($(this).data('statusawal')=='Pembaharuan'){
				$('#magang').hide();
				$('#kontraksatu').hide();
				$('#kontrakdua').hide();
				$('#pembaharuan').hide();
				$('#permanen').show();
				$('.finish').show();
			}
			else if($(this).data('statusawal')=='Permanen'){
				$('#magang').hide();
				$('#kontraksatu').hide();
				$('#kontrakdua').hide();
				$('#pembaharuan').hide();
				$('#permanen').hide();
				$('.finish').show();
			}
			$('#modal-update').modal('show');
		});
	</script>
	<script type="text/javascript">
		$(document).on('click', '.update-modal', function() {
			$('#nmemployee').val($(this).data('nmemployee'));
			$('#idemployee').val($(this).data('idemployee'));
			$('#modal-update').modal('show');
		});
	</script>
	<script>
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/Status/KSK/"+periode;
		});
	</script>
	<script type="text/javascript">
		$(document).on('click', '.updatekontrak-modal', function() {
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
			if($(this).data('statusawal')==''){
				$('#magang').show();
				$('#kontraksatu').show();
				$('#kontrakdua').hide();
				$('#pembaharuan').hide();
				$('#permanen').hide();
				$('.finish').hide();
			}
			else if($(this).data('statusawal')=='Magang'){
				$('#magang').hide();
				$('#kontraksatu').show();
				$('#kontrakdua').show();
				$('#pembaharuan').hide();
				$('#permanen').hide();
				$('.finish').show();
			}
			else if($(this).data('statusawal')=='Kontrak 1'){
				$('#magang').hide();
				$('#kontraksatu').hide();
				$('#kontrakdua').show();
				$('#pembaharuan').hide();
				$('#permanen').hide();
				$('.finish').show();
			}
			else if($(this).data('statusawal')=='Kontrak 2'){
				$('#magang').hide();
				$('#kontraksatu').hide();
				$('#kontrakdua').hide();
				$('#pembaharuan').show();
				$('#permanen').show();
				$('.finish').show();
			}
			else if($(this).data('statusawal')=='Pembaharuan'){
				$('#magang').hide();
				$('#kontraksatu').hide();
				$('#kontrakdua').hide();
				$('#pembaharuan').show();
				$('#permanen').show();
				$('.finish').show();
			}
			else if($(this).data('statusawal')=='Permanen'){
				$('#magang').hide();
				$('#kontraksatu').hide();
				$('#kontrakdua').hide();
				$('#pembaharuan').hide();
				$('#permanen').hide();
				$('.finish').show();
			}
			$('#modal-updatekontrak').modal('show');
		});
	</script>

@endsection
