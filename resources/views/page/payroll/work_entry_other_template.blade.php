<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=work_entry_other_template.xls");
?>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>EMS_ID</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Dept</th>
            <th>Periode</th>
            <th>Days</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=0;?>
        @foreach($tb_employee as $dt)
        <tr>
            <td>{{ ++$no }}</td>
            <td>{{ $dt->id_employee }}</td>
            <td>{{ $dt->NIK }}</td>
            <td>{{ $dt->employee_name ?? $dt->nama_karyawan }}</td>
            <td>{{ $dt->department }}</td>
            <td>{{ $periode }}</td>
            <td>0</td>
        </tr>
        @endforeach
    </tbody>
</table>
