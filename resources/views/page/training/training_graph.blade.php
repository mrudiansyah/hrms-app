@extends('layouts/admin')
@section('Contents')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <h1>
                <i class="fa fa-line-chart text-green"></i> Grafik Pencapaian Training
                <small>Plan vs Actual Participant & Average Test Scores per Training</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/Dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="/Training/Overview/0">Training</a></li>
                <li class="active">Grafik Pencapaian</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Filter Card -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Periode / Tahun</h3>
                            <div class="box-tools pull-right">
                                <a href="/Training/Overview/0" class="btn btn-primary btn-md"><i class="fa fa-calendar"></i>
                                    Anual Planning</a>
                                <a href="/TrainingGraph/Periode/0" class="btn btn-info btn-md"><i
                                        class="fa fa-bar-chart"></i> Summary</a>
                            </div>
                        </div>
                        <div class="box-body">
                            <form method="GET" id="filter-form" class="form-inline">
                                <div class="form-group">
                                    <label for="select-periode" style="margin-right: 10px;">Pilih Periode/Tahun:</label>
                                    <select id="select-periode" class="form-control input-sm" style="min-width: 180px;"
                                        onchange="changePeriode(this.value)">
                                        <option value="0" {{ $periode == '0' ? 'selected' : '' }}>Tahun Ini ({{ date('Y') }})
                                        </option>
                                        @foreach($available_periods as $pr)
                                            <option value="{{ $pr }}" {{ $periode == (string) $pr ? 'selected' : '' }}>
                                                {{ $pr }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ count($summary_table) }}</h3>
                            <p>Total Subjek Training</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-book"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{ array_sum($plan_data) }}</h3>
                            <p>Total Plan Participant</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{ array_sum($actual_data) }}</h3>
                            <p>Total Actual Participant</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <?php 
                                                    $valid_posts = array_filter($avg_post_data, function ($val) {
        return $val > 0;
    });
    $overall_avg = count($valid_posts) > 0 ? round(array_sum($valid_posts) / count($valid_posts), 1) : 0;
                                                ?>
                            <h3>{{ $overall_avg }}</h3>
                            <p>Rata-rata Post Test Score</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Chart 1: Plan vs Actual -->
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-bar-chart"></i> 1. Plan vs Actual Participant per Training
                            </h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="chart-container" style="position: relative; height:320px;">
                                <canvas id="planActualChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Average Score -->
                <div class="col-md-6">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-line-chart"></i> 2. Average Score Participant (Pre vs
                                Post)</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="chart-container" style="position: relative; height:320px;">
                                <canvas id="avgScoreChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Data Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-table"></i> Detail Data Pencapaian Training</h3>
                        </div>
                        <div class="box-body table-responsive">
                            <table id="table-summary" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr style="background-color: #2F4F4F; color: white;">
                                        <th style="width: 40px; text-align: center;">No</th>
                                        <th>Training Name</th>
                                        <th>Category / Type</th>
                                        <th style="text-align: center;">Plan Qty</th>
                                        <th style="text-align: center;">Actual Qty</th>
                                        <th style="text-align: center;">Achievement (%)</th>
                                        <th style="text-align: center;">Avg Free Test</th>
                                        <th style="text-align: center;">Avg Post Test</th>
                                        <th style="text-align: center;">Gain Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 0; ?>
                                    @foreach($summary_table as $row)
                                        <?php    $no++; ?>
                                        <tr>
                                            <td style="text-align: center;">{{ $no }}</td>
                                            <td><b>{{ $row['training_name'] }}</b></td>
                                            <td><span class="label label-info">{{ $row['skill_type'] }}</span></td>
                                            <td style="text-align: center;">{{ $row['plan_qty'] }}</td>
                                            <td style="text-align: center;">{{ $row['actual_qty'] }}</td>
                                            <td style="text-align: center;">
                                                <span
                                                    class="badge {{ $row['achievement_pct'] >= 100 ? 'bg-green' : ($row['achievement_pct'] >= 50 ? 'bg-yellow' : 'bg-red') }}">
                                                    {{ $row['achievement_pct'] }}%
                                                </span>
                                            </td>
                                            <td style="text-align: center;">{{ $row['avg_free'] }}</td>
                                            <td style="text-align: center;"><b>{{ $row['avg_post'] }}</b></td>
                                            <td style="text-align: center;">
                                                <span class="text-{{ $row['score_gain'] >= 0 ? 'green' : 'red' }}">
                                                    {{ $row['score_gain'] > 0 ? '+' . $row['score_gain'] : $row['score_gain'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('Scripts')
    <!-- Chart.js inclusion -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script>
        function changePeriode(val) {
            window.location.href = '/TrainingGraph/Periode/' + val;
        }

        $(document).ready(function () {
            $('#table-summary').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 10
            });

            var labels = {!! json_encode($chart_labels) !!};
            var planData = {!! json_encode($plan_data) !!};
            var actualData = {!! json_encode($actual_data) !!};
            var avgFreeData = {!! json_encode($avg_free_data) !!};
            var avgPostData = {!! json_encode($avg_post_data) !!};

            // Chart 1: Plan vs Actual (Grouped Bar Chart)
            var ctx1 = document.getElementById('planActualChart').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Plan Participant',
                            backgroundColor: 'rgba(243, 156, 18, 0.85)',
                            borderColor: 'rgba(243, 156, 18, 1)',
                            borderWidth: 1,
                            data: planData
                        },
                        {
                            label: 'Actual Participant',
                            backgroundColor: 'rgba(0, 166, 90, 0.85)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1,
                            data: actualData
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { position: 'top' },
                    scales: {
                        yAxes: [{
                            ticks: { beginAtZero: true, stepSize: 1 }
                        }],
                        xAxes: [{
                            ticks: {
                                callback: function (value) {
                                    return value.length > 15 ? value.substr(0, 15) + '...' : value;
                                }
                            }
                        }]
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    }
                }
            });

            // Chart 2: Average Score (Free vs Post Test)
            var ctx2 = document.getElementById('avgScoreChart').getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Avg Free Test (Pre)',
                            backgroundColor: 'rgba(60, 141, 188, 0.85)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1,
                            data: avgFreeData
                        },
                        {
                            label: 'Avg Post Test (Post)',
                            backgroundColor: 'rgba(96, 92, 168, 0.85)',
                            borderColor: 'rgba(96, 92, 168, 1)',
                            borderWidth: 1,
                            data: avgPostData
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { position: 'top' },
                    scales: {
                        yAxes: [{
                            ticks: { beginAtZero: true, max: 100 }
                        }],
                        xAxes: [{
                            ticks: {
                                callback: function (value) {
                                    return value.length > 15 ? value.substr(0, 15) + '...' : value;
                                }
                            }
                        }]
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    }
                }
            });
        });
    </script>
@endsection