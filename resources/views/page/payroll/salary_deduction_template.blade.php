<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=deduction_template.xls");
?>
<table border="1">
    <tr>
        <th>No</th>
        <th>PERIODE</th>
        <th>TIPE</th>
        <th>EMS_ID</th>
        <th>NIK</th>
        <th>EMPLOYEE</th>
        <th>DEPARTMENT</th>
        <th>COMPONENT</th>
        <th>AMOUNT</th>
        <th>DESCRIPTION</th>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>koperasi,seragam,pant,lain_lain</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <?php $no=0;?>
    @foreach($tb_employee as $dt)
    <tr>
        <td><?php $no++;echo $no;?></td>
        <td>{{$periode}}</td>
        <td>{{$dt->tipe_kontrak}}</td>
        <td>{{$dt->id}}</td>
        <td>{{$dt->NIK}}</td>
        <td>{{$dt->nama_karyawan}}</td>
        <td>{{$dt->dept_code}}</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    @endforeach
</table>