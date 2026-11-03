@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Activity</small></h1>
    </section>
    <section class="content">
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode QCC</h3>
            </div>
            <div class="box-body">
                <form action="{{ url('/qcc_activity') }}" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="activity_periode_id">Periode</label>
                        <select class="form-control" id="activity_periode_id" name="periode_id" required>
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

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-tasks text-blue"></i> Team QCC
                    @if($selectedPeriod)
                        <small>
                            {{ \Illuminate\Support\Carbon::parse($selectedPeriod->periode_start)->format('d M Y') }}
                            – {{ \Illuminate\Support\Carbon::parse($selectedPeriod->periode_finish)->format('d M Y') }}
                        </small>
                    @endif
                </h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-gray">
                            <th>Team</th>
                            <th>Theme of Periode</th>
                            <th>Theme of Team</th>
                            <th style="width:90px;" class="text-center">Anggota</th>
                            <th style="width:180px;">Progress</th>
                            <th style="width:120px;" class="text-center">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamPeriods as $teamPeriod)
                            <tr>
                                <td><strong>{{ $teamPeriod->group_name }}</strong></td>
                                <td>{{ $teamPeriod->sugested_thema }}</td>
                                <td>
                                    {{ $teamPeriod->theme_team ?: '-' }}
                                    <div class="pull-right">
                                        <button class="btn btn-warning btn-xs" type="button" title="Edit tema team"
                                            data-toggle="modal" data-target="#edit-team-theme-{{ $teamPeriod->id }}">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">{{ $teamPeriod->member_count }}</td>
                                <td>
                                    @php($progressPercent = (int) round(($teamPeriod->progress_step / 8) * 100))
                                    <div>Step {{ $teamPeriod->progress_step }} / 8</div>
                                    <div class="progress" style="height:10px; margin:5px 0 0;">
                                        <div class="progress-bar progress-bar-success" role="progressbar"
                                            aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"
                                            style="width:{{ $progressPercent }}%;"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <form action="{{ url('/qcc_activity/'.$teamPeriod->id.'/open') }}" method="POST">
                                        @csrf
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fa fa-folder"></i> Show Detail
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted" style="padding:30px;">
                                    <i class="fa fa-tasks fa-2x"></i>
                                    <p style="margin:10px 0 0;">
                                        {{ !$selectedPeriod ? 'Belum ada periode QCC.' : ($isCommittee ? 'Belum ada team yang terdaftar pada periode QCC.' : 'Anda belum terdaftar sebagai anggota team QCC pada periode ini.') }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
              </div>
              @if($isCommittee)
                  @foreach($teamPeriods as $teamPeriod)
                      <div class="modal fade" id="edit-team-theme-{{ $teamPeriod->id }}" tabindex="-1" role="dialog"
                          aria-labelledby="edit-team-theme-title-{{ $teamPeriod->id }}">
                          <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                  <form action="{{ url('/qcc_activity/'.$teamPeriod->id.'/theme') }}" method="POST">
                                      @csrf
                                      @method('PUT')
                                      <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                          </button>
                                          <h4 class="modal-title" id="edit-team-theme-title-{{ $teamPeriod->id }}">
                                              Edit Tema Team {{ $teamPeriod->group_name }}
                                          </h4>
                                      </div>
                                      <div class="modal-body">
                                          <div class="form-group">
                                              <label for="theme-team-{{ $teamPeriod->id }}">Tema Team</label>
                                              <input class="form-control" id="theme-team-{{ $teamPeriod->id }}"
                                                  name="theme_team" value="{{ $teamPeriod->theme_team }}"
                                                  maxlength="255" required>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                          <button type="submit" class="btn btn-primary">
                                              <i class="fa fa-save"></i> Simpan
                                          </button>
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>
                  @endforeach
              @endif
            </div>
        </div>
    </section>
</div>
@endsection
