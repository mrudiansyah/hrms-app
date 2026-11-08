@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   <style>
        tr:hover {
          background-color: #DCDCDC;
		  cursor:pointer;
        }
   </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
		<h1 onclick="">
			Employees Shift
			<small>all departments</small>
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
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-calender"></i>Date : {{$Tgl}}</h3>
					<div class="box-tools pull-right">
						<?php 
							if($show=='Pagi'){$bg='btn-success';$ref=2;}
							if($show=='Sore'){$bg='btn-primary';$ref=3;}
							if($show=='Malam'){$bg='btn-warning';$ref=1;}
						?>
						<a href="/MealsShifts/{{$Tgl}}/{{$show}}" type="button" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i>&nbsp; Back</a>
						<a href="/MealsShift/Check" type="button" class="btn btn-info btn-md"><i class="fa fa-refresh"></i>&nbsp; Manual Check</a>
						<a href="/MealsShift/Create/{{$ref}}" type="button" class="btn {{$bg}} btn-md"><i class="fa fa-upload"></i>&nbsp; Create Meals</a>
						<a href="/MealsShift/Delete/{{$ref}}" type="button" class="delete-modal btn btn-danger btn-md" data-delid="{{$ref}}"><i class="fa fa-trash"></i>&nbsp; Delete Meals</a>
					</div>
				</div>

				<div class="box-body">
				<table id="tables" class="table table-bordered">
					<thead>
						<tr>
							<th>No</th>
							<th>PIN</th>
							<th>NIK</th>
							<th>Employee Name</th>
							<th>Dept</th>
							<th>Position</th>
							<th>Working Time</th>
							<th>Actual</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=0;$absen=0;?>
						@foreach($temp_shift as $dt)
							<?php if($dt->status==0)$absen++;?>
							<tr>
								<td><?php $no++;echo $no;?></td>
								<td>{{$dt->PIN}}</td>
								<td>{{$dt->NIK}}</td>
								<td>{{$dt->employee_name}}</td>
								<td>{{$dt->dept_code}}</td>
								<td>{{$dt->position_name}}</td>
								<td><?php echo date('H:i',strtotime($dt->plan_cin)).' ~ '.date('H:i',strtotime($dt->plan_cout));?></td>
								<td><?php if($dt->status==1){echo "<div style='color:#00F;'><i class='fa fa-check-square-o pull-right'></i> <span class='badge bg-green'>Present</span></div>";}else{echo "<div style='color:#F00;'><i class='fa fa-square-o pull-right' style='color:#00F;'></i> <span class='badge bg-red'>Absent</span></div>";}?></td>
							</tr>
							<?php $present=$no-$absen;?>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>Present: <div class="pull-right"><span class="badge bg-green">{{$present}}</span></div></th>
							<th>&nbsp;</th>
							<th>Absen: <div class="pull-right"><span class="badge bg-red">{{$absen}}</span></div></th>
							<th>&nbsp;</th>
						</tr>
					</tfoot>
				</table>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->

			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Meal allowance this periode will be removed...
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

@endsection
@section('Scripts')
	<!-- page script Tabel-->
	<script>
	$(function () {
		$('#table1').DataTable({
		'paging'      : true,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		//"pageLength"  : 25,
		'autoWidth'   : false
		})
		$('#table2').DataTable({
		'paging'      : true,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : true
		})
	})
	</script>
	<!-- page script alert-->
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
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			window.location.href='/MealsShift/Delete/'+x;
		});
	</script>

@endsection
