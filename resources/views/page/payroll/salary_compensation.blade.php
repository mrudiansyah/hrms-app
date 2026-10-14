@extends('layouts/payroll_payments')
@section('Contents')
<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<h3 class="box-title">Compensation (Contract)</h3>
						<div class="pull-right" style="margin-left: 10px; display: flex; align-items: center;">
							<input type="month" id="periode" class="form-control" value="{{$periode}}">	
							&nbsp;&nbsp;
							<a href="{{$site}}/CompensationUpdate/{{$periode}}" class="btn btn-primary btn-md"><i class="fa fa-refresh"></i> &nbsp;Update</a>
							&nbsp;&nbsp;
							<a href="{{$site}}/CompensationExcel/{{$periode}}" class="btn btn-success btn-md"><i class="fa fa-file-excel-o"></i> &nbsp;Excel</a>
						</div>
					</div>
					<div class="box-body" style="overflow: auto;">

						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>DEPARTMENT</th>
									<th>ACTION</th>
									<th>PERIODE</th>
									<th>MONTH</th>
									<th>SALARY</th>
									<th>AMOUNT</th>
									<th>PPH21</th>
									<th>RECEIVED</th>
									<th>ACTION</th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							@foreach($tb_kompensasi as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->Nama}}</td>
									<td>{{$dt->Bagian}}</td>
									<td>{{$dt->contract_name}}</td>
									<td><?php echo date('d-M-y',strtotime($dt->Start_Kontrak)).' ~ '.date('d-M-y',strtotime($dt->Finish_Kontrak));?></td>
									<td>{{$dt->Jumlah_Bulan}}</td>
									<td><?php echo number_format($dt->Total_Pendapatan,0);?></td>
									<td><?php echo number_format($dt->Kompensasi,0);?></td>
									<td><?php echo number_format($dt->PPH21,0);?></td>
									<td><?php echo number_format($dt->Total_Kompensasi,0);?></td>
									<td>
										<div class="pull-right">
											<?php if($dt->send_mail==0){?><a href="{{$site}}/SendMailCompensation/{{$dt->id}}"><button type="button" class="btn btn-info btn-xs"><i class="fa fa-envelope-o"></i></button></a><?php }?>
											<a href="{{$site}}/Compensation/Slip/{{$dt->id}}" target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
											<form action="{{$site}}/CompensationDelete/{{$dt->id}}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
												@csrf
												<button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
											</form>
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
	</section>




@endsection
@section('Scripts')
	<script>
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			window.location.href="{{$site}}/Compensation/"+periode;
		});
	</script>
@endsection

