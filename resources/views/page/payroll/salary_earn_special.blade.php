@extends('layouts/payroll')
@section('Contents')
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						&nbsp;
						<?php $today=date('Y-m-d');?>
						<button type="button" class="btn btn-primary btn-md import-modal" id="import"><i class="fa fa-upload"></i> &nbsp;Import</button>
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="{{$periode}}">	
						</div>
						<div class="pull-right">
							<a href="{{$site}}/RapelDistribute/{{$periode}}/{{$tipe}}" class="btn btn-success btn-md"><i class="fa fa-envelope"></i> &nbsp;Distribute Slip</a>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th style="width:100px;">NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th style="width:100px;">POSITION</th>
									<th style="width:100px;">DEPARTMENT</th>
									<th style="width:50px;">STATUS</th>
									<th style="width:80px;">PERIODE</th>
									<th style="width:100px;">RAPEL_GAJI</th>
									<th style="width:100px;">RAPEL_OT</th>
									<th style="width:100px;">RAPEL_THR</th>
									<th style="width:180px;">RECEIVED</th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							<?php $no=0;?>
							@foreach($tb_salary_earn_special as $dt)
								
								<tr>
									<td>
										<?php $no++;?>
										{{$no}}
									</td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->nama_karyawan}}</td>
									<td>{{$dt->jabatan}}</td>
									<td>{{$dt->department}}</td>
									<td>{{$dt->statuspajak}}</td>
									<td>{{$dt->periode}}</td>
									<td><?php echo number_format($dt->amount_1,0);?></td>
									<td><?php echo number_format($dt->amount_2,0);?></td>
									<td><?php echo number_format($dt->amount_3,0);?></td>
									<td><?php echo number_format($dt->amount_received,0);?>
										<div class="pull-right">
											<a href="{{$site}}/Rapels/Slip/{{$dt->id}}" target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
										</div>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
						<input type="hidden" id="jumlah" value="{{$no}}">
					</div>
					<!-- /.box-body -->
				</div>

			</div>
		</div>
	</section>
@endsection
@section('Modals')
	<div class="modal fade" id="modal-import">
		<div class="modal-dialog box box-primary" style="width:400px;">
				<div class="modal-content">
					<form method="post" action="{{$site}}/Rapels/Import" enctype="multipart/form-data">
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
							</div>
						</div>
						<div class="modal-footer">
							<a href="{{$site}}/RapelTemplate/{{$periode}}/{{$tipe}}" id="template" target="_blank" class="btn btn-default btn-md"><i class="fa fa-download"></i> &nbsp;Template</a>
							<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary pull-left">Import</button>
						</div>
					</form>		
				</div>

				<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
    </div>

@endsection
@section('Scripts')
	<script>
		$('body').on("change","#periode",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			var tipe='{{$tipe}}';
			window.location.href="{{$site}}/Additional/"+periode+"/"+department+"/THR"+"/"+tipe;
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			var tipe='{{$tipe}}';
			window.location.href="{{$site}}/Additional/"+periode+"/"+department+"/THR"+"/"+tipe;
		});
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
 		$(document).on('click', '#form', function() {
			$('#idComponent').val($(this).data('idcomponent'));
			$('#periodeEarn').val($(this).data('periode'));
			$('#paydate').val($(this).data('paydate'));
			$('#idemployee').val($(this).data('idemployee'));
			$('#amount').val($(this).data('amount'));
			$('#componentName').val($(this).data('componentname'));
			$('#description').val($(this).data('description'));
			var kondisi=$(this).data('idemployee');
			if(kondisi>0){
				$('.opsiedit').show();
				$('.opsitambah').hide();
			}else{
				$('.opsiedit').hide();
				$('.opsitambah').show();
			}
			var angka = $('#amount').val();
			formatRupiahS(angka,'amount');
			$('#modal-form').modal('show');
		});
		$(document).on('change', '#idemployeeShow', function() {
			$('#idemployee').val($(this).val());
		});
		$(document).on('keyup', '#amount', function() {
			var biaya = document.getElementById('amount');
			biaya.addEventListener('keyup', function(e){
				biaya.value = formatRupiah(this.value, '');
			});
		});
		$(document).on('click', '#simpan', function() {
			var idcomponent=$('#idComponent').val();
			var periodeearn=$('#periodeEarn').val();
			var paydate=$('#paydate').val();
			var idemployee=$('#idemployee').val();
			var amount=$('#amount').val().replace(/\./g,'');
			var componentname=$('#componentName').val();
			var description=$('#description').val();

			if(paydate==''||idemployee==''||amount==''){
				alert("Data belum lengkap");
				//alert(paydate+'#'+idemployee+'#'+amount);
			}else{
				$.ajaxSetup({
					type:"POST",
					url: "{{$site}}/Additional/Simpan",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{idcomponent:idcomponent,periodeearn:periodeearn,idemployee:idemployee,amount:amount,componentname:componentname,description:description,tanggal_pembayaran:paydate},
					success: function(respond){
						alert(respond);
						if(respond=='Sukses'){
							location.reload();
						}
					}
				})
			}
		});
		function formatRupiah(angka, prefix){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
 
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}
 
			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
		}
		function formatRupiahS(angka,pemilik){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
 
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}
 
			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			$('#'+pemilik).val(rupiah);
		}

	</script>
	<script type="text/javascript">
		$(document).on('click', '.import-modal', function() {
			$('#modal-import').modal('show');
		});
	</script>
@endsection
