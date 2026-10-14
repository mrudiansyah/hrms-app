<style>
    html{
        margin:40px;
    }
    ol li{
        padding-bottom:5px;
    }
    .tabelot th{
        text-align:center;
        font-weight:normal;
        font-size:8px;
    }
    .tabelot td{
        text-align:center;
        font-size:10px;
    }
    .absolute {
        position: absolute;
        width:30px;
        right:0px;
        top:0px;
    }		
    .relative {
        left:0px;
        position: relative;
        width:30px;
        height:50px;
    }	
    .ttd {
        width: 30px;
        height: 40px;
        object-fit: cover;
        object-position: 100% 100%;
    }
    .total-row {
        /* font-weight: bold; */
    }
</style>
<?php
$sudah="<img src={{ asset('public/gambar/yes.png') }} style='width:180px;align:center;'>";
$belum="<img src={{ asset('public/gambar/no.png') }} style='width:180px;align:center;'>";
?>
<div style="font-size: 0.65em">
    <table cellspacing="0" cellpadding="0" border="1" width="100%">
        <thead style="font-size:0.8em;">
        <tr>
            <td rowspan="6" colspan="2" style="text-align:center;padding:5px;">
                <img src="{{ asset('public/gambar/logosai.png') }}" style="width:180px;align:center;">
            </td>
            <td rowspan="3" colspan="3" style="text-align:center;">MATRIC COMPETENCE</td>
            <td colspan="1" style="font-size:0.75em;text-align:center;">INDEX</td>
            <td colspan="10" style="text-align:center;">DESCRIPTION</td>
        </tr>
        <tr>
            <td colspan="1" style="text-align:center;">0</td>
            <td colspan="10">{{ $data['v0'] }}</td>
        </tr>
        <tr>
            <td colspan="1" style="text-align:center;">1</td>
            <td colspan="10">{{ $data['v1'] }}</td>
        </tr>
        <tr>
            <td style="text-align:center;width:120px;">EMPLOYEE NAME</td>
            <td colspan="2" style="text-align:center;">POSITION</td>
            <td colspan="1" style="text-align:center;">2</td>
            <td colspan="10">{{ $data['v2'] }}</td>
        </tr>
        <tr>
            <td style="text-align:center;"><b>{{ $data['nama_karyawan'] }}</b></td>
            <td colspan="2" style="text-align:center;">{{ $data['jabatan'] }}</td>
            <td colspan="1" style="text-align:center;">3</td>
            <td colspan="10">{{ $data['v3'] }}</td>
        </tr>
        <tr>
            <td style="text-align:center;">NIK</td>
            <td colspan="2" style="text-align:center;">DEPARTMENT</td>
            <td colspan="1" style="text-align:center;">4</td>
            <td colspan="10">{{ $data['v4'] }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center;height:18px;">PT.SUMMIT ADYAWINSA INDONESIA</td>
            <td style="text-align:center;">{{ $data['NIK'] }}</td>
            <td colspan="2" style="text-align:center;">{{ $data['department'] }}</td>
            <td colspan="1" style="text-align:center;">TD</td>
            <td colspan="10">Tidak Disyaratkan</td>
        </tr>
        </thead>
        <tr>
            <th style="width:30px;vertical-align:middle;" rowspan="2">No</th>
            <th style="vertical-align:middle;" rowspan="2" colspan="2">Competence Name</th>
            <th style="width:60px;text-align:center;vertical-align:middle;" rowspan="2">Minimum</th>
            <?php
                $awal = date('Y') - 2;
                $akhir = date('Y');
                $z = 0;
                for($x = $awal; $x <= $akhir; $x++){
                    $z++;
                    if($z != 2)
                        echo "<th style='background:#EEE;' colspan='4'>".$x."</th>";
                    else
                        echo "<th colspan='4'>".$x."</th>";
                }
            ?>
        </tr>
        <tr>
            <?php
                $awal = date('Y') - 2;
                $akhir = date('Y');
                $z = 0;
                for($x = $awal; $x <= $akhir; $x++){
                    $z++;
                    for($y = 1; $y <= 4; $y++){
                        if($z != 2)
                            echo "<th style='background:#EEE;width:25px;'>".$y."</th>";
                        else
                            echo "<th style='width:25px;'>".$y."</th>";
                    }
                }
            ?>
        </tr>
        
        <?php
            $no = 0;
            $currentType = '';
            $nos = 0;
            $ave = 0;
            
            // Inisialisasi array untuk menyimpan total per kolom
            $columnCount = 4 * (date('Y') - (date('Y') - 2) + 1); // Jumlah kolom kuartal
            $columnTotals = array_fill(0, $columnCount, 0);
            $columnCounts = array_fill(0, $columnCount, 0); // Untuk menghitung jumlah nilai yang valid per kolom
            
            // Pastikan variabel $competences dan $periodes tersedia
            $competences = $competences ?? [];
            $periodes = $periodes ?? [];
            
            foreach($competences as $competence) {
                $no++;
                $nos = $nos + $competence->minimum_level;
                
                // Check if type_skill changed
                if($currentType != $competence->type_skill) {
                    $currentType = $competence->type_skill;
                    echo '<tr style="background-color: #f39c12;">';
                    echo '<td colspan="'.(4 + ($columnCount)).'" style="font-weight: bold; padding-left: 10px;height:18px;">';
                    echo strtoupper($competence->type_skill);
                    echo '</td>';
                    echo '</tr>';
                }
                
                echo '<tr>';
                echo '<td style="text-align:center;height:17px;">'.$no.'</td>';
                echo '<td colspan="2" style="text-align:left; padding-left: 10px;">'.$competence->competence_name.'</td>';
                echo '<td style="text-align:center;">'.$competence->minimum_level.'</td>';
                
                $awal = date('Y') - 2;
                $akhir = date('Y');
                $colIndex = 0; // Indeks untuk melacak kolom kuartal
                
                foreach(range($awal, $akhir) as $year) {
                    for($quarter = 1; $quarter <= 4; $quarter++) {
                        $quarterMonths = ['03', '06', '09', '12'];
                        $periode = $year . '-' . $quarterMonths[$quarter-1];
                        $columnName = 'period_' . str_replace(['-', ' '], '_', $periode);
                        
                        $value = isset($competence->$columnName) ? $competence->$columnName : '0';
                        echo '<td style="text-align:center;">'.$value.'</td>';
                        
                        // Hitung total per kolom jika nilai valid
                        if (is_numeric($value)) {
                            $columnTotals[$colIndex] += $value;
                            $columnCounts[$colIndex]++;
                        }
                        
                        $colIndex++;
                    }
                }
                
                echo '</tr>';
            }
            $ave = $no > 0 ? $nos / $no : 0;
        ?>
        
        <!-- Baris untuk menampilkan total per kolom -->
        <tr class="total-row">
            <td colspan="3" style="text-align:center;border-top:2px solid #000;height:20px;">TOTAL</td>
            <td style="text-align:center;border-top:2px solid #000;"><?php echo $nos; ?></td>
            <?php
                for ($i = 0; $i < $columnCount; $i++) {
                    echo '<td style="text-align:center;border-top:2px solid #000;">'.$columnTotals[$i].'</td>';
                }
            ?>
        </tr>
        
        <!-- Baris untuk menampilkan rata-rata per kolom -->
        <tr class="total-row">
            <td colspan="3" style="text-align:center;height:20px;">AVERAGE</td>
            <td style="text-align:center;"><?php echo number_format($ave, 2); ?></td>
            <?php
                for ($i = 0; $i < $columnCount; $i++) {
                    $average = $columnCounts[$i] > 0 ? $columnTotals[$i] / $columnCounts[$i] : 0;
                    echo '<td style="text-align:center;">'.number_format($average, 2).'</td>';
                }
            ?>
        </tr>
        <!-- Sign -->
        <tr>
            <td colspan="2" style="text-align:left;vertical-align:top;border-right:0px;" rowspan="3">Remark:</td>
            <td style="text-align:right;border-left:0px;vertical-align:middle;padding-right:3px;" rowspan="3">
                <img title="{{$data['photo']}}" class="profile-user-img img-responsive img-square" style="width:80px;border:1px solid #ccc;padding:3px;" src="<?php echo $data['photo'];?>" alt="User profile picture">            
            </td>
            <td style="text-align:center;height:40px;">Employee</td>
            <?php
                foreach ($periodes as $index => $periode) {
                    $signStatus = 0;
                    
                    foreach ($signData as $sign) {
                        if (isset($sign['periode']) && $sign['periode'] == $periode) {
                            $signStatus = $sign['sign_status_1'] ?? 0;
                            break;
                        } elseif (isset($sign->periode) && $sign->periode == $periode) {
                            $signStatus = $sign->sign_status_1 ?? 0;
                            break;
                        }
                    }
                    
                    if ($signStatus == 1) {?>
                        <td style="text-align:center;"><img src="{{ asset('public/gambar/yes.png') }}" style="width:13px;align:center;"></td>
                    <?php } else {?>
                        <td style="text-align:center;"><img src="{{ asset('public/gambar/no.png') }}" style="width:10px;align:center;"></td>
                    <?php }
                }
            ?>
        </tr>
        <tr>
            <td style="text-align:center;height:40px;">Sign 1</td>
            <?php
                foreach ($periodes as $index => $periode) {
                    $signStatus = 0;
                    
                    foreach ($signData as $sign) {
                        if (isset($sign['periode']) && $sign['periode'] == $periode) {
                            $signStatus = $sign['sign_status_2'] ?? 0;
                            break;
                        } elseif (isset($sign->periode) && $sign->periode == $periode) {
                            $signStatus = $sign->sign_status_2 ?? 0;
                            break;
                        }
                    }
                    
                    if ($signStatus == 1) {?>
                        <td style="text-align:center;"><img src="{{ asset('public/gambar/yes.png') }}" style="width:13px;align:center;"></td>
                    <?php } else {?>
                        <td style="text-align:center;"><img src="{{ asset('public/gambar/no.png') }}" style="width:10px;align:center;"></td>
                    <?php }
                }
            ?>
        </tr>
        <tr>
            <td style="text-align:center;height:40px;">Sign 2</td>
            <?php
                foreach ($periodes as $index => $periode) {
                    $signStatus = 0;
                    
                    foreach ($signData as $sign) {
                        if (isset($sign['periode']) && $sign['periode'] == $periode) {
                            $signStatus = $sign['sign_status_3'] ?? 0;
                            break;
                        } elseif (isset($sign->periode) && $sign->periode == $periode) {
                            $signStatus = $sign->sign_status_3 ?? 0;
                            break;
                        }
                    }
                    
                    if ($signStatus == 1) {?>
                        <td style="text-align:center;"><img src="{{ asset('public/gambar/yes.png') }}" style="width:13px;align:center;"></td>
                    <?php } else {?>
                        <td style="text-align:center;"><img src="{{ asset('public/gambar/no.png') }}" style="width:10px;align:center;"></td>
                    <?php }
                }
            ?>
        </tr>
        <!-- End Sign -->
    </table>
</div>