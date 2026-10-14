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
							<input type="month" id="periode" value="{{$periode}}">	
							<select id="category" name="category" style="height:27px;">
								<option value="payroll">Payroll</option>
								<option value="overtime">Overtime</option>
								<option value="thr">THR</option>
								<option value="bonus">Bonus</option>
							</select>
						</div>
						<div class="pull-right">
							<a href="{{$site}}/Summary/{{$periode}}/{{$department}}/{{$tipe}}" id="template" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Kembali</a>
						</div>
					</div>
					<div class="box-body" style="overflow: auto;">
						<table id="tables" class="table table-bordered" style="min-width:100%;">
							<thead>
								<tr>
									<th style="width:30px;">No<br>&nbsp;</th>
									<th style="width:100px;">NIK<br>&nbsp;</th>
									<th >NAME<br>&nbsp;</th>
									<th style="width:70px;">DEPT<br>&nbsp;</th>
									<th style="width:70px;">POSITION<br>&nbsp;</th>
									<th style="width:70px;">STATUS<br>&nbsp;</th>
									<th style="width:70px;">CATEGORY<br>&nbsp;</th>
									<th style="width:70px;">ADDITIONAL<br><small style="font-weight:normal;">A</small></th>
									<th style="width:70px;">EARN<br><small style="font-weight:normal;">B</small></th>
									<th style="width:70px;">SUM EARN<br><small style="font-weight:normal;">C=A+B</small></th>
									<th style="width:70px;">PERSEN<br><small style="font-weight:normal;">D</small></th>
									<th style="width:70px;">PPH21<br><small style="font-weight:normal;">E=CxD</small></th>
									<th style="width:70px;">PAID<br><small style="font-weight:normal;">F</small></th>
									<th style="width:70px;">BALANCE<br><small style="font-weight:normal;">G=E-F</small></th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							@foreach($tb1 as $dt1)
								<tr>
									<td>
										<?php
											$no++;
											$add=$dt1->monthly_salary+$dt1->prev_amount;
										?>
										{{$no}}
									</td>
									<td>{{$dt1->NIK}}</td>
									<td>{{$dt1->nama_karyawan}}</td>
									<td>{{$dt1->department}}</td>
									<td>{{$dt1->jabatan}}</td>
									<td>{{$dt1->status_pajak}}</td>
									<td>{{$dt1->kategori}}</td>
									<td><?php echo number_format($add,0);?></td>
									<td><?php echo number_format($dt1->current_amount,0);?></td>
									<td><?php echo number_format($dt1->cumulative_amount,0);?></td>
									<td><?php echo number_format($dt1->prosentase,0);?>%</td>
									<td><?php echo number_format($dt1->tax_amount,0);?></td>
									<td><?php echo number_format($dt1->previous_payment,0);?></td>
									<td><?php echo number_format($dt1->balance_amount,0);?></td>
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
@section('Scripts')
	<script>
		$('body').on("change","#periode",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="{{$site}}/TaxTerMonthly/"+periode+"/"+department+"/"+tipe+"/payroll";
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="{{$site}}/TaxTerMonthly/"+periode+"/"+department+"/"+tipe+"/payroll";
		});
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);

	</script>
	<script>
		$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
		});
	</script>
@endsection
