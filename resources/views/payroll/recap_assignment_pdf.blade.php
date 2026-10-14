@extends('layouts/pdf')
@section('Contents')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-body">

                            <div style="margin-bottom: 15px;">
                                <h4 style="margin: 0; font-weight: bold;">RECAPITULATION INCENTIVE PAYMENT PT. SUMMIT ADYAWINSA INDONESIA</h4>
                                <h5 style="margin: 5px 0; font-weight: bold;">LEVEL SECTION HEAD & DEPARTMENT HEAD</h5>
                                <h5 style="margin: 0; font-weight: bold;">PERIOD {{ $periode_name }}</h5>
                            </div>

                            <table class="table table-bordered" style="border: 2px solid #000; width: 50%; float: left;">
                                <thead style="border-bottom: 3px double #000;">
                                    <tr>
                                        <th style="text-align: center; border-right: 2px solid #000;">DEPARTMENT</th>
                                        <th style="text-align: center; width: 150px;">NOMINAL INCENTIVE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total_incentive = 0; @endphp
                                    @foreach($tb_rekap as $dt)
                                        @php $total_incentive += $dt->nominal_incentive; @endphp
                                        <tr>
                                            <td style="border-right: 2px solid #000;">{{ $dt->department != '' ? $dt->department : ($dt->dept_code != '' ? $dt->dept_code : '-') }}</td>
                                            <td style="text-align: right;">{{ number_format($dt->nominal_incentive, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="border-top: 3px double #000; font-weight: bold; background: #eee;">
                                    <tr>
                                        <td style="border-right: 2px solid #000;"><b>SUB TOTAL</b></td>
                                        <td style="text-align: right;"><b>{{ number_format($total_incentive, 0) }}</b></td>
                                    </tr>
                                    <tr style="background:#000; color:#fff;">
                                        <td style="border-right: 2px solid #000; text-align:center;"><b>TOTAL</b></td>
                                        <td style="text-align: right;"><b>{{ number_format($total_incentive, 0) }}</b></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div style="clear: both;"></div>
                            <br><br>

                            <div style="font-size:12px;">
                                <table border="1" cellspacing="0" cellpadding="5" align="center" style="width: 100%;">
                                    <tr>
                                        <td colspan="4" style="text-align:center;">Approved by</td>
                                        <td style="text-align:center;">Verified by</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">&nbsp;</td>
                                        <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">&nbsp;</td>
                                        <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">&nbsp;</td>
                                        <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">&nbsp;</td>
                                        <td style="text-align:center;vertical-align:middle;height:70px;" class="relative">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;vertical-align:bottom;">RISTIYONO</td>
                                        <td style="text-align:center;vertical-align:bottom;">ARIF BASUKI</td>
                                        <td style="text-align:center;vertical-align:bottom;">EKA NOVIANTO NUGROHO</td>
                                        <td style="text-align:center;vertical-align:bottom;">PORNTEP KLINSRISUK</td>
                                        <td style="text-align:center;vertical-align:bottom;">YUPAWAN CHARUARAYANANT</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">HRGA DEPT. HEAD</td>
                                        <td style="text-align:center;">ASST GENERAL MANAGER</td>
                                        <td style="text-align:center;">VICE PRESIDENT DIRECTOR</td>
                                        <td style="text-align:center;">PRESIDENT DIRECTOR</td>
                                        <td style="text-align:center;">FA DIRECTOR</td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('Scripts')
@endsection
