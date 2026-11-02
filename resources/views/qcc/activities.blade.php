@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Activity</small></h1>
    </section>
    <section class="content">
        @include('qcc._navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-tasks text-blue"></i> Team QCC</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-gray">
                            <th>Team</th>
                            <th>Tema Periode</th>
                            <th style="width:220px;">Tanggal Periode</th>
                            <th style="width:90px;" class="text-center">Anggota</th>
                            <th style="width:150px;" class="text-center">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamPeriods as $teamPeriod)
                            <tr>
                                <td><strong>{{ $teamPeriod->group_name }}</strong></td>
                                <td>{{ $teamPeriod->sugested_thema }}</td>
                                <td>
                                    {{ \Illuminate\Support\Carbon::parse($teamPeriod->periode_start)->format('d/m/Y') }}
                                    – {{ \Illuminate\Support\Carbon::parse($teamPeriod->periode_finish)->format('d/m/Y') }}
                                </td>
                                <td class="text-center">{{ $teamPeriod->member_count }}</td>
                                <td class="text-center">
                                    <form action="{{ url('/qcc_activity/'.$teamPeriod->id.'/open') }}" method="POST">
                                        @csrf
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fa fa-eye"></i> Show Activity
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted" style="padding:30px;">
                                    <i class="fa fa-tasks fa-2x"></i>
                                    <p style="margin:10px 0 0;">
                                        {{ $isCommittee ? 'Belum ada team yang terdaftar pada periode QCC.' : 'Anda belum terdaftar sebagai anggota team QCC.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
