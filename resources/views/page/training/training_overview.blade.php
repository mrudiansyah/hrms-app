@extends('layouts/admin')
@section('Contents')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #tables_overview th {
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
            background-color: #2F4F4F;
            color: white;
            text-align: center;
            vertical-align: middle;
        }

        #tables_overview td {
            vertical-align: middle;
        }

        .month-cell {
            text-align: center;
            font-weight: bold;
            color: #0056b3;
            min-width: 45px;
        }
    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Training Overview
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <div class="box box-primary" style="background:#FFF;">
                        <div class="box-header with-border">
                            <i class="fa fa-calendar"></i>
                            <h3 class="box-title">Training Schedule Periode {{ $tahun }}</h3>
                            <div class="box-tools pull-right">
                                <a href="/Training/Overview/0" class="btn btn-primary btn-md"><i class="fa fa-calendar"></i>
                                    Anual Planning</a>
                                <a href="/TrainingGraph/Periode/0" class="btn btn-info btn-md"><i
                                        class="fa fa-bar-chart"></i> Summary</a>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-4 col-sm-6">
                                    <label for="filter_tahun">Pilih Tahun:</label>
                                    <select id="filter_tahun" class="form-control"
                                        style="width: 150px; display: inline-block; margin-left: 10px;">
                                        @foreach($available_years as $yr)
                                            <option value="{{ $yr }}" {{ (int) $yr == (int) $tahun ? 'selected' : '' }}>{{ $yr }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div style="overflow-x: auto;">
                                <table id="tables_overview" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:30px;">No</th>
                                            <th>Training</th>
                                            <th>Department</th>
                                            <th>Nama Level</th>
                                            <th>Trainer</th>
                                            <th>Trainee</th>
                                            <th>Jan</th>
                                            <th>Feb</th>
                                            <th>Mar</th>
                                            <th>Apr</th>
                                            <th>Mei</th>
                                            <th>Jun</th>
                                            <th>Jul</th>
                                            <th>Agu</th>
                                            <th>Sep</th>
                                            <th>Okt</th>
                                            <th>Nov</th>
                                            <th>Des</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 0; ?>
                                        @forelse($overview_data as $row)
                                            <?php    $no++; ?>
                                            <tr>
                                                <td style="text-align:center;">{{ $no }}</td>
                                                <td><strong>{{ $row['training_name'] }}</strong></td>
                                                <td>{{ $row['department'] }}</td>
                                                <td>{{ $row['nama_level'] }}</td>
                                                <td>{{ $row['nara_sumber'] }}</td>
                                                <td style="text-align:center;">{{ $row['draft_qty'] }}</td>
                                                @for($m = 1; $m <= 12; $m++)
                                                    <td class="month-cell">
                                                        @if(!empty($row['months'][$m]))
                                                            @foreach($row['months'][$m] as $tgl)
                                                                <a href="/Training/Plan/{{ $tgl['id'] }}" class="label label-primary"
                                                                    target="_blank"
                                                                    style="display:inline-block; margin: 1px; cursor:pointer;"
                                                                    title="Detail Training Schedule">{{ $tgl['day'] }}</a>
                                                            @endforeach
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endfor
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="18" style="text-align:center;">Tidak ada data training plan untuk
                                                    tahun {{ $tahun }}.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('Scripts')
    <script>
        $(document).ready(function () {
            var table = $('#tables_overview').DataTable({
                'paging': false,
                'lengthChange': true,
                'searching': true,
                'ordering': false,
                'info': true,
                'autoWidth': false,
                'pageLength': 25,
                'lengthMenu': [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
            });

            new $.fn.dataTable.Buttons(table, {
                buttons: ['copy', 'excel', 'print']
            });

            table.buttons(0, null).container().prependTo(
                table.table().container()
            );

            $('#filter_tahun').on('change', function () {
                var year = $(this).val();
                if (year) {
                    window.location.href = "/Training/Overview/" + year;
                }
            });
        });
    </script>
@endsection