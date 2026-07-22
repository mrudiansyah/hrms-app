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
						<h3 class="box-title" style="padding-bottom:25px;">{{$Judul}}</h3>
						<div class="box-tools pull-right">
							<a href='/Status/KSK/Detail/{{$id_ksk}}/{{$periode}}'><button type="button" class="btn btn-default btn-xs"><i class="fa fa-backward"></i> &nbsp;BACK</button></a>
							@if($id_ksk>0)
								<a href='/Employee/KSK/Print/{{$id_ksk}}' target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> &nbsp;PRINT</button></a>
							@else
								<button type="button" class="btn btn-primary btn-xs import-modal" id="import"><i class="fa fa-upload"></i> &nbsp;Import</button>
								<!-- <a href='/Status/KSK/Import/{{$periode}}'>
									<button type="button" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i> &nbsp;Refresh</button>
								</a> -->
							@endif
						</div>
					</div>
					<div class="box-body" style="overflow-x:scroll;">
						<table id="table2" class="table table-hover tabel2">
							<thead>
								<tr>
									<th>NO</th>
									<th>NIK</th>
									<th>NAME</th>
									<th>DEPT</th>
									<th>WARNING_LETER</th>
									<th>SICK</th>
									<th>PERMIT</th>
									<th>ALPA</th>
									<th>LATE(Frequency)</th>
									<th>LATE(Minutes)</th>
									<th>PERFORMANCE</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_ksk as $dt)
								<?php $bulan=$dt->months%12;$tahun=($dt->months-$bulan)/12;?>
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->employee_name}}</td>
									<td>{{$dt->dept_code}}</td>
									<td>{{$dt->warning_letter}}</td>
									<td>{{$dt->sick}}</td>
									<td>{{$dt->permit}}</td>
									<td>{{$dt->alpa}}</td>
									<td>{{$dt->late}}</td>
									<td>{{$dt->minutes}}</td>
									<td>{{$dt->performance}}
										<div class="pull-right">
											<?php //if($dt->judge==''){?><button type="button" class="btn btn-primary btn-xs update-modal" data-idksk='{{$dt->id}}' data-warningletter='{{$dt->warning_letter}}' data-sick='{{$dt->sick}}' data-permit='{{$dt->permit}}' data-alpa='{{$dt->alpa}}' data-late='{{$dt->late}}' data-minutes='{{$dt->minutes}}' data-performance='{{$dt->performance}}'><i class="fa fa-edit"></i></button><?php //}?>
										</div>
									</td>
								</tr>
								@endforeach
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
			{{ csrf_field() }}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Update Performance</h4>
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
									<option value="B+">B+</option>
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
			</form>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-import">
		<div class="modal-dialog box box-primary" style="width:400px;">
				<div class="modal-content">
					<form method="post" action="/Status/KSK/Import" enctype="multipart/form-data">
						<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Import File Excel</h4>
						</div>
						<div class="modal-body">
							{{ csrf_field() }}
							<label></label>
							<div class="form-group">
								<input type="file" name="file" required="required">
								<input type="hidden" value="{{$periode}}" name="periode">
							</div>
						</div>
						<div class="modal-footer">
							<a href="/Status/KSK/Template/{{$periode}}" id="template" target="_blank" class="btn btn-default btn-md"><i class="fa fa-download"></i> &nbsp;Template</a>
							<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-primary pull-left" value="Import">
						</div>
					</form>		
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
	</script>
	<script type="text/javascript">
		$(document).on('click', '.import-modal', function() {
			$('#modal-import').modal('show');
		});
	</script>
@endsection
