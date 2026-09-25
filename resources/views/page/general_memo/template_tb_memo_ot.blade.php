<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=template_memo_ot.xls");
?>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>ID Memo</th>
            <th>Part No.</th>
            <th>Part Name</th>
            <th>Line</th>
            <th>GSPH/JPH</th>
            <th>Process</th>
            <th>Date OT</th>
            <th>Start</th>
            <th>Finish</th>
            <th>Qty</th>
            <th>Reason OT</th>
            <th>Remark</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td>{{$data['id_memo']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>YYYY-MM-DD</td>
            <td>HH:mm:ss</td>
            <td>HH:mm:ss</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
