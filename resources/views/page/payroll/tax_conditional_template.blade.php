<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=tax_conditional_template.xls");
?>
<table border="1" style="min-width:100%;">
    <thead>
        <tr>
            <th style="width:50px;">No<br>&nbsp;</th>
            <th>NIK<br>&nbsp;</th>
            <th >NAME<br>&nbsp;</th>
            <th>DEPT<br>&nbsp;</th>
            <th>PERIODE<br>&nbsp;</th>
            <th>ID_TAX<br>&nbsp;</th>
            <th>PPh21<br>Conditional</th>
            <th>Status<br>Implement</th>
        </tr>
    </thead>
    <tbody>
    <?php $no=0;?>
    @foreach($tb_salary_tax as $dt)
        <tr>
            <td>
                <?php
                    $no++;
                ?>
                {{$no}}
            </td>
            <td>{{$dt->NIK}}</td>
            <td>{{$dt->nama_karyawan}}</td>
            <td>{{$dt->dept_code}}</td>
            <td>{{$dt->periode}}</td>
            <td>{{$dt->id}}</td>
            <td><?php if($dt->tax_amount=='')echo "0";else echo $dt->tax_amount?></td>
            <td><?php if($dt->status_implement=='')echo "0";else echo $dt->status_implement;?></td>
        </tr>
    @endforeach
    </tbody>
</table>
