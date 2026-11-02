@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Periode Team</small></h1>
    </section>
    <section class="content">
        @include('qcc._team_navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode QCC</h3>
            </div>
            <div class="box-body">
                <form action="{{ url('/qcc_team/periode') }}" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="periode_id">Periode</label>
                        <select class="form-control" id="periode_id" name="periode_id" required>
                            <option value="">Pilih periode QCC</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" @selected($selectedPeriod && (int) $selectedPeriod->id === (int) $period->id)>
                                    {{ $period->sugested_thema }}
                                    ({{ \Illuminate\Support\Carbon::parse($period->periode_start)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($period->periode_finish)->format('d/m/Y') }})
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
                    <h3 class="box-title">Daftar Team untuk: {{ $selectedPeriod->sugested_thema }}</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="bg-gray">
                                <th style="width:70px;" class="text-center">Terdaftar</th>
                                <th>Nama Team</th>
                                <th style="width:180px;">Tanggal Registrasi</th>
                                <th style="width:120px;" class="text-center">Anggota</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teams as $team)
                                @php($teamPeriod = $teamPeriods->get($team->id))
                                @php($detailsId = 'team-members-' . $team->id)
                                <tr>
                                    <td class="text-center">
                                        <form action="{{ url('/qcc_team_periode/toggle') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id_qcc_periode" value="{{ $selectedPeriod->id }}">
                                            <input type="hidden" name="id_qcc_team" value="{{ $team->id }}">
                                            <input type="hidden" name="registered" value="{{ $teamPeriod ? 0 : 1 }}">
                                            <input type="checkbox" aria-label="Daftarkan {{ $team->group_name }} pada periode ini"
                                                @checked($teamPeriod)
                                                onchange="if (!this.checked && !confirm('Hapus team beserta semua anggotanya dari periode ini?')) { this.checked = true; return; } this.form.elements.registered.value = this.checked ? 1 : 0; this.form.submit();">
                                        </form>
                                    </td>
                                    <td>{{ $team->group_name }}</td>
                                    <td>{{ $team->registered_at ? \Illuminate\Support\Carbon::parse($team->registered_at)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-default btn-xs" type="button" data-toggle="modal" data-target="#{{ $detailsId }}">
                                            <i class="fa fa-users"></i> Lihat Anggota
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada team QCC.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @foreach($teams as $team)
                @php($teamPeriod = $teamPeriods->get($team->id))
                @php($detailsId = 'team-members-' . $team->id)
                <div class="modal fade" id="{{ $detailsId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $detailsId }}-title">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="{{ $detailsId }}-title">
                                    Anggota Team {{ $team->group_name }} - {{ $selectedPeriod->sugested_thema }}
                                </h4>
                            </div>
                            <div class="modal-body">
                                @if(!$teamPeriod)
                                    <p class="text-muted">Centang team terlebih dahulu untuk mendaftarkan team pada periode ini dan mengelola anggotanya.</p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Karyawan</th>
                                                    <th>Posisi Team</th>
                                                    <th style="width:145px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($members->get($teamPeriod->id, collect()) as $member)
                                                    <tr>
                                                        <td>{{ $member->NIK }} - {{ $member->employee_name ?? 'Karyawan tidak ditemukan' }}</td>
                                                        <td>{{ $member->position_name ?? '-' }}</td>
                                                        <td>
                                                            <form action="{{ url('/qcc_team_member/'.$member->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-danger btn-xs" type="submit" onclick="return confirm('Hapus anggota ini?');"><i class="fa fa-trash"></i> Hapus</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @php($addMemberFormId = 'add-member-' . $teamPeriod->id)
                                                <tr>
                                                    <td>
                                                        <form id="{{ $addMemberFormId }}" action="{{ url('/qcc_team_periode/'.$teamPeriod->id.'/member') }}" method="POST">
                                                            @csrf
                                                        </form>
                                                        <select class="form-control" name="id_employee" form="{{ $addMemberFormId }}" aria-label="Pilih karyawan" required>
                                                            <option value="">Pilih karyawan</option>
                                                            @foreach($employees as $employee)
                                                                <option value="{{ $employee->id }}">{{ $employee->NIK }} - {{ $employee->employee_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control" name="id_position_team" form="{{ $addMemberFormId }}" aria-label="Pilih posisi team" required>
                                                            <option value="">Pilih posisi team</option>
                                                            @foreach($positions as $position)
                                                                <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-success btn-sm" type="submit" form="{{ $addMemberFormId }}">
                                                            <i class="fa fa-plus"></i> Tambah
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </section>
</div>
@endsection
