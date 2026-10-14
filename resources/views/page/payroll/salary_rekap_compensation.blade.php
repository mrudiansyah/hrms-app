@extends('layouts/payroll_recap')
@section('Contents')
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF; padding: 20px;">
					<div class="box-header">
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="{{$periode}}">	
						</div>
						<div class="pull-right">
                            <a href="{{$site}}/RecapPDF/{{$periode}}/{{$tipe}}" class="btn btn-primary btn-md" target="_blank"><i class="fa fa-print"></i> &nbsp;Preview</a>
						</div>
					</div>
					<div class="text-center" style="margin-bottom: 20px;">
						<h3 style="margin: 0; font-weight: bold;">PT SUMMIT ADYAWINSA INDONESIA</h3>
						<h4 style="margin: 5px 0; font-weight: bold;">RECAPITULATION EMPLOYEE COMPENSATION PAYMENT</h4>
						<h4 style="margin: 0; font-weight: bold;">PERIODE {{ date('Y, F', strtotime($periode.'-01')) }}</h4>
					</div>
					<div class="box-body">
						<table class="table table-bordered" style="border: 2px solid #000;">
							<thead style="border-bottom: 3px double #000;">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000;">DEPARTEMEN</th>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000; width: 100px;">COST CENTER</th>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000; width: 100px;">Total Employee</th>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000; width: 120px;">SALARY</th>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000; width: 150px;">COMPENSATION<br>(Gross)</th>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000; width: 120px;">TAX</th>
                                    <th style="text-align: center; vertical-align: middle; width: 150px;">COMPENSATION<br>(take home pay)</th>
                                </tr>
							</thead>
							<tbody>
                                @php 
                                    $prev_dept = '';
                                    $total_emp = 0; $total_sal = 0; $total_gross = 0; $total_tax = 0; $total_nett = 0;
                                @endphp
                                @foreach($tb_rekap as $dt)
                                    @php 
                                        $total_emp += $dt->jml_employee;
                                        $total_sal += $dt->salary;
                                        $total_gross += $dt->compensation_gross;
                                        $total_tax += $dt->tax;
                                        $total_nett += $dt->compensation_nett;
                                    @endphp
                                    <tr>
                                        @if($prev_dept != $dt->bagian)
                                            @php 
                                                $rowspan = $tb_rekap->where('bagian', $dt->bagian)->count();
                                                $prev_dept = $dt->bagian;
                                            @endphp
                                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle; font-weight: bold; border-right: 2px solid #000;">{{ $dt->bagian }}</td>
                                        @endif
                                        <td style="text-align: center; border-right: 2px solid #000;">{{ $dt->cc_code }}</td>
                                        <td style="text-align: center; border-right: 2px solid #000;">{{ $dt->jml_employee }}</td>
                                        <td style="text-align: right; border-right: 2px solid #000;">{{ number_format($dt->salary, 0) }}</td>
                                        <td style="text-align: right; border-right: 2px solid #000;">{{ number_format($dt->compensation_gross, 0) }}</td>
                                        <td style="text-align: right; border-right: 2px solid #000;">{{ number_format($dt->tax, 0) }}</td>
                                        <td style="text-align: right;">{{ number_format($dt->compensation_nett, 0) }}</td>
                                    </tr>
                                @endforeach
							</tbody>
                            <tfoot style="border-top: 3px double #000; font-weight: bold; background: #eee;">
                                <tr>
                                    <td colspan="2" style="text-align: right; border-right: 2px solid #000;">GRAND TOTAL</td>
                                    <td style="text-align: center; border-right: 2px solid #000;">{{ number_format($total_emp, 0) }}</td>
                                    <td style="text-align: right; border-right: 2px solid #000;">{{ number_format($total_sal, 0) }}</td>
                                    <td style="text-align: right; border-right: 2px solid #000;">{{ number_format($total_gross, 0) }}</td>
                                    <td style="text-align: right; border-right: 2px solid #000;">{{ number_format($total_tax, 0) }}</td>
                                    <td style="text-align: right;">{{ number_format($total_nett, 0) }}</td>
                                </tr>
                            </tfoot>
						</table>
                        <br><br>
                        @foreach($tb_approval as $dt2)
                            <table border="1" cellspacing="0" cellpadding="5" width="900" align="center">
                                <tr>
                                    <td colspan="4" style="text-align:center;">Approved by</td>
                                    <td style="text-align:center;">Verified by</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;vertical-align:middle;height:100px;" class="relative">
                                        @if($dt2->id_approver_1==$id_employee&&$dt2->status_1==0)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="1" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="1" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_1==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_1}}
                                        @elseif($dt2->status_1==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_1}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        @if($dt2->id_approver_2==$id_employee&&$dt2->status_2==0&&$dt2->status_1==1)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="2" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="2" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_2==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_2}}
                                        @elseif($dt2->status_2==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_2}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        @if($dt2->id_approver_3==$id_employee&&$dt2->status_3==0&&$dt2->status_2==1)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="3" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="3" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_3==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_3}}
                                        @elseif($dt2->status_3==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_3}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        @if($dt2->id_approver_4==$id_employee&&$dt2->status_4==0&&$dt2->status_3==1)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="4" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="4" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_4==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_4}}
                                        @elseif($dt2->status_4==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_4}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        @if($dt2->id_approver_5==$id_employee&&$dt2->status_5==0&&$dt2->status_4==1)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="4" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="4" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_5==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_5}}
                                        @elseif($dt2->status_5==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_5}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_1}}</th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_2}}</th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_3}}</th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_4}}</th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_5}}</th>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">{{$dt2->pos_1}}</td>
                                    <td style="text-align:center;">{{$dt2->pos_2}}</td>
                                    <td style="text-align:center;">{{$dt2->pos_3}}</td>
                                    <td style="text-align:center;">{{$dt2->pos_4}}</td>
                                    <td style="text-align:center;">{{$dt2->pos_5}}</td>
                                </tr>
                            </table>
                        @endforeach
					</div>
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
        var tipe="<?php echo $tipe;?>";
        window.location.href="{{$site}}/Recap/"+periode+"/"+tipe;
    });
</script>
@endsection
