@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Schedule</small></h1>
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
                                        <th rowspan="2" class="qcc-fixed">ID Step</th>
                                        <th rowspan="2" class="qcc-fixed qcc-description">Step Description</th>
                                        <th rowspan="2" class="qcc-fixed qcc-date">Start</th>
                                        <th rowspan="2" class="qcc-fixed qcc-date">Finish</th>
                                        <th rowspan="2" class="qcc-action">Aksi</th>
                                        @foreach($monthGroups as $month)
                                            <th colspan="{{ $month['count'] }}" class="text-center">
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
                                                @if($isCommittee)
                                                    <input class="form-control input-sm" type="date" name="plan_start"
                                                        form="{{ $formId }}" value="{{ $planStart }}" required>
                                                @else
                                                    {{ \Illuminate\Support\Carbon::parse($planStart)->format('d/m/Y') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($isCommittee)
                                                    <input class="form-control input-sm" type="date" name="plan_finish"
                                                        form="{{ $formId }}" value="{{ $planFinish }}" required>
                                                @else
                                                    {{ \Illuminate\Support\Carbon::parse($planFinish)->format('d/m/Y') }}
                                                @endif
                                            </td>
                                            <td class="qcc-action">
                                                @if($isCommittee)
                                                    <form id="{{ $formId }}" action="{{ url('/qcc_schedule/' . $schedule->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button class="btn btn-primary btn-xs" type="submit" title="Simpan tanggal">
                                                            <i class="fa fa-save"></i>
                                                        </button>
                                                        <input type="hidden" name="periode_id" value="{{ $selectedPeriod->id }}">
                                                    </form>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            @foreach($weeks as $week)
                                                @php($active = $planStart <= $week['finish'] && $planFinish >= $week['start'])
                                                @php($previousActive = !$loop->first && $planStart <= $weeks[$loop->index - 1]['finish'] && $planFinish >= $weeks[$loop->index - 1]['start'])
                                                @php($nextActive = !$loop->last && $planStart <= $weeks[$loop->index + 1]['finish'] && $planFinish >= $weeks[$loop->index + 1]['start'])
                                                <td class="qcc-week text-center"
                                                    title="{{ $active ? $schedule->step_description : '' }}">
                                                    @if($active)
                                                        <span class="qcc-week-bar {{ $previousActive ? '' : 'qcc-week-bar-start' }} {{ $nextActive ? '' : 'qcc-week-bar-end' }}"
                                                            role="img" aria-label="Schedule aktif"></span>
                                                    @endif
                                                </td>
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
    .qcc-timeline {
        overflow-x: auto;
        border: 1px solid #b8c9dc;
        border-radius: 4px;
    }
    .qcc-timeline table {
        min-width: max-content;
        margin-bottom: 0;
        background: #fff;
    }
    .qcc-timeline th, .qcc-timeline td { vertical-align: middle !important; }
    .qcc-timeline .table-bordered { border-color: #d4dfeb; }
    .qcc-timeline .table-bordered > thead > tr > th {
        color: #24415f !important;
        border-color: #c5d6e8 !important;
        background-color: #e8f1fa !important;
        background-image: linear-gradient(180deg, #f3f8fd 0%, #e2edf8 100%) !important;
        text-shadow: none;
    }
    .qcc-fixed { min-width: 90px; }
    .qcc-description { min-width: 220px; }
    .qcc-date { min-width: 140px; }
    .qcc-week { min-width: 48px; }
    .qcc-timeline .table-bordered > thead > tr > th.qcc-month-label {
        color: #214f5b !important;
        border-color: #c1dcdf !important;
        background-color: #e3f1f2 !important;
        background-image: linear-gradient(180deg, #f0f8f8 0%, #dceeee 100%) !important;
    }
    .qcc-timeline .table-bordered > tbody > tr > td { border-color: #d7e2ee; }
    .qcc-timeline .table-bordered > tbody > tr:hover > td { background-color: #f3f8fc; }
    .qcc-week-bar { position: relative; z-index: 1; display: block; width: calc(100% + 16px); height: 8px; margin-left: -8px; background-color: #4b83bd; }
    .qcc-week-bar-start { border-top-left-radius: 4px; border-bottom-left-radius: 4px; }
    .qcc-week-bar-end { border-top-right-radius: 4px; border-bottom-right-radius: 4px; }
    .qcc-action { min-width: 58px; text-align: center; }
</style>
@endsection
