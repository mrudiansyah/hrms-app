@extends('layouts/admin')

@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Functionality Test <small>KSK Approval Reminder</small></h1>
            <ol class="breadcrumb">
                <li><a href="/Dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Functionality Test</li>
            </ol>
        </section>

        <section class="content">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Daftar approval KSK yang menunggu</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. KSK</th>
                                <th>Periode</th>
                                <th>Deadline</th>
                                <th>Employee Name</th>
                                <th>Position Name</th>
                                <th>Nomor Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $index => $record)
                                @php($employee = $employees->get($record->approver_id))
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $record->no_ksk }}</td>
                                    <td>{{ $record->periode }}</td>
                                    <td>{{ $record->dead_line }}</td>
                                    <td>{{ $employee->employee_name ?? '-' }}</td>
                                    <td>{{ $employee->position_name ?? '-' }}</td>
                                    <td>{{ $employee->nomor_telepon ?? '-' }}</td>
                                    <td>
                                        @if (!empty($employee->nomor_telepon))
                                            <form action="{{ url('/functionality-test/' . $record->id . '/send-reminder') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="Kirim reminder WhatsApp">
                                                    <i class="fa fa-send"></i> Kirim
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-default btn-sm" disabled>Nomor kosong</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada approval KSK yang menunggu pada periode belum terkunci.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection