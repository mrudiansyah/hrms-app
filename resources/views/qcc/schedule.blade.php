@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC Management <small>Schedule</small></h1>
    </section>
    <section class="content">
        @include('qcc._navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode</h3>
            </div>
            <div class="box-body">
                <form action="{{ url('/qcc_schedule') }}" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="periode_id">Periode QCC</label>
                        <select class="form-control" id="periode_id" name="periode_id" required>
                            <option value="">Pilih tema QCC</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}"
                                    {{ optional($selectedPeriod)->id == $period->id ? 'selected' : '' }}>
                                    {{ $period->sugested_thema }} ({{ \Illuminate\Support\Carbon::parse($period->periode_start)->format('d M Y') }}
                                    – {{ \Illuminate\Support\Carbon::parse($period->periode_finish)->format('d M Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
                </form>
            </div>
        </div>

        @if($selectedPeriod)
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $selectedPeriod->sugested_thema }}</h3>
                    <p class="text-muted">
                        {{ \Illuminate\Support\Carbon::parse($selectedPeriod->periode_start)->format('d M Y') }}
                        – {{ \Illuminate\Support\Carbon::parse($selectedPeriod->periode_finish)->format('d M Y') }}
                        · W1 dimulai dari tanggal awal periode. Ubah tanggal start/finish untuk mengatur warna minggu; overlap antar-step diperbolehkan.
                    </p>
                </div>
                <div class="box-body">
                    @if($schedules->isEmpty())
                        <div class="alert alert-warning">
                            Belum ada schedule untuk periode ini. Pastikan Master Steps 1–8 sudah terisi, lalu buat ulang periode.
                        </div>
                    @else
                        <div class="table-responsive qcc-timeline">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th rowspan="3" class="qcc-fixed">ID Step</th>
                                        <th rowspan="3" class="qcc-fixed qcc-description">Step Description</th>
                                        <th rowspan="3" class="qcc-fixed qcc-date">Start</th>
                                        <th rowspan="3" class="qcc-fixed qcc-date">Finish</th>
                                        <th rowspan="3" class="qcc-action">Aksi</th>
                                        <th colspan="{{ count($weeks) }}" class="text-center qcc-month-label">Month</th>
                                    </tr>
                                    <tr>
                                        @foreach($monthGroups as $month)
                                            <th colspan="{{ $month['count'] }}" class="text-center qcc-month-label">
                                                {{ $month['label'] }}
                                            </th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach($weeks as $week)
                                            <th class="text-center qcc-week" title="{{ $week['start'] }} – {{ $week['finish'] }}">
                                                W{{ $week['number'] }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                        @php($formId = 'update-schedule-' . $schedule->id)
                                        @php($planStart = \Illuminate\Support\Carbon::parse($schedule->plan_start)->toDateString())
                                        @php($planFinish = \Illuminate\Support\Carbon::parse($schedule->plan_finish)->toDateString())
                                        <tr>
                                            <td class="text-center">{{ $schedule->step_sequance }}</td>
                                            <td class="qcc-description">{{ $schedule->step_description }}</td>
                                            <td>
                                                <input class="form-control input-sm" type="date" name="plan_start"
                                                    form="{{ $formId }}" value="{{ $planStart }}" required>
                                            </td>
                                            <td>
                                                <input class="form-control input-sm" type="date" name="plan_finish"
                                                    form="{{ $formId }}" value="{{ $planFinish }}" required>
                                            </td>
                                            <td class="qcc-action">
                                                <form id="{{ $formId }}" action="{{ url('/qcc_schedule/' . $schedule->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button class="btn btn-primary btn-xs" type="submit" title="Simpan tanggal">
                                                        <i class="fa fa-save"></i>
                                                    </button>
                                                    <input type="hidden" name="periode_id" value="{{ $selectedPeriod->id }}">
                                                </form>
                                            </td>
                                            @foreach($weeks as $week)
                                                @php($active = $planStart <= $week['finish'] && $planFinish >= $week['start'])
                                                <td class="qcc-week {{ $active ? 'qcc-active' : '' }}"
                                                    title="{{ $active ? $schedule->step_description : '' }}"></td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </section>
</div>
<style>
    .qcc-navigation .btn { margin-right: 6px; }
    .qcc-timeline { overflow-x: auto; }
    .qcc-timeline table { min-width: max-content; }
    .qcc-timeline th, .qcc-timeline td { vertical-align: middle !important; }
    .qcc-fixed { min-width: 90px; }
    .qcc-description { min-width: 220px; }
    .qcc-date { min-width: 140px; }
    .qcc-week { min-width: 48px; }
    .qcc-month-label { min-width: 48px; background: #e8eef5; }
    .qcc-active { background-color: #49a56a !important; }
    .qcc-action { min-width: 58px; text-align: center; }
</style>
@endsection
