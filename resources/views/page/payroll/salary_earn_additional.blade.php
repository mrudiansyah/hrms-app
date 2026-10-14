@extends('layouts/payroll')
@section('Contents')
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						&nbsp;
						<?php $today=date('Y-m-d');$componens=strtoupper($component);?>
						<?php if($componens=='OVERTIME'){?>
							<button type="button" class="btn btn-warning btn-md ot-modal" id="import" data-periode="{{$periode}}"><i class="fa fa-download"></i> &nbsp;Collect OT</button>
						<?php }elseif($componens=='THR'){?>
							<button type="button" class="btn btn-warning btn-md thr-modal" id="import"><i class="fa fa-download"></i> &nbsp;Generate THR</button>
						<?php }else{?>
							<button type="button" class="btn btn-primary btn-md import-modal" id="import"><i class="fa fa-upload"></i> &nbsp;Import</button>
							<button class="btn btn-primary btn-md" id="form" data-idcomponent="" data-periode="{{$periode}}" data-idemployee="" data-amount="0" data-componentname="{{$component}}" data-pph21="" data-received="" data-description="" data-paydate="{{$today}}"><i class="fa fa-plus"></i> &nbsp;Add One</button>
						<?php }?>
						<!-- <a href="{{$site}}/Additionals/Tax/{{$periode}}/{{$department}}/{{$componens}}/{{$tipe}}" class="btn btn-info btn-md"><i class="fa fa-dollar"></i> &nbsp;PPh21</a> -->
						<a href="{{$site}}/TaxTerMonthly/{{$periode}}/{{$department}}/{{$tipe}}/{{$component}}" class="btn btn-info btn-md"><i class="fa fa-dollar"></i> &nbsp;PPh21</a>
						<a href="{{$site}}/SendMail/{{$periode}}/{{$department}}/{{$tipe}}/tb_salary_earn_additional" class="btn btn-primary btn-md"><i class="fa fa-envelope"></i> &nbsp;Send Slip</a>
						<button title="Reset" type="button" class="reset-modal btn btn-danger btn-md" data-delid="{{$periode}}" data-delname="{{$periode}}" id="reset"><i class="fa fa-trash"></i> Reset</button>
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="{{$periode}}">	
						</div>
						<!--
						<div class="pull-right">
							<div class="btn-group">
							<button type="button" class="btn btn-default">Select Group</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="{{$site}}/Additional/{{$periode}}/0/THR/SAI">SAI</a></li>
								<li><a href="{{$site}}/Additional/{{$periode}}/0/THR/MAGANG">MAGANG</a></li>
								<li><a href="{{$site}}/Additional/{{$periode}}/0/THR/PSAB">PSAB</a></li>
							</ul>
							</div>

						</div>
						-->
						</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered">
							<thead>
								<tr>
									<th style="width:50px;">No</th>
									<th style="width:100px;">NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th style="width:150px;">POSITION</th>
									<th style="width:100px;">DEPARTMENT</th>
									<th style="width:50px;">STATUS</th>
									<th style="width:50px;">PERIODE</th>
									<th style="width:50px;">PERCENT</th>
									<th style="width:100px;">COMPONENT</th>
									<th style="width:70px;">AMOUNT</th>
									<th style="width:70px;">PPH21</th>
									<th style="width:180px;">RECEIVED</th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							<?php $no=0;?>
							@foreach($tb_salary_earn_additional as $dt)
								
								<tr>
									<td>
										<?php $no++;if($dt->masa_kerja>=12)$percent=100;else $percent=$dt->masa_kerja*100/12;?>
										{{$no}}
									</td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->nama_karyawan}}</td>
									<td>{{$dt->jabatan}}</td>
									<td>{{$dt->department}}</td>
									<td>{{$dt->statuspajak}}</td>
									<td>{{$dt->periode}}</td>
									<td title="<?php if($percent<100)echo number_format($dt->masa_kerja,0).'/12';?>"><?php echo number_format($percent,2);?>%</td>
									<td><?php echo strtoupper($dt->component_name);?></td>
									<td><?php echo number_format($dt->earn_amount,0);?></td>
									<td <?php if($dt->pph21_calculate==0){echo "style='background-color:#FF0;'";}?>><?php echo number_format($dt->pph21,0);?></td>
									<td><?php echo number_format($dt->amount_received,0);?>
										<div class="pull-right">
											@if($dt->component_name!='overtime')
											<button class="btn btn-primary btn-xs" id="form" data-idcomponent="{{$dt->id}}" data-periode="{{$dt->periode}}" data-idemployee="{{$dt->id_employee}}" data-amount="<?php echo number_format($dt->earn_amount,0,",",".");?>" data-componentname="{{$dt->component_name}}" data-pph21="{{$dt->pph21}}" data-received="{{$dt->amount_received}}" data-description="{{$dt->description}}" data-paydate="{{$dt->tanggal_pembayaran}}"><i class="fa fa-edit"></i></button>
											<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->nama_karyawan}}" data-akses="<?php if($status==1)echo 'Delete';else echo 'Recreate';?>"><i class="<?php if($status==1)echo 'fa fa-trash';else echo 'fa fa-upload';?>"></i></button>
											<?php if($dt->mail_status==0){?><a href="{{$site}}/SendMailOne/{{$dt->id}}/tb_salary_earn_additional"><button type="button" class="btn btn-info btn-xs"><i class="fa fa-envelope-o"></i></button></a><?php }?>
											<a href="{{$site}}/Additionals/Slip/{{$dt->id}}" target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
											@endif
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
	<div class="modal fade" id="modal-form">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
					<form>
					{{ csrf_field() }}
						<div class="modal-header">	
							FORM ADDITIONAL EARN
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="idComponent" class="form-control">
							<div class="form-group opsitambah">
								<label>Employee</label>
								<!-- <select id="idKaryawan" class="form-control selectpicker" data-live-search="true"> -->
								<select id="idemployeeShow" class="form-control selectpicker" data-live-search="true">
									<option value=""></option>
									@foreach($tb_employee as $dt2)
										<option value="{{$dt2->id_employee}}">{{$dt2->nama_karyawan}} ({{$dt2->NIK}})</option>
									@endforeach

								</select>
							</div>
							<div class="form-group opsiedit">
								<label>Employee</label>
								<select id="idemployee" class="form-control" disabled>
									<option value=""></option>
									@foreach($tb_employee as $dt2)
										<option value="{{$dt2->id_employee}}">{{$dt2->nama_karyawan}} ({{$dt2->NIK}})</option>
									@endforeach

								</select>
							</div>
							<div class="form-group">
								<label>Periode</label>
								<input type="month" id="periodeEarn" class="form-control">
							</div>
							<div class="form-group">
								<label>Paid Date</label>
								<input type="date" id="paydate" class="form-control">
							</div>
							<div class="form-group">
								<label>Component</label>
								<!-- <select id="idKaryawan" class="form-control selectpicker" data-live-search="true"> -->
								<select id="componentName" class="form-control">
									<option value="thr">THR</option>
									<option value="bonus">Bonus</option>
								</select>
							</div>
							<div class="form-group">
								<label>Amount</label>
								<input type="text" id="amount" class="form-control" onclick="select();">
							</div>
							<div class="form-group">
								<label>Remark</label>
								<textarea id="description" class="form-control"></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-primary pull-right" id="simpan" data-dismiss="modal">Save</button>
						</div>
					</form>
			</div>

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
	<div class="modal fade" id="modal-reset">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					<b id="aksesText">Reset </b> <b id="resetname"></b> ?
					<input type="hidden" id="resetid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left reset" data-dismiss="modal">Yes, Delete</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-import">
		<div class="modal-dialog box box-primary" style="width:400px;">
				<div class="modal-content">
					<form method="post" action="{{$site}}/Additional/Import" enctype="multipart/form-data">
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
							<a href="{{$site}}/Additionals/Template/{{$periode}}/{{$component}}/{{$tipe}}" id="template" target="_blank" class="btn btn-default btn-md"><i class="fa fa-download"></i> &nbsp;Template</a>
							<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary pull-left">Import</button>
						</div>
					</form>		
				</div>

				<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
    </div>
	<div class="modal fade" id="modal-thr">
		<div class="modal-dialog box box-primary" style="width:300px;">
				<div class="modal-content">
					<form method="post" action="{{$site}}/Additional/Generate" enctype="multipart/form-data">
						<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Setup Date</h4>
						</div>
						<div class="modal-body">
							{{ csrf_field() }}
							<div class="form-group">
								<label>Cut Off Date</label>
								<input type="date" id="tglcutoff" name="tgl_cutoff" value="{{$today}}" class="form-control" required="required">
							</div>
							<div class="form-group">
								<label>Paid Date</label>
								<input type="date" id="tgldibayar" name="tgl_dibayar" value="{{$today}}" class="form-control" required="required">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary pull-left">Generate</button>
						</div>
					</form>		
				</div>

				<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
    </div>
	<div class="modal fade" id="modal-ot">
		<div class="modal-dialog box box-warning" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					<b id="aksesText">Summary Overtime Periode </b> <b id="periodename"></b> ?
					<input type="hidden" id="periodeid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-warning pull-left summaryot" data-dismiss="modal">Collect Overtime</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
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
			var component='{{$component}}';
			window.location.href="{{$site}}/AdditionalTer/"+periode+"/"+department+"/"+component+"/"+tipe;
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			var tipe='{{$tipe}}';
			var component='{{$component}}';
			window.location.href="{{$site}}/AdditionalTer/"+periode+"/"+department+"/"+component+"/"+tipe;
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
			var tipekontrak="{{$tipe}}";

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
					data:{idcomponent:idcomponent,periodeearn:periodeearn,idemployee:idemployee,amount:amount,componentname:componentname,description:description,tanggal_pembayaran:paydate,tipe_kontrak:tipekontrak},
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
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#aksesText').text($(this).data('akses'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			window.location.href='{{$site}}/Additional/Delete/'+x;
		});

	</script>
	<script type="text/javascript">
		$( document ).ready(function() {
			$('#idDepartment').val({{$department}});
			var jumlah = document.getElementById('jumlah').value;
			if(jumlah>0){
				document.getElementById("import").disabled = true;
				//document.getElementById("thr").disabled = true;
				var element = document.getElementById("template");
  				element.classList.add("disabled");
				document.getElementById("reset").disabled = false;
			}else{
				document.getElementById("import").disabled = false;
				//document.getElementById("thr").disabled = false;
				var element = document.getElementById("template");
				element.classList.remove("disabled");
				document.getElementById("reset").disabled = true;
			}
		});
		$(document).on('click', '.import-modal', function() {
			$('#modal-import').modal('show');
		});
		$(document).on('click', '.thr-modal', function() {
			$('#modal-thr').modal('show');
		});
		$(document).on('click', '.ot-modal', function() {
			$('#periodeid').val($(this).data('periode'));
			$('#periodename').text($(this).data('periode'));
			$('#modal-ot').modal('show');
		});
		$('.modal-footer').on('click', '.summaryot', function() {
			var x=$('#periode').val();
			window.location.href='{{$site}}/SummaryOvertime/'+x;
		});
		// Delete Data
		$(document).on('click', '.reset-modal', function() {
			$('#resetid').val($(this).data('delid'));
			$('#resetname').text($(this).data('delname'));
			$('#aksesText').text($(this).data('akses'));
			$('#modal-reset').modal('show');
		});
		$('.modal-footer').on('click', '.reset', function() {
			var x=$('#resetid').val();
			var y="{{$component}}";
			window.location.href='{{$site}}/Additional/Reset/'+x+'/'+y;
		});
	</script>
@endsection
