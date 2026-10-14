<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=work_entry_template.xls");
?>
<table border="1">
    <tr>
        <th>No</th>
        <th>EMS_ID</th>
        <th>Group</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>Dept</th>
        <th>Periode</th>
        <th>Transport</th>
        <th>Shift</th>
        <th>Ketidakhadiran</th>
        <!--
        <th>Hari Kerja</th>
        <th>Cuti</th>
        <th>Ijin</th>
        <th>Sakit</th>
        <th>Mangkir</th>
        <th>OT Weekend</th>
        -->
    </tr>
    <?php $no=0;?>
    @foreach($tb_employee as $dt)
    <tr>
        <td><?php $no++;echo $no;?></td>
        <td>{{$dt->id}}</td>
        <td>{{$dt->tipe_kontrak}}</td>
        <td>{{$dt->NIK}}</td>
        <td>{{$dt->nama_karyawan}}</td>
        <td>{{$dt->dept_code}}</td>
        <td>{{$periode}}</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <!--
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        -->
    </tr>
    @endforeach
</table>