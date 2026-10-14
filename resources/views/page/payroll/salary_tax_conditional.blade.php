@extends('layouts/payroll')
@section('Styles')
	<style>th{text-align:center;}</style>
@endsection
@section('Contents')
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="{{$periode}}">	
						</div>
						<div class="pull-right">
							<a href="{{$site}}/Summary/{{$periode}}/{{$department}}/{{$tipe}}" id="template" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Kembali</a>
							<button type="button" class="btn btn-primary btn-md import-modal" id="import"><i class="fa fa-upload"></i> &nbsp;Import</button>
						</div>
					</div>
					<div class="box-body" style="overflow: auto;">
						<table id="tables" class="table table-bordered" style="min-width:100%;">
							<thead>
								<tr>
									<th style="width:30px;">No<br>&nbsp;</th>
									<th style="width:70px;">NIK<br>&nbsp;</th>
									<th >NAME<br>&nbsp;</th>
									<th style="width:50px;">DEPT<br>&nbsp;</th>
									<th style="width:50px;">STATUS<br>&nbsp;</th>
									<th style="width:50px;" title="SALARY + TRANSPORT + SHIFT + RAPEL + JKK/JKM/BPJS">INCOME<br><small style="font-weight:normal;">A</small></th>
									<th style="width:50px;">JHT/JP<br><small style="font-weight:normal;">B</small></th>
									<th style="width:50px;">ABSENT<br><small style="font-weight:normal;">C</small></th>
									<th style="width:50px;">MONTHY_SALARY<br><small style="font-weight:normal;">D=(A)-(B+C)</small></th>
									<th style="width:50px;">YEARLY_SALARY<br><small style="font-weight:normal;">E=Dx12</small></th>
									<th style="width:50px;">POSITION
										<br><small style="font-weight:normal;">F=Ex<?php echo number_format($biaya_jabatan_p,0);?>% <i style="cursor:pointer;" class="fa fa-info-circle" title="Max: <?php echo number_format($biaya_jabatan_l,0);?>" style="color:blue;"></i></small>
									</th>
									<th style="width:50px;">
										PTKP
										<br>
										<small style="font-weight:normal;">
											G
											<a style="cursor:pointer;" data-placement="bottom" data-html="true" data-toggle="popover" title="<i class='fa fa-file-text-o'></i> List PTKP" data-content="
											<?php 
												foreach($tb_pph21_status as $dt){
													if(strlen($dt->status_pajak)==2)$tambahan="&nbsp;&nbsp;";else $tambahan="";
													echo $dt->status_pajak.$tambahan." :".number_format($dt->ptkp,0)."<br>";
												}
											?>
											"><i class="fa fa-info-circle"></i></a>
										</small>
									</th>
									<th style="width:50px;">PKP<br><small style="font-weight:normal;">H=(E-F)-G</small></th>
									<th style="width:50px;">PPH21<br>
										<small style="font-weight:normal;">
											I&nbsp;<i style="cursor:pointer;" class="fa fa-info-circle" id="pajak" style="color:blue;"></i>
										</small>
									</th>
									<th style="width:50px;">PPh21<br><small style="font-weight:normal;">F A</small></th>
									<th style="width:50px;">IMPLEMENT<br><small style="font-weight:normal;">(0 / 1)</small></th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							@foreach($tb_salary_tax as $dt)
								<tr>
									<td>
										<?php
											$no++;
										?>
										{{$no}}
									</td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->nama_karyawan}}</td>
									<td>{{$dt->dept_code}}</td>
									<td>{{$dt->statuspajak}}</td>
									<td title="Transport:{{$dt->tunjangan_transport}} Shift:{{$dt->tunjangan_shift}}">
										<?php 
											$jht_jp=$dt->jaminan_hari_tua+$dt->jaminan_pensiun;
											$income=$dt->total_salary+$dt->tunjangan_transport+$dt->tunjangan_shift+$dt->rapel+$jht_jp;
											echo number_format($income,0);
										?>
									</td>
									<td style="color:blue;"><?php $jht_jp=$dt->jaminan_hari_tua+$dt->jaminan_pensiun;echo number_format($jht_jp,0);?></td>
									<td style="color:blue;"><?php echo number_format($dt->ketidakhadiran,0);?></td>
									<td><?php echo number_format($dt->gaji,0);?></td>
									<td><?php echo number_format($dt->penghasilan_bruto,0);?></td>
									<td style="color:blue;"><?php echo number_format($dt->biaya_jabatan,0);?></td>
									<td><?php echo number_format($dt->ptkp,0);?></td>
									<td style="text-align:right;"><?php if($dt->pkp>0)echo number_format($dt->pkp,0);?></td>
									<td style="text-align:right;"><?php if($dt->pph21_salary>0)echo number_format($dt->pph21_salary,0);?></td>
									<td>{{$dt->tax_amount}}</td>
									<td>
										<div class="pull-right">
											<i class="fa fa-edit conditional-modal" data-idtaxconditional="{{$dt->idconditional}}" data-periodetax="{{$dt->periode}}" data-taxamount="{{$dt->tax_amount}}" data-implement="{{$dt->status_implement}}" style="cursor:pointer;" id="taxconditional"></i>											
										</div>
										{{$dt->status_implement}}
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
	</section>
@endsection
@section('Modals')
	<div class="modal fade" id="modal-tax">
		<div class="modal-dialog box box-primary" style="width:300px;">
			<div class="modal-content">
				<div class="modal-header">	
					<b>PROSENTASE PPH21</b>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<?php 
						foreach($tb_pph21 as $dt){
							$bawah=number_format($dt->batas_bawah,0);
							$atas=number_format($dt->batas_atas,0);
							if($dt->id==1)echo "<div class='row'><div class='col-xs-8'> <= ".$atas." </div><div class='col-xs-4'>:".number_format($dt->prosentase,0)."%</div></div>";
							else if($dt->id==4)echo "<div class='row'><div class='col-xs-8'> > ".$bawah." </div><div class='col-xs-4'>:".number_format($dt->prosentase,0)."%</div></div>";
							else echo "<div class='row'><div class='col-xs-8'>".$bawah." s/d ".$atas." </div><div class='col-xs-4'>:".number_format($dt->prosentase,0)."%</div></div>";
						}
					?>
				</div>
			</div>

			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-import">
		<div class="modal-dialog box box-primary" style="width:400px;">
				<div class="modal-content">
					<form method="post" action="{{$site}}/ConditionalTax/Import" enctype="multipart/form-data">
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
							<a href="{{$site}}/ConditionalTax/Template/{{$periode}}/{{$department}}/{{$tipe}}" id="template" target="_blank" class="btn btn-default btn-md"><i class="fa fa-download"></i> &nbsp;Template</a>
							<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-primary pull-left" value="Import">
						</div>
					</form>		
				</div>

				<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
    </div>
	<div class="modal fade" id="modal-conditional">
		<div class="modal-dialog box box-primary" style="width:250px;">
				<div class="modal-content">
					<form method="post" action="{{$site}}/ConditionalTax/Update" enctype="multipart/form-data">
						<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Form Conditional tax</h4>
						</div>
						<div class="modal-body">
							{{ csrf_field() }}
							<div class="form-group">
								<label>Tax Amount</label>
								<input type="number" class="form-control" id="taxamount" name="taxamount" required="required">
								<input type="hidden" id="idtaxconditional" name="idtaxconditional">
								<input type="hidden" id="periodetax" name="periode">
							</div>
							<div class="form-group">
								<label>Implement Status</label>
								<input type="number" min="0" max="1" class="form-control" id="implement" name="status_implement">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-primary pull-left" value="Update">
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
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="{{$site}}/Tax/"+periode+"/"+department+"/"+tipe;
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="{{$site}}/Tax/"+periode+"/"+department+"/"+tipe;
		});
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
 		$(document).on('click', '#pajak', function() {
			$('#modal-tax').modal('show');
		});

	</script>
	<script>
		$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
		});
	</script>
	<script type="text/javascript">
		$(document).on('click', '.import-modal', function() {
			$('#modal-import').modal('show');
		});
	</script>
	<script type="text/javascript">
		$(document).on('click', '.conditional-modal', function() {
			$('#idtaxconditional').val($(this).data('idtaxconditional'));
			$('#periodetax').val($(this).data('periodetax'));
			$('#taxamount').val($(this).data('taxamount'));
			$('#implement').val($(this).data('implement'));
			$('#modal-conditional').modal('show');
		});
	</script>
@endsection
