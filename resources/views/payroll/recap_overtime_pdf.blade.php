@extends('layouts/pdf')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h3 class="box-title"><i class="fa fa-list"></i> RECAPITULATION OVERTIME & MEAL ALLOWANCE PAYMENT PT SUMMIT ADYAWINSA INDONESIA<br>PERIODE {{ $periode }}</h3>
        </section>

        <section class="content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">

                    <!-- Data Box -->
                    <div class="box box-primary">
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="tables" class="table table-bordered table-hover" style="font-size: 12px; white-space: nowrap;">
                                    <thead style="text-align: center;">
                                        <tr>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">DEPARTMENT</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">SEGMENT</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">COST CENTER</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">COST CENTER NAME</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">EMPLOYEE</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">HOUR</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">CONVERT</th>
                                            <th colspan="3" class="text-center align-middle" style="vertical-align: middle;">OVERTIME AMOUNT</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">OVERTIME AMOUNT<br>ALL</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">%</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">BACKPAY</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">MEAL</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">TOTAL GROSS</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">AVERAGE/EMPLOYEE</th>
                                            <th colspan="2" class="text-center">PAYMENT TERM</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">TAX</th>
                                            <th rowspan="2" class="text-center align-middle" style="vertical-align: middle; background-color: #e6ff99;">TOTAL PAID</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">DIRECT</th>
                                            <th class="text-center">INDIRECT</th>
                                            <th class="text-center">SUPPORTING</th>
                                            <th class="text-center">TRANSFER</th>
                                            <th class="text-center">MANUAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $rowspans = [];
                                            $count = count($tb1);
                                            for ($i = 0; $i < $count; $i++) {
                                                $rowspans[$i] = ['dept_name' => 1, 'dept_category' => 1];
                                            }
                                            
                                            // Variables for Grand Totals
                                            $grand_employee = 0;
                                            $grand_hour_actual = 0;
                                            $grand_hour_convert = 0;
                                            $grand_ot_direct = 0;
                                            $grand_ot_indirect = 0;
                                            $grand_ot_supporting = 0;
                                            $grand_ot_all=0;
                                            $grand_rapel = 0;
                                            $grand_meal = 0;
                                            $grand_gross = 0;
                                            $grand_transfer = 0;
                                            $grand_manual = 0;
                                            $grand_tax = 0;
                                            $grand_net = 0;
                                            
                                            // Calculate consecutive matches and totals
                                            for ($i = 0; $i < $count; $i++) {
                                                
                                                // Sum Grand Totals
                                                $grand_employee += $tb1[$i]->total_employee;
                                                $grand_hour_actual += $tb1[$i]->total_hours_act;
                                                $grand_hour_convert += $tb1[$i]->total_hour_convertion;
                                                if($tb1[$i]->dept_category == 'DIRECT STAMPING'||$tb1[$i]->dept_category == 'DIRECT ASSEMBLY') {
                                                    $grand_ot_direct += $tb1[$i]->total_ot_amount;
                                                } elseif($tb1[$i]->dept_category == 'INDIRECT') {
                                                    $grand_ot_indirect += $tb1[$i]->total_ot_amount;
                                                } elseif($tb1[$i]->dept_category == 'OPEX') {
                                                    $grand_ot_supporting += $tb1[$i]->total_ot_amount;
                                                }
                                                $grand_ot_all+= $tb1[$i]->total_ot_amount;
                                                $grand_rapel += $tb1[$i]->total_rapel_amount;
                                                $grand_meal += $tb1[$i]->total_meal_amount;
                                                $grand_gross += $tb1[$i]->total_gross_amount;
                                                if($tb1[$i]->is_transfer == 1) {
                                                    $grand_transfer += $tb1[$i]->total_net_amount;
                                                } else {
                                                    $grand_manual += $tb1[$i]->total_net_amount;
                                                }
                                                $grand_tax += $tb1[$i]->total_pph21_amount;
                                                $grand_net += $tb1[$i]->total_net_amount;
                                                
                                                if ($rowspans[$i]['dept_category'] > 0) {
                                                    $span = 1;
                                                    for ($j = $i + 1; $j < $count; $j++) {
                                                        if ($tb1[$i]->dept_category == $tb1[$j]->dept_category) {
                                                            $span++;
                                                            $rowspans[$j]['dept_category'] = 0;
                                                        } else {
                                                            break;
                                                        }
                                                    }
                                                    $rowspans[$i]['dept_category'] = $span;
                                                }
                                                
                                                if ($rowspans[$i]['dept_name'] > 0) {
                                                    $span = 1;
                                                    for ($j = $i + 1; $j < $count; $j++) {
                                                        if ($tb1[$i]->dept_name == $tb1[$j]->dept_name && $tb1[$i]->dept_category == $tb1[$j]->dept_category) {
                                                            $span++;
                                                            $rowspans[$j]['dept_name'] = 0;
                                                        } else {
                                                            break;
                                                        }
                                                    }
                                                    $rowspans[$i]['dept_name'] = $span;
                                                }
                                            }
                                        @endphp
                                        @foreach($tb1 as $index => $item)
                                        <tr>
                                            @if($rowspans[$index]['dept_name'] > 0)
                                            <td class="text-left" style="vertical-align: middle;">{{ $item->dept_name }}</td>
                                            @else
                                            <td class="text-left" style="vertical-align: middle;"></td>
                                            @endif
                                            
                                            @if($rowspans[$index]['dept_category'] > 0)
                                            <td style="vertical-align: middle;">{{ $item->dept_category }}</td>
                                            @else
                                            <td style="vertical-align: middle;"></td>
                                            @endif
                                            
                                            <td class="text-center">{{ $item->cc_code }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td class="text-center">{{ number_format($item->total_employee, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ number_format($item->total_hours_act, 2, ',', '.') }}</td>
                                            <td class="text-center">{{ number_format($item->total_hour_convertion, 2, ',', '.') }}</td>
                                            <td class="text-right">@if($item->dept_category=='DIRECT STAMPING'||$item->dept_category=='DIRECT ASSEMBLY'){{ number_format($item->total_ot_amount, 0, ',', '.') }}@else{{ number_format(0, 0, ',', '.') }}@endif</td>
                                            <td class="text-right">@if($item->dept_category=='INDIRECT'){{ number_format($item->total_ot_amount, 0, ',', '.') }}@else{{ number_format(0, 0, ',', '.') }}@endif</td>
                                            <td class="text-right">@if($item->dept_category=='OPEX'){{ number_format($item->total_ot_amount, 0, ',', '.') }}@else{{ number_format(0, 0, ',', '.') }}@endif</td>
                                            <td class="text-right">{{ number_format($item->total_ot_amount, 0, ',', '.') }}</td>
                                            <td class="text-right">
                                                <?php $persent_amount=($amount_ot > 0 ? ($item->total_ot_amount/$amount_ot*100) : 0);?>
                                                {{ number_format($persent_amount,2) }}%
                                            </td>
                                            <td class="text-right">{{ number_format($item->total_rapel_amount, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($item->total_meal_amount, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($item->total_gross_amount, 0, ',', '.') }}</td>
                                            <td class="text-right">
                                                <?php $average_amount=($item->total_employee > 0 ? ($item->total_ot_amount/$item->total_employee) : 0);?>
                                                {{ number_format($average_amount,2) }}
                                            </td>
                                            <td class="text-center">{{ $item->is_transfer == 1 ? number_format($item->total_net_amount, 0, ',', '.') : '0' }}</td>
                                            <td class="text-center">{{ $item->is_transfer == 0 ? number_format($item->total_net_amount, 0, ',', '.') : '0' }}</td>
                                            <td class="text-right">{{ number_format($item->total_pph21_amount, 0, ',', '.') }}</td>
                                            <td class="text-right" style="font-weight: bold;">{{ number_format($item->total_net_amount, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot style="background-color: #f4f4f4; font-weight: bold;">
                                        <tr>
                                            <td colspan="4" class="text-right">GRAND TOTAL</td>
                                            <td class="text-center">{{ number_format($grand_employee, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ number_format($grand_hour_actual, 2, ',', '.') }}</td>
                                            <td class="text-center">{{ number_format($grand_hour_convert, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($grand_ot_direct, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($grand_ot_indirect, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($grand_ot_supporting, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($grand_ot_all, 0, ',', '.') }}</td>
                                            <td class="text-right">100.00%</td>
                                            <td class="text-right">{{ number_format($grand_rapel, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($grand_meal, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($grand_gross, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($grand_employee > 0 ? ($grand_ot_direct + $grand_ot_indirect + $grand_ot_supporting) / $grand_employee : 0, 2) }}</td>
                                            <td class="text-center">{{ number_format($grand_transfer, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ number_format($grand_manual, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($grand_tax, 0, ',', '.') }}</td>
                                            <td class="text-right" style="background-color: #e6ff99;">{{ number_format($grand_net, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <br><br>
                            <div style="font-size:14px;">
                                <table border="1" cellspacing="0" cellpadding="5" align="center" style="width: 900px;">
                                    <tr>
                                        <td colspan="4" style="text-align:center;">Approved by</td>
                                        <td style="text-align:center;">Verified by</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">&nbsp;</td>
                                        <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">&nbsp;</td>
                                        <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">&nbsp;</td>
                                        <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">&nbsp;</td>
                                        <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:180px;text-align:center;vertical-align:bottom;">RISTIYONO</td>
                                        <td style="width:180px;text-align:center;vertical-align:bottom;">ARIF BASUKI</td>
                                        <td style="width:180px;text-align:center;vertical-align:bottom;">EKA NOVIANTO NUGROHO</td>
                                        <td style="width:180px;text-align:center;vertical-align:bottom;">PORNTEP KLINSRISUK</td>
                                        <td style="width:180px;text-align:center;vertical-align:bottom;">YUPAWAN CHARUARAYANANT</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">HRGA DEPT. HEAD</td>
                                        <td style="text-align:center;">ASST GENERAL MANAGER</td>
                                        <td style="text-align:center;">VICE PRESIDENT DIRECTOR</td>
                                        <td style="text-align:center;">PRESIDENT DIRECTOR</td>
                                        <td style="text-align:center;">FA DIRECTOR</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('Scripts')

@endsection
