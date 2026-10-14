@extends('layouts/pdf')
@section('Contents')
<div style="font-size:12px;">
	<div class="text-left" style="margin-bottom: 10px;">
		<h4 style="margin: 0; font-weight: bold;">PT SUMMIT ADYAWINSA INDONESIA</h4>
		<h4 style="margin: 2px 0; font-weight: bold;">RECAPITULATION EMPLOYEE COMPENSATION PAYMENT</h4>
		<h4 style="margin: 0; font-weight: bold;">PERIODE {{ date('Y, F', strtotime($periode.'-01')) }}</h4>
	</div>
	<table class="table" style="border: 1px solid #000;">
		<thead>
            <tr>
                <th style="border: 1px solid #000;">DEPARTEMEN</th>
                <th style="border: 1px solid #000; width: 60px;">COST CENTER</th>
                <th style="border: 1px solid #000; width: 60px;">Total Employee</th>
                <th style="border: 1px solid #000; width: 80px;">SALARY</th>
                <th style="border: 1px solid #000; width: 100px;">COMPENSATION<br>(Gross)</th>
                <th style="border: 1px solid #000; width: 80px;">TAX</th>
                <th style="border: 1px solid #000; width: 100px;">COMPENSATION<br>(take home pay)</th>
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
                        <td rowspan="{{ $rowspan }}" style="vertical-align: middle; font-weight: bold; border: 1px solid #000;">{{ $dt->bagian }}</td>
                    @endif
                    <td style="text-align: center; border: 1px solid #000;">{{ $dt->cc_code }}</td>
                    <td style="text-align: center; border: 1px solid #000;">{{ $dt->jml_employee }}</td>
                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($dt->salary, 0) }}</td>
                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($dt->compensation_gross, 0) }}</td>
                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($dt->tax, 0) }}</td>
                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($dt->compensation_nett, 0) }}</td>
                </tr>
            @endforeach
		</tbody>
        <tfoot style="font-weight: bold; background: #eee;">
            <tr>
                <td colspan="2" style="text-align: right; border: 1px solid #000;">GRAND TOTAL</td>
                <td style="text-align: center; border: 1px solid #000;">{{ number_format($total_emp, 0) }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ number_format($total_sal, 0) }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ number_format($total_gross, 0) }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ number_format($total_tax, 0) }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ number_format($total_nett, 0) }}</td>
            </tr>
        </tfoot>
	</table>
    <div style="height:50px;">&nbsp;</div>
    <div style="width:100%;">
        @foreach($tb_approval as $dt2)
            <table border="1" cellspacing="0" cellpadding="5" width="100%" align="center">
                <tr>
                    <td colspan="4" style="text-align:center;">Approved by</td>
                    <td style="text-align:center;">Verified by</td>
                </tr>
                <tr>
                    <td style="text-align:center;vertical-align:middle;height:100px;" class="relative">
                        <?php
                        $confirm=base_path().'/public/approval/confirm.png';
                        $rejected=base_path().'/public/approval/rejected.png';
                        ?>
                        @if($dt2->status_1==1)
                            <img src='{{ $confirm }}' style='width:80px;height:40px;'>
                            <br>{{$dt2->date_1}}
                        @elseif($dt2->status_1==2)
                            <img src='{{ $rejected }}' style='width:80px;height:60px;'>
                            <br>{{$dt2->date_1}}
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td style="text-align:center;vertical-align:middle;">
                        @if($dt2->status_2==1)
                            <img src='{{ $confirm }}' style='width:80px;height:40px;'>
                            <br>{{$dt2->date_2}}
                        @elseif($dt2->status_2==2)
                            <img src='{{ $rejected }}' style='width:80px;height:60px;'>
                            <br>{{$dt2->date_2}}
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td style="text-align:center;vertical-align:middle;">
                        @if($dt2->status_3==1)
                            <img src='{{ $confirm }}' style='width:80px;height:40px;'>
                            <br>{{$dt2->date_3}}
                        @elseif($dt2->status_3==2)
                            <img src='{{ $rejected }}' style='width:80px;height:60px;'>
                            <br>{{$dt2->date_3}}
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td style="text-align:center;vertical-align:middle;">
                        @if($dt2->status_4==1)
                            <img src='{{ $confirm }}' style='width:80px;height:40px;'>
                            <br>{{$dt2->date_4}}
                        @elseif($dt2->status_4==2)
                            <img src='{{ $rejected }}' style='width:80px;height:60px;'>
                            <br>{{$dt2->date_4}}
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td style="text-align:center;vertical-align:middle;">
                        @if($dt2->status_5==1)
                            <img src='{{ $confirm }}' style='width:80px;height:40px;'>
                            <br>{{$dt2->date_5}}
                        @elseif($dt2->status_5==2)
                            <img src='{{ $rejected }}' style='width:80px;height:60px;'>
                            <br>{{$dt2->date_5}}
                        @else
                            &nbsp;
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="width:120px;text-align:center;vertical-align:bottom;">{{$dt2->name_1}}</th>
                    <th style="width:120px;text-align:center;vertical-align:bottom;">{{$dt2->name_2}}</th>
                    <th style="width:120px;text-align:center;vertical-align:bottom;">{{$dt2->name_3}}</th>
                    <th style="width:120px;text-align:center;vertical-align:bottom;">{{$dt2->name_4}}</th>
                    <th style="width:120px;text-align:center;vertical-align:bottom;">{{$dt2->name_5}}</th>
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
@endsection
