@extends('layouts/payroll_other')
@section('Contents')
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<h3 class="box-title">{{$juduls}} - {{$periode}}</h3>
						<div class="pull-right">
                            <a href="{{$site}}/SummaryPKL/Refresh/{{$periode}}" class="btn btn-primary btn-md" id="refresh"><i class="fa fa-refresh"></i> &nbsp;Refresh</a>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>NO</th>
									<th>EMPLOYEE_NAME</th>
                                    <th>DAYS</th>
                                    <th>SALARY/DAY</th>
                                    <th>RAPEL</th>
                                    <th>TOTAL_AMOUNT</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0; $grand_total=0;?>
								@foreach($tb_salary_summary as $dt)
									<tr>
										<td>{{ ++$no }}</td>
										<td>{{ $dt->nama ?? '' }}</td>
										<td>{{ $dt->days ?? '0' }}</td>
                                        <td style="text-align:right;">{{ isset($dt->salary_perday) ? number_format($dt->salary_perday, 0) : '0' }}</td>
                                        <td style="text-align:right;">{{ isset($dt->rapel) ? number_format($dt->rapel, 0) : '0' }}</td>
                                        <td style="text-align:right;">{{ isset($dt->total_amount) ? number_format($dt->total_amount, 0) : '0' }}</td>
									</tr>
                                    <?php $grand_total += ($dt->total_amount ?? 0); ?>
								@endforeach
							</tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align:right;">GRAND TOTAL</th>
                                    <th style="text-align:right;">{{ number_format($grand_total, 0) }}</th>
                                </tr>
                            </tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
@section('Scripts')

@endsection
