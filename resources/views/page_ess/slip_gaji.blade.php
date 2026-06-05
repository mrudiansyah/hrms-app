@extends('layouts/home')
@section('Contents')
    <section class="content">
        <div class="row">
            <div class="col-xs-12 col-md-6 col-lg-6">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="box-header with-border">
                        <h3 class="box-title">Salary Slip</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body" style="overflow: auto;">
                        <table id="polos" class="table table-bordered" style="min-width:100%;">
                            <thead>
                                <tr>
                                    <th>PERIODE</th>
                                    <th>EARN</th>
                                    <th>DEDUCTION</th>
                                    <th>RECEIVED</th>
                                    <th style="width:30px;">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 0;?>
                                @foreach($tb1 as $dt)
                                                            <?php
                                    $pendapatan = $dt->upah_pokok + $dt->tunjangan_jabatan + $dt->tunjangan_prestasi_tahunan + $dt->tunjangan_skill + $dt->rapel + $dt->insentif_pph21 + $dt->pph21_lebih_bayar + $dt->tunjangan_shift + $dt->tunjangan_transport;
                                    $potongan = $dt->ketidakhadiran + $dt->standby_oncall + $dt->pph21_ter + $dt->jaminan_hari_tua + $dt->jaminan_pensiun + $dt->bpjs_kesehatan + $dt->serikat + $dt->koperasi + $dt->lain_lain + $dt->pph21_kurang_bayar;
                                    $receive = $pendapatan - $potongan;
                                                                                                                                                ?>
                                                            <tr>
                                                                <td>{{$dt->periode}}</td>
                                                                <td style="text-align:right;"><?php    echo number_format($pendapatan, 0);?></td>
                                                                <td style="text-align:right;"><?php    echo number_format($potongan, 0);?></td>
                                                                <td style="text-align:right;"><b
                                                                        id="netto{{$dt->id}}"><?php    echo number_format($receive, 0);?></b></td>
                                                                <td>
                                                                    <a href="/Slip/{{$dt->periode}}/{{$dt->id_employee}}" target="_blank"><button
                                                                            type="button" class="btn btn-primary btn-xs"><i
                                                                                class="fa fa-print"></i></button></a>

                                                                </td>
                                                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>

            </div>
            <div class="col-xs-12 col-md-6 col-lg-6">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="box-header with-border">
                        <h3 class="box-title">Previous Slip</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body" style="overflow: auto;">
                        <table id="polos" class="table table-bordered" style="min-width:100%;">
                            <thead>
                                <tr>
                                    <th>PERIODE</th>
                                    <th>EARN</th>
                                    <th>DEDUCTION</th>
                                    <th>RECEIVED</th>
                                    <th style="width:30px;">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 0;?>
                                @foreach($tb_salary_excel as $dt)
                                                            <?php
                                    $pendapatan = $dt->Upah_Pokok + $dt->Tunjangan_Jabatan + $dt->Tunjangan_Prestasi + $dt->Tunjangan_Skill + $dt->Rapel + $dt->PPH21_Insentif + $dt->PPH21_Lebih_Bayar + $dt->tunjangan_shift + $dt->tunjangan_transport + $dt->tunjangan_masa_kerja;
                                    $potongan = $dt->Ketidakhadiran_Ammount + $dt->Standby_OnCall + $dt->PPH21 + $dt->BPJS_JHT + $dt->BPJS_Pensiun + $dt->BPJS_Kesehatan + $dt->Serikat + $dt->Koperasi + $dt->Lain_Lain + $dt->PPH21_Kurang_Bayar;
                                                                                                                                                ?>
                                                            <tr>
                                                                <td>{{$dt->Periode}}</td>
                                                                <td style="text-align:right;"><?php    echo number_format($pendapatan, 0);?></td>
                                                                <td style="text-align:right;"><?php    echo number_format($potongan, 0);?></td>
                                                                <td style="text-align:right;"><b id="netto{{$dt->id}}"><?php    $netto = $pendapatan - $potongan;
                                    echo number_format($netto, 0);?></b>
                                                                </td>
                                                                <td>
                                                                    <a href="/SlipGaji/Temp/{{$dt->Periode}}/{{$dt->NIK}}" target="_blank"><button
                                                                            type="button" class="btn btn-primary btn-xs"><i
                                                                                class="fa fa-print"></i></button></a>

                                                                </td>
                                                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>

            </div>
        </div>
    </section>
@endsection