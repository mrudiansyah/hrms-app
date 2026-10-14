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
							<?php if($component=='kompensasi'){?>
								<a href="{{$site}}/Compensation/{{$periode}}" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a>
							<?php }else{?>
								<a href="{{$site}}/Additional/{{$periode}}/{{$department}}/{{$component}}/{{$tipe}}" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a>
							<?php }?>
						</div>
					</div>
					<div class="box-body" style="overflow: auto;">
						<table id="tables" class="table table-bordered" style="min-width:100%;">
							<thead>
								<tr>
									<th style="width:30px;">No<br>&nbsp;</th>
									<th style="width:100px;">NIK<br>&nbsp;</th>
									<th >EMPLOYEE_NAME<br>&nbsp;</th>
									<th style="width:80px;">DEPARTMENT<br>&nbsp;</th>
									<th style="width:50px;">STATUS<br>&nbsp;</th>
									<th style="width:50px;">AMOUNT<br><small style="font-weight:normal;">A</small></th>
									<th style="width:50px;">SALARY/YEAR<br><small style="font-weight:normal;">B</small></th>
									<th style="width:50px;">BRUTO<br><small style="font-weight:normal;">C=A+B</small></th>
									<th style="width:50px;">ALLOWANCE<br><small style="font-weight:normal;">D=Cx<?php echo number_format($biaya_jabatan_p,0);?>% <i style="cursor:pointer;" class="fa fa-info-circle" title="Max: <?php echo number_format($biaya_jabatan_l,0);?>" style="color:blue;"></i></small></th>
									<th style="width:50px;">NETTO<br><small style="font-weight:normal;">E=C-D</small></th>
									<th style="width:50px;">
										PTKP
										<br>
										<small style="font-weight:normal;">
											F
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
									<th style="width:50px;">PKP<br><small style="font-weight:normal;">G=E-F</small></th>
									<th style="width:50px;">PPH21<br>
										<small style="font-weight:normal;">
											H&nbsp;<i style="cursor:pointer;" class="fa fa-info-circle" id="pajak" style="color:blue;"></i>
										</small>
									</th>
									<th style="width:50px;">SALARY<br><small style="font-weight:normal;">I (PPh21)</small></th>
									<th style="width:50px;">BALANCE<br><small style="font-weight:normal;">J=H-I</small></th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							@foreach($tb_salary_tax_more as $dt)
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
									<td><?php echo number_format($dt->amount,0);?></td>
									<td><?php echo number_format($dt->penghasilan_bruto,0);?></td>
									<td><?php echo number_format($dt->amount_bruto,0);?></td>
									<td style="color:blue;"><?php echo number_format($dt->biaya_jabatan,0);?></td>
									<td><?php echo number_format($dt->penghasilan_netto,0);?></td>
									<td style="color:blue;"><?php echo number_format($dt->ptkp,0);?></td>
									<td style="text-align:right;"><?php if($dt->pkp>0)echo number_format($dt->pkp,0);?></td>
									<td style="text-align:right;"><?php if($dt->pph21_amount>0)echo number_format($dt->pph21_amount,0);?></td>
									<td style="text-align:right;"><?php if($dt->pph21_amount>0)echo number_format($dt->pph21_salary,0);?></td>
									<td style="text-align:right;"><?php if($dt->pph21_amount>0)echo number_format($dt->pph21_balance,0);?></td>
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
@endsection
@section('Scripts')
	<script>
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			window.location.href="{{$site}}/Compensation/"+periode;
		});

	</script>
	<script>
		$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
		});
		$(document).on('click', '#pajak', function() {
			$('#modal-tax').modal('show');
		});

	</script>
@endsection
