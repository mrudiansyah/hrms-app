<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=ksk_performance_template.xls");
?>
<table border="1">
    <thead>
        <tr>
            <th>NO</th>
            <th>PERIODE</th>
            <th>ID_KSK_DETAIL</th>
            <th>NIK</th>
            <th>NAME</th>
            <th>DEPT</th>
            <th>WARNING_LETER</th>
            <th>SICK</th>
            <th>PERMIT</th>
            <th>ALPA</th>
            <th>LATE(Frequency)</th>
            <th>LATE(Minutes)</th>
            <th>PERFORMANCE</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=0;?>
        @foreach($tb_ksk as $dt)
            <?php $bulan=$dt->months%12;$tahun=($dt->months-$bulan)/12;?>
            <tr>
                <td><?php $no++;echo $no;?></td>
                <td>{{$periode}}</td>
                <td>{{$dt->id}}</td>
                <td>{{$dt->NIK}}</td>
                <td>{{$dt->employee_name}}</td>
                <td>{{$dt->dept_code}}</td>
                <td>{{$dt->warning_letter}}</td>
                <td>{{$dt->sick}}</td>
                <td>{{$dt->permit}}</td>
                <td>{{$dt->alpa}}</td>
                <td>{{$dt->late}}</td>
                <td>{{$dt->minutes}}</td>
                <td>{{$dt->performance}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
