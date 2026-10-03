<style>
    @page {
        margin: 20mm;
    }
    html {
        margin: 0;
    }
    body {
        padding: 15mm;
	}
	ol li{
		padding-bottom:5px;
	}
	.a td{
		text-align:left;
		font-weight:normal;
		border:0px solid #000;
        font-size:14px;
	}
	.a .b td{
		text-align:left;
		font-weight:normal;
		border:1px solid #AAA;
		padding:5px;
	}
    .b > tr > td,
    .b > tbody > tr > td {
        padding:10px;
        border:1px solid #AAA;
    }
    .rubric-table {
        width:100%;
        table-layout:fixed;
    }
    .rubric-table td {
        padding:5px;
        border:1px solid #AAA;
    }
    .rubric-description {
        text-align:justify;
    }
    .handover-table > tr > td,
    .handover-table > tbody > tr > td {
        padding:10px;
        border:1px solid #AAA;
    }
    .c td{
        text-align:center;
        border:1px solid #AAA;
    }
	.absolute {
		position: absolute;
		width:180px;
		left:0px;
		top:-10px;
	}		
	.relative {
		left:40px;
		position: relative;
		height:20px;
	}	
	.absolute2 {
		position: absolute;
		width:130px;
		left:0px;
		top:-10px;
	}		
	.relative2 {
		left:0px;
		position: relative;
		height:20px;
	}	
</style>
@foreach($tb_performance as $row)
<table style="width:100%;" border="0" cellspacing="5">
	<tr>
		<td style="width:150px;"><img src="{{ base_path() }}/public/gambar/logosai.png" style="width:150px;"></td>
		<td style="text-align:center;font-size:20px;"><b>PT. SUMMIT ADYAWINSA INDONESIA</b></td>
		<td style="width:150px;text-align:center;font-size=6px;">SAI/RC/P&GA/03/01</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td colspan="3" style="text-align:center;font-size:22px;"><b>FORMULIR PENILAIAN KINERJA KARYAWAN</b></td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td colspan="3" style="text-align:center;font-size:18px;">KARYAWAN TETAP - LEVEL PELAKSANA</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3" style="border:1px solid #AAA;text-align:center;padding:10px;">PERIODE UJI KERJA : JANUARI  S/D DESEMBER {{$year}}</td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
        <td colspan="3">
            <table style="width:100%;" border="1" cellspacing="5" class="b">
                <tr><td colspan="2" style="text-align:center;padding:10px;">1. DATA KARYAWAN YANG AKAN DINILAI</td></tr>
                <tr>
                    <td style="width:47%;padding:20px;">
                       <table style="width:100%;" class="a">
                            <tr><td style="width:120px;">Nama Karyawan</td><td style="width:1px;padding:7px 0px;">:</td><td style="text-align:left;"><b>{{$row->nama_karyawan}}</b></td></tr>
                            <tr><td>Tanggal Masuk</td><td style="width:1px;padding:7px 0px;">:</td><td>{{$row->tanggal_masuk}}</td></tr>
                            <tr><td>Div & Sub Div</td><td style="width:1px;padding:7px 0px;">:</td><td>{{$row->department}}</td></tr>
                       </table>
                    </td>
                    <td style="padding:20px;">
                       <table style="width:100%;" class="a">
                            <tr><td style="width:220px;">Nama Jabatan / Gol / NIK</td><td style="width:1px;padding:7px 0px;">:</td><td>{{$row->jabatan}}</td></tr>
                            <tr><td>Nama Atasan Langsung</td><td style="width:1px;padding:7px 0px;">:</td><td>{{$row->atasan_langsung}}</td></tr>
                            <tr>
                                <td>Masa kerja dibawah atasan langsung</td><td style="width:1px;padding:7px 0px;">:</td>
                                <td>
                                    <?php $thn=floor($row->masa_kerja_member/12);$bln=$row->masa_kerja_member%12;?>
                                    <?php if($thn>0)echo $thn.' Tahun';if($thn>0&&$bln>0)echo " ";if($bln>0)echo $bln.' Bulan';?>
                                </td>
                            </tr>
                       </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
        <td colspan="3">
            <table style="width:100%;" border="1" cellspacing="5" class="b">
                <tr><td style="text-align:center;padding:10px;">2. SASARAN / TARGET / HASIL KERJA YANG DIHARAPKAN</td></tr>
                <tr>
                    <td style="height:150px;text-align:center;vertival-align:middle;">
                        <p>{{$row->target}}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
        <td colspan="3">
            <table style="width:100%;" border="1" cellspacing="5" class="b">
                <tr><td style="text-align:center;padding:10px;">3. HASIL AKHIR PENILAIAN</td></tr>
                <tr>
                    <td style="height:180px;padding:20px;">
                       <table style="width:100%;" class="a">
                            <tr>
                                <td style="width:58%;">
                                    Nilai <b>ANGKA</b>
                                    <table style="width:100%;" class="c">
                                        <tr>
                                            <!-- <td class="c" style="width:19%">Jan-Mar</td> -->
                                            <td class="c" style="width:19%" colspan="2">Jan-Jun</td>
                                            <!-- <td class="c" style="width:19%">Jul-Sep</td> -->
                                            <td class="c" style="width:19%" colspan="2">Jul-Des</td>
                                            <td style="width:1%;border:0px;">&nbsp;</td>
                                            <td class="c">Average</td>
                                        </tr>
                                        <tr>
                                            <!-- <td class="c" style="width:19%;height:40px;">{{$data['T1']}}</td> -->
                                            <td class="c" style="width:19%" colspan="2">{{$data['T2']}}</td>
                                            <!-- <td class="c" style="width:19%">{{$data['T3']}}</td> -->
                                            <td class="c" style="width:19%" colspan="2">{{$data['T4']}}</td>
                                            <td style="width:1%;border:0px;">&nbsp;</td>
                                            <td class="c">{{number_format($data['TAve'],2)}}</td>
                                        </tr>
                                    </table>
                                </td>
                                <td rowspan="2" style="padding-left:70px;">
                                    Range angka & kategori nilai
                                    <table style="width:100%;" class="a">
                                        @foreach($tb_perform_grade as $dt)
                                            <tr>
                                                <td style="text-align:left;width:140px;">Diatas {{number_format($dt->range_bawah,1)}} s/d {{$dt->range_atas}}</td>
                                                <td style="width:1px;">:</td>
                                                <td style="width:3px;">{{$dt->grade}}</td>
                                                <td>{{$dt->keterangan}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Nilai <b>HURUF</b>
                                    <table style="width:100%;" class="c">
                                        <tr>
                                            <!-- <td class="c" style="width:19%">Jan-Mar</td> -->
                                            <td class="c" style="width:19%" colspan="2">Jan-Jun</td>
                                            <!-- <td class="c" style="width:19%">Jul-Sep</td> -->
                                            <td class="c" style="width:19%" colspan="2">Jul-Des</td>
                                            <td style="width:1%;border:0px;">&nbsp;</td>
                                            <td class="c">Average</td>
                                        </tr>
                                        <tr>
                                            <!-- <td class="c" style="width:19%;height:30px;">{{$data['G1']}}</td> -->
                                            <td class="c" style="width:19%" colspan="2">{{$data['G2']}}</td>
                                            <!-- <td class="c" style="width:19%">{{$data['G3']}}</td> -->
                                            <td class="c" style="width:19%" colspan="2">{{$data['G4']}}</td>
                                            <td style="width:1%;border:0px;">&nbsp;</td>
                                            <td class="c">{{$data['GAve']}}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
        <td colspan="3">
            <table style="width:100%;" border="1" cellspacing="5" class="b">
                <tr><td colspan="2" style="text-align:center;padding:10px;">4. CATATAN / ULASAN PARA PENILAI (Disampaikan pada setiap akhir penilaian tahunan)</td></tr>
                <tr>
                    <td style="width:50%;height:150px;vertical_align:top;text-align:center;">
                        <label style="font-size:12px;">Penilai I.</label>
                        <p>{{$row->catatan_penilai_1}}</p>
                    </td>
                    <td style="width:50%;height:150px;vertical_align:top;text-align:center;">
                        <label style="font-size:12px;">Penilai II.</label>
                        <p>{{$row->catatan_penilai_2}}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
        <td colspan="3">
            <table style="width:100%;" border="1" cellspacing="5" class="b">
                <tr><td colspan="3" style="text-align:center;padding:10px;">5. PENGESAHAN HASIL PENILAIAN (Dilakukan pada setiap akhir tahunan)</td></tr>
                <tr>
                    <td style="width:33%;height:100px;vertical_align:top;text-align:center;">
                        <label style="font-size:12px;">Penilaian I.</label>
                        <table class="a">
                            <tr>
                                <td colspan="3" style="height:40px;text-align:center;">
                                    <div class="relative">
                                    <?php 
                                        $id_penilai_1=$row->id1;
                                        if($row->status_penilai_1=='1'){
                                            $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                            $confirm=base_path().'/public/approval/confirm.png';
                                            $reject=base_path().'/public/approval/rejected.png';
                                            $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                            if (file_exists($filename)){?>
                                                <img src='{{ $ttd_app }}' class="absolute">
                                            <?php }else{?>
                                                <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                            <?php }
                                        }
                                    ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:30px;">Nama Jelas</td><td style="width:1px;">:</td><td style="width:100px;">{{$row->atasan_langsung}}</td>
                            </tr>
                            <tr>
                                <td>Tanggal TTD</td><td>:</td><td>{{$row->tgl_penilai_1}}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:33%;height:100px;vertical_align:top;text-align:center;">
                        <label style="font-size:12px;">Penilaian II.</label>
                        <table class="a">
                            <tr>
                                <td colspan="3" style="height:40px;text-align:center;">
                                    <div class="relative">
                                    <?php 
                                        $id_penilai_2=$row->id2;
                                        if($row->status_penilai_2=='1'){
                                            $ttd_app=base_path().'/public/approval/'.$id_penilai_2.'.png';
                                            $confirm=base_path().'/public/approval/confirm.png';
                                            $reject=base_path().'/public/approval/rejected.png';
                                            $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_2.'.png';
                                            if (file_exists($filename)){?>
                                                <img src='{{ $ttd_app }}' class="absolute">
                                            <?php }else{?>
                                                <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                            <?php }
                                        }
                                    ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:30px;">Nama Jelas</td><td style="width:1px;">:</td><td style="width:100px;">{{$row->nama_penilai_2}}</td>
                            </tr>
                            <tr>
                                <td>Tanggal TTD</td><td>:</td><td>{{$row->tgl_penilai_2}}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:34%;height:100px;vertical_align:top;text-align:center;">
                        <label style="font-size:12px;">Div. Manager / Factory Mgr. / Direktur</label>
                        <table class="a">
                            <tr>
                                <td colspan="3" style="height:40px;text-align:center;">
                                    <div class="relative">
                                    <?php 
                                        $id_penilai_3=$row->id3;
                                        if($row->status_direktur=='1'){
                                            $ttd_app=base_path().'/public/approval/'.$id_penilai_3.'.png';
                                            $confirm=base_path().'/public/approval/confirm.png';
                                            $reject=base_path().'/public/approval/rejected.png';
                                            $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_3.'.png';
                                            if (file_exists($filename)){?>
                                                <img src='{{ $ttd_app }}' class="absolute">
                                            <?php }else{?>
                                                <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                            <?php }
                                        }
                                    ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:30px;">Nama Jelas</td><td style="width:1px;">:</td><td style="width:100px;">{{$row->nama_direktur}}</td>
                            </tr>
                            <tr>
                                <td>Tanggal TTD</td><td>:</td><td>{{$row->tgl_direktur}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
        <td style="border:1px solid #000;">
            <table width="100%" border="0" cellspacing="5">
                <tr><td style="text-align:right;"><label style="background:#0ca5df;padding:5px;">Untuk Jabatan Pelaksana</label></td></tr>
                <tr><td style="text-align:left;"><label style="padding:5px;"><b>6. TABEL PANDUAN PENILAIAN</b></label></td></tr>
                <tr>
                    <td>
                        <table border="1" cellspacing="0" cellpadding="3" class="rubric-table">
                            <tr>
                                <td rowspan="2" style="width:24%;text-align:center;font-size:14px;">ASPEK NILAI</td>
                                <td colspan="3" style="text-align:center;font-size:14px;">D</td>
                                <td colspan="3" style="text-align:center;font-size:14px;">C</td>
                                <td colspan="3" style="text-align:center;font-size:14px;">B</td>
                                <td colspan="3" style="text-align:center;font-size:14px;">A</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align:center;font-size:14px;">KURANG</td>
                                <td colspan="3" style="text-align:center;font-size:14px;">CUKUP</td>
                                <td colspan="3" style="text-align:center;font-size:14px;">BAIK</td>
                                <td colspan="3" style="text-align:center;font-size:14px;">SANGAT BAIK</td>
                            </tr>
                            <?php 
                                $no=0;
                                $a1=0;
                                $b1=0;
                                $c1=0;
                                $d1=0;
                                $a2=0;
                                $b2=0;
                                $c2=0;
                                $d2=0;
                                $a3=0;
                                $b3=0;
                                $c3=0;
                                $d3=0;
                            ?>
                            @foreach($tb_perform_aspek as $dt)
                            <?php 
                                $no++;$idaspek=$dt->id;
                            ?>
                            <tr>
                                <td style="height:20px;width:24%;vertical-align:top;font-size:10px;border-bottom:0px;"><label style="color:#AAA;">{{$no}}</label></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'D1'],2)}}<?php $d1=$d1+$data[$idaspek.'D1'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'D2'],2)}}<?php $d2=$d2+$data[$idaspek.'D2'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'D3'],2)}}<?php $d3=$d3+$data[$idaspek.'D3'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'C1'],2)}}<?php $c1=$c1+$data[$idaspek.'C1'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'C2'],2)}}<?php $c2=$c2+$data[$idaspek.'C2'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'C3'],2)}}<?php $c3=$c3+$data[$idaspek.'C3'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'B1'],2)}}<?php $b1=$b1+$data[$idaspek.'B1'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'B2'],2)}}<?php $b2=$b2+$data[$idaspek.'B2'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'B3'],2)}}<?php $b3=$b3+$data[$idaspek.'B3'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'A1'],2)}}<?php $a1=$a1+$data[$idaspek.'A1'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'A2'],2)}}<?php $a2=$a2+$data[$idaspek.'A2'];?></td>
                                <td style="text-align:center;font-size:14px;">{{number_format($data[$idaspek.'A3'],2)}}<?php $a3=$a3+$data[$idaspek.'A3'];?></td>
                            </tr>
                            <tr>
                                <td style="text-align:center;font-size:14px;border-top:0px;vertical-align:top;width:24%;">{{$dt->nama_aspek}}</td>
                                <td class="rubric-description" style="font-size:10px;" colspan="3">
                                    <?php 
                                        $teks = $data[$idaspek.'D'];
                                        $jumlah = substr_count($teks, '#');
                                        $array = explode("#", $teks);
                                        if($jumlah>0){
                                            for($i=0;$i<=$jumlah;$i++){
                                                echo str_replace("#", "", $array[$i])."<br>";
                                            }
                                        }else{
                                            echo $teks;
                                        }
                                    ?>
                                </td>
                                <td class="rubric-description" style="font-size:10px;" colspan="3">
                                    <?php 
                                        $teks = $data[$idaspek.'C'];
                                        $jumlah = substr_count($teks, '#');
                                        $array = explode("#", $teks);
                                        if($jumlah>0){
                                            for($i=0;$i<=$jumlah;$i++){
                                                echo str_replace("#", "", $array[$i])."<br>";
                                            }
                                        }else{
                                            echo $teks;
                                        }
                                    ?>
                                </td>
                                <td class="rubric-description" style="font-size:10px;" colspan="3">
                                    <?php 
                                        $teks = $data[$idaspek.'B'];
                                        $jumlah = substr_count($teks, '#');
                                        $array = explode("#", $teks);
                                        if($jumlah>0){
                                            for($i=0;$i<=$jumlah;$i++){
                                                echo str_replace("#", "", $array[$i])."<br>";
                                            }
                                        }else{
                                            echo $teks;
                                        }
                                    ?>
                                </td>
                                <td class="rubric-description" style="font-size:10px;" colspan="3">
                                    <?php 
                                        $teks = $data[$idaspek.'A'];
                                        $jumlah = substr_count($teks, '#');
                                        $array = explode("#", $teks);
                                        if($jumlah>0){
                                            for($i=0;$i<=$jumlah;$i++){
                                                echo str_replace("#", "", $array[$i])."<br>";
                                            }
                                        }else{
                                            echo $teks;
                                        }
                                    ?>
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td>&nbsp;</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($d1,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($d2,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($d3,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($c1,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($c2,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($c3,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($b1,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($b2,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($b3,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($a1,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($a2,2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($a3,2)}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td style="border:0px;height:5px;">&nbsp;</td></tr>
    <tr>
        <td style="border:1px solid #000;">
            <table width="100%" border="0" cellspacing="5">
                <tr><td style="text-align:right;"><label style="background:#0ca5df;padding:5px;">Untuk Jabatan Pelaksana</label></td></tr>
                <tr><td style="text-align:left;"><label style="padding:5px;"><b>7. KOLOM PENILAIAN</b></label></td></tr>
                <tr>
                    <td>
                        <table border="1" cellspacing="0" cellpadding="3" width="100%" style="border:0px;" class="rubric-table">
                            <tr>
                                <td style="text-align:center;font-size:14px;height:20px;">ASPEK NILAI</td>
                                <td style="text-align:center;font-size:14px;" colspan="2">JAN-JUN</td>
                                <td style="text-align:center;font-size:14px;" colspan="2">JUL-DES</td>
                                <td style="text-align:center;font-size:14px;">RATA-RATA</td>
                            </tr>
                            <?php $no=0;?>
                            @foreach($tb_perform_aspek as $dt)
                            <?php 
                                $no++;
                                $idaspek=$dt->id;
								$jml=0;
								$isi=0;
								$ave=0;
								$idaspek=$dt->id;
                                //if(isset($data[$id_performance.'1'.$idaspek]))
								if(isset($data[$id_performance.'1'.$idaspek])&&$data[$id_performance.'1'.$idaspek]>0)$isi++;
                                else{$data[$id_performance.'1'.$idaspek]=0;}
								if($data[$id_performance.'2'.$idaspek]>0)$isi++;
								if(isset($data[$id_performance.'3'.$idaspek])&&$data[$id_performance.'3'.$idaspek]>0)$isi++;
                                else{$data[$id_performance.'3'.$idaspek]=0;}
								if($data[$id_performance.'4'.$idaspek]>0)$isi++;
								$jml=$jml+$data[$id_performance.'1'.$idaspek]+$data[$id_performance.'2'.$idaspek]+$data[$id_performance.'3'.$idaspek]+$data[$id_performance.'4'.$idaspek];
								if($isi>0)$ave=$jml/$isi;
								else $ave=0;
                            
                            ?>
                            <tr>
                                <td style="text-align:left;font-size:14px;border-top:0px;vertical-align:top;"><label>{{$no}}. {{$dt->nama_aspek}}</label></td>
                                <!-- <td style="text-align:center;font-size:14px;">
                                    {{number_format($data[$id_performance.'1'.$idaspek],2)}}
                                </td> -->
                                <td style="text-align:center;font-size:14px;" colspan="2">{{number_format($data[$id_performance.'2'.$idaspek],2)}}</td>
                                <!-- <td style="text-align:center;font-size:14px;">
                                    {{number_format($data[$id_performance.'3'.$idaspek],2)}}
                                </td> -->
                                <td style="text-align:center;font-size:14px;" colspan="2">{{number_format($data[$id_performance.'4'.$idaspek],2)}}</td>
                                <td style="text-align:center;font-size:14px;">{{number_format($ave,2)}}</td>
                            </tr>
                            @endforeach
                            <tr><td style="border:0px;height:5px;" colspan="6">&nbsp;</td></tr>
                            <tr>
                                <td style="text-align:center;font-size:14px;height:20px;" rowspan="2">PERNYATAAN BERSAMA</td>
                                <td style="text-align:center;font-size:14px;border-bottom:0px;" colspan="4">Penilaian kinerja ini telah dibahas bersama oleh kami yang bertanda tangan dibawah ini.</td>
                                <td style="text-align:center;font-size:14px;height:20px;" rowspan="2">Catatan</td>
                            </tr>
                            <tr>
                                <td style="text-align:center;font-size:14px;border-top:0px;" colspan="4">TANDA TANGAN & TANGGAL</td>
                            </tr>
                            <tr>
                                <td style="text-align:center;font-size:14px;" rowspan="2">Karyawan</td>
                                <!-- <td style="text-align:center;font-size:14px;height:30px;border-bottom:0px;">
                                     <div class="relative2">
                                        <?php 
                                            $id_penilai_1=$row->id_employee;
                                            if($data['TTD1']=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td> -->
                                <td style="text-align:center;font-size:14px;height:30px;border-bottom:0px;" colspan="2">
                                     <div class="relative2">
                                        <?php 
                                            $id_penilai_1=$row->id_employee;
                                            if($data['TTD2']=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td>
                                <!-- <td style="text-align:center;font-size:14px;height:30px;border-bottom:0px;">
                                     <div class="relative2">
                                        <?php 
                                            $id_penilai_1=$row->id_employee;
                                            if($data['TTD3']=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td> -->
                                <td style="text-align:center;font-size:14px;height:30px;border-bottom:0px;" colspan="2">
                                     <div class="relative2">
                                        <?php 
                                            $id_penilai_1=$row->id_employee;
                                            if($data['TTD4']=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td>
                                <td style="text-align:center;font-size:14px;" rowspan="4">{{$data['NOTE']}}</td>
                            </tr>
                            <tr>
                                <!-- <td style="text-align:center;font-size:10px;border-top:0px;">{{$data['TGLTTD1']}}</td> -->
                                <td style="text-align:center;font-size:10px;border-top:0px;" colspan="2">{{$data['TGLTTD2']}}</td>
                                <!-- <td style="text-align:center;font-size:10px;border-top:0px;">{{$data['TGLTTD3']}}</td> -->
                                <td style="text-align:center;font-size:10px;border-top:0px;" colspan="2">{{$data['TGLTTD4']}}</td>
                            </tr>
                            <tr>
                                <td style="text-align:center;font-size:14px;" rowspan="2">Penilai 1</td>
                                <!-- <td style="text-align:center;font-size:14px;height:30px;border-bottom:0px;">
                                     <div class="relative2">
                                        <?php 
                                            $id_penilai_1=$row->id1;
                                            if($data['TTDL1']=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td> -->
                                <td style="text-align:center;font-size:14px;height:30px;border-bottom:0px;" colspan="2">
                                     <div class="relative2">
                                        <?php 
                                            $id_penilai_1=$row->id1;
                                            if($data['TTDL2']=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td>
                                <!-- <td style="text-align:center;font-size:14px;height:30px;border-bottom:0px;">
                                     <div class="relative2">
                                        <?php 
                                            $id_penilai_1=$row->id1;
                                            if($data['TTDL3']=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td> -->
                                <td style="text-align:center;font-size:14px;height:30px;border-bottom:0px;" colspan="2">
                                     <div class="relative2">
                                        <?php 
                                            $id_penilai_1=$row->id1;
                                            if($data['TTDL4']=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <!-- <td style="text-align:center;font-size:10px;border-top:0px;">{{$data['TGLTTDL1']}}</td> -->
                                <td style="text-align:center;font-size:10px;border-top:0px;" colspan="2">{{$data['TGLTTDL2']}}</td>
                                <!-- <td style="text-align:center;font-size:10px;border-top:0px;">{{$data['TGLTTDL3']}}</td> -->
                                <td style="text-align:center;font-size:10px;border-top:0px;" colspan="2">{{$data['TGLTTDL4']}}</td>
                            </tr>
                       </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table style="width:100%;" border="1" cellspacing="5" class="rubric-table">
                <tr>
                    <td style="text-align:center;font-size:14px;height:30px" colspan="3">8. KENDALA DALAM BEKERJA & UPAYA UNTUK MENGATASI & / ANTISIPASI</td>
                    <td style="text-align:center;font-size:12px;" rowspan="3"><label style="width:10px;">Mintakanlah <br>Tanda Tangan HRD<br>Manager apabila ada<br>usulan Training</label></td>
                </tr>
                <tr>
                    <td style="text-align:center;font-size:14px;" rowspan="2">BULAN</td>
                    <td style="text-align:center;font-size:14px;" rowspan="2">URAIAN KENDALA KARYAWAN DALAM BEKERJA</td>
                    <td style="text-align:center;font-size:14px;">UPAYA UNTUK MENGATASI & / ANTISIPASI</td>
                </tr>
                <tr>
                    <td style="text-align:center;">Diisi dengan ulasan & atau usulan training</td>
                </tr>
                <?php for($i=1;$i<=12;$i++){?>
                    <tr>
                        <td style="text-align:center;font-size:14px;height:85px;">{{$data['P'.$i]}}</td>
                        <td style="text-align:center;">{{$data['K'.$i]}}</td>
                        <td style="text-align:center;">{{$data['U'.$i]}}</td>
                        <td style="width:5px;text-align:center;">{{$data['A'.$i]}}</td>
                    </tr>
                <?php }?>
            </table>
        </td>
    </tr>
    <tr><td style="border:0px;height:30px;">&nbsp;</td></tr>
    <tr>
        <td>
            <table style="width:100%;" border="1" cellspacing="5" class="handover-table">
                <tr>
                    <td style="text-align:center;font-size:14px;height:50px;" colspan="6">
                        9. DATA TANGGAL SERAH TERIMA FORMULIR<br>
                        (Setelah diisi dengan semestinya, formulir ini wajib diserahkan kembali kebagian HRD, paling lambat akhir bulan Januari tahun berikutnya)
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center;font-size:14px;height:30px;" colspan="3">PENYERAHAN DARI HRD KE PENILAIAN TERKAIT</td>
                    <td style="text-align:center;font-size:14px;" colspan="3">PENERIMAAN KEMBALI DARI PENILAI TERKAIT KE HRD</td>
                </tr>
                <tr>
                    <td style="text-align:center;font-size:14px;height:100px;vertical-align:top;">
                        <table width="100%" border="0">
                            <tr><td style="text-align:center;">Tanggal</td></tr>
                            <tr><td style="height:80px;text-align:center;">{{$row->tgl_distribusi}}</td></tr>
                        </table>
                    </td>
                    <td style="text-align:center;font-size:14px;height:100px;vertical-align:top;">
                        <table width="100%" border="0">
                            <tr><td style="text-align:center;">Yang Menyerahkan</td></tr>
                            <tr>
                                <td style="height:60px;text-align:center;vertical-align:top;">
                                    <div class="relative2">
                                        <?php 
                                            $id_penilai_1='97';
                                            if($row->status_dibuka=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr><td style="text-align:center;"><u>NOVA TRIANA N</u></td></tr>
                        </table>
                    </td>
                    <td style="text-align:center;font-size:14px;height:100px;vertical-align:top;">
                        <table width="100%" border="0">
                            <tr><td style="text-align:center;">Yang Menerima</td></tr>
                            <tr>
                                <td style="height:60px;text-align:center;vertical-align:top;">
                                    <div class="relative2">
                                        <?php 
                                            $id_penilai_1=$row->id1;
                                            if($row->status_dibuka=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr><td style="text-align:center;"><u>{{ucwords($row->atasan_langsung)}}</u></td></tr>
                        </table>
                    </td>
                    <td style="text-align:center;font-size:14px;height:100px;vertical-align:top;">
                        <table width="100%" border="0">
                            <tr><td style="text-align:center;">Yang Menyerahkan</td></tr>
                            <tr>
                                <td style="height:60px;text-align:center;vertical-align:top;">
                                    <div class="relative2">
                                        <?php 
                                            $id_penilai_1=$row->id1;
                                            if($row->status_konfirmasi=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr><td style="text-align:center;"><u>{{ucwords($row->atasan_langsung)}}</u></td></tr>
                        </table>
                    </td>
                    <td style="text-align:center;font-size:14px;height:100px;vertical-align:top;">
                        <table width="100%" border="0">
                            <tr><td style="text-align:center;">Yang Menerima</td></tr>
                            <tr>
                                <td style="height:60px;text-align:center;vertical-align:top;">
                                    <div class="relative2">
                                        <?php 
                                            $id_penilai_1='97';
                                            if($row->status_konfirmasi=='1'){
                                                $ttd_app=base_path().'/public/approval/'.$id_penilai_1.'.png';
                                                $confirm=base_path().'/public/approval/confirm.png';
                                                $reject=base_path().'/public/approval/rejected.png';
                                                $filename = 'c:/xampp/htdocs/EMS/public/approval/'.$id_penilai_1.'.png';
                                                if (file_exists($filename)){?>
                                                    <img src='{{ $ttd_app }}' class="absolute2">
                                                <?php }else{?>
                                                    <img src='{{ $confirm }}' style='width:100px;height:40px;'>
                                                <?php }
                                            }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr><td style="text-align:center;"><u>NOVA TRIANA N</u></td></tr>
                        </table>
                    </td>
                    <td style="text-align:center;font-size:14px;height:100px;vertical-align:top;">
                        <table width="100%" border="0">
                            <tr><td style="text-align:center;">Tanggal</td></tr>
                            <tr><td style="height:80px;text-align:center;">{{$row->tgl_konfirmasi}}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endforeach