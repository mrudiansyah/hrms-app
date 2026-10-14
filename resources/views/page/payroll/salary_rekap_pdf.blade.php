<style>
    div{font-family: Arial, Helvetica, sans-serif;}
    th,td{
        border:1px solid #000;
        font-size:18px;
    }
    td{
        text-align:right;
    }
</style>
<div style="text-align:left;padding:0px 0px 20px 0px;font-size:18px;"><b>
PT SUMMIT ADYAWINSA INDONESIA<br>
RECAPITULATION EMPLOYEE SALARY PAYMENT<br>
PERIOD <?php echo strtoupper(date('F Y',strtotime($periode.'-01')));?>
</b>
</div>
<div>
    <table cellspacing="0" cellpadding="5" width="100%">
        <tr>
            <th rowspan="2" style="width:120px;border-bottom:3px double #000;">DEPARTMENT</th>
            <th rowspan="2" style="width:180px;border-bottom:3px double #000;">SEGMENT</th>
            <th rowspan="2" style="width:120px;border-bottom:3px double #000;">CC_CODE</th>
            <th rowspan="2" style="width:420px;border-bottom:3px double #000;">COST_CENTER</th>
            <th rowspan="2" style="border-bottom:3px double #000;">TOTAL<br><small style="font-size:8px;border-bottom:3px double #000;">EMPLOYEE</small></th>
            <th colspan="3">SALARY(gross)</th>
            <th rowspan="2" style="border-bottom:3px double #000;">RAPEL</th>
            <th rowspan="2" style="border-bottom:3px double #000;">TOTAL</th>
            <th rowspan="2" style="width:1px;border-style: none;">&nbsp;</th>
            <th colspan="2">ALLOWANCE</th>
            <th rowspan="2" style="border-bottom:3px double #000;">TOTAL</th>
            <th rowspan="2" style="width:1px;border-style: none;">&nbsp;</th>
            <th rowspan="2" style="border-bottom:3px double #000;">TOTAL SALARY<br><small>(gross)&</small><br>ALLOWANCE</th>
            <th rowspan="2" style="width:1px;border-style: none;">&nbsp;</th>
            <th colspan="3">SALARY(take home pay)</th>
            <th rowspan="2" style="border-bottom:3px double #000;">TOTAL</th>
            <th colspan="2">TO PAY</th>
            <th rowspan="2" style="border-bottom:3px double #000;">TOTAL</th>
        </tr>
        <tr>
            <th style="border-bottom:3px double #000;">DIRECT</th>
            <th style="border-bottom:3px double #000;">INDIRECT</th>
            <th style="border-bottom:3px double #000;">OPEX<br><small style="font-size:10px;">operating expenditure</small></th>
            <th style="border-bottom:3px double #000;">SHIFT</th>
            <th style="border-bottom:3px double #000;">TRANSPORT</th>
            <th style="border-bottom:3px double #000;">DIRECT</th>
            <th style="border-bottom:3px double #000;">INDIRECT</th>
            <th style="border-bottom:3px double #000;">OPEX</th>
            <th style="border-bottom:3px double #000;">TRANSFER</th>
            <th style="border-bottom:3px double #000;">MANUAL</th>
        </tr>
        <?php $a=0;$b=0;$c=0;$d=0;$e=0;$e1=0;$f=0;$g=0;$h=0;$i=0;$j=0;$k=0;$l=0;$m=0;$n=0;$o=0;$p=0;?>
        @foreach($tb_rekap as $dt)
        <?php
            $department=$dt->department;
            $cc_code=$dt->cc_code;
            $host = mysqli_connect("localhost","ems","123456","db_ems_payroll");
            if($tipe=='MAGANG'){
                $qry=mysqli_query($host,"select sum(pendapatan_netto) as 'jumlah_diterima' from tb_salary_summary left join tb_salary_contract on tb_salary_contract.id_employee=tb_salary_summary.id_employee and tb_salary_contract.tipe_kontrak=tb_salary_summary.tipe_kontrak where periode='$periode' and tb_salary_contract.cc_code='$cc_code' and tb_salary_summary.tipe_kontrak='MAGANG' and department='$department' and status_rekening='1'")or die(mysqli_error($host));
            }
            else{
                $qry=mysqli_query($host,"select sum(pendapatan_netto) as 'jumlah_diterima' from tb_salary_summary left join tb_salary_contract on tb_salary_contract.id_employee=tb_salary_summary.id_employee where periode='$periode' and tb_salary_contract.cc_code='$cc_code' and (tb_salary_summary.tipe_kontrak='SAI' or tb_salary_summary.tipe_kontrak='TKA') and department='$department' and status_rekening='1'")or die(mysqli_error($host));
            }
            while($row=mysqli_fetch_array($qry)){
                $jumlah_diterima=$row['jumlah_diterima'];
            }
            if($jumlah_diterima!=$dt->total_paid){
                $manual=$dt->total_paid-$jumlah_diterima;
            }
            else $manual=0;
        ?>
        <tr>
            <td style="text-align:left;">{{$dt->department}}</td>  
            <td style="text-align:left;">{{$dt->segment}}</td>  
            <td style="text-align:left;">{{$dt->cc_code}}</td>  
            <td style="text-align:left;">{{$dt->cc_name}}</td>  
            <td style="text-align:center;">{{$dt->jml_employee}}<?php $a=$a+$dt->jml_employee;?></td>            
            <td><?php if($dt->category=='DIRECT'){echo  number_format($dt->total_salary,0);$b=$b+$dt->total_salary;}?></td>            
            <td><?php if($dt->category=='INDIRECT'){echo  number_format($dt->total_salary,0);$c=$c+$dt->total_salary;}?></td>            
            <td><?php if($dt->category=='OPEX'){echo  number_format($dt->total_salary,0);$d=$d+$dt->total_salary;}?></td>            
            <td><?php echo number_format($dt->total_rapel,0);$e1=$e1+$dt->total_rapel;?></td>            
            <td><?php echo number_format($dt->total_salary,0);$e=$e+$dt->total_salary;?></td>            
            <td style="width:1px;border-style: none;">&nbsp;</td>
            <td><?php echo number_format($dt->total_shift,0);$f=$f+$dt->total_shift;?></td>            
            <td><?php echo number_format($dt->total_transport,0);$g=$g+$dt->total_transport;?></td>            
            <td><?php $allowance=$dt->total_shift+$dt->total_transport;echo number_format($allowance,0);$h=$h+$allowance;?></td>            
            <td style="width:1px;border-style: none;">&nbsp;</td>
            <td><?php $salary_allowance=$dt->total_salary+$allowance+$dt->total_rapel;echo number_format($salary_allowance,0);$i=$i+$salary_allowance;?></td>            
            <td style="width:1px;border-style: none;">&nbsp;</td>
            <td><?php if($dt->category=='DIRECT'){echo  number_format($dt->total_paid,0);$j=$j+$dt->total_paid;}?></td>            
            <td><?php if($dt->category=='INDIRECT'){echo  number_format($dt->total_paid,0);$k=$k+$dt->total_paid;}?></td>            
            <td><?php if($dt->category=='OPEX'){echo  number_format($dt->total_paid,0);$l=$l+$dt->total_paid;}?></td>            
            <td><?php echo number_format($dt->total_paid,0);$m=$m+$dt->total_paid;?></td>            
            <td>
                <?php 
                    echo number_format($dt->total_paid,0);
                    //echo number_format($jumlah_diterima,0);
                    $n=$n+$jumlah_diterima;
                ?>
            </td>            
            <td><?php if($manual>0){echo number_format($manual,0);$o=$o+$manual;}?></td>            
            <td><?php echo number_format($dt->total_paid,0);$p=$p+$dt->total_paid;?></td>            
        </tr>
        @endforeach
        <tr>
            <td style="border-top:3px double #000;">&nbsp;</td>  
            <td style="border-top:3px double #000;">&nbsp;</td>  
            <td style="border-top:3px double #000;">&nbsp;</td>  
            <td style="border-top:3px double #000;">&nbsp;</td>  
            <td style="text-align:center;border-top:3px double #000;">{{$a}}</td>            
            <td style="border-top:3px double #000;"><?php echo number_format($b,0);?></td>            
            <td style="border-top:3px double #000;"><?php echo number_format($c,0);?></td>            
            <td style="border-top:3px double #000;"><?php echo number_format($d,0);?></td>            
            <td style="border-top:3px double #000;"><?php echo number_format($e1,0);?></td>            
            <td style="border-top:3px double #000;"><?php echo number_format($e,0);?></td>            
            <td style="width:1px;border-style: none;">&nbsp;</td>
            <td style="border-top:3px double #000;"><?php echo number_format($f,0);?></td>            
            <td style="border-top:3px double #000;"><?php echo number_format($g,0);?></td>            
            <td style="border-top:3px double #000;"><?php echo number_format($h,0);?></td>            
            <td style="width:1px;border-style: none;">&nbsp;</td>
            <td style="border-top:3px double #000;"><?php echo number_format($i,0);?></td>            
            <td style="width:1px;border-style: none;">&nbsp;</td>
            <td style="border-top:3px double #000;"><?php echo number_format($j,0);?></td>            
            <td style="border-top:3px double #000;"><?php echo number_format($k,0);?></td>            
            <td style="border-top:3px double #000;"><?php echo number_format($l,0);?></td>            
            <td style="border-top:3px double #000;"><?php echo number_format($m,0);?></td>            
            <td style="border-top:3px double #000;">
                <?php echo number_format($m,0);?>
            </td>            
            <td style="border-top:3px double #000;"><?php echo number_format($o,0);?></td>            
            <td style="border-top:3px double #000;"><?php echo number_format($p,0);?></td>            
        </tr>
    </table>
</div>
<div style="height:20px;">&nbsp;</div>
<div style="font-size:18px;">
    @foreach($tb_approval as $dt2)
        <table border="1" cellspacing="0" cellpadding="5" width="900" align="center">
            <tr>
                <td colspan="4" style="text-align:center;">Approved by</td>
                <td style="text-align:center;">Verified by</td>
            </tr>
            <tr>
                <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">
                    <?php
                    $confirm=base_path().'/public/approval/confirm.png';
                    $rejected=base_path().'/public/approval/rejected.png';
                    ?>
                    @if($dt2->status_1==1)
                        <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                        <br>{{$dt2->date_1}}
                    @elseif($dt2->status_1==2)
                        <img src='{{ $rejected }}' style='width:100px;height:60px;'>
                        <br>{{$dt2->date_1}}
                    @else
                        &nbsp;
                    @endif
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    @if($dt2->status_2==1)
                        <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                        <br>{{$dt2->date_2}}
                    @elseif($dt2->status_2==2)
                        <img src='{{ $rejected }}' style='width:100px;height:60px;'>
                        <br>{{$dt2->date_2}}
                    @else
                        &nbsp;
                    @endif
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    @if($dt2->status_3==1)
                        <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                        <br>{{$dt2->date_3}}
                    @elseif($dt2->status_3==2)
                        <img src='{{ $rejected }}' style='width:100px;height:60px;'>
                        <br>{{$dt2->date_3}}
                    @else
                        &nbsp;
                    @endif
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    @if($dt2->status_4==1)
                        <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                        <br>{{$dt2->date_4}}
                    @elseif($dt2->status_4==2)
                        <img src='{{ $rejected }}' style='width:100px;height:60px;'>
                        <br>{{$dt2->date_4}}
                    @else
                        &nbsp;
                    @endif
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    @if($dt2->status_5==1)
                        <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                        <br>{{$dt2->date_5}}
                    @elseif($dt2->status_5==2)
                        <img src='{{ $rejected }}' style='width:100px;height:60px;'>
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