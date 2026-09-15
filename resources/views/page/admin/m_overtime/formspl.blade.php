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
				Form SPL
				<small>Surat Perintah Lembur</small>
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
			<div class="col-xs-12 col-sm-12 col-lg-4">
				<form action="/Admin/Overtime/Create" method="post" class="addot">
					{{ csrf_field() }}
					<input type="hidden" name="sysid" id="syside">
					<div class="box box-success box-solid">
						<div class="box-header with-border">
							<label>Form Header Overtime</label>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
							</div>
						</div>
						<div class="box-body">
							<div class="form-group">
								<label>Nomor SPL</label>
								<input id="nospl" type="hidden" name="id_overtime" class="form-control">
								<input id="nosplshow" type="text" name="id_overtime" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Date </label><?php $today=date('Y-m-d');?>
								@if($lock_backdate==1)
									<i class="fa fa-info pull-right" title="Saat ini HR sedang menaktifkan feature lock backdate. Silahkan hubungi HR untuk open Lock."></i>
								@endif
								<input id="otdate" type="date" value="{{old('otdate')}}" name="ot_date" class="form-control" <?php if($lock_backdate==1)echo "min='".$today."'";?>>
							</div>
							<div class="form-group">
								<label>Department</label>
								<select class="form-control" name="dept_id" id="deptid">
									<option value=""></option>
									@foreach($tb_departments as $dt)	
										<option value="{{$dt->id}}#{{$dt->dept_name}}">{{$dt->dept_name}}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label>Order By</label>
								<select class="form-control" name="diperintah" id="diperintah">
									<option value=""></option>
								</select>
							</div>
							<div class="form-group">
								<label>Approved By</label>
								<select class="form-control" name="disetujui" id="disetujui">
									<option value=""></option>
								</select>
							</div>
							<div class="form-group">
								<label>Seen By</label>
								<select class="form-control" name="diketahui" id="diketahui">
									<option value=""></option>
								</select>
							</div>
						</div>
						<div class="box-footer">
							@if($lock_create==1)
								<div class="pull-left">
									<p>Saat ini HR sedang menaktifkan feature lock delay, jika pesan ini muncul & tombol Simpan hilang artinya Anda masih punya draft SPL atau SPL yang belum selesai approval. Silahkan hubungi HR untuk open Lock.</p>
								</div>
							@else
								<div class="form-group pull-right">
									<a href="/Admin/Overtime" class="btn btn-default cancel-edit">Cancel</a>
									<button type="submit" class="btn btn-success">Simpan</button>
								</div>
							@endif
						</div>
						<!-- /.box-body -->
					</div>
				</form>
			</div>
			<div class="col-xs-12 col-sm-12 col-lg-8">
				<div class="row">
					<div class="box box-primary" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-calendar"></i>
							<h3 class="box-title">Draft SPL</h3>
						</div>
						<div class="box-body">
							<table id="table2" class="table table-striped" style="min-width:100%;">
								<thead>
									<tr>
										<th style="width:90px;">NO.SPL</th>
										<th style="width:90px;">OT.DATE</th>
										<th>DEPARTMENT</th>
										<th style="width:150px;">ORDER</th>
										<th style="width:90px;">ACTION</th>
									</tr>
								</thead>
								<tbody>
									@foreach($tb_overtime as $dt)
									<tr>
										<td>{{$dt->id_overtime}}</td>
										<td>{{$dt->ot_date}}</td>
										<td>{{$dt->dept_name}}</td>
										<td>
											{{$dt->employee_name}}
										</td>
										<td>
											<a title="Show" href='/Admin/Overtime/Draft/<?php echo $dt->id;?>'><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-folder-open-o"></i></button></a>
											<button type="button" class="btn btn-success btn-xs edit-ot" data-nospl="{{$dt->id_overtime}}" data-otdate="{{$dt->ot_date}}"><i class="fa fa-wrench"></i></button>
											<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->id_overtime}}"><i class="fa fa-trash"></i></button>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>
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
	@if ($errors->any())
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				@if($errors->has('ot_date'))
					- Date harus diisi<br>
				@endif
				@if($errors->has('dept_id'))
					- Department belum dipilih<br>
				@endif
				@if($errors->has('diperintah'))
					- Kolom Order belum dipilih<br>
				@endif
				@if($errors->has('disetujui'))
					- Kolom Approve belum dipilih<br>
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
			"pageLength"  : 10,
			'autoWidth'   : false,
			"scrollX"	  : true
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
			"scrollX"	  : true
			})
		})
	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		$(function(){
			$("#deptid").change(function(){
				$.ajaxSetup({
					type:"POST",
					url: "/Admin/Overtime/Select",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				var valdep=$("#deptid").val();
				$.ajax({
					data:{deptid:valdep},
					success: function(respond){
						$("#diperintah").html(respond);

						$('#disetujui')
							.find('option')
							.remove()
							.end()
							.append('<option value=""></option>')
							.val()
						;
						$('#diketahui')
							.find('option')
							.remove()
							.end()
							.append('<option value=""></option>')
							.val()
						;
					//alert(valdep);
					}
				})
			});
			$("#diperintah").change(function(){
				$.ajaxSetup({
					type:"POST",
					url: "/Admin/Overtime/Disetujui",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				var valdip=$("#diperintah").val();
				$.ajax({
					data:{dipid:valdip},
					success: function(respond){
						$("#disetujui").html(respond);
						
						$('#diketahui')
							.find('option')
							.remove()
							.end()
							.append('<option value=""></option>')
							.val()
						;
						//alert(valdip);

					}
				})
			});
			$("#disetujui").change(function(){
				$.ajaxSetup({
					type:"POST",
					url: "/Admin/Overtime/Diketahui",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				var valdis=$("#disetujui").val();
				$.ajax({
					data:{disid:valdis},
					success: function(respond){
						$("#diketahui").html(respond);
					}
				})
			});
		})
	</script>
	<script>
		$(document).on('click', '.edit-ot', function() {
			document.getElementById("nosplshow").value = $(this).data('nospl');
			document.getElementById("nospl").value = $(this).data('nospl');
			document.getElementById("otdate").value = $(this).data('otdate');
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			window.location.href='/Admin/Overtime/Delete/'+x;
		});
	</script>

@endsection
