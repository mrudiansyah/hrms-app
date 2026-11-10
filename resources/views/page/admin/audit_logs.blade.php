@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Audit Log <small>Riwayat perubahan profil karyawan</small></h1>
    </section>

    <section class="content">
        @php($activeFilterCount = count(array_filter($filters, fn ($value, $key) => $value !== null && $value !== '' && !($defaultDateRange && in_array($key, ['date_from', 'date_to'], true)), ARRAY_FILTER_USE_BOTH)))
        <div class="audit-toolbar">
            <div>
                <div class="audit-count">{{ number_format($logs->total()) }} <span>log tercatat</span></div>
                <div class="audit-filter-summary">
                    @if($defaultDateRange)
                        Menampilkan aktivitas 1 bulan terakhir
                        @if($activeFilterCount)
                            · {{ $activeFilterCount }} filter lainnya aktif
                        @endif
                    @elseif($activeFilterCount)
                        {{ $activeFilterCount }} filter aktif
                    @else
                        Menampilkan seluruh aktivitas
                    @endif
                </div>
            </div>
            <div class="audit-toolbar-actions">
                @if($activeFilterCount)
                    <a href="{{ url('/AuditLogs') }}" class="btn btn-default"><i class="fa fa-times"></i> Reset</a>
                @endif
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#auditFilterModal">
                    <i class="fa fa-filter"></i> Filter
                    @if($activeFilterCount)
                        <span class="audit-filter-badge">{{ $activeFilterCount }}</span>
                    @endif
                </button>
            </div>
        </div>

        <div class="modal fade" id="auditFilterModal" tabindex="-1" role="dialog" aria-labelledby="auditFilterTitle">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form method="GET" action="{{ url('/AuditLogs') }}">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="auditFilterTitle"><i class="fa fa-filter"></i> Filter audit log</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nama user</label>
                                        <input type="text" name="name" class="form-control" value="{{ $filters['name'] ?? '' }}" maxlength="50" placeholder="Cari nama user">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Aksi</label>
                                        <select name="event" class="form-control">
                                            <option value="">Semua aksi</option>
                                            @foreach(['created' => 'Tambah', 'updated' => 'Ubah', 'deleted' => 'Hapus'] as $value => $label)
                                                <option value="{{ $value }}" @selected(($filters['event'] ?? '') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tabel</label>
                                        <select name="subject_type" class="form-control">
                                            <option value="">Semua tabel</option>
                                            @foreach($subjectTypes as $subjectType)
                                                <option value="{{ $subjectType }}" @selected(($filters['subject_type'] ?? '') === $subjectType)>{{ $subjectType }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>ID record</label>
                                        <input type="number" name="subject_id" class="form-control" value="{{ $filters['subject_id'] ?? '' }}" min="1" placeholder="ID data terkait">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tanggal dari</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tanggal sampai</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <input type="text" name="description" class="form-control" value="{{ $filters['description'] ?? '' }}" maxlength="255" placeholder="Cari deskripsi">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>IP address</label>
                                        <input type="text" name="ip_address" class="form-control" value="{{ $filters['ip_address'] ?? '' }}" maxlength="50" placeholder="Cari IP address">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ url('/AuditLogs') }}" class="btn btn-default pull-left">Reset semua</a>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Terapkan filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="box box-default audit-table-box">
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped audit-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Data</th>
                            <th>Deskripsi</th>
                            <th>Perubahan</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->{'created-at'} ? \Illuminate\Support\Carbon::parse($log->{'created-at'})->format('d-m-Y H:i:s') : '-' }}</td>
                                <td>{{ $log->name ?: 'System' }}</td>
                                <td>
                                    @php($eventLabels = ['created' => 'Tambah', 'updated' => 'Ubah', 'deleted' => 'Hapus'])
                                    <span class="label label-{{ $log->event === 'deleted' ? 'danger' : ($log->event === 'created' ? 'success' : 'warning') }}">
                                        {{ $eventLabels[$log->event] ?? $log->event }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $log->subject_type }}</strong><br>
                                    <small>ID: {{ $log->subject_id ?? '-' }}</small>
                                </td>
                                <td>{{ $log->description }}</td>
                                <td class="audit-change">
                                    @if($log->old_values)
                                        <details>
                                            <summary>Sebelum</summary>
                                            <pre>{{ $log->old_values }}</pre>
                                        </details>
                                    @endif
                                    @if($log->new_values)
                                        <details>
                                            <summary>Sesudah</summary>
                                            <pre>{{ $log->new_values }}</pre>
                                        </details>
                                    @endif
                                    @if(!$log->old_values && !$log->new_values)
                                        <span class="text-muted">Tidak ada detail</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $log->ip_address }}
                                    @if($log->user_agent)
                                        <br><small title="{{ $log->user_agent }}">{{ $log->user_agent }}</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada audit log yang cocok dengan filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                {{ $logs->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </section>

    <style>
        .audit-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
            padding: 16px 20px;
            border: 1px solid #e4eaf1;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(30, 55, 80, .05);
        }
        .audit-count { color: #23364d; font-size: 20px; font-weight: 700; }
        .audit-count span, .audit-filter-summary { color: #738196; font-size: 13px; font-weight: 400; }
        .audit-toolbar-actions { display: flex; align-items: center; gap: 8px; }
        .audit-filter-badge { margin-left: 4px; padding: 2px 6px; border-radius: 10px; background: #fff; color: #337ab7; }
        .audit-table-box { border: 1px solid #e4eaf1; border-radius: 8px; box-shadow: 0 2px 8px rgba(30, 55, 80, .05); }
        .audit-table-box .box-body { padding: 0; }
        .audit-table { margin-bottom: 0; }
        .audit-table th { white-space: nowrap; background: #f7f9fc; color: #46566b; }
        .audit-table td { vertical-align: middle !important; }
        .audit-table-box .box-footer { border-top: 1px solid #edf1f5; background: #fff; }
        .audit-change details { margin-bottom: 5px; }
        .audit-change summary { cursor: pointer; color: #337ab7; }
        .audit-change pre { max-width: 430px; max-height: 220px; overflow: auto; margin: 6px 0; white-space: pre-wrap; word-break: break-word; }
        #auditFilterModal .form-group { margin-bottom: 16px; }
        #auditFilterModal .form-control { border-radius: 4px; }
        @media (max-width: 767px) {
            .audit-toolbar { align-items: flex-start; padding: 14px; }
            .audit-toolbar-actions { flex-shrink: 0; }
        }
    </style>
</div>
@endsection
