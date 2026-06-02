<!DOCTYPE html>
<html>
<head>
    <title>Data Manifest {{ $tgl }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #d3d8d8; }
        .dept-header { background-color: #f4f4f4; font-weight: bold; font-size: 1.1em; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Data Manifest - {{ $tgl }}</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Employee</th>
                <th>Masuk</th>
                <th>Ijin</th>
                <th>TL</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($manifests as $dept => $employees)
                <tr class="dept-header">
                    <td></td>
                    <td colspan="5">{{ $dept ?: 'NO DEPARTMENT' }}</td>
                </tr>
                <?php $no=0;?>
                @foreach($employees as $dt)
                <?php $no++;?>
                <tr>
                    <td>{{$no}}</td>
                    <td>{{ $dt->employee_name }}<br>{{ $dt->NIK }}</td>
                    <td>
                        @if($dt->masuk!=null)
                            {{ date('H:i',strtotime($dt->masuk)) }}
                        @endif
                    </td>
                    <td>{{ $dt->ijin }}</td>
                    <td class="text-center">{{ $dt->tugas_luar == '1' ? '√' : ' ' }}</td>
                    <td class="text-center">{{ $dt->status == '1' ? '√' : ' ' }}</td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" class="text-center">No data found for {{ $tgl }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
