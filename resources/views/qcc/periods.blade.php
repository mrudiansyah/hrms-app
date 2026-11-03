@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Periode</small></h1>
    </section>
    <section class="content">
        @include('qcc._navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        @if($isCommittee)
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Tambah Periode QCC</h3>
                </div>
                    <form action="{{ url('/qcc_perriode') }}" method="POST">
                        @csrf
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sugested_thema">Tema QCC</label>
                                        <input class="form-control" id="sugested_thema" name="sugested_thema"
                                            value="{{ old('sugested_thema') }}" maxlength="255" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="periode_start">Periode Start</label>
                                        <input class="form-control" type="date" id="periode_start" name="periode_start"
                                            value="{{ old('periode_start') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="periode_finish">Periode Finish</label>
                                        <input class="form-control" type="date" id="periode_finish" name="periode_finish"
                                            value="{{ old('periode_finish') }}" required>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted">
                                Saat periode dibuat, sistem membuat 8 schedule berurutan dengan durasi 2/1/3/3/6/3/2/1 minggu.
                                Jadwal yang melewati periode finish dibatasi ke tanggal finish; step sisanya ditempatkan pada tanggal itu.
                            </p>
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-plus"></i> Buat Periode dan Schedule</button>
                            <a class="btn btn-default" href="{{ url('/qcc_step') }}">Atur Master Steps</a>
                        </div>
                    </form>
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Daftar Periode</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-gray">
                            <th>Tema</th>
                            <th style="width:130px;">Start</th>
                            <th style="width:130px;">Finish</th>
                            <th style="width:100px;">Schedule</th>
                            <th style="width:320px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periods as $period)
                            @php($formId = 'update-period-' . $period->id)
                            <tr>
                                <td>
                                    @if($isCommittee)
                                        <input class="form-control" name="sugested_thema" form="{{ $formId }}"
                                            value="{{ $period->sugested_thema }}" maxlength="255" required>
                                    @else
                                        {{ $period->sugested_thema }}
                                    @endif
                                </td>
                                <td>
                                    @if($isCommittee)
                                        <input class="form-control" type="date" name="periode_start" form="{{ $formId }}"
                                            value="{{ \Illuminate\Support\Carbon::parse($period->periode_start)->format('Y-m-d') }}" required>
                                    @else
                                        {{ \Illuminate\Support\Carbon::parse($period->periode_start)->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if($isCommittee)
                                        <input class="form-control" type="date" name="periode_finish" form="{{ $formId }}"
                                            value="{{ \Illuminate\Support\Carbon::parse($period->periode_finish)->format('Y-m-d') }}" required>
                                    @else
                                        {{ \Illuminate\Support\Carbon::parse($period->periode_finish)->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>{{ $scheduleCounts[$period->id] ?? 0 }} / 8</td>
                                <td>
                                    @if($isCommittee)
                                        <form id="{{ $formId }}" action="{{ url('/qcc_perriode/' . $period->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-primary btn-sm" type="submit" title="Simpan perubahan">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </form>
                                        <form action="{{ url('/qcc_perriode/' . $period->id . '/toggle-lock') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button class="btn {{ $period->isLocked ? 'btn-warning' : 'btn-default' }} btn-sm" type="submit"
                                                title="{{ $period->isLocked ? 'Buka lock periode' : 'Lock periode' }}"
                                                aria-label="{{ $period->isLocked ? 'Buka lock periode' : 'Lock periode' }}">
                                                <i class="fa {{ $period->isLocked ? 'fa-unlock' : 'fa-lock' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a class="btn btn-info btn-sm"
                                        href="{{ url('/qcc_schedule?periode_id=' . $period->id) }}">
                                        <i class="fa fa-calendar"></i> Schedule
                                    </a>
                                    @if($isCommittee)
                                        <form action="{{ url('/qcc_perriode/' . $period->id) }}" method="POST" style="display:inline;"
                                            onsubmit="return confirm('Hapus periode ini beserta semua schedule?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit" title="Hapus periode">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">Belum ada periode QCC.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
