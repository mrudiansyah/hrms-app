<style>
    html {
        margin: 20px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 8px;
    }

    .bingkai {
        border: 1px solid #000;
    }
</style>
@foreach($tb_slip as $dt)
    <?php 
            $pendapatan = $dt->Upah_Pokok + $dt->Tunjangan_Jabatan + $dt->Tunjangan_Prestasi + $dt->Tunjangan_Skill + $dt->Rapel + $dt->PPH21_Insentif + $dt->PPH21_Lebih_Bayar + $dt->tunjangan_shift + $dt->tunjangan_transport + $dt->tunjangan_masa_kerja;
        $potongan = $dt->Ketidakhadiran_Ammount + $dt->Standby_OnCall + $dt->PPH21 + $dt->BPJS_JHT + $dt->BPJS_Pensiun + $dt->BPJS_Kesehatan + $dt->Serikat + $dt->Koperasi + $dt->Lain_Lain + $dt->PPH21_Kurang_Bayar + $dt->union_fundraising;
        ?>
    <table style="width:100%;" cellspacing="0">
        <tr>
            <td class="bingkai" style="padding:7px;">
                <table style="width:100%;" cellspacing="0">
                    <tr>
                        <td style="width:120px;"><img src="{{ base_path() }}/public/gambar/logosai.png" style="width:100%;">
                        </td>
                        <td style="text-align:center;">
                            <b style="font-size:14px;">SLIP GAJI KARYAWAN</b><br>
                            <label>PT SUMMIT ADYAWINSA INDONESIA</label><br>
                            <label>Tahun {{$thn}}</label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <table style="width:100%;">
                                <tr>
                                    <td style="width:15%">&nbsp;</td>
                                    <td
                                        style="text-align:center; padding:5px;border:1px solid #C00;color:#F00;font-size:18px;">
                                        R A H A S I A</td>
                                    <td style="width:15%">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="height:5px;"></td>
        </tr>
        <tr>
            <td class="bingkai">
                <table style="width:100%;">
                    <tr>
                        <td style="width:170px;">PERIODE PEMBAYARAN</td>
                        <td style="text-align:center;width:5px;">:</td>
                        <td>{{$periode_text}}</td>
                    </tr>
                    <tr>
                        <td>DIBAYAR TANGGAL</td>
                        <td style="text-align:center;">:</td>
                        <td><?php    echo date('d F Y', strtotime($dt->Doc_Date));?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="bingkai">
                <table style="width:100%;">
                    <tr>
                        <td style="width:170px;">NAMA KARYAWAN</td>
                        <td style="text-align:center;width:5px;">:</td>
                        <td>{{$dt->Nama}}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td style="text-align:center;">:</td>
                        <td>{{$dt->NIK}}</td>
                    </tr>
                    <tr>
                        <td>SUB.DEPT./DEPT./DIV/DIREKTORAT</td>
                        <td style="text-align:center;">:</td>
                        <td>{{$dt->Bagian}}</td>
                    </tr>
                    <tr>
                        <td>JABATAN</td>
                        <td style="text-align:center;">:</td>
                        <td>{{$dt->Jabatan}}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="bingkai" style="padding:10px 5px;">
                <table style="width:100%;">
                    <tr>
                        <td style="width:170px;">PENDAPATAN</td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:5px;">I. KOMPONEN UPAH</td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;"> 1. Upah Pokok</td>
                        <td style="text-align:center;width:5px;">:</td>
                        <td style="text-align:right;width:45px;"><?php    echo number_format($dt->Upah_Pokok, 0);?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">2. Tunjangan Tetap</td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">2.1. Tunjangan Jabatan</td>
                        <td style="text-align:center;">:</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->Tunjangan_Jabatan, 0);?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">3. Tunjangan Tidak Tetap</td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">3.1. Tunjangan Prestasi Tahunan</td>
                        <td style="text-align:center;">:</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->Tunjangan_Prestasi, 0);?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">3.2. Tunjangan Skill</td>
                        <td style="text-align:center;">:</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->Tunjangan_Skill, 0);?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">3.3. Tunjangan Transport</td>
                        <td style="text-align:center;">:</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->tunjangan_transport, 0);?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">3.4. Tunjangan Shft</td>
                        <td style="text-align:center;">:</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->tunjangan_shift, 0);?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">3.5. Tunjangan Masa Kerja</td>
                        <td style="text-align:center;">:</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->tunjangan_masa_kerja, 0);?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">4. Rapel</td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">4.1. Rapel</td>
                        <td style="text-align:center;">:</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->Rapel, 0);?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">5. PPH21</td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">5.1. Insentif PPh21</td>
                        <td style="text-align:center;">:</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->PPH21_Insentif, 0);?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">5.2. PPh21 JAN <?php    $dulu = $thn - 1;
        echo $dulu;?> (Lebih Bayar)</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->PPH21_Lebih_Bayar, 0);?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:5px;">Total Pendapatan Kotor</td>
                        <td style="text-align:center;">:</td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align:right;padding-right:10px;"><?php    echo number_format($pendapatan, 0);?></td>
                    </tr>
                </table>
                <table style="width:100%;">
                    <tr>
                        <td style="width:170px;padding-left:5px;">II. POTONGAN</td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">1. Ketidakhadiran</td>
                        <td style="text-align:center;width:5px">:</td>
                        <td style="width:30px;">&nbsp;</td>
                        <td style="width:45px;text-align:right;"><?php    echo number_format($dt->Ketidakhadiran_Ammount, 0);?>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">2. Stanby OnCall (OFF)</td>
                        <td style="text-align:center;">:</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->Standby_OnCall, 0);?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">3. Pajak/PPh21</td>
                        <td style="text-align:center;">:</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->PPH21, 0);?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">4. BPJS KETENAGAKERJAAN</td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">- Jaminan Hari Tua (JHT)</td>
                        <td style="text-align:center;">:</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->BPJS_JHT, 0);?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:25px;">- Jaminan Pensiun (JP)</td>
                        <td style="text-align:center;">:</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->BPJS_Pensiun, 0);?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">5. BPJS KESEHATAN</td>
                        <td style="text-align:center;">:</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->BPJS_Kesehatan, 0);?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">6. Serikat</td>
                        <td style="text-align:center;">:</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->Serikat, 0);?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">7. Koperasi</td>
                        <td style="text-align:center;">:</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->Koperasi, 0);?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">8. Lain-Lain</td>
                        <td style="text-align:center;">:</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->Lain_Lain, 0);?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">9. Lebih Bayar Tunjangan Masa Kerja</td>
                        <td style="text-align:center;">:</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->union_fundraising, 0);?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">10. PPh21 JAN <?php    $dulu = $thn - 1;
        echo $dulu;?> (Kurang Bayar)</td>
                        <td style="text-align:center;">:</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><?php    echo number_format($dt->PPH21_Kurang_Bayar, 0);?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:5px;"><i>Sub Total Potongan</i></td>
                        <td style="text-align:center;">:</td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align:right;padding-right:10px;border-bottom:1px solid #000;">
                            <?php    echo number_format($potongan, 0);?></td>
                    </tr>
                    <tr>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:5px;"><b>Total Pendapatan Bersih diterima</b></td>
                        <td style="text-align:center;">:</td>
                        <td colspan="2">&nbsp;</td>
                        <td style="text-align:right;padding-right:10px;border-bottom:1px double #000;">
                            <b><?php    $nett = $pendapatan - $potongan;
        echo number_format($nett, 0);?></b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="bingkai" style="background: #000;color:#FFF;text-align:center;font-weight:bold;">LEGALISASI</td>
        </tr>
        <tr>
            <td class="bingkai">
                <table style="width:100%;">
                    <tr>
                        <td style="text-align:center;width:50%;">Dibayar oleh,<br><br><br><br>Payroll Adm</td>
                        <td style="text-align:center;width:50%;">Diterima oleh,<br><br><br><br>{{$dt->Nama}}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="bingkai" style="background:#F00;color:#FFF;text-align:center;font-weight:bold;">CATATAN PENTING</td>
        </tr>
        <tr>
            <td class="bingkai" style="text-align:center;color:#C00;font-weight:bold;">
                Data gaji dalam slip ini bersifat RAHASIA. Setiap karyawan WAJIB menyimpan dengan baik & seaman mungkin slip
                ini. DILARANG KERAS membuang slip ini dengan sembarangan. Pelanggaran terhadap ketentuan ini akan dikenai
                sanksi SP.
            </td>
        </tr>
    </table>
@endforeach

<script>
    $(function () {
        $('#table2').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': false,
            'info': false,
            "pageLength": 10,
            'autoWidth': true,
        })
    })
    $(function () {
        $('#table3').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': false,
            'info': true,
            "pageLength": 5,
            'autoWidth': false,
        })
    })
</script>