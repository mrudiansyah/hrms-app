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
				Employee Permit
				<small>out of duty</small>
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
			<div class="col-lg-4 col-sm-12 col-xs-12">
				<form action="/Permit/Add" method="post">
					{{ csrf_field() }}
					<input type="hidden" name="sysid" id="sysid">
					<input type="hidden" name="otisoma" id="isoma">
					<input type="hidden" name="minutes" id="hoursplan">
					<div class="box box-success box-solid">
						<div class="box-header with-border">
							<label>Form Apply Permit</label>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
							</div>
						</div>
						<div class="box-body">
							<div class="form-group">
								<label>Form Type</label>
								<select class="form-control" name="category" id="category" style="width:240px;">
									<option value=""></option>
									<option value="A">A. Izin Tidak Masuk Kerja</option>
									<option value="B">B. Izin 1/2 Hari Kerja</option>
									<option value="C">C. Izin Keluar Pabrik</option>
									<option value="D">D. Izin Berobat</option>
								</select>
							</div>
								<div class="form-group">
								<label>Employee</label>
								<select name="id_employee" id="id_employee" class="form-control selectpicker" data-live-search="true">
									<option value=""></option>
									@foreach($tb_employee as $dt2)
										<option value="{{$dt2->id}}">{{$dt2->employee_name}} ({{$dt2->NIK}})</option>
									@endforeach

								</select>
							</div>
							<div class="form-group">
								<label>Direct Leader</label>
								<select class="form-control" name="disetujui" id="disetujui">
									<option value=""></option>
								</select>
							</div>
							<div class="form-group">
								<label>Permite Date</label>
								<input type="date" name="apply_date" class="form-control" style="width:240px;" <?php if($lock_backdate==1)echo "min='".$Tgl."'";?>>
							</div>
							<div class="form-group">
								<div class="col-xs-6 start" style="padding:5px 2px">
									<label>Start</label>
									<input type="datetime-local" name="start_izin" class="form-control" id='start' <?php if($lock_backdate==1)echo "min='".$Jam."'";?>>
								</div>
								<div class="col-xs-6 finish" style="padding:5px 2px">
									<label>Finish</label>
									<input type="datetime-local" name="finish_izin" class="form-control" id='finish' <?php if($lock_backdate==1)echo "min='".$Jam."'";?>>
								</div>
							</div>

							<div class="form-group isoma">
								<div class="col-xs-5" style="padding:20px 3px 10px 0px;">
									<label>Break Hours</label>
									<select name="otisoma2" id="isoma2" class="form-control" disabled>
										<option value="0">0 Minute</option>
										<option value="30">30 Minutes</option>
										<option value="45">45 Minutes</option>
										<option value="90">90 Minutes</option>
									</select>
								</div>
								<div class="col-xs-7" style="padding:20px 0px 10px 3px;">
									<label>Minutes</label>
									<input type="number" name="minutes2" id="hoursplan2" class="form-control" step="0.25" disabled>
								</div>
								
							</div>

							<div class="form-group keperluan col-xs-12" style="padding:5px 0px">
								<label>Keperluan</label>
								<textarea name="keperluan" id="keperluan" class="form-control" rows="3" placeholder="Enter ..."></textarea>
							</div>
							<div class="form-group dokter">
								<label>Keluhan</label>
								<input type="text" name="keluhan" class="form-control">								
							</div>
							<div class="form-group dokter">
								<label>Berobat Ke</label>
								<input type="text" name="berobat_ke" class="form-control">								
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
			<div class="col-lg-8 col-sm-12 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-calendar"></i>
						<h3 class="box-title">Permit List</h3>
					</div>
					<div class="box-body" style="overflow-x:scroll;">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">NO</th>
									<th style="width:50px;">NIK</th>
									<th>EMPLOYEE</th>
									<th style="width:30px;">FORM</th>
									<th style="width:50px;">DATE</th>
									<th style="width:60px;">TIME</th>
									<th>KEPERLUAN</th>
								</tr>
							</thead>
							<tbody>
								<?PHP $no=0;?>
								@foreach($tb_izin as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->employee_name}}</td>
									<td>{{$dt->category}}</td>
									<td><?php echo date('d-M',strtotime($dt->apply_date));?></td>
									<td>
										<?php if($dt->category!='A')echo date('H:i',strtotime($dt->start_izin));?>
										<?php if($dt->category=='C'||$dt->category=='D')echo ' ~ '.date('H:i',strtotime($dt->finish_izin));?>
									</td>
									<td>
										{{$dt->keperluan}}
										<div class="pull-right">
											<?php if($dt->status_disetujui=='0'&&$dt->admin==$nama){?><button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->employee_name}} for {{$dt->apply_date}}"><i class="fa fa-trash"></i></button><?php }?>
											<a href="/Permit/Preview/{{$dt->id}}" title="Cetak" type="button" class="btn btn-info btn-xs" target="_balnk"><i class="fa fa-print"></i></a>
										</div>
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
				@if($errors->has('id_employee'))
					- Employee harus diisi<br>
				@endif
				@if($errors->has('date_off'))
					- Date Off belum diisi<br>
				@endif
				@if($errors->has('date_on'))
					- Date On belum diisi<br>
				@endif
				@if($errors->has('check_in'))
					- Checkin belum diisi<br>
				@endif
				@if($errors->has('check_out'))
					- Checkout belum diisi<br>
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
			'autoWidth'   : true,
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
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
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
			window.location.href='/Permit/Delete/'+x;
		});
	</script>
	<script type="text/javascript">
		$(function(){
			$("#id_employee").change(function(){
				$.ajaxSetup({
					type:"POST",
					url: "/Permit/SelectApprove",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				var idemployee=$("#id_employee").val();
				$.ajax({
					data:{idemployee:idemployee},
					success: function(respond){
					$("#disetujui").html(respond);
					//alert(idemployee);

					}
				})
			});
		})
	</script>
	<script>
		$( document ).ready(function() {
			$(".start").hide();
			$(".finish").hide();
			$(".dokter").hide();
			$(document).on('change', '#category', function() {
				if($(this).val()=='A'){
					$(".start").hide();
					$(".finish").hide();
					$(".isoma").hide();
					$(".dokter").hide();
					$(".keperluan").show();
					$("#keperluan").val('');
				}
				if($(this).val()=='B'){
					$(".start").show();
					$(".finish").hide();
					$(".isoma").hide();
					$(".dokter").hide();
					$(".keperluan").show();
					$("#keperluan").val('');
				}
				if($(this).val()=='C'){
					$(".start").show();
					$(".finish").show();
					$(".isoma").show();
					$(".dokter").hide();
					$(".keperluan").show();
					$("#keperluan").val('');
				}
				if($(this).val()=='D'){
					$(".keperluan").hide();
					$(".start").show();
					$(".finish").show();
					$(".isoma").show();
					$(".dokter").show();
					$("#keperluan").val('Berobat');
				}
			});
			$("#start").change(function(){
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
				if(Awal<Break1 && Akhir>Break1){$('#isoma').val('45');$('#isoma2').val('45');}
				else if(Awal<Break2 && Akhir>Break2){
					if(Hari==5){$('#isoma').val('90');$('#isoma2').val('90');}
					else{$('#isoma').val('45');$('#isoma2').val('45');}
				}
				else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('45');$('#isoma2').val('45');}
				else{$('#isoma').val('0');$('#isoma2').val('0');}

				var now=new Date($('#start').val());
				var bitDate=new Date($('#finish').val());
				
				var check=((bitDate-now)/60000);
				var check2=now.getDay();
				var n=((bitDate-now)/60000)-($('#isoma').val());
				$('#hoursplan').val(n);
				$('#hoursplan2').val(n);
				if(n>=360){
					alert('Waktu lebih dari 6 Jam, Redirect ke Form A');
					$("#category").val('A');
					$(".start").hide();
					$(".finish").hide();
					$(".isoma").hide();
					$(".dokter").hide();
					$(".keperluan").show();
					$("#keperluan").val('');
				}
				else if(n>180){
					alert('Waktu lebih dari 3 Jam, Redirect ke Form B');
					$("#category").val('B');
					$(".start").show();
					$(".finish").hide();
					$(".isoma").hide();
					$(".dokter").hide();
					$(".keperluan").show();
					$("#keperluan").val('');
				}
			});
			$("#finish").change(function(){
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
				if(Awal<Break1 && Akhir>Break1){$('#isoma').val('45');$('#isoma2').val('45');}
				else if(Awal<Break2 && Akhir>Break2){
					if(Hari==5){$('#isoma').val('90');$('#isoma2').val('90');}
					else{$('#isoma').val('45');$('#isoma2').val('45');}
				}
				else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('45');$('#isoma2').val('45');}
				else{$('#isoma').val('0');$('#isoma2').val('0');}

				var now=new Date($('#start').val());
				var bitDate=new Date($('#finish').val());
				
				var check=((bitDate-now)/60000);
				var check2=now.getDay();

				var n=((bitDate-now)/60000)-($('#isoma').val());
				$('#hoursplan').val(n);
				$('#hoursplan2').val(n);
				if(n>=360){
					alert('Waktu lebih dari 6 Jam, Redirect ke Form A');
					$("#category").val('A');
					$(".start").hide();
					$(".finish").hide();
					$(".isoma").hide();
					$(".dokter").hide();
					$(".keperluan").show();
					$("#keperluan").val('');
				}
				else if(n>180){
					alert('Waktu lebih dari 3 Jam, Redirect ke Form B');
					$("#category").val('B');
					$(".start").show();
					$(".finish").hide();
					$(".isoma").hide();
					$(".dokter").hide();
					$(".keperluan").show();
					$("#keperluan").val('');
				}
			});
			$("#isoma").change(function(){
				var now=new Date($('#start').val());
				var bitDate=new Date($('#finish').val());
				var n=((bitDate-now)/60000)-($('#isoma').val());
				$('#hoursplan').val(n);
			});
		});
	</script>
    <script>
      $(document).ready(function() {
        var table = $('#tables').DataTable({
          'paging'      : false,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 25,
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

@endsection
