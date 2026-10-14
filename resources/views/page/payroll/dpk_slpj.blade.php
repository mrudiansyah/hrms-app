@extends('layouts/dpk')
@section('Contents')
	<section class="content">
		<div class="row">
			<div class="col-xs-8">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<div class="pull-left">
							<button type="button" class="btn btn-primary btn-md edit-modal"><i class="fa fa-edit"></i> &nbsp;Update SLPJ</button>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>DEPT</th>
									<th>SLPJ</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;$item='';?>
								@foreach($tb_salaries as $dt)
									<?php $no++;?>
									<tr>
										<td>{{$no}}</td>
										<td>{{$dt->NIK}}</td>
										<td>{{$dt->employee_name}}</td>
										<td>{{$dt->dept_code}}</td>
										<td>{{$dt->slpj}}</td>
									</tr>
								@endforeach

							</tbody>

						</table>
					</div>
					<!-- /.box-body -->
				</div>

			</div>
		</div>
	</section>
@endsection
@section('Modals')
    <div class="modal fade" id="modal-edit">
		<div class="modal-dialog" style="width:300px;">
			<div class="modal-content">
				<form>
					<input type="hidden" id="idSalaryContract">
					{{ csrf_field() }}
					<div class="modal-body">
						<div class="box box-primary box-solid" style="border:0px;">
							<div class="box-header" style="padding:5px 10px;font-size:20px;">
								<label id="Judul">List DPK</label>
							</div>
							<div class="row" style="padding-top:20px;">
								<div class="col-lg-12 col-md-12 col-xs-12" style="border-right:1px solid #EEE;">
									<div class="box-body" style="padding:10px 0px;">
										<div class="form-group">
											<label>Employee</label>
											<select id="idKaryawan" class="form-control selectpicker" data-live-search="true">
												<option value=""></option>
												@foreach($tb_employee as $dt2)
													<option value="{{$dt2->id}}"><?php echo strtoupper($dt2->employee_name)." (".$dt2->NIK." ".strtoupper($dt2->dept_code).")";?></option>
												@endforeach

											</select>
										</div>
										<div class="form-group">
											<label>SLPJ</label>
											<input type="number" id="slpj" class="form-control" style="text-align:right;" onclick="select();">
										</div>
									</div>
								</div>
							</div>
							<div class="box-footer">
								<button type="button" class="btn btn-success pull-right" id="simpan">Save</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
						</div>
					</div>
				</form>		
			</div>
		</div>
    </div>
	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					<b id="aksesText">Delete </b> <b id="delname"></b> ?
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
	<script type="text/javascript">
		$(document).on('click', '.edit-modal', function() {
			$('#modal-edit').modal('show');
		});
		$(document).on('click', '#simpan', function() {
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/DPKSLPJ/Submit",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var idemployee=$('#idKaryawan').val();
			var slpj=$('#slpj').val();
			
			$.ajax({
				data:{idemployee:idemployee,slpj:slpj},
				success: function(respond){
					//alert(respond);
					if(respond=='Sukses'){
						location.reload();
					}else{
						alert("Gagal");
					}
				}
			})
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
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#aksesText').text($(this).data('akses'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			//window.location.href='{{$site}}/ContractDelete/'+x;
		});
	</script>

@endsection
