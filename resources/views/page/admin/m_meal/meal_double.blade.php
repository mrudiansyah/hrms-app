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
				Form Meals
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
			<div class="col-xs-3">
				<form action="/Meals/Create" method="post">
				{{ csrf_field() }}
                <input type="hidden" name="sysid" id="syside">
				<div class="box box-success box-solid">
					<div class="box-header with-border">
						<label>Form Group</label>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class="form-group">
							<label>Employee</label>
							<select id="idemployee" name="id_employee" class="form-control selectpicker" data-live-search="true">
								<option value=""></option>
								@foreach($tb_employee as $dt2)
									<option value="{{$dt2->id}}">{{$dt2->employee_name}}</option>
								@endforeach

							</select>
							<div class="pull-right" style="color:#F00;padding-right:10px;" id="info">&nbsp;</div>
						</div>
						<div class="form-group">
							<label>Date</label>
							<input id="dateon" type="date" value="{{old('date_on')}}" name="date_on" class="form-control">
						</div>
						<div class="form-group">
							<label>Category</label>
							<select class="form-control" name="category" id="category">
								<option value=""></option>	
								<option value="meal_off">meal_off</option>
								<option value="meal_ot">meal_ot</option>
								<option value="meal_tl">meal_tl</option>
							</select>
						</div>
						<div class="form-group">
							<label>Meal Ammount</label>
							<input id="meal" type="number" value="15000" name="meal" class="form-control">
							
						</div>
					</div>
					<div class="box-footer">
						<div class="form-group pull-right">
							<button type="submit" class="btn btn-success">Simpan</button>
						</div>

					</div>
					<!-- /.box-body -->
				</div>
				</form>
			</div>
			<div class="col-xs-9">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-calendar"></i>
						<h3 class="box-title">Meal Allowance</h3>
						<div class="box-tools pull-right">
							<input type="month" class="form-control" id="periode" name="periode" value="{{$periode}}">
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
                                    <th style="width:70px;">&nbsp;</th>
									<th style="width:50px;">No</th>
                                    <th style="width:50px;">NIK</th>
									<th>Employee Name</th>
									<th style="width:100px;">Date</th>
									<th style="width:100px;">Category</th>
									<th style="width:100px;">Total Ammount</th>

								</tr>
							</thead>
							<tbody>
								<?php $no=0;$item='';?>
								@foreach($tb_meal as $dt)
								<?php 
									$no++;$new_item=$dt->id_employee.'#'.$dt->date_on.'#'.$dt->category;
									if($item==$new_item){
								?>

								<tr>
									<td>
										<a title="Show" href='/Meals/{{$periode}}/{{$dt->id_employee}}'><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-folder-open-o"></i></button></a>
										<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->employee_name}} on {{$dt->date_on}}"><i class="fa fa-trash"></i></button>
									</td>
									<td>{{$no}}</td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->employee_name}}</td>
									<td><?php echo date('l, d-m-Y',strtotime($dt->date_on));?></td>
									<td>{{$dt->category}}</td>
									<td>{{$dt->meal}}</td>
								</tr>
								<?php }$item=$new_item;?>
								@endforeach
							</tbody>
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

    <div class="modal fade" id="modal-delete">
            <div class="modal-dialog box box-danger" style="width:400px;">
                    <div class="modal-content">
                            <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Delete Confirmation</h4>
                            </div>
                            <div class="modal-body">
                                    Click Yes to Delete : <b id="delname1"></b> ?
                                    <input type="hidden" id="delid1">
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
			"pageLength"  : 15,
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
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			//alert(idemployee);
			window.location.href="/MealsCheck/"+periode;
		});
	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid1').val($(this).data('delid'));
			$('#delname1').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid1').val();
			window.location.href="/MealsDelete/"+x;
			//alert("Masuk");
		});
	</script>
@endsection
