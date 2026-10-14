<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=earn_template.xls");
?>
<table border="1">
    <tr>
        <th rowspan="2">No</th>
        <th rowspan="2">COMPONENT</th>
        <th rowspan="2">PERIODE</th>
        <th rowspan="2">TIPE</th>
        <th rowspan="2">EMS_ID</th>
        <th rowspan="2">NIK</th>
        <th rowspan="2">EMPLOYEE</th>
        <th rowspan="2">DEPARTMENT</th>
        <th colspan="2">MASA_KERJA</th>
        <th rowspan="2">TOTAL</th>
        <th rowspan="2">PAY_DATE</th>
        <th rowspan="2">AMOUNT</th>
        <th rowspan="2">DESCRITION</th>
    </tr>
    <tr>
        <th>YEAR</th>
        <th>MONTH</th>
    </tr>
    <?php $no=0;?>
    @foreach($tb_employee as $dt)
    <?php
        $joindate=$dt->tanggal_kontrak;
        $sekarang=date('Y-m-d');
        $datetime1 = date_create($sekarang);
        $datetime2 = date_create($joindate);
        $bulan = date_diff($datetime1, $datetime2);
        $lama_tahun=$bulan->format('%y');
        $lama_bulan=$bulan->format('%m');
        $masa_kerja=number_format(($lama_tahun*12)+$lama_bulan,2);
    ?>
    <tr>
        <td><?php $no++;echo $no;?></td>
        <td><?php echo strtoupper($component);?></td>
        <td>{{$periode}}</td>
        <td>{{$dt->tipe_kontrak}}</td>
        <td>{{$dt->id}}</td>
        <td>{{$dt->NIK}}</td>
        <td>{{$dt->nama_karyawan}}</td>
        <td>{{$dt->dept_code}}</td>
        <td>{{$lama_tahun}}</td>
        <td>{{$lama_bulan}}</td>
        <td>{{$masa_kerja}}</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    @endforeach
</table>